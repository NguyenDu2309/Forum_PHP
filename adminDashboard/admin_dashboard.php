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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sidebar Styling */
        .sidebar {
            background-color: #343a40;
            color: white;
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            overflow-y: auto;
            padding-top: 20px;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-250px);
        }

        .sidebar a {
            color: #ddd;
            padding: 15px 20px;
            text-decoration: none;
            display: block;
            font-size: 1rem;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: white;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Navbar Styling */
        .navbar {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1040;
        }

        .navbar .navbar-brand {
            margin: 0 auto;
            font-weight: bold;
            font-size: 1.5rem;
        }

        /* Hamburger Icon */
        .hamburger {
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
            display: none;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 0;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <span class="hamburger" id="hamburger"><i class="bi bi-list"></i></span>
            <span class="navbar-brand">Chào mừng Quản trị viên</span>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h4>Bảng điều khiển quản trị</h4>
        <a href="admin_dashboard.php" class="bg-primary text-white">
            <i class="bi bi-speedometer2"></i> Bảng điều khiển
        </a>
        <a href="manage_users.php"><i class="bi bi-people text-warning"></i> Quản lý người dùng</a>
        <a href="add_category.php"><i class="bi bi-folder-plus text-success"></i> Thêm danh mục</a>
        <a href="manage_category.php"><i class="bi bi-folder text-info"></i> Quản lý danh mục</a>
        <a href="manage_posts.php"><i class="bi bi-file-earmark-text text-primary"></i> Quản lý câu hỏi đã đăng</a>
        <a href="manage_comments.php"><i class="bi bi-chat-dots text-secondary"></i> Quản lý bình luận</a>
        <a href="feedback.php"><i class="bi bi-chat-dots text-danger"></i> Tin nhắn/Phản hồi</a>
        <a href="change_password.php"><i class="bi bi-key text-warning"></i> Thay đổi mật khẩu</a>
        <a href="admin_profile.php"><i class="bi bi-person-circle text-warning"></i> Hồ sơ</a>
        <a href="admin_logout.php"><i class="bi bi-box-arrow-right text-danger"></i> Đăng xuất</a>

    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container mt-5">
            <div class="row g-4">
                <!-- Total Users -->
                <div class="col-md-6 col-lg-3">
                    <div class="card text-white bg-primary shadow">
                        <div class="card-body text-center">
                            <i class="bi bi-people display-4"></i>
                            <h4 class="card-title mt-3">Tổng số người dùng</h4>
                            <p class="card-text fs-4"><?= $totalUsers ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Categories -->
                <div class="col-md-6 col-lg-3">
                    <div class="card text-white bg-success shadow">
                        <div class="card-body text-center">
                            <i class="bi bi-folder display-4"></i>
                            <h4 class="card-title mt-3">Tổng số danh mục</h4>
                            <p class="card-text fs-4"><?= $totalCategories ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Posts -->
                <div class="col-md-6 col-lg-3">
                    <div class="card text-white bg-warning shadow">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-text display-4"></i>
                            <h4 class="card-title mt-3">Tổng số bài viết</h4>
                            <p class="card-text fs-4"><?= $totalThreads ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Comments -->
                <div class="col-md-6 col-lg-3">
                    <div class="card text-white bg-danger shadow">
                        <div class="card-body text-center">
                            <i class="bi bi-chat-dots display-4"></i>
                            <h4 class="card-title mt-3">Tổng số bình luận</h4>
                            <p class="card-text fs-4"><?= $totalComments ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const hamburger = document.getElementById('hamburger');

        // Toggle sidebar visibility
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('collapsed');
        });
    </script>

</body>

</html>
