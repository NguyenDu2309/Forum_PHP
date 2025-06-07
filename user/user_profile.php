<?php
    session_start();
    include '../Partials/db_connection.php';

    // Verify if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        header('Location: index.php');
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['username'];

    // Đếm số câu hỏi đã đăng
    $query = "SELECT COUNT(*) AS total_threads FROM thread WHERE thread_user_name = '$user_name'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $totalThreads = $row['total_threads'];

    // Đếm số bình luận đã đăng (bao gồm cả comment và reply)
    $query = "
        SELECT 
            (SELECT COUNT(*) FROM comments WHERE user_name = '$user_name') +
            (SELECT COUNT(*) FROM replies WHERE user_name = '$user_name')
            AS total_comments
    ";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $totalComments = $row['total_comments'];

    // Đếm tổng số bình luận đã thích (bao gồm comment và reply)
    $query = "
        SELECT 
            (SELECT COUNT(*) FROM comment_likes WHERE user_id = '$user_id') +
            (SELECT COUNT(*) FROM reply_likes WHERE user_id = '$user_id')
        AS total_likes
    ";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $totalLikes = $row['total_likes'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển người dùng</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-20 shadow">
        <div class="flex items-center justify-between px-4 py-3">
            <button id="toggle-btn" class="md:hidden text-2xl focus:outline-none">
                <i class="bi bi-list"></i>
            </button>
            <span class="mx-auto font-semibold text-lg">Xin chào, <?= htmlspecialchars($user_name) ?></span>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar"
         class="fixed top-[48px] left-0 h-[calc(100vh-48px)] w-64 bg-gray-800 text-white z-10 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col pt-6">
        <h4 class="text-xl font-bold px-6 mb-6">Bảng điều khiển người dùng</h4>
        <a href="../index.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-house-door text-primary mr-2"></i> Đến Trang Chủ</a>
        <a href="manage_likes.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-heart text-red-500 mr-2"></i> Bình luận đã thích</a>
        <a href="manage_posted_question.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-file-earmark-text text-green-500 mr-2"></i> Câu hỏi đã đăng</a>
        <a href="../user/manage_comments.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-chat-dots text-blue-400 mr-2"></i> Bình luận đã đăng</a>
        <a href="manage_account.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-key text-yellow-400 mr-2"></i> Đổi mật khẩu</a>
        <a href="profile.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-person-circle text-yellow-400 mr-2"></i> Thông tin cá nhân</a>
        <a href="../Partials/_handle_logout.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-box-arrow-right text-yellow-400 mr-2"></i> Đăng xuất</a>
        <a href="delete_account.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition"><i class="bi bi-trash text-red-500 mr-2"></i> Xóa tài khoản</a>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="pt-20 md:ml-64 transition-all duration-300">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-green-500 text-white rounded-lg shadow text-center p-6">
                    <h4 class="text-lg font-semibold mb-2">Câu hỏi đã đăng</h4>
                    <p class="text-3xl font-bold"><?= $totalThreads ?></p>
                </div>
                <div class="bg-red-500 text-white rounded-lg shadow text-center p-6">
                    <h4 class="text-lg font-semibold mb-2">Bình luận đã đăng</h4>
                    <p class="text-3xl font-bold"><?= $totalComments ?></p>
                </div>
                <div class="bg-blue-500 text-white rounded-lg shadow text-center p-6">
                    <h4 class="text-lg font-semibold mb-2">Bình luận đã thích</h4>
                    <p class="text-3xl font-bold"><?= $totalLikes ?></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');
        toggleBtn.addEventListener('click', function () {
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Close sidebar when clicking outside (mobile)
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Responsive: show sidebar by default on md+
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
</body>
</html>