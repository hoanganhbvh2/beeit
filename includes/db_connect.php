<?php
$servername = "localhost"; // Reverted from "127.0.0.1" back to "localhost"
$username = "root"; // Thay đổi nếu cần
$password = "";     // Thay đổi nếu cần
$dbname = "club_members";
$socket = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock"; // Add this line

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;unix_socket=$socket;charset=utf8", $username, $password);
    // Đặt chế độ lỗi PDO thành ngoại lệ
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>
