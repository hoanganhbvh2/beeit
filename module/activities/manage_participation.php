<?php
session_start(); // Start the session
include '../../includes/db_connect.php';

$activity_id = $_GET['activity_id'] ?? null;

if (!$activity_id) {
    echo "Không có ID hoạt động được cung cấp.";
    exit();
}

// Fetch activity details
$activity = null;
$stmt = $conn->prepare("SELECT id, name FROM activities WHERE id = ?");
$stmt->bindParam(1, $activity_id, PDO::PARAM_INT);
$stmt->execute();
$activity = $stmt->fetch(PDO::FETCH_ASSOC);
//$stmt->close(); // PDO does not have a close method like mysqli

if (!$activity) {
    echo "Hoạt động không tìm thấy.";
    exit();
}

// Handle POST request to update participation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $activity_id = $_POST['activity_id'];
    $member_participations = $_POST['participation'] ?? []; // Only submitted (checked) member_ids will be present

    // Fetch all members to determine their participation status
    $all_members_stmt = $conn->prepare("SELECT id FROM members");
    $all_members_stmt->execute();
    $all_member_ids = $all_members_stmt->fetchAll(PDO::FETCH_COLUMN);
    $all_members_stmt->closeCursor(); // Close the cursor to allow new queries

    foreach ($all_member_ids as $member_id) {
        $status = 'khong_tham_gia'; // Default to 'không tham gia'
        if (isset($member_participations[$member_id]) && $member_participations[$member_id] == 'tham_gia') {
            $status = 'tham_gia'; // Set to 'tham_gia' if checkbox was checked
        }

        // Check if a record already exists
        $check_stmt = $conn->prepare("SELECT * FROM member_activities WHERE member_id = ? AND activity_id = ?");
        $check_stmt->execute([$member_id, $activity_id]);
        $record_exists = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($record_exists) {
            // Update existing record
            $update_stmt = $conn->prepare("UPDATE member_activities SET participation_status = ? WHERE member_id = ? AND activity_id = ?");
            $update_stmt->execute([$status, $member_id, $activity_id]);
        } else {
            // Insert new record
            $insert_stmt = $conn->prepare("INSERT INTO member_activities (member_id, activity_id, participation_status) VALUES (?, ?, ?)");
            $insert_stmt->execute([$member_id, $activity_id, $status]);
        }
    }
    // Add a success message to the session before redirecting
    $_SESSION['success_message'] = "Cập nhật trạng thái tham gia thành công!";
    header("Location: manage_participation.php?activity_id=" . $activity_id);
    exit();
} // End of POST request handler

// Display success message if set
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success mt-3">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Clear the message after displaying
}

// Fetch all members and their participation status for this activity
$members = [];
$sql = "SELECT m.id, m.name, ma.participation_status 
        FROM members m
        LEFT JOIN member_activities ma ON m.id = ma.member_id AND ma.activity_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $activity_id, PDO::PARAM_INT);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tham Gia Hoạt Động</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <a class="navbar-brand" href="#">Quản Lý</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Danh sách nhân sự</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="activity_management.php">Quản lý hoạt động</a>
                    </li>
                </ul>
            </div>
        </nav>
        <h1 class="my-4 text-center">Quản Lý Tham Gia Hoạt Động: <?php echo htmlspecialchars($activity['name']); ?></h1>
        <form action="manage_participation.php?activity_id=<?php echo $activity_id; ?>" method="POST">
            <input type="hidden" name="activity_id" value="<?php echo $activity_id; ?>">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-3">
                <?php foreach ($members as $member): ?>
                <div class="col">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="participation[<?php echo $member['id']; ?>]" id="member_<?php echo $member['id']; ?>" value="tham_gia" <?php echo ($member['participation_status'] == 'tham_gia') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="member_<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']) . " (BEE" . $member['id'] . ")"; ?></label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                <a href="activity_management.php" class="btn btn-secondary">Quay lại quản lý hoạt động</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
