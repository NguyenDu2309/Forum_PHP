student_forum/
│── app/
│   ├── controllers/          # Handles requests
│   │   ├── AuthController.php  # Handles login, register, logout
│   │   ├── PostController.php  # Handles post creation, update, delete
│   │   ├── CommentController.php  # Manages comments on posts
│   ├── models/               # Business logic & database interactions
│   │   ├── User.php          # User model (authentication, registration)
│   │   ├── Post.php          # Post model (CRUD operations)
│   │   ├── Comment.php       # Comment model (CRUD operations)
│   ├── views/                # HTML templates for rendering
│   │   ├── components/         # Reusable components (header, footer, navbar)
│   │   │   ├── header.php    
│   │   │   ├── footer.php    
│   │   │   ├── navbar.php    
│   │   ├── auth/             # Authentication pages
│   │   │   ├── login.php     
│   │   │   ├── register.php  
│   │   ├── posts/            # Forum post pages
│   │   │   ├── new_post.php  
│   │   │   ├── edit_post.php 
│   │   │   ├── delete_post.php  
│   │   ├── comments/         # Comments system
│   │   │   ├── add_comment.php  
│   │   │   ├── delete_comment.php  
│   │   ├── home.php          # Main forum page (list posts)
│── config/
│   ├── database.php          # Database connection using PDO
│── public/
│   ├── assets/               # Static files (CSS, JS, images)
│   │   ├── css/              # Stylesheets
│   │   │   ├── styles.css    
│   │   ├── js/               # JavaScript files
│   │   │   ├── script.js    
│   │   ├── images/           # Uploaded images
│   ├── uploads/              # User-uploaded files (post images)
│   ├── index.php             # Main entry point (loads home.php)
│── .htaccess                 # URL rewriting (optional)
│── README.md                 # Project documentation



📌 Giải thích Folder Structure
1️⃣ app/ - Xử lý logic
controllers/ → Xử lý request từ người dùng.
models/ → Tương tác với Database.
views/ → Chứa giao diện trang web.
2️⃣ config/ - Cấu hình
database.php → Kết nối MySQL bằng PDO.
config.php → Các biến môi trường (base URL, tên site...).

4️⃣ public/ - File có thể truy cập từ trình duyệt
index.php → Điểm vào của ứng dụng (Front Controller).
assets/ → Chứa hình ảnh, CSS, JS.
5️⃣ routes/ - Điều hướng URL
web.php → Define các route, ví dụ:


Using __DIR__ to get the current directory


student_forum/
│── admin/
│   │── index.php  (Admin Dashboard)
│   │── users.php  (Manage Users)
│   │── posts.php  (Manage Posts)
│   │── modules.php  (Manage Modules)
│   │── styles.css  (Admin Dashboard Styling)
│── app/
│── public/
│── config/
│── ...
