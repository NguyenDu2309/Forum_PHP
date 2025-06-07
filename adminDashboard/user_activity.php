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
    <title>Hoạt động người dùng</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-10 shadow">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <a class="font-bold text-lg" href="admin_dashboard.php">Bảng điều khiển quản trị</a>
            <div class="flex gap-4">
                <a class="hover:text-blue-400 transition" href="admin_dashboard.php">Quay lại Bảng điều khiển</a>
                <a class="hover:text-blue-400 transition" href="admin_logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto pt-28 px-2 max-w-5xl">
        <h2 class="mb-4 text-2xl font-bold text-gray-800">Hoạt động của <?= htmlspecialchars($user_name) ?></h2>
        <a href="manage_users.php" class="inline-block bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900 transition text-sm mb-4">← Quay lại Quản lý người dùng</a>

        <!-- Posted Questions (Threads) -->
        <h3 class="mb-3 text-lg font-semibold text-blue-700">Câu hỏi đã đăng</h3>
        <div class="overflow-x-auto rounded shadow bg-white mb-8">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tiêu đề</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Mô tả chủ đề</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Thời gian đăng</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if ($result_threads->num_rows > 0): ?>
                        <?php while ($row = $result_threads->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td><?= htmlspecialchars($row['thread_title']); ?></td>
                                <td><?= htmlspecialchars($row['thread_desc']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['time']); ?></td>
                                <td class="text-center">
                                    <a href="delete_post.php?thread_id=<?= htmlspecialchars($row['thread_id']); ?>&user=<?= urlencode($user_name) ?>"
                                       class="inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold transition"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                        Xóa bài viết
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 text-lg">Không tìm thấy bài viết nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Comments by the User -->
        <h3 class="mb-3 text-lg font-semibold text-blue-700">Bình luận</h3>
        <div class="overflow-x-auto rounded shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tiêu đề</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Bình luận</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Thời gian bình luận</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if ($result_comments->num_rows > 0): ?>
                        <?php while ($row = $result_comments->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td><?= htmlspecialchars($row['thread_title']); ?></td>
                                <td><?= htmlspecialchars($row['comment']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['comment_time']); ?></td>
                                <td class="text-center">
                                    <a href="delete_comment.php?comment_id=<?= htmlspecialchars($row['comment_id']); ?>&user=<?= urlencode($user_name) ?>"
                                       class="inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold transition"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?');">
                                        Xóa bình luận
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 text-lg">Không tìm thấy bình luận nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
