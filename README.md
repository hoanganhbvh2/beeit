# Quản Lý Thành Viên Câu Lạc Bộ

Đây là một dự án quản lý thành viên câu lạc bộ và hoạt động, được xây dựng bằng HTML, CSS và PHP, sử dụng MySQL làm cơ sở dữ liệu và hệ thống định tuyến (routing) tùy chỉnh.

## Tính năng

-   Hiển thị danh sách thành viên với mã ID tiền tố `BEE`.
-   Thêm, sửa, xóa thành viên với các thông tin: Tên, Email, Điện thoại, Sở trường và Ảnh đại diện (có xem trước ảnh và xác thực cơ bản).
-   **Quản lý hoạt động:**
    -   Hiển thị danh sách hoạt động.
    -   Thêm, sửa, xóa hoạt động.
    -   Giao diện quản lý thành viên tham gia hoặc không tham gia hoạt động.
    -   Thống kê 3 hoạt động có nhiều người tham gia nhất và 5 thành viên tham gia nhiều hoạt động nhất (sử dụng biểu đồ cột).
-   Hiển thị thông báo thành công sau khi thêm hoặc cập nhật dữ liệu (sử dụng session).
-   Hệ thống định tuyến tùy chỉnh để quản lý các URL một cách rõ ràng.

## Yêu cầu

-   Web server (ví dụ: Apache với XAMPP, WAMP) với `mod_rewrite` được kích hoạt.
-   PHP (phiên bản 7.x trở lên).
-   MySQL.

## Hướng dẫn triển khai (Deployment Guide)

Để triển khai dự án này trên môi trường mới, hãy làm theo các bước sau:

1.  **Sao chép dự án:**
    -   Đặt toàn bộ thư mục `project1` vào thư mục gốc của web server của bạn (ví dụ: `htdocs` nếu bạn dùng XAMPP).

2.  **Cấu hình kết nối cơ sở dữ liệu:**
    -   Mở tệp `includes/db_connect.php`.
    -   Cập nhật các biến `$servername`, `$username`, `$password`, `$dbname` và `$socket` sao cho phù hợp với cấu hình MySQL của bạn (ví dụ: mật khẩu MySQL của bạn và đường dẫn `unix_socket` của XAMPP).

3.  **Chuẩn bị cơ sở dữ liệu và chạy Migrations:**
    -   **Quan trọng:** Đảm bảo MySQL server của bạn đang chạy.
    -   Mở Terminal hoặc Command Prompt.
    -   Điều hướng đến thư mục gốc của dự án của bạn (nơi chứa tệp `migrate.php`):
        ```bash
        cd /path/to/your/project1
        ```
    -   Chạy script migration. Script này sẽ:
        -   Tạo cơ sở dữ liệu `club_members` nếu nó chưa tồn tại.
        -   Tạo bảng `members`, `activities`, `member_activities` và bảng `migrations` nếu chúng chưa tồn tại.
        -   Thực thi bất kỳ file migration `.sql` hoặc `.php` nào mới trong thư mục `migrations/`.
        ```bash
        php migrate.php
        ```
    -   (Lưu ý: Bạn cần đảm bảo `php` có thể được gọi từ dòng lệnh. Nếu không, bạn cần cung cấp đường dẫn đầy đủ đến tệp thực thi `php`, ví dụ: `/Applications/XAMPP/bin/php migrate.php`).

4.  **Cấu hình Apache `mod_rewrite` và `.htaccess`:**
    -   Đảm bảo module `mod_rewrite` được kích hoạt trong cấu hình Apache của bạn (`httpd.conf`). Tìm dòng `LoadModule rewrite_module modules/mod_rewrite.so` và đảm bảo nó không bị comment. Khởi động lại Apache sau khi thay đổi.
    -   Đảm bảo tệp `.htaccess` tồn tại trong thư mục gốc của dự án (`/project1/`) với nội dung sau:
        ```apache
        Options -Indexes
        RewriteEngine On
        RewriteBase /project1/
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [L]
        ```

5.  **Kiểm tra và cấu hình quyền thư mục (nếu cần):**
    -   Đảm bảo thư mục `uploads/` có quyền ghi để PHP có thể lưu trữ ảnh vào đó. Trên Linux/macOS, bạn có thể cần chạy:
        ```bash
        chmod -R 775 uploads/
        ```

6.  **Truy cập ứng dụng:**
    -   Mở trình duyệt web của bạn và truy cập:
        `http://localhost/project1/` (hoặc đường dẫn tương ứng với cấu hình web server của bạn).
    -   Tất cả các liên kết và chức năng sẽ được xử lý thông qua hệ thống định tuyến.

## Cấu trúc thư mục

```
project1/
├── .htaccess
├── includes/
│   └── db_connect.php
├── migrations/
│   └── 2023_01_01_initial_schema.sql
├── uploads/
├── views/
│   ├── activities/
│   │   ├── activity_management.php
│   │   ├── add_activity.php
│   │   ├── delete_activity.php
│   │   ├── edit_activity.php
│   │   └── manage_participation.php
│   ├── css/
│   │   └── style.css
│   ├── add_member.php
│   ├── delete_member.php
│   ├── edit_member.php
│   └── members.php
├── index.php
├── migrate.php
└── README.md
```
