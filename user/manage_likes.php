<?php
session_start();
include '../Partials/db_connection.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Verify if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Pagination setup
$limit = 10; // Number of comments per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = ($page <= 0) ? 1 : $page; // If page is less than or equal to 0, reset to 1
$offset = ($page - 1) * $limit; // Offset for SQL query

// Query to get liked comments and replies
$query = "
    SELECT 'comment' AS type, lc.comment_id AS id, c.comment, c.comment_time AS created_time, t.thread_title, t.thread_user_name, NULL AS reply_text
    FROM comment_likes lc
    INNER JOIN comments c ON lc.comment_id = c.comment_id
    INNER JOIN thread t ON c.thread_comment_id = t.thread_id
    WHERE lc.user_id = ?
    UNION ALL
    SELECT 'reply' AS type, lr.reply_id AS id, NULL AS comment, r.reply_time AS created_time, t.thread_title, t.thread_user_name, r.reply_text
    FROM reply_likes lr
    INNER JOIN replies r ON lr.reply_id = r.reply_id
    INNER JOIN comments c ON r.comment_id = c.comment_id
    INNER JOIN thread t ON c.thread_comment_id = t.thread_id
    WHERE lr.user_id = ?
    ORDER BY created_time DESC
    LIMIT ?, ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $user_id, $user_id, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();



// Get total number of liked comments and replies for pagination
$total_query = "
    SELECT 
        (SELECT COUNT(*) FROM comment_likes WHERE user_id = ?) +
        (SELECT COUNT(*) FROM reply_likes WHERE user_id = ?)
    AS total_liked
";
$stmt_total = $conn->prepare($total_query);
$stmt_total->bind_param("ii", $user_id, $user_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_liked_comments = $total_result->fetch_row()[0];
$total_pages = ceil($total_liked_comments / $limit);
$stmt_total->close();


// Handle unlike comment
if (isset($_GET['unlike_id']) && isset($_GET['type'])) {
    $unlike_id = intval($_GET['unlike_id']);
    $type = $_GET['type'];
    if ($type == 'comment') {
        $unlike_query = "DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?";
        $stmt_unlike = $conn->prepare($unlike_query);
        $stmt_unlike->bind_param("ii", $unlike_id, $user_id);
        $stmt_unlike->execute();
        $stmt_unlike->close();

        // Update comment like count
        $count_query = "SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?";
        $stmt_count = $conn->prepare($count_query);
        $stmt_count->bind_param("i", $unlike_id);
        $stmt_count->execute();
        $stmt_count->bind_result($new_likes);
        $stmt_count->fetch();
        $stmt_count->close();

        $update_comment = $conn->prepare("UPDATE comments SET likes = ? WHERE comment_id = ?");
        $update_comment->bind_param("ii", $new_likes, $unlike_id);
        $update_comment->execute();
        $update_comment->close();
    } else if ($type == 'reply') {
        $unlike_query = "DELETE FROM reply_likes WHERE reply_id = ? AND user_id = ?";
        $stmt_unlike = $conn->prepare($unlike_query);
        $stmt_unlike->bind_param("ii", $unlike_id, $user_id);
        $stmt_unlike->execute();
        $stmt_unlike->close();
        // Nếu muốn cập nhật số like cho reply, thêm code ở đây
    }
    header('Location: manage_likes.php');
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bình luận đã thích</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-gray-800 text-white fixed top-0 left-0 right-0 z-10 shadow">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <a class="font-bold text-lg" href="user_profile.php">Bảng điều khiển người dùng</a>
            <div class="flex space-x-4">
                <a class="hover:text-blue-400 transition" href="user_profile.php">Trở về Bảng điều khiển</a>
                <a class="hover:text-blue-400 transition" href="../Partials/_handle_logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto pt-20 flex-1 w-full">
        <div class="mt-2 mb-4">
            <a href="user_profile.php" class="inline-block bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 transition text-sm">← Trở về Bảng điều khiển</a>
        </div>

        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Bình luận đã thích</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-2 text-center font-semibold whitespace-nowrap">#</th>
                            <th class="py-2 px-2 text-left font-semibold whitespace-nowrap">Câu hỏi được đăng bởi</th>
                            <th class="py-2 px-2 text-left font-semibold whitespace-nowrap">Câu hỏi</th>
                            <th class="py-2 px-2 text-left font-semibold whitespace-nowrap">Nội dung</th>
                            <th class="py-2 px-2 text-center font-semibold whitespace-nowrap">Thời gian</th>
                            <th class="py-2 px-2 text-center font-semibold whitespace-nowrap">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $serial = $offset + 1;
                        while ($like = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-2 text-center font-semibold bg-gray-200"><?= $serial++; ?></td>
                            <td class="py-2 px-2 text-blue-700 font-medium break-words max-w-[120px] md:max-w-xs"><?= htmlspecialchars($like['thread_user_name']); ?></td>
                            <td class="py-2 px-2 text-green-700 break-words max-w-[160px] md:max-w-xs"><?= htmlspecialchars($like['thread_title']); ?></td>
                            <td class="py-2 px-2 break-words max-w-[200px] md:max-w-sm">
                                <?php if ($like['type'] == 'comment'): ?>
                                    <span class="font-semibold text-red-600">Bình luận:</span>
                                    <?= htmlspecialchars($like['comment']); ?>
                                <?php else: ?>
                                    <span class="font-semibold text-blue-500">Phản hồi:</span>
                                    <?= htmlspecialchars($like['reply_text']); ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-2 text-center text-gray-500 whitespace-nowrap"><?= htmlspecialchars($like['created_time']); ?></td>
                            <td class="py-2 px-2 text-center">
                                <?php if ($like['type'] == 'comment'): ?>
                                    <a href="manage_likes.php?unlike_id=<?= $like['id']; ?>&type=comment"
                                        class="inline-block bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200 hover:text-red-900 font-semibold text-sm transition whitespace-nowrap"
                                        style="min-width: 80px;"
                                        onclick="return confirm('Are you sure you want to unlike this comment?');">
                                        <span class="text-lg font-bold align-middle">✖</span> Bỏ thích
                                    </a>
                                <?php else: ?>
                                    <a href="manage_likes.php?unlike_id=<?= $like['id']; ?>&type=reply"
                                        class="inline-block bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200 hover:text-red-900 font-semibold text-sm transition whitespace-nowrap"
                                        style="min-width: 80px;"
                                        onclick="return confirm('Are you sure you want to unlike this reply?');">
                                        <span class="text-lg font-bold align-middle">✖</span> Bỏ thích
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav class="flex justify-center mt-4">
            <ul class="inline-flex items-center -space-x-px">
                <li>
                    <a href="manage_likes.php?page=<?= max($page - 1, 1); ?>"
                       class="px-3 py-1 rounded-l border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                        Previous
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="manage_likes.php?page=<?= $i; ?>"
                           class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-blue-100 <?= $i == $page ? 'bg-blue-500 text-white font-bold' : '' ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li>
                    <a href="manage_likes.php?page=<?= min($page + 1, $total_pages); ?>"
                       class="px-3 py-1 rounded-r border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page >= $total_pages ? 'pointer-events-none opacity-50' : '' ?>">
                        Next
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="mt-auto">
        <?php include '../Partials/_footer.php'; ?>
    </div>
</body>
</html>