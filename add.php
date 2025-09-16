<?php
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $specialization = $_POST['specialization'];
    $image = null;

    // Xử lý tải lên ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $original_filename = basename($_FILES['image']['name']);
        $image_file_type = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $unique_filename = uniqid() . "." . $image_file_type;
        $target_file = $target_dir . $unique_filename;

        // Kiểm tra loại tệp
        $allowed_types = array("jpg", "png", "jpeg", "gif");
        if (!in_array($image_file_type, $allowed_types)) {
            echo "Chỉ chấp nhận các tệp JPG, JPEG, PNG & GIF.";
            exit();
        }

        // Kiểm tra kích thước tệp (ví dụ: tối đa 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            echo "Tệp của bạn quá lớn.";
            exit();
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $unique_filename;
        } else {
            echo "Có lỗi khi tải lên tệp của bạn.";
            exit();
        }
    }

    $sql = "INSERT INTO members (name, email, phone, specialization, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $email, $phone, $specialization, $image]);

    header("Location: index.php");
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
    <title>Thêm Thành Viên Mới</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imageUploadSection = document.querySelector('.image-upload-section');

            form.addEventListener('submit', function(event) {
                // Reset custom validity messages
                nameInput.setCustomValidity('');
                emailInput.setCustomValidity('');

                if (!nameInput.value.trim()) {
                    nameInput.setCustomValidity('Vui lòng nhập tên thành viên.');
                }
                if (!emailInput.value.trim()) {
                    emailInput.setCustomValidity('Vui lòng nhập địa chỉ email.');
                } else if (!/^[\w-]+(?:\.[\w-]+)*@(?:[\w-]+\.)+[a-zA-Z]{2,7}$/.test(emailInput.value)) {
                    emailInput.setCustomValidity('Vui lòng nhập địa chỉ email hợp lệ.');
                }

                // If any field has a custom validity message, prevent submission
                if (!form.checkValidity()) {
                    event.preventDefault();
                }

                // Show custom validation messages
                form.reportValidity();
            });

            // Xử lý xem trước ảnh
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.add('show');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '';
                    imagePreview.classList.remove('show');
                }
            });

            // Kích hoạt input file khi click vào image-upload-section
            imageUploadSection.addEventListener('click', function() {
                imageInput.click();
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="my-4 text-center">Thêm Thành Viên Mới</h1>
        <form action="add.php" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên:</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Điện Thoại:</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="specialization" class="form-label">Sở Trường:</label>
                        <input type="text" id="specialization" name="specialization" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="image" class="form-label">Ảnh Đại Diện:</label>
                        <div class="image-upload-section p-3 border rounded text-center mb-3">
                            Chọn ảnh (JPG, PNG, GIF, tối đa 5MB)
                            <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                        </div>
                        <img id="imagePreview" class="img-fluid img-thumbnail" src="" alt="Xem trước ảnh" style="display: none;">
                    </div>
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Thêm Thành Viên</button>
                <a href="index.php" class="btn btn-secondary">Quay lại danh sách</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
