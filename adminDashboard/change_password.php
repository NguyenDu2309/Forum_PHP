<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Handle password change
if (isset($_POST['submit'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Get the current password from the database
    $admin_id = $_SESSION['admin_id'];
    $query = "SELECT password FROM admin_users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    // Verify current password (plain text comparison)
    if ($current_password === $stored_password) {
        if ($new_password === $confirm_password) {
            // Update the password in the database (plain text)
            $update_query = "UPDATE admin_users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("si", $new_password, $admin_id);

            if ($stmt->execute()) {
                echo "<script>alert('Thay đổi mật khẩu thành công!'); window.location.href = 'admin_dashboard.php';</script>";
            } else {
                echo "<script>alert('Lỗi khi cập nhật mật khẩu!');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Mật khẩu mới và mật khẩu xác nhận không khớp!');</script>";
        }
    } else {
        echo "<script>alert('Mật khẩu hiện tại không đúng!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thay đổi mật khẩu</title>
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
    <div class="container mx-auto pt-28 px-2 max-w-md">
        <h2 class="mb-6 text-2xl font-bold text-gray-800 text-center">Thay đổi mật khẩu</h2>
        <form method="POST" action="change_password.php" class="bg-white rounded-xl shadow p-6 space-y-5">
            <div>
                <label for="current_password" class="block font-semibold mb-1 text-gray-700">Mật khẩu hiện tại</label>
                <input type="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="current_password" name="current_password" required>
            </div>
            <div>
                <label for="new_password" class="block font-semibold mb-1 text-gray-700">Mật khẩu mới</label>
                <input type="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="new_password" name="new_password" required>
            </div>
            <div>
                <label for="confirm_password" class="block font-semibold mb-1 text-gray-700">Xác nhận mật khẩu mới</label>
                <input type="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold">Thay đổi mật khẩu</button>
        </form>
    </div>
</body>
</html>
