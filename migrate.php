<?php

// Bao gồm tệp kết nối cơ sở dữ liệu
include 'includes/db_connect.php';

$migrations_dir = __DIR__ . '/migrations';

// --- Hàm hỗ trợ --- //

function log_message($message) {
    echo date('[Y-m-d H:i:s]') . " " . $message . "\n";
}

function get_ran_migrations($conn) {
    $ran_migrations = [];
    $sql = "SELECT migration FROM migrations";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $ran_migrations[] = $row['migration'];
        }
    } else {
        log_message("Lỗi khi đọc bảng migrations: " . $conn->error);
    }
    return $ran_migrations;
}

function record_migration($conn, $migration_file, $batch) {
    $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $migration_file, $batch);

    if (!$stmt->execute()) {
        log_message("Lỗi khi ghi log migration '{$migration_file}': " . $stmt->error);
    }
    $stmt->close();
}

function get_next_batch_number($conn) {
    $sql = "SELECT MAX(batch) as max_batch FROM migrations";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return ($row['max_batch'] ?? 0) + 1;
    }
    return 1;
}

function execute_sql_file($conn, $file_path) {
    $sql_content = file_get_contents($file_path);
    if ($conn->multi_query($sql_content)) {
        do {
            // Duyệt qua các kết quả để tránh lỗi commands out of sync
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        return true;
    } else {
        log_message("Lỗi khi thực thi SQL từ '{$file_path}': " . $conn->error);
        return false;
    }
}

function execute_php_file($file_path, $conn) {
    // PHP migration files can interact with $conn directly
    require_once $file_path;
    return true;
}

// --- Logic Migration Chính --- //

log_message("Bắt đầu quá trình migration...");

if (!is_dir($migrations_dir)) {
    log_message("Thư mục migrations không tồn tại: {$migrations_dir}");
    exit(1);
}

$all_migration_files = [];
foreach (scandir($migrations_dir) as $file) {
    if ($file !== '.' && $file !== '..') {
        $all_migration_files[] = $file;
    }
}

sort($all_migration_files); // Sắp xếp để đảm bảo thứ tự thực thi

$ran_migrations = get_ran_migrations($conn);
$migrations_to_run = [];

foreach ($all_migration_files as $file) {
    if (!in_array($file, $ran_migrations)) {
        $migrations_to_run[] = $file;
    }
}

if (empty($migrations_to_run)) {
    log_message("Không có migration mới để chạy.");
} else {
    $next_batch = get_next_batch_number($conn);
    log_message("Tìm thấy " . count($migrations_to_run) . " migration mới. Batch: " . $next_batch);

    foreach ($migrations_to_run as $migration_file) {
        $file_path = $migrations_dir . '/' . $migration_file;
        log_message("Đang chạy migration: {$migration_file}");

        $success = false;
        $extension = pathinfo($migration_file, PATHINFO_EXTENSION);

        if ($extension === 'sql') {
            $success = execute_sql_file($conn, $file_path);
        } elseif ($extension === 'php') {
            $success = execute_php_file($file_path, $conn);
        } else {
            log_message("Bỏ qua tệp migration không hợp lệ (không phải .sql hoặc .php): {$migration_file}");
            continue;
        }

        if ($success) {
            record_migration($conn, $migration_file, $next_batch);
            log_message("Migration '{$migration_file}' đã chạy thành công.");
        } else {
            log_message("Migration '{$migration_file}' thất bại. Dừng quá trình.");
            // Bạn có thể chọn rollback hoặc xử lý lỗi tùy theo yêu cầu
            exit(1);
        }
    }
    log_message("Tất cả migration trong batch " . $next_batch . " đã chạy xong.");
}

$conn->close();
log_message("Quá trình migration hoàn tất.");

?>
