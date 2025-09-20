<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM activities WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: /project1/activities/activity_management.php"); // Updated redirect path
    exit();

    //$stmt->close(); // PDO does not have a close method like mysqli
    //$conn->close(); // PDO does not have a close method like mysqli
} else {
    echo "Không có ID hoạt động được cung cấp để xóa.";
    exit();
}
?>
