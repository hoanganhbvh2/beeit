<?php
include 'includes/db_connect.php';

$member = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    $stmt->close();
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
    $stmt->bind_param("sssssi", $name, $email, $phone, $specialization, $image, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Thành Viên</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imageUploadSection = document.querySelector('.image-upload-section');
            const currentImage = document.getElementById('currentImageHidden').value; // Lấy ảnh hiện tại

            // Hiển thị ảnh hiện tại khi tải trang
            if (currentImage) {
                imagePreview.src = 'uploads/' + currentImage;
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
                    imagePreview.src = 'uploads/' + currentImage;
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
        <h1>Sửa Thông Tin Thành Viên</h1>
        <?php if ($member): ?>
        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
            <input type="hidden" name="current_image" id="currentImageHidden" value="<?php echo htmlspecialchars($member['image'] ?? ''); ?>">
            <div class="form-layout">
                <div class="form-left">
                    <div class="form-group">
                        <label for="name">Tên:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Điện Thoại:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="specialization">Sở Trường:</label>
                        <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($member['specialization'] ?? ''); ?>">
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
                <input type="submit" value="Cập Nhật Thành Viên">
            </div>
        </form>
        <?php else: ?>
            <p>Không tìm thấy thành viên.</p>
        <?php endif; ?>
        <p><a href="index.php">Quay lại danh sách</a></p>
    </div>
</body>
</html>
