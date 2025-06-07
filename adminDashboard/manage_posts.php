<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Pagination setup
$limit = 10; // Number of posts per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$offset = ($page - 1) * $limit;

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE thread_title LIKE '%$search%' OR thread_user_name LIKE '%$search%'";
}

// Query to get posts with pagination and search functionality
$query = "SELECT * FROM thread $search_query ORDER BY time DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of posts for pagination
$total_query = "SELECT COUNT(*) FROM thread $search_query";
$total_result = $conn->query($total_query);
$total_posts = $total_result->fetch_row()[0];
$total_pages = ceil($total_posts / $limit);

$stmt->close();

// Handle post deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM thread WHERE thread_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_posts.php'); // Redirect to avoid resubmitting form
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài viết</title>
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
        <h2 class="mb-4 text-2xl font-bold text-gray-800">Quản lý bài viết</h2>

        <!-- Search Form -->
        <form class="flex flex-col sm:flex-row gap-2 mb-4" method="GET" action="manage_posts.php">
            <input type="text" name="search" class="flex-1 rounded border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Tìm kiếm theo tiêu đề hoặc tác giả" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Tìm kiếm</button>
        </form>

        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="manage_posts.php" class="inline-block bg-gray-300 text-gray-800 px-3 py-1 rounded mb-3">Quay lại tất cả bài viết</a>
        <?php endif; ?>

        <div class="overflow-x-auto rounded shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">#</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tên bài viết</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tác giả</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Danh mục</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Được tạo lúc</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $serial = $offset + 1;
                    while ($post = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="text-center"><?= $serial++; ?></td>
                        <td class="font-medium text-blue-700"><?= htmlspecialchars($post['thread_title']); ?></td>
                        <td class="text-green-700"><?= htmlspecialchars($post['thread_user_name']); ?></td>
                        <td class="text-gray-700"><?= htmlspecialchars($post['thread_cat_id']); ?></td>
                        <td class="text-center text-gray-500"><?= htmlspecialchars($post['time']); ?></td>
                        <td class="text-center">
                            <a href="manage_posts.php?delete_id=<?= $post['thread_id']; ?>"
                               class="inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold transition"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="flex justify-center mt-6">
            <ul class="inline-flex items-center -space-x-px">
                <li>
                    <a href="manage_posts.php?page=<?= max($page - 1, 1); ?>&search=<?= isset($_GET['search']) ? urlencode($_GET['search']) : ''; ?>"
                       class="px-3 py-1 rounded-l border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                        Previous
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="manage_posts.php?page=<?= $i; ?>&search=<?= isset($_GET['search']) ? urlencode($_GET['search']) : ''; ?>"
                           class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-blue-100 <?= $i == $page ? 'bg-blue-500 text-white font-bold' : '' ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li>
                    <a href="manage_posts.php?page=<?= min($page + 1, $total_pages); ?>&search=<?= isset($_GET['search']) ? urlencode($_GET['search']) : ''; ?>"
                       class="px-3 py-1 rounded-r border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page >= $total_pages ? 'pointer-events-none opacity-50' : '' ?>">
                        Next
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</body>
</html>
