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
$limit = 10; // Number of items per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = ($page <= 0) ? 1 : $page; // If page is less than or equal to 0, reset to 1
$offset = ($page - 1) * $limit; // Offset for SQL query

// Search functionality
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "AND (c.comment LIKE '%$search%' OR t.thread_user_name LIKE '%$search%' OR t.thread_title LIKE '%$search%' OR r.reply_text LIKE '%$search%')";
}

// Query to get both comments and replies
$combined_query = "
    (SELECT 
        'comment' AS type,
        c.comment_id AS id,
        c.comment AS content,
        c.comment_time AS time,
        t.thread_id,
        t.thread_title,
        t.thread_user_name,
        NULL AS parent_comment,
        NULL AS parent_comment_id
    FROM comments c
    INNER JOIN thread t ON c.thread_comment_id = t.thread_id
    WHERE c.user_name = '$user_name' $search_query)
    
    UNION ALL
    
    (SELECT 
        'reply' AS type,
        r.reply_id AS id,
        r.reply_text AS content,
        r.reply_time AS time,
        t.thread_id,
        t.thread_title,
        t.thread_user_name,
        c.comment AS parent_comment,
        c.comment_id AS parent_comment_id
    FROM replies r
    INNER JOIN comments c ON r.comment_id = c.comment_id
    INNER JOIN thread t ON c.thread_comment_id = t.thread_id
    WHERE r.user_name = '$user_name' $search_query)
    
    ORDER BY time DESC
    LIMIT $offset, $limit";

$combined_result = mysqli_query($conn, $combined_query);

// Get total number of items for pagination
$total_query = "
    SELECT COUNT(*) FROM (
        (SELECT c.comment_id
        FROM comments c
        INNER JOIN thread t ON c.thread_comment_id = t.thread_id
        WHERE c.user_name = '$user_name' $search_query)
        
        UNION ALL
        
        (SELECT r.reply_id
        FROM replies r
        INNER JOIN comments c ON r.comment_id = c.comment_id
        INNER JOIN thread t ON c.thread_comment_id = t.thread_id
        WHERE r.user_name = '$user_name' $search_query)
    ) AS combined";

$total_result = $conn->query($total_query);
$total_items = $total_result->fetch_row()[0];
$total_pages = ceil($total_items / $limit); // Calculate total pages

// Handle comment deletion
if (isset($_GET['delete_comment_id'])) {
    $delete_id = intval($_GET['delete_comment_id']);
    $delete_query = "DELETE FROM comments WHERE comment_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php' . (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : ''));
    exit();
}

// Handle comment editing
if (isset($_POST['edit_comment'])) {
    $comment_id = $_POST['comment_id'];

    // Sanitize input (remove line breaks)
    $new_comment = $_POST['comment']; // Basic: no sanitization beyond escaping
    $new_comment = str_replace(array("\r", "\n"), ' ', $new_comment); // Replace line breaks with space

    // Escape for SQL (Do this right before the query)
    $new_comment = mysqli_real_escape_string($conn, $new_comment);

    $update_query = "UPDATE comments SET comment = ? WHERE comment_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_comment, $comment_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php' . (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : ''));
    exit();
}

