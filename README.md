# Quản Lý Thành Viên Câu Lạc Bộ

Đây là một dự án quản lý thành viên câu lạc bộ đơn giản được xây dựng bằng HTML, CSS và PHP, sử dụng MySQL làm cơ sở dữ liệu.

## Tính năng

- Hiển thị danh sách thành viên với mã ID tiền tố `BEE`.
- Thêm thành viên mới với các thông tin: Tên, Email, Điện thoại, Sở trường và Ảnh đại diện (có xem trước ảnh và xác thực cơ bản).
- Sửa thông tin thành viên hiện có.
- Xóa thành viên.
- Hệ thống migration để quản lý thay đổi cơ sở dữ liệu.

## Yêu cầu

- Web server (ví dụ: Apache với XAMPP, WAMP).
- PHP (phiên bản 7.x trở lên).
- MySQL.

## Hướng dẫn triển khai (Deployment Guide)

Để triển khai dự án này trên môi trường mới, hãy làm theo các bước sau:

1.  **Sao chép dự án:**
    - Đặt toàn bộ thư mục `project1` vào thư mục gốc của web server của bạn (ví dụ: `htdocs` nếu bạn dùng XAMPP).

2.  **Cấu hình kết nối cơ sở dữ liệu:**
    - Mở tệp `includes/db_connect.php`.
    - Cập nhật các biến `$servername`, `$username`, `$password`, `$dbname` sao cho phù hợp với cấu hình MySQL của bạn (ví dụ: mật khẩu MySQL của bạn).

3.  **Chuẩn bị cơ sở dữ liệu và chạy Migrations:**
    - **Quan trọng:** Đảm bảo MySQL server của bạn đang chạy.
    - Mở Terminal hoặc Command Prompt.
    - Điều hướng đến thư mục gốc của dự án của bạn (nơi chứa tệp `migrate.php`):
        ```bash
        cd /path/to/your/project1
        ```
    - Chạy script migration. Script này sẽ:
        - Tạo cơ sở dữ liệu `club_members` nếu nó chưa tồn tại (từ `2023_01_01_initial_schema.sql`).
        - Tạo bảng `members` và bảng `migrations` nếu chúng chưa tồn tại.
        - Thực thi bất kỳ file migration `.sql` hoặc `.php` nào mới trong thư mục `migrations/`.
        ```bash
        php migrate.php
        ```
    - (Lưu ý: Bạn cần đảm bảo `php` có thể được gọi từ dòng lệnh. Nếu không, bạn cần cung cấp đường dẫn đầy đủ đến tệp thực thi `php`, ví dụ: `/Applications/XAMPP/bin/php migrate.php`).

4.  **Kiểm tra và cấu hình quyền thư mục (nếu cần):**
    - Đảm bảo thư mục `uploads/` có quyền ghi để PHP có thể lưu trữ ảnh vào đó. Trên Linux/macOS, bạn có thể cần chạy:
        ```bash
        chmod -R 775 uploads/
        ```

5.  **Truy cập ứng dụng:**
    - Mở trình duyệt web của bạn và truy cập:
        `http://localhost/project1/index.php` (hoặc đường dẫn tương ứng với cấu hình web server của bạn).
    - Bây giờ bạn có thể thêm, sửa, xóa thành viên và tải ảnh lên.

## Cấu trúc thư mục

```
project1/
├── css/
│   └── style.css
├── includes/
│   └── db_connect.php
├── migrations/
│   └── 2023_01_01_initial_schema.sql
├── uploads/
├── add.php
├── delete.php
├── edit.php
├── index.php
├── migrate.php
└── README.md
```
