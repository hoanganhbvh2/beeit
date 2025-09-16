<?php
include '../../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM activities WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: activity_management.php");
    exit();

    //$stmt->close(); // PDO does not have a close method like mysqli
    //$conn->close(); // PDO does not have a close method like mysqli
} else {
    echo "Không có ID hoạt động được cung cấp để xóa.";
    exit();
}
?>
