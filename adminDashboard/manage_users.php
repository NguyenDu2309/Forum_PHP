<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Delete user account along with their comments, threads, and likes
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get username before deleting
        $stmt = $conn->prepare("SELECT user_name FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($user_name);
        $stmt->fetch();
        $stmt->close();

        if (!empty($user_name)) {
            // Xóa reply của user này
            $deleteUserReplies = "DELETE FROM replies WHERE user_name = ?";
            $stmt = $conn->prepare($deleteUserReplies);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->close();

            // Xóa reply liên quan đến comment của user này
            $deleteRepliesOfUserComments = "
                DELETE FROM replies 
                WHERE comment_id IN (SELECT comment_id FROM comments WHERE user_name = ?)
            ";
            $stmt = $conn->prepare($deleteRepliesOfUserComments);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->close();

            // Delete user's likes
            $deleteLikes = "DELETE FROM comment_likes WHERE user_id = ?";
            $stmt = $conn->prepare($deleteLikes);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            // Delete user's comments
            $deleteComments = "DELETE FROM comments WHERE user_name = ?";
            $stmt = $conn->prepare($deleteComments);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->close();

            // Delete user's threads
            $deleteThreads = "DELETE FROM thread WHERE thread_user_name = ?";
            $stmt = $conn->prepare($deleteThreads);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->close();
        }

        // Finally, delete user
        $deleteUser = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($deleteUser);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo "<script>alert('Người dùng và tất cả dữ liệu liên quan đã được xóa thành công!'); window.location.href = 'manage_users.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Lỗi khi xóa dữ liệu người dùng: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Fetch all users
$query = "SELECT user_id, user_name, login_time FROM users";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Error fetching users: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
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
        <h2 class="mb-4 text-3xl font-bold text-gray-800">Quản lý người dùng</h2>
        <a href="admin_dashboard.php" class="inline-block bg-gray-700 text-white px-5 py-2 rounded hover:bg-gray-900 transition text-base mb-4">← Quay lại Bảng điều khiển</a>

        <div class="overflow-x-auto rounded shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">#</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tên</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Đăng nhập lần cuối</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if ($result->num_rows > 0): ?>
                        <?php 
                        $serial_number = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="text-center"><?= $serial_number++; ?></td>
                                <td class="font-medium text-blue-700 text-lg"><?= htmlspecialchars($row['user_name']); ?></td>
                                <td class="text-center text-gray-500"><?= htmlspecialchars($row['login_time']); ?></td>
                                <td class="text-center space-y-2">
                                    <a href="user_activity.php?user=<?= htmlspecialchars($row['user_name']); ?>"
                                       class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold transition mb-1">
                                        Xem bài viết & bình luận
                                    </a>
                                    <a href="manage_users.php?delete=<?= htmlspecialchars($row['user_id']); ?>"
                                       class="inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold transition mb-1"
                                       onclick="return confirmDelete(<?= $row['user_id']; ?>);">
                                        Xóa tài khoản
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 text-lg">Không tìm thấy người dùng nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            return confirm("Bạn có chắc chắn muốn xóa tài khoản này?");
        }
    </script>
</body>
</html>
