<?php include 'includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thành Viên Câu Lạc Bộ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Danh Sách Thành Viên</h1>
        <a href="add.php" class="button">Thêm Thành Viên Mới</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Điện Thoại</th>
                    <th>Sở Trường</th>
                    <th>Ảnh</th>
                    <th>Ngày Tham Gia</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM members";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . "BEE" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["phone"] . "</td>";
                        echo "<td>" . $row["specialization"] . "</td>";
                        echo "<td>";
                        if ($row['image']) {
                            echo "<img src='uploads/" . htmlspecialchars($row['image']) . "' alt='Ảnh thành viên' class='member-image'>";
                        } else {
                            echo "Không có ảnh";
                        }
                        echo "</td>";
                        echo "<td>" . $row["join_date"] . "</td>";
                        echo "<td>
                                <a href='edit.php?id=" . $row["id"] . "' class='button edit'>Sửa</a>
                                <a href='delete.php?id=" . $row["id"] . "' class='button delete'>Xóa</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Không có thành viên nào.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
