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
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = ($page <= 0) ? 1 : $page;
$offset = ($page - 1) * $limit;

// Search functionality
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "AND (thread_title LIKE '%$search%' OR thread_user_name LIKE '%$search%')";
}

// Query to get threads with pagination and search
$query = "SELECT thread.*, category.category_name 
          FROM thread 
          INNER JOIN category ON thread.thread_cat_id = category.category_id
          WHERE thread_user_name = '$user_name' $search_query
          ORDER BY time DESC 
          LIMIT $offset, $limit";
          
$result = mysqli_query($conn, $query);

// Get total number of questions for pagination
$total_query = "SELECT COUNT(*) FROM thread WHERE thread_user_name = '$user_name' $search_query";
$total_result = $conn->query($total_query);
$total_threads = $total_result->fetch_row()[0];
$total_pages = ceil($total_threads / $limit);

// Handle thread deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Delete associated comments first
    $delete_comments_query = "DELETE FROM comments WHERE thread_comment_id = ?";
    $stmt_comments = $conn->prepare($delete_comments_query);
    $stmt_comments->bind_param("i", $delete_id);
    $stmt_comments->execute();
    $stmt_comments->close();

    // Delete the thread
    $delete_query = "DELETE FROM thread WHERE thread_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_posted_question.php' . (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : ''));
    exit();
}

