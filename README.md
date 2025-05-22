# 🗣️ Forum Website (PHP)

Diễn đàn thảo luận trực tuyến xây dựng bằng PHP thuần, với các chức năng như đăng ký, đăng nhập, đăng bài, bình luận và đặc biệt là **lọc ngôn ngữ không phù hợp** trong nội dung bài viết và bình luận.

## 🚀 Tính năng nổi bật

- 👥 Đăng ký, đăng nhập, đăng xuất người dùng
- 📝 Tạo bài viết, chỉnh sửa, xóa bài
- 💬 Bình luận bài viết
- 🔒 Quản lý người dùng và bài viết (admin)
- 🧹 **Mô hình lọc ngôn ngữ thô tục/bạo lực** để giữ môi trường diễn đàn lành mạnh
- 🕵️ Tìm kiếm bài viết theo từ khóa

## 🧠 Công nghệ sử dụng

- PHP (thuần, không framework)
- MySQL
- HTML/CSS (Bootstrap)
- JavaScript (AJAX một số phần)
- Bộ lọc ngôn ngữ: Tự xây dựng bằng cách đối chiếu danh sách từ cấm

## 🏗️ Cấu trúc thư mục

Forum_website/
│
├── admin/               # Trang quản trị bài viết và người dùng
├── includes/            # Các file cấu hình, kết nối CSDL, session,...
├── posts/               # Giao diện và xử lý đăng bài
├── users/               # Giao diện và xử lý tài khoản
├── assets/              # File CSS, hình ảnh, JavaScript
├── filter.php           # Mô-đun lọc ngôn ngữ không phù hợp
├── index.php            # Trang chủ
└── README.md

## ⚙️ Cài đặt và sử dụng

### 1. Clone repo

git clone https://github.com/NguyenDu2309/Forum_website.git

### 2. Tạo database

- Import file `forum.sql` trong thư mục gốc vào MySQL để tạo database và các bảng.

### 3. Cấu hình kết nối database

- Mở file `includes/db.php` và chỉnh thông tin kết nối:

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'forum';

### 4. Chạy ứng dụng

- Mở trình duyệt và truy cập `http://localhost/Forum_website/`

## 🧼 Mô hình lọc ngôn ngữ

- File `filter.php` chứa danh sách các từ cần lọc (tiếng Việt và tiếng Anh).
- Khi người dùng đăng bài hoặc bình luận, hệ thống sẽ kiểm tra nội dung và **ẩn hoặc thay thế** các từ không phù hợp.
- Có thể mở rộng bằng AI hoặc thư viện học máy để phân loại nội dung.

Ví dụ xử lý:

function filterBadWords($text) {
    $badWords = ['bậy', 'chửi', 'xxx', 'đm', 'cc'];
    return str_ireplace($badWords, '***', $text);
}

## 🔐 Tài khoản mẫu

- **Admin**
  - Username: `admin`
  - Password: `admin123`

- **Người dùng**
  - Tự đăng ký qua giao diện

## 📌 Kế hoạch mở rộng

- Nâng cấp mô hình lọc ngôn ngữ bằng NLP
- Thêm markdown cho bài viết
- Giao diện responsive tốt hơn (Tailwind hoặc React frontend)
- Thêm hệ thống thông báo

## 📄 Giấy phép

Mã nguồn mở theo giấy phép MIT.
