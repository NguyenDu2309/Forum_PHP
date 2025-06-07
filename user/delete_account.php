<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    // Database connection
    include "../Partials/db_connection.php";
    $user_name = $_SESSION['username'];
  


    // Begin transaction to ensure all deletions are processed together
    $conn->begin_transaction();

    try {

        // Delete user from the users table
         $sql_user = "DELETE FROM users WHERE user_name = ?";
         $stmt_user = $conn->prepare($sql_user);
         $stmt_user->bind_param("s", $user_name);
         $stmt_user->execute();

        // Delete user's comments
        $sql_comments = "DELETE FROM comments WHERE user_name = ?";
        $stmt_comments = $conn->prepare($sql_comments);
        $stmt_comments->bind_param("s", $user_name);
        $stmt_comments->execute();

        // Delete user's likes (Assuming there's a table for likes)
        $sql_likes = "DELETE FROM comment_likes WHERE user_id = ?";
        $stmt_likes = $conn->prepare($sql_likes);
        $stmt_likes->bind_param("s", $user_name);
        $stmt_likes->execute();

        // Delete user's posts/threads
        $sql_threads = "DELETE FROM thread WHERE thread_user_name = ?";
        $stmt_threads = $conn->prepare($sql_threads);
        $stmt_threads->bind_param("s", $user_name);
        $stmt_threads->execute();

        // Commit the transaction
        $conn->commit();

        // Destroy session and redirect
        session_destroy();
        header("Location: http://localhost/index.php"); // Redirect after deletion
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $message = "Failed to delete your account. Please try again.";
        
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xóa tài khoản</title>
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

    <div class="flex-1 flex flex-col justify-center items-center pt-24 pb-8">
        <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md mx-2">
            <h2 class="text-2xl font-bold text-center mb-4">Xóa tài khoản của bạn</h2>
            <?php if (!empty($message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-3"><?= $message ?></div>
            <?php endif; ?>
            <p class="mb-4 text-gray-700">
                Bạn có chắc chắn muốn xóa tài khoản của mình không?<br>
                <span class="font-semibold text-red-600">Hành động này không thể đảo ngược</span> và sẽ xóa vĩnh viễn hồ sơ của bạn cùng tất cả bài đăng và bình luận trên diễn đàn.
            </p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="flex flex-col sm:flex-row gap-3 justify-center mt-4">
                    <button type="submit" name="confirm_delete"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded transition">
                        Có, Xóa Tài khoản của tôi
                    </button>
                    <a href="user_profile.php"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-5 py-2 rounded transition text-center">
                        Không, Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-auto">
        <?php include "../Partials/_footer.php"; ?>
    </div>
</body>
</html>