// Handle reply deletion
if (isset($_GET['delete_reply_id'])) {
    $delete_reply_id = intval($_GET['delete_reply_id']);
    $delete_reply_query = "DELETE FROM replies WHERE reply_id = ?";
    $stmt = $conn->prepare($delete_reply_query);
    $stmt->bind_param("i", $delete_reply_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php' . (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : ''));
    exit();
}

// Handle reply editing
if (isset($_POST['edit_reply'])) {
    $reply_id = intval($_POST['reply_id']);
    $reply_text = trim($_POST['reply_text']);
    $reply_text = mysqli_real_escape_string($conn, $reply_text);
    $update_reply_query = "UPDATE replies SET reply_text = ? WHERE reply_id = ?";
    $stmt = $conn->prepare($update_reply_query);
    $stmt->bind_param("si", $reply_text, $reply_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php' . (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : ''));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bình luận và trả lời</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-10 shadow">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <a class="font-bold text-lg" href="user_profile.php">Bảng điều khiển người dùng</a>
            <div class="flex space-x-4">
                <a class="hover:text-blue-400 transition" href="user_profile.php">Trở về bảng điều khiển</a>
                <a class="hover:text-blue-400 transition" href="../Partials/_handle_logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto pt-20 flex-1 w-full">
        <a href="user_profile.php" class="inline-block bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 transition text-sm mb-4">← Trở về bảng điều khiển</a>
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Quản lý bình luận và trả lời</h2>

        <!-- Search Form -->
        <form class="mb-4 flex flex-col sm:flex-row gap-2" method="GET" action="manage_comments.php">
            <input type="text" name="search" class="flex-1 rounded border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Tìm kiếm bình luận, trả lời hoặc câu hỏi" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Tìm kiếm</button>
        </form>
        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="manage_comments.php" class="inline-block bg-gray-300 text-gray-800 px-3 py-1 rounded mb-3">Quay lại tất cả các mục</a>
        <?php endif; ?>

        <!-- Combined Table of Comments and Replies -->
        <div class="overflow-x-auto rounded shadow bg-white">
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
                    while ($item = $combined_result->fetch_assoc()):
                        $is_comment = ($item['type'] == 'comment');
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="bg-gray-200 text-center font-semibold"><?= $serial++; ?></td>
                        <td class="px-2 py-2 text-blue-700 font-medium break-words max-w-[120px] md:max-w-xs">
                            <span class="font-bold text-blue-600"> <?= htmlspecialchars($item['thread_user_name']); ?> </span>
                            <br>
                            <span class="<?= $is_comment ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?> rounded px-2 py-1 text-xs font-bold">
                                <?= $is_comment ? 'Comment' : 'Reply' ?>
                            </span>
                        </td>
                        <td class="px-2 py-2 text-green-700 break-words max-w-[160px] md:max-w-xs">
                            <span class="font-bold text-green-600">Câu hỏi:</span>
                            <?= htmlspecialchars($item['thread_title']); ?>
                        </td>
                        <td class="px-2 py-2 break-words max-w-[200px] md:max-w-sm bg-yellow-50">
                            <?php if (!$is_comment): ?>
                                <span class="font-bold text-red-600">Câu trả lời của bạn:</span>
                                <?= nl2br(htmlspecialchars($item['content'])); ?>
                                <?php if ($item['parent_comment']): ?>
                                    <div class="bg-gray-100 border-l-4 border-gray-400 pl-3 mt-2 italic text-sm">
                                        <small>In response to: <?= htmlspecialchars($item['parent_comment']); ?></small>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="font-bold text-red-600">Bình luận của bạn:</span>
                                <?= nl2br(htmlspecialchars($item['content'])); ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center text-gray-500 whitespace-nowrap"><?= htmlspecialchars($item['time']); ?></td>
                        <td class="text-center space-y-2">
                            <?php if ($is_comment): ?>
                                <!-- Edit Comment Button -->
                                <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm font-semibold transition w-full sm:w-auto mt-1"
                                    onclick="document.getElementById('editCommentModal<?= $item['id']; ?>').classList.remove('hidden')">
                                    ✏️ Chỉnh sửa
                                </button>
                                <!-- Delete Comment Button -->
                                <a href="manage_comments.php?delete_comment_id=<?= $item['id']; ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-semibold transition w-full sm:w-auto mt-1 inline-block"
                                    onclick="return confirm('Are you sure you want to delete this comment?');">
                                    ❌ Xóa
                                </a>
                            <?php else: ?>
                                <!-- Edit Reply Button -->
                                <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm font-semibold transition w-full sm:w-auto mt-1"
                                    onclick="document.getElementById('editReplyModal<?= $item['id']; ?>').classList.remove('hidden')">
                                    ✏️ Chỉnh sửa
                                </button>
                                <!-- Delete Reply Button -->
                                <a href="manage_comments.php?delete_reply_id=<?= $item['id']; ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-semibold transition w-full sm:w-auto mt-1 inline-block"
                                    onclick="return confirm('Are you sure you want to delete this reply?');">
                                    ❌ Xóa
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div id="edit<?= $is_comment ? 'Comment' : 'Reply' ?>Modal<?= $item['id']; ?>" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-2">
                            <div class="flex justify-between items-center border-b px-4 py-2">
                                <h5 class="font-bold text-lg"><?= $is_comment ? 'Chỉnh sửa bình luận' : 'Chỉnh sửa trả lời' ?></h5>
                                <button type="button" class="text-2xl font-bold text-gray-500 hover:text-red-500" onclick="document.getElementById('edit<?= $is_comment ? 'Comment' : 'Reply' ?>Modal<?= $item['id']; ?>').classList.add('hidden')">&times;</button>
                            </div>
                            <div class="p-4">
                                <form action="manage_comments.php<?= (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : '') ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="<?= $is_comment ? 'comment' : 'reply_text_' . $item['id']; ?>" class="block font-semibold mb-1"><?= $is_comment ? 'Bình luận' : 'Trả lời' ?></label>
                                        <textarea class="w-full border border-gray-300 rounded px-3 py-2" name="<?= $is_comment ? 'comment' : 'reply_text' ?>" id="<?= $is_comment ? 'comment' : 'reply_text_' . $item['id']; ?>" rows="4"><?= htmlspecialchars($item['content']); ?></textarea>
                                    </div>
                                    <input type="hidden" name="<?= $is_comment ? 'comment_id' : 'reply_id' ?>" value="<?= $item['id']; ?>">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition" onclick="document.getElementById('edit<?= $is_comment ? 'Comment' : 'Reply' ?>Modal<?= $item['id']; ?>').classList.add('hidden')">Hủy</button>
                                        <button type="submit" name="<?= $is_comment ? 'edit_comment' : 'edit_reply' ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Lưu thay đổi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="flex justify-center mt-6">
            <ul class="inline-flex items-center -space-x-px">
                <li>
                    <a href="manage_comments.php?page=<?= max($page - 1, 1); ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
                       class="px-3 py-1 rounded-l border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                        Previous
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="manage_comments.php?page=<?= $i; ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
                           class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-blue-100 <?= $i == $page ? 'bg-blue-500 text-white font-bold' : '' ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li>
                    <a href="manage_comments.php?page=<?= min($page + 1, $total_pages); ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
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
    <script>
        // Đóng modal khi nhấn Esc
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                document.querySelectorAll('[id^="editCommentModal"], [id^="editReplyModal"]').forEach(modal => modal.classList.add('hidden'));
            }
        });
    </script>
</body>
</html>