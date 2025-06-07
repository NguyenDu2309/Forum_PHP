<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Pagination setup
$limit = 10; // Number of comments per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$offset = ($page - 1) * $limit;

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE comment LIKE '%$search%' OR user_name LIKE '%$search%'";
}

// Query to get comments with pagination and search functionality
$query = "SELECT * FROM comments $search_query ORDER BY comment_time DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of comments for pagination
$total_query = "SELECT COUNT(*) FROM comments $search_query";
$total_result = $conn->query($total_query);
$total_comments = $total_result->fetch_row()[0];
$total_pages = ceil($total_comments / $limit);

$stmt->close();

// Handle comment deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Xóa reply trước
    $conn->query("DELETE FROM replies WHERE comment_id = $delete_id");
    // Xóa comment
    $delete_query = "DELETE FROM comments WHERE comment_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php'); // Redirect to avoid resubmitting form
}

// Handle reply deletion
if (isset($_GET['delete_reply_id'])) {
    $delete_reply_id = intval($_GET['delete_reply_id']);
    $delete_reply_query = "DELETE FROM replies WHERE reply_id = ?";
    $stmt = $conn->prepare($delete_reply_query);
    $stmt->bind_param("i", $delete_reply_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php' . (isset($_GET['page']) ? '?page=' . intval($_GET['page']) : ''));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bình luận</title>
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
    <div class="container mx-auto pt-28 px-2 max-w-6xl">
        <h2 class="mb-4 text-2xl font-bold text-gray-800">Quản lý Bình luận</h2>

        <!-- Search Form -->
        <form class="flex flex-col sm:flex-row gap-2 mb-4" method="GET" action="manage_comments.php">
            <input type="text" name="search" class="flex-1 rounded border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Tìm kiếm bình luận hoặc tác giả" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Tìm kiếm</button>
        </form>

        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="manage_comments.php" class="inline-block bg-gray-300 text-gray-800 px-3 py-1 rounded mb-3">Quay lại tất cả bình luận</a>
        <?php endif; ?>

        <!-- Table of Comments -->
        <div class="overflow-x-auto rounded shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">#</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Bình luận</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tác giả</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tên bài viết</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Thời gian</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $serial = $offset + 1;
                    while ($comment = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="text-center"><?= $serial++; ?></td>
                        <td><?= htmlspecialchars($comment['comment']); ?></td>
                        <td class="text-blue-700"><?= htmlspecialchars($comment['user_name']); ?></td>
                        <td><?= htmlspecialchars($comment['thread_comment_id']); ?></td>
                        <td class="text-center text-gray-500"><?= htmlspecialchars($comment['comment_time']); ?></td>
                        <td class="text-center">
                            <a href="manage_comments.php?delete_id=<?= $comment['comment_id']; ?>"
                               class="inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold transition mb-1"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?');">
                                Xóa
                            </a>
                        </td>
                    </tr>
                    <?php
                    // Lấy các reply cho comment này
                    $reply_query = "SELECT * FROM replies WHERE comment_id = " . intval($comment['comment_id']) . " ORDER BY reply_id ASC";
                    $reply_result = mysqli_query($conn, $reply_query);
                    if ($reply_result && mysqli_num_rows($reply_result) > 0):
                        while ($reply = mysqli_fetch_assoc($reply_result)):
                    ?>
                    <tr class="bg-gray-50">
                        <td></td>
                        <td colspan="4" class="pl-8 border-l-4 border-blue-400">
                            <span class="font-semibold text-green-700">Trả lời bởi <?= htmlspecialchars($reply['user_name']); ?>:</span>
                            <?= htmlspecialchars($reply['reply_text']); ?>
                            <span class="text-gray-500 ml-2 text-xs">
                                <?= isset($reply['reply_time']) ? htmlspecialchars($reply['reply_time']) : ''; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="manage_comments.php?delete_reply_id=<?= $reply['reply_id']; ?>&page=<?= $page; ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                               class="inline-block bg-red-400 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?');">
                                Xóa
                            </a>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    endif;
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="flex justify-center mt-6">
            <ul class="inline-flex items-center -space-x-px">
                <li>
                    <a href="manage_comments.php?page=<?= max($page - 1, 1); ?>&search=<?= isset($_GET['search']) ? urlencode($_GET['search']) : ''; ?>"
                       class="px-3 py-1 rounded-l border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                        Previous
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="manage_comments.php?page=<?= $i; ?>&search=<?= isset($_GET['search']) ? urlencode($_GET['search']) : ''; ?>"
                           class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-blue-100 <?= $i == $page ? 'bg-blue-500 text-white font-bold' : '' ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li>
                    <a href="manage_comments.php?page=<?= min($page + 1, $total_pages); ?>&search=<?= isset($_GET['search']) ? urlencode($_GET['search']) : ''; ?>"
                       class="px-3 py-1 rounded-r border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page >= $total_pages ? 'pointer-events-none opacity-50' : '' ?>">
                        Next
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</body>
</html>
