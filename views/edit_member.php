<?php

$member = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    //$stmt->close(); // PDO does not have a close method like mysqli
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $specialization = $_POST['specialization'];
    $current_image = $_POST['current_image'] ?? null; // Ảnh hiện tại
    $image = $current_image;

    // Xử lý tải lên ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/"; // Updated path
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
            // Xóa ảnh cũ nếu có và không phải ảnh mặc định
            if ($current_image && file_exists($target_dir . $current_image)) {
                unlink($target_dir . $current_image);
            }
        } else {
            echo "Có lỗi khi tải lên tệp của bạn.";
            exit();
        }
    }

    $sql = "UPDATE members SET name = ?, email = ?, phone = ?, specialization = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $email, $phone, $specialization, $image, $id]);

    header("Location: /project1"); // Updated redirect path
    exit();

    //$stmt->close(); // PDO does not have a close method like mysqli
}
//$conn->close(); // PDO does not have a close method like mysqli
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Thành Viên</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Updated to relative path within views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imageUploadSection = document.querySelector('.image-upload-section');
            const currentImage = document.getElementById('currentImageHidden').value; // Lấy ảnh hiện tại

            // Hiển thị ảnh hiện tại khi tải trang
            if (currentImage) {
                imagePreview.src = '/project1/uploads/' + currentImage; // Updated to absolute path
                imagePreview.classList.add('show');
            }

            // Xử lý xem trước ảnh mới
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.add('show');
                    };
                    reader.readAsDataURL(file);
                } else if (currentImage) { // Nếu không có tệp mới nhưng có ảnh cũ
                    imagePreview.src = '/project1/uploads/' + currentImage; // Updated to absolute path
                    imagePreview.classList.add('show');
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
        <h1 class="my-4 text-center">Sửa Thông Tin Thành Viên</h1>
        <?php if ($member): ?>
        <form action="/project1/edit.php" method="POST" enctype="multipart/form-data"> <!-- Updated action path -->
            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
            <input type="hidden" name="current_image" id="currentImageHidden" value="<?php echo htmlspecialchars($member['image'] ?? ''); ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Điện Thoại:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="specialization" class="form-label">Sở Trường:</label>
                        <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($member['specialization'] ?? ''); ?>" class="form-control">
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
                <button type="submit" class="btn btn-primary">Cập Nhật Thành Viên</button>
                <a href="/project1" class="btn btn-secondary">Quay lại danh sách</a> <!-- Updated href -->
            </div>
        </form>
        <?php else: ?>
            <p class="alert alert-warning">Không tìm thấy thành viên.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
