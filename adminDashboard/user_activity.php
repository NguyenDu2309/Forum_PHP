<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Get the username of the user whose posts and comments we are viewing
if (isset($_GET['user'])) {
    $user_name = htmlspecialchars($_GET['user']);
} else {
    header('Location: manage_users.php');
    exit();
}

// Fetch posts (threads) by the selected user
$query_threads = "
    SELECT thread_id, thread_title, thread_desc, time
    FROM thread
    WHERE thread_user_name = ?
";
$stmt_threads = $conn->prepare($query_threads);
$stmt_threads->bind_param("s", $user_name);
$stmt_threads->execute();
$result_threads = $stmt_threads->get_result();

// Fetch comments by the selected user
$query_comments = "
    SELECT comment_id, comment, comment_time, thread_title
    FROM comments c
    JOIN thread t ON c.thread_comment_id = t.thread_id
    WHERE c.user_name = ?
";
$stmt_comments = $conn->prepare($query_comments);
$stmt_comments->bind_param("s", $user_name);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();

// Check if the queries were successful
if (!$result_threads || !$result_comments) {
    die("Error fetching user activity: " . $conn->error); // Display an error if the query failed
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for the page */
        body {
            background-color: #f4f4f9; /* Light grayish background */
        }

        .container {
            margin-top: 80px; /* Adjusting for navbar space */
        }

        /* Thicker borders for the table */
        .table {
            border: 2px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            border: 2px solid #ddd; /* Thicker borders */
        }

        /* Table Header Styling */
        .table thead {
            background-color: #007bff; /* Blue background for header */
            color: white;
        }

        /* Hover effect on table rows */
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1); /* Light blue hover effect */
        }

        /* Button Styling */
        .btn {
            padding: 8px 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .table th,
            .table td {
                font-size: 14px;
            }

            .navbar-brand {
                font-size: 16px;
            }

            .navbar-toggler {
                border-color: #fff;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">Bảng điều khiển quản trị</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">Quay lại Bảng điều khiển</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2 class="mb-4">Hoạt động của <?= $user_name ?> </h2>
        <a href="manage_users.php" class="btn btn-secondary mb-4">Quay lại Quản lý người dùng</a>

        <!-- Posted Questions (Threads) -->
        <h3 class="mb-3">Câu hỏi đã đăng</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Mô tả chủ đề</th>
                        <th>Thời gian đăng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_threads->num_rows > 0): ?>
                        <?php while ($row = $result_threads->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['thread_title']); ?></td>
                                <td><?= htmlspecialchars($row['thread_desc']); ?></td>
                                <td><?= htmlspecialchars($row['time']); ?></td>
                                <td>
                                    <a href="delete_post.php?thread_id=<?= htmlspecialchars($row['thread_id']); ?>" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">Xóa bài viết</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không tìm thấy bài viết nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Comments by the User -->
        <h3 class="mb-3">Bình luận</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Bình luận</th>
                        <th>Thời gian bình luận</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_comments->num_rows > 0): ?>
                        <?php while ($row = $result_comments->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['thread_title']); ?></td>
                                <td><?= htmlspecialchars($row['comment']); ?></td>
                                <td><?= htmlspecialchars($row['comment_time']); ?></td>
                                <td>
                                    <a href="delete_comment.php?comment_id=<?= htmlspecialchars($row['comment_id']); ?>" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?');">Xóa bình luận</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không tìm thấy bình luận nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
