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
    $stmt->bind_param("sssss", $name, $email, $phone, $specialization, $image);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Thành Viên Mới</title>
    <link rel="stylesheet" href="css/style.css">
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
        <h1>Thêm Thành Viên Mới</h1>
        <form action="add.php" method="POST" enctype="multipart/form-data">
            <div class="form-layout">
                <div class="form-left">
                    <div class="form-group">
                        <label for="name">Tên:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Điện Thoại:</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="specialization">Sở Trường:</label>
                        <input type="text" id="specialization" name="specialization">
                    </div>
                </div>
                <div class="form-right">
                    <div class="form-group">
                        <label for="image">Ảnh Đại Diện:</label>
                        <div class="image-upload-section">
                            Chọn ảnh (JPG, PNG, GIF, tối đa 5MB)
                            <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                        </div>
                        <img id="imagePreview" class="image-preview" src="" alt="Xem trước ảnh">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="Thêm Thành Viên">
            </div>
        </form>
        <p><a href="index.php">Quay lại danh sách</a></p>
    </div>
</body>
</html>
