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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            background-color: #343a40;
            color: white;
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            transition: left 0.3s ease-in-out;
            z-index: 1000;
            margin-top: 45px;
        }

        .sidebar.show {
            left: 0;
        }

        .sidebar a {
            color: #ddd;
            padding: 15px;
            text-decoration: none;
            display: block;

        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .main-content {
            transition: margin-left 0.3s ease-in-out;
        }

        .toggle-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
        }

        @media (min-width: 768px) {
            .sidebar {
                left: 0;
            }

            .main-content {
                margin-left: 250px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <span class="toggle-btn" id="toggle-btn"><i class="bi bi-list"></i></span>
            <span class="navbar-brand mx-auto">Xin chào, <?= htmlspecialchars($user_name) ?></span>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <h4 class=" mt-3 p-3">Bảng điều khiển người dùng</h4>
        
        <a href="../index.php"><i class="bi bi-house-door text-primary"></i> Đến Trang Chủ</a>
        <a href="manage_likes.php"><i class="bi bi-heart text-danger"></i> Bình luận đã thích</a>
        <a href="manage_posted_question.php"><i class="bi bi-file-earmark-text text-success"></i> Câu hỏi đã đăng</a>
        <a href="../user/manage_comments.php"><i class="bi bi-chat-dots text-info"></i> Bình luận đã đăng</a>
        <a href="manage_account.php"><i class="bi bi-key text-warning"></i> Đổi mật khẩu</a>
        <a href="profile.php"><i class="bi bi-person-circle text-warning"></i> Thông tin cá nhân</a>
        <a href="../Partials/_handle_logout.php"><i class="bi bi-box-arrow-right text-warning"></i> Đăng xuất</a>
        <a href="delete_account.php"><i class="bi bi-trash text-danger mt-5"></i> Xóa tài khoản</a>

    </div>

    <div class="main-content p-4" id="main-content">
        <div class="container mt-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white text-center p-3">
                        <h4>Câu hỏi đã đăng</h4>
                        <p class="fs-4"> <?= $totalThreads ?> </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-danger text-white text-center p-3">
                        <h4>Bình luận đã đăng</h4>
                        <p class="fs-4"> <?= $totalComments ?> </p>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="card bg-primary text-white text-center p-3">
                        <h4>Bình luận đã thích</h4>
                        <p class="fs-4"> <?= $totalLikes ?> </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggle-btn').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
</body>

</html>