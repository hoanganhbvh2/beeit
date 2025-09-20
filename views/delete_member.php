<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    header("Location: /project1"); // Updated redirect path
    exit();

    //$stmt->close(); // PDO does not have a close method like mysqli
    //$conn->close(); // PDO does not have a close method like mysqli
} else {
    header("Location: /project1"); // Updated redirect path
    exit();
}
?>
