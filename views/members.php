<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial="1.0">
    <title>Quản Lý Thành Viên Câu Lạc Bộ</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Updated to relative path within views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <a class="navbar-brand" href="/project1">Quản Lý</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="/project1">Danh sách nhân sự</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/project1/views/activities/activity_management.php">Quản lý hoạt động</a>
                    </li>
                </ul>
            </div>
        </nav>

        <h3>Danh Sách Thành Viên</h3>
        <a href="/project1/views/add_member.php" class="button">Thêm Thành Viên Mới</a>
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
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($members) > 0) {
                    foreach($members as $row) {
                        echo "<tr>";
                        echo "<td>" . "BEE" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["phone"] . "</td>";
                        echo "<td>" . $row["specialization"] . "</td>";
                        echo "<td>";
                        if ($row['image']) {
                            echo "<img src='/project1/uploads/" . htmlspecialchars($row['image']) . "' alt='Ảnh thành viên' class='member-image'>"; // Updated to absolute path
                        } else {
                            echo "Không có ảnh";
                        }
                        echo "</td>";
                        echo "<td>" . $row["join_date"] . "</td>";
                        echo "<td>
                                <a href='/project1/views/edit_member.php?id=" . $row["id"] . "' class='button edit'>Sửa</a>
                                <a href='/project1/views/delete_member.php?id=" . $row["id"] . "' class='button delete'>Xóa</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Không có thành viên nào.</td></tr>";
                }
                //$conn->close(); // PDO does not have a close method like mysqli
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