// Handle thread editing
if (isset($_POST['edit_thread'])) {
    $thread_id = $_POST['thread_id'];
    $new_title = mysqli_real_escape_string($conn, $_POST['thread_title']);

    // Handle image upload
    $imageFileName = null;
    if (isset($_FILES['thread_image']) && $_FILES['thread_image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../uploads/thread_images/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imageFileName = uniqid() . '_' . basename($_FILES["thread_image"]["name"]);
        $targetFile = $targetDir . $imageFileName;
        move_uploaded_file($_FILES["thread_image"]["tmp_name"], $targetFile);

        // Get old image name to delete if needed (optional)
        $oldImgQuery = "SELECT thread_image FROM thread WHERE thread_id = ?";
        $stmtOld = $conn->prepare($oldImgQuery);
        $stmtOld->bind_param("i", $thread_id);
        $stmtOld->execute();
        $stmtOld->bind_result($oldImg);
        $stmtOld->fetch();
        $stmtOld->close();
        if ($oldImg && file_exists($targetDir . $oldImg)) {
            @unlink($targetDir . $oldImg);
        }
    }

    // Update query
    if ($imageFileName) {
        $update_query = "UPDATE thread SET thread_title = ?, thread_image = ? WHERE thread_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $new_title, $imageFileName, $thread_id);
    } else {
        $update_query = "UPDATE thread SET thread_title = ? WHERE thread_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_title, $thread_id);
    }
    $stmt->execute();
    $stmt->close();
    header('Location: manage_posted_question.php' . (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : ''));
    exit();
}

// Total comments and replies by the user
$total_comments_query = "SELECT COUNT(*) FROM comments WHERE thread_comment_id IN (SELECT thread_id FROM thread WHERE thread_user_name = '$user_name')";
$total_comments_result = mysqli_query($conn, $total_comments_query);
$total_comments = $total_comments_result ? mysqli_fetch_row($total_comments_result)[0] : 0;

$total_replies_query = "SELECT COUNT(*) FROM replies WHERE comment_id IN (SELECT comment_id FROM comments WHERE thread_comment_id IN (SELECT thread_id FROM thread WHERE thread_user_name = '$user_name'))";
$total_replies_result = mysqli_query($conn, $total_replies_query);
$total_replies = $total_replies_result ? mysqli_fetch_row($total_replies_result)[0] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý câu hỏi đã đăng</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-10 shadow">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <a class="font-bold text-lg" href="user_profile.php">Bảng điều khiển người dùng</a>
            <div class="flex space-x-4">
                <a class="hover:text-blue-400 transition" href="user_profile.php">Quay lại Bảng điều khiển</a>
                <a class="hover:text-blue-400 transition" href="../Partials/_handle_logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto pt-20 flex-1 w-full">
        <a href="user_profile.php" class="inline-block bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 transition text-sm mb-4">← Quay lại Bảng điều khiển</a>
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Quản lý câu hỏi đã đăng</h2>

        <!-- Search Form -->
        <form class="mb-4 flex flex-col sm:flex-row gap-2" method="GET" action="manage_posted_question.php">
            <input type="text" name="search" class="flex-1 rounded border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Tìm kiếm câu hỏi" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Tìm kiếm</button>
        </form>
        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="manage_posted_question.php" class="inline-block bg-gray-300 text-gray-800 px-3 py-1 rounded mb-3">Quay về các câu hỏi</a>
        <?php endif; ?>

        <!-- Table of Questions -->
        <div class="overflow-x-auto rounded shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-2 text-center font-semibold whitespace-nowrap">#</th>
                        <th class="py-2 px-2 text-left font-semibold whitespace-nowrap">Danh mục & Câu hỏi</th>
                        <th class="py-2 px-2 text-center font-semibold whitespace-nowrap">Thời gian</th>
                        <th class="py-2 px-2 text-center font-semibold whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $serial = $offset + 1;
                    while ($thread = $result->fetch_assoc()):
                        $thread_id = $thread['thread_id'];
                        $comment_count_query = "SELECT COUNT(*) FROM comments WHERE thread_comment_id = $thread_id";
                        $comment_count_result = mysqli_query($conn, $comment_count_query);
                        $comment_count = $comment_count_result ? mysqli_fetch_row($comment_count_result)[0] : 0;
                        $reply_count_query = "SELECT COUNT(*) FROM replies WHERE comment_id IN (SELECT comment_id FROM comments WHERE thread_comment_id = $thread_id)";
                        $reply_count_result = mysqli_query($conn, $reply_count_query);
                        $reply_count = $reply_count_result ? mysqli_fetch_row($reply_count_result)[0] : 0;
                    ?>
                    <tr>
                        <td class="bg-gray-200 text-center font-semibold"><?= $serial++; ?></td>
                        <td class="px-2 py-2">
                            <div class="font-semibold text-blue-700 mb-1">Danh mục: <?= htmlspecialchars($thread['category_name']); ?></div>
                            <div class="font-bold text-red-600">Câu hỏi:</div>
                            <div class="mb-2"><?= htmlspecialchars($thread['thread_title']); ?></div>
                            <span class="inline-block bg-blue-100 text-blue-800 rounded px-2 py-1 text-xs mr-2">Bình luận: <?= $comment_count ?></span>
                            <span class="inline-block bg-green-100 text-green-800 rounded px-2 py-1 text-xs">Trả lời: <?= $reply_count ?></span>
                        </td>
                        <td class="text-center text-gray-500 whitespace-nowrap"><?= htmlspecialchars($thread['time']); ?></td>
                        <td class="text-center space-y-2">
                            <!-- Edit Button -->
                            <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm font-semibold transition w-full sm:w-auto mt-1"
                                onclick="document.getElementById('editThreadModal<?= $thread['thread_id']; ?>').classList.remove('hidden')">
                                ✏️ Chỉnh sửa
                            </button>
                            <!-- Delete Button -->
                            <a href="manage_posted_question.php?delete_id=<?= $thread['thread_id']; ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-semibold transition w-full sm:w-auto mt-1 inline-block"
                                onclick="return confirm('Are you sure you want to delete this question and its comments?');">
                                ❌ Xóa
                            </a>
                        </td>
                    </tr>

                    <!-- Edit Thread Modal (Tailwind) -->
                    <div id="editThreadModal<?= $thread['thread_id']; ?>" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-2">
                            <div class="flex justify-between items-center border-b px-4 py-2">
                                <h5 class="font-bold text-lg">Chỉnh sửa câu hỏi</h5>
                                <button type="button" class="text-2xl font-bold text-gray-500 hover:text-red-500" onclick="document.getElementById('editThreadModal<?= $thread['thread_id']; ?>').classList.add('hidden')">&times;</button>
                            </div>
                            <div class="p-4">
                                <form action="manage_posted_question.php<?= (isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : '') ?>" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="thread_title" class="block font-semibold mb-1">Câu hỏi</label>
                                        <textarea class="w-full border border-gray-300 rounded px-3 py-2" name="thread_title" id="thread_title" rows="4"><?= htmlspecialchars($thread['thread_title']); ?></textarea>
                                    </div>
                                    <?php if (!empty($thread['thread_image'])): ?>
                                        <div class="mb-3">
                                            <label class="block font-semibold mb-1">Ảnh hiện tại:</label>
                                            <img src="../uploads/thread_images/<?= htmlspecialchars($thread['thread_image']); ?>" alt="Thread Image" class="max-w-[120px] max-h-[120px] rounded shadow">
                                        </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label for="thread_image" class="block font-semibold mb-1">Thay đổi hình ảnh (Tùy chọn)</label>
                                        <input type="file" class="block w-full border border-gray-300 rounded px-3 py-2" name="thread_image" id="thread_image" accept="image/*">
                                    </div>
                                    <input type="hidden" name="thread_id" value="<?= $thread['thread_id']; ?>">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition" onclick="document.getElementById('editThreadModal<?= $thread['thread_id']; ?>').classList.add('hidden')">Hủy</button>
                                        <button type="submit" name="edit_thread" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Lưu thay đổi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Comment Section -->
                    <tr>
                        <td colspan="4" class="bg-gray-50">
                            <?php
                            $comment_query = "SELECT comments.*, users.user_name
                                                FROM comments
                                                INNER JOIN users ON comments.user_name = users.user_name
                                                WHERE comments.thread_comment_id = " . $thread['thread_id'];
                            $comment_result = mysqli_query($conn, $comment_query);

                            if ($comment_result && mysqli_num_rows($comment_result) > 0) {
                                while ($comment = mysqli_fetch_assoc($comment_result)) {
                                    ?>
                                    <div class="bg-gray-100 rounded p-3 mb-2">
                                        <p>
                                            <span class="font-bold text-blue-600"><?= htmlspecialchars($comment['user_name']); ?>:</span>
                                            <?= htmlspecialchars($comment['comment']); ?>
                                        </p>
                                        <?php
                                        // Lấy các reply cho comment này
                                        $reply_query = "SELECT * FROM replies WHERE comment_id = " . $comment['comment_id'] . " ORDER BY reply_id ASC";
                                        $reply_result = mysqli_query($conn, $reply_query);
                                        if ($reply_result && mysqli_num_rows($reply_result) > 0) {
                                            while ($reply = mysqli_fetch_assoc($reply_result)) {
                                                ?>
                                                <div class="ml-4 pl-3 border-l-4 border-blue-400 mb-1">
                                                    <span class="text-green-700 font-semibold"><?= htmlspecialchars($reply['user_name']); ?>:</span>
                                                    <?= htmlspecialchars($reply['reply_text']); ?>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php

                                }
                            } else {
                                echo '<p class="text-center italic text-gray-500">Chưa có trả lời nào.</p>';
                            }
                            ?>
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
                    <a href="manage_posted_question.php?page=<?= max($page - 1, 1); ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
                       class="px-3 py-1 rounded-l border border-gray-300 bg-white text-gray-700 hover:bg-gray-200 <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                        Previous
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="manage_posted_question.php?page=<?= $i; ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
                           class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-blue-100 <?= $i == $page ? 'bg-blue-500 text-white font-bold' : '' ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li>
                    <a href="manage_posted_question.php?page=<?= min($page + 1, $total_pages); ?><?= (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''); ?>"
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
                document.querySelectorAll('[id^="editThreadModal"]').forEach(modal => modal.classList.add('hidden'));
            }
        });
    </script>
</body>
</html>