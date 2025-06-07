<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch total number of users
$query = "SELECT COUNT(*) AS total_users FROM users";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalUsers = $row['total_users'];

// Fetch total number of categories
$query = "SELECT COUNT(*) AS total_categories FROM category";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalCategories = $row['total_categories'];

// Fetch total number of threads (posts)
$query = "SELECT COUNT(*) AS total_threads FROM thread";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalThreads = $row['total_threads'];

// Fetch total number of comments
$query = "SELECT COUNT(*) AS total_comments FROM comments";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalComments = $row['total_comments'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-20 shadow">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <button id="hamburger" class="text-2xl sm:hidden focus:outline-none">
                <i class="bi bi-list"></i>
            </button>
            <span class="font-bold text-lg text-center flex-1 sm:flex-none ml-24">Chào mừng Quản trị viên</span>
            <span class="hidden sm:block w-8"></span>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 left-0 h-full w-56 bg-gray-900 text-white z-30 pt-20 transition-transform duration-300 transform -translate-x-full sm:translate-x-0">
        <h4 class="text-center text-xl font-bold mb-6">Admin</h4>
        <nav class="flex flex-col gap-1">
            <a href="admin_dashboard.php" class="flex items-center gap-2 px-5 py-3 bg-blue-700 text-white rounded-r-full font-semibold">
                <i class="bi bi-speedometer2"></i> Bảng điều khiển
            </a>
            <a href="manage_users.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-people text-yellow-400"></i> Quản lý người dùng
            </a>
            <a href="add_category.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-folder-plus text-green-400"></i> Thêm danh mục
            </a>
            <a href="manage_category.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-folder text-blue-300"></i> Quản lý danh mục
            </a>
            <a href="manage_posts.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-file-earmark-text text-blue-400"></i> Quản lý câu hỏi đã đăng
            </a>
            <a href="manage_comments.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-chat-dots text-gray-400"></i> Quản lý bình luận
            </a>
            <a href="feedback.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-chat-dots text-red-400"></i> Tin nhắn/Phản hồi
            </a>
            <a href="change_password.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-key text-yellow-400"></i> Thay đổi mật khẩu
            </a>
            <a href="admin_profile.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-person-circle text-yellow-400"></i> Hồ sơ
            </a>
            <a href="admin_logout.php" class="flex items-center gap-2 px-5 py-3 hover:bg-gray-800 rounded-r-full">
                <i class="bi bi-box-arrow-right text-red-400"></i> Đăng xuất
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="pt-24 px-4 transition-all duration-300 sm:ml-56">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div class="bg-blue-600 text-white rounded-xl shadow flex flex-col items-center py-8">
                    <i class="bi bi-people text-4xl mb-2"></i>
                    <div class="text-lg font-semibold">Tổng số người dùng</div>
                    <div class="text-2xl font-bold mt-2"><?= $totalUsers ?></div>
                </div>
                <!-- Total Categories -->
                <div class="bg-green-600 text-white rounded-xl shadow flex flex-col items-center py-8">
                    <i class="bi bi-folder text-4xl mb-2"></i>
                    <div class="text-lg font-semibold">Tổng số danh mục</div>
                    <div class="text-2xl font-bold mt-2"><?= $totalCategories ?></div>
                </div>
                <!-- Total Posts -->
                <div class="bg-yellow-500 text-white rounded-xl shadow flex flex-col items-center py-8">
                    <i class="bi bi-file-earmark-text text-4xl mb-2"></i>
                    <div class="text-lg font-semibold">Tổng số bài viết</div>
                    <div class="text-2xl font-bold mt-2"><?= $totalThreads ?></div>
                </div>
                <!-- Total Comments -->
                <div class="bg-red-500 text-white rounded-xl shadow flex flex-col items-center py-8">
                    <i class="bi bi-chat-dots text-4xl mb-2"></i>
                    <div class="text-lg font-semibold">Tổng số bình luận</div>
                    <div class="text-2xl font-bold mt-2"><?= $totalComments ?></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const hamburger = document.getElementById('hamburger');
        const mainContent = document.getElementById('main-content');
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 640 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });
        // Responsive sidebar on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 640) {
                sidebar.classList.remove('-translate-x-full');
            }
        });
    </script>
</body>

</html>
