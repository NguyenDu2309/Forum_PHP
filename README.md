Công nghệ sử dụng
PHP (thuần)

MySQL

Bootstrap 5

HTML, CSS, JavaScript

📌 Tính năng chính
✅ Đăng ký / Đăng nhập người dùng

✅ Tạo chủ đề mới (threads)

✅ Thảo luận trong chủ đề bằng cách bình luận

✅ Quản lý người dùng và chủ đề từ trang admin

✅ Phân quyền người dùng: người dùng thường và admin

✅ Responsive UI với Bootstrap

📂 Cấu trúc thư mục

/

├── admin/               # Trang quản trị

├── components/          # Các phần header, navbar, footer tái sử dụng

├── includes/            # Kết nối CSDL, xử lý logic

├── partials/            # Các phần nhỏ của trang như form, alerts

├── uploads/             # Nơi lưu file được upload

├── threads/             # Trang chi tiết từng chủ đề

├── index.php            # Trang chủ diễn đàn

└── ...
⚙️ Cài đặt
Clone dự án:

git clone https://github.com/NguyenDu2309/Forum_website.git
Import cơ sở dữ liệu:

Mở file it_forum.sql bằng phpMyAdmin hoặc MySQL Workbench.

Tạo một database mới và import file đó.

Cấu hình kết nối cơ sở dữ liệu:

Mở file includes/db.php

Sửa thông tin kết nối:

php

$servername = "localhost";
$username = "root";
$password = "";
$database = "tên database trên phpadmin";
Chạy ứng dụng:

Dùng XAMPP hoặc một local server khác.

Đặt project trong thư mục htdocs/ nếu dùng XAMPP.

Truy cập: http://localhost/Forum_website

👤 Tài khoản mẫu
Vai trò	Tên đăng nhập	Mật khẩu
Admin	admin	123
User	user	user123

Lưu ý: Có thể phải tạo thủ công tài khoản nếu chưa có sẵn.

📝 Ghi chú
Dự án mang tính học tập, không nên dùng trực tiếp cho môi trường production.
