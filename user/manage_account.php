<?php
session_start();
include '../Partials/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$current_email = $_SESSION['email_id']; // Assuming email is stored in session as well

$error = '';
$success = '';
$image_error = '';
$image_success = '';
$email_error = '';
$email_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update username and password logic
    if (isset($_POST['update_credentials'])) {
        $new_username = trim($_POST['new_username']);
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'Please fill in all fields.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New password and confirm password do not match.';
        } else {
            $stmt = $conn->prepare("SELECT user_password FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user || !password_verify($old_password, $user['user_password'])) {
                $error = 'Incorrect old password.';
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET user_password = ?, user_name = ? WHERE user_id = ?");
                $stmt->bind_param("ssi", $hashed_password, $new_username, $user_id);

                if ($stmt->execute()) {
                    $_SESSION['username'] = $new_username;
                    $success = 'Username and password updated successfully.';
                } else {
                    $error = 'Error updating details. Please try again.';
                }
            }
        }
    }

    // Update profile image logic
    if (isset($_POST['update_image'])) {
        if (!empty($_FILES['user_image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $image_name = $_FILES['user_image']['name'];
            $image_tmp = $_FILES['user_image']['tmp_name'];
            $image_type = mime_content_type($image_tmp);
            
            if (!in_array($image_type, $allowed_types)) {
                $image_error = 'Only JPG, PNG, and GIF images are allowed.';
            } else {
                $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
                $new_image_name = "user_" . $user_id . "_" . time() . "." . $image_ext;
                $image_path = "../uploads/user_images/" . $new_image_name;

                if (move_uploaded_file($image_tmp, $image_path)) {
                    $stmt = $conn->prepare("UPDATE users SET user_image = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $new_image_name, $user_id);

                    if ($stmt->execute()) {
                        $image_success = 'Profile image updated successfully.';
                    } else {
                        $image_error = 'Error updating image in database.';
                    }
                } else {
                    $image_error = 'Error uploading image.';
                }
            }
        } else {
            $image_error = 'Please select an image to upload.';
        }
    }

    // Update email logic
    if (isset($_POST['update_email'])) {
        $new_email = trim($_POST['new_email']);

        if (empty($new_email)) {
            $email_error = 'Please provide a new email.';
        } else {
            // Check if the new email already exists, excluding the current user
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email_id = ? AND user_id != ?");
            $stmt->bind_param("si", $new_email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $email_error = 'This email is already in use by another account.';
            } else {
                $stmt = $conn->prepare("UPDATE users SET email_id = ? WHERE user_id = ?");
                $stmt->bind_param("si", $new_email, $user_id);

                if ($stmt->execute()) {
                    $_SESSION['email'] = $new_email;
                    $email_success = 'Email updated successfully.';
                } else {
                    $email_error = 'Error updating email. Please try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật tài khoản</title>
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

    <div class="container mx-auto pt-24 flex-1 w-full">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Đổi tên đăng nhập và mật khẩu -->
            <div class="w-full md:w-1/2">
                <div class="bg-white p-6 rounded shadow mt-4 mb-6">
                    <h2 class="text-xl font-bold mb-4">Đổi tên đăng nhập và mật khẩu</h2>
                    <?php if ($error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-3"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3"><?= $success ?></div>
                    <?php endif; ?>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="update_credentials">
                        <div>
                            <label class="block font-semibold mb-1">Tên đăng nhập mới</label>
                            <input type="text" name="new_username" value="<?= htmlspecialchars($current_username); ?>" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Mật khẩu cũ</label>
                            <input type="password" name="old_password" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Mật khẩu mới</label>
                            <input type="password" name="new_password" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold">Cập nhật</button>
                    </form>
                </div>
            </div>

            <!-- Cập nhật hình ảnh và email -->
            <div class="w-full md:w-1/2 flex flex-col gap-6">
                <div class="bg-white p-6 rounded shadow mt-4">
                    <h2 class="text-xl font-bold mb-4">Cập nhật hình ảnh hồ sơ</h2>
                    <?php if ($image_error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-3"><?= $image_error ?></div>
                    <?php endif; ?>
                    <?php if ($image_success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3"><?= $image_success ?></div>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="update_image">
                        <div>
                            <label class="block font-semibold mb-1">Tải lên hình ảnh mới</label>
                            <input type="file" name="user_image" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold">Cập nhật</button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-xl font-bold mb-4">Thay đổi Email</h2>
                    <?php if ($email_error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-3"><?= $email_error ?></div>
                    <?php endif; ?>
                    <?php if ($email_success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3"><?= $email_success ?></div>
                    <?php endif; ?>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="update_email">
                        <div>
                            <label class="block font-semibold mb-1">Email mới</label>
                            <input type="email" name="new_email" value="<?= htmlspecialchars($current_email); ?>" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Remove alert after 3 seconds
        document.querySelectorAll('.bg-red-100, .bg-green-100').forEach(function (alert) {
            setTimeout(() => {
                alert.style.display = "none";
            }, 3000);
        });
    </script>

    <?php include '../Partials/_footer.php'; ?>
</body>
</html>
