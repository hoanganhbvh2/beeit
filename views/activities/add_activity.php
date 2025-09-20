<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $activity_date = $_POST['activity_date'];

    $stmt = $conn->prepare("INSERT INTO activities (name, description, activity_date) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $activity_date]);

    header("Location: /project1/activities/activity_management.php"); // Updated redirect path
    exit();

    //$stmt->close(); // PDO does not have a close method like mysqli
    //$conn->close(); // PDO does not have a close method like mysqli
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Hoạt Động Mới</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Updated path -->
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
                        <a class="nav-link" href="/project1">Danh sách nhân sự</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="/project1/activities/activity_management.php">Quản lý hoạt động</a>
                    </li>
                </ul>
            </div>
        </nav>
        <h1 class="my-4 text-center">Thêm Hoạt Động Mới</h1>
        <form action="/project1/activities/add_activity.php" method="POST"> <!-- Updated action path -->
            <div class="mb-3">
                <label for="name" class="form-label">Tên Hoạt Động:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô Tả:</label>
                <textarea id="description" name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="activity_date" class="form-label">Ngày Hoạt Động:</label>
                <input type="date" id="activity_date" name="activity_date" class="form-control" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Thêm Hoạt Động</button>
                <a href="/project1/activities/activity_management.php" class="btn btn-secondary">Hủy</a> <!-- Updated href -->
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
