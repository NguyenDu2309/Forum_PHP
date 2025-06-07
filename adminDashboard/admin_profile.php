<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$query = "SELECT username, password FROM admin_users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username, $admin_password);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($new_password)) {
        if ($new_password === $confirm_password) {
            // Update username and password
            $update_query = "UPDATE admin_users SET username = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->bind_param("ssi", $new_username, $hashed_password, $admin_id);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('Hồ sơ đã được cập nhật thành công!'); window.location.href = 'admin_profile.php';</script>";
        } else {
            echo "<script>alert('Mật khẩu không khớp!'); window.location.href = 'admin_profile.php'; </script>";
        }
    } else {
        // Update only the username
        $update_query = "UPDATE admin_users SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_username, $admin_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Username updated successfully!'); window.location.href = 'admin_profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleEditMode() {
            document.getElementById('profile-view').classList.add('hidden');
            document.getElementById('profile-edit').classList.remove('hidden');
        }
    </script>
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

    <!-- Profile Section -->
    <div class="container mx-auto pt-28 px-2 max-w-xl">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-2xl font-bold text-center mb-6">Hồ sơ quản trị</h3>
            <!-- Profile View -->
            <div id="profile-view">
                <p class="mb-2"><span class="font-semibold">Tên đăng nhập:</span> <span class="text-blue-700"><?php echo htmlspecialchars($admin_username); ?></span></p>
                <p class="mb-4"><span class="font-semibold">Mật khẩu:</span> <span class="break-all text-gray-500"><?php echo htmlspecialchars($admin_password); ?></span></p>
                <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold" onclick="toggleEditMode()">Thay đổi</button>
            </div>

            <!-- Profile Edit -->
            <div id="profile-edit" class="hidden">
                <form method="POST" action="admin_profile.php" class="space-y-4">
                    <div>
                        <label for="new_username" class="block font-semibold mb-1 text-gray-700">Tên đăng nhập mới</label>
                        <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="new_username" name="new_username" value="<?php echo htmlspecialchars($admin_username); ?>" required>
                    </div>
                    <div>
                        <label for="new_password" class="block font-semibold mb-1 text-gray-700">Mật khẩu mới (tùy chọn)</label>
                        <input type="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="new_password" name="new_password">
                    </div>
                    <div>
                        <label for="confirm_password" class="block font-semibold mb-1 text-gray-700">Xác nhận mật khẩu mới</label>
                        <input type="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="confirm_password" name="confirm_password">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" name="update" class="flex-1 bg-green-500 text-white py-2 rounded hover:bg-green-600 transition font-semibold">Lưu thay đổi</button>
                        <a href="admin_profile.php" class="flex-1 bg-gray-400 text-white py-2 rounded hover:bg-gray-600 transition font-semibold text-center">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
