<?php
$servername = "localhost";
$username = "root"; // Thay đổi nếu cần
$password = "";     // Thay đổi nếu cần
$dbname = "club_members";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
