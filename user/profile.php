<?php
session_start();
include '../Partials/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user details
$user_image = "images/user.png"; // Default image
$username = "";
$time = ""; // Thêm dòng này để khởi tạo biến $time

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Query to get user image
    $sql = "SELECT * FROM users WHERE user_name = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['user_image'])) {
            $user_image = "../uploads/user_images/" . $row['user_image'];
        }
        // Lấy thời gian đăng ký tài khoản (hoặc trường phù hợp, ví dụ: created_at)
        $time = !empty($row['created_at']) ? $row['created_at'] : (isset($row['login_time']) ? $row['login_time'] : '');
    }
}

// Fetch total posts and comments count
$post_query = "SELECT COUNT(*) AS total_posts FROM thread WHERE thread_user_name = ?";
$comment_query = "SELECT COUNT(*) AS total_comments FROM comments WHERE user_name = ?";
$stmt = $conn->prepare($post_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$post_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare($comment_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$comment_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ người dùng</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-10 shadow">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <a class="font-bold text-lg" href="user_profile.php">Bảng điều khiển người dùng</a>
            <div class="flex space-x-4">
                <a class="hover:text-blue-400 transition" href="user_profile.php">Về bảng điều khiển</a>
                <a class="hover:text-blue-400 transition" href="../Partials/_handle_logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <div class="container mx-auto flex-1 flex flex-col justify-center items-center pt-24 pb-8">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-auto p-8 flex flex-col items-center">
            <img src="<?= $user_image ?>" alt="User Image" class="rounded-full w-28 h-28 object-cover border-4 border-gray-200 shadow mb-4">
            <h3 class="text-2xl font-bold mt-2 mb-1"><?= htmlspecialchars($username) ?></h3>
            <p class="text-gray-500 mb-4"><strong>Tham gia:</strong> <?= $time ? date('d/m/Y', strtotime($time)) : 'Không rõ' ?></p>
            <a href="manage_account.php" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition font-semibold flex items-center justify-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 00-4-4l-8 8v3zm-2 6h12"></path></svg>
                Chỉnh sửa thông tin cá nhân
            </a>
            <a href="javascript:history.back()" class="w-full bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                Trở về
            </a>
        </div>
    </div>

    <div class="mt-auto">
        <?php include '../Partials/_footer.php'; ?>
    </div>
</body>
</html>
