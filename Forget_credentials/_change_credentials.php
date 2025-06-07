<?php
session_start();

// Check if OTP verification has been done
if (!isset($_SESSION['email'])) {
    $_SESSION['message'] = "❌ OTP Verification required!";
    header('Location: _forgot_credentials.php'); 
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../Partials/db_connection.php'; // Include database connection

    // Retrieve and sanitize form data
    $new_username = trim($_POST['new_username']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = $_SESSION['email'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "❌ Mật khẩu không khớp!";
        header('Location: _change_credentials.php');
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update username and password in the users table
    $sql = "SELECT * FROM users WHERE user_name = '$new_username' AND email_id != '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $_SESSION['message'] = "❌ Tên người dùng đã tồn tại!";
        header('Location: _change_credentials.php');
        exit();
    }
    else{
    $sql = "UPDATE users SET user_name = ?, user_password = ? WHERE email_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $new_username, $hashed_password, $email);
   
    if ($stmt->execute()) {
        // Update username in `comments` table
        $sql_comments = "UPDATE comments SET user_name = ? WHERE email_id = ?";
        $stmt_comments = $conn->prepare($sql_comments);
        $stmt_comments->bind_param("ss", $new_username, $_SESSION['email']);
        $stmt_comments->execute();
        $stmt_comments->close();

        // Update username in `thread` table
        $sql_threads = "UPDATE thread SET thread_user_name = ? WHERE email_id = ?";
        $stmt_threads = $conn->prepare($sql_threads);
        $stmt_threads->bind_param("ss", $new_username, $_SESSION['email']);
        $stmt_threads->execute();
        $stmt_threads->close();


        $_SESSION['message'] = "✅ Tên người dùng và mật khẩu đã được cập nhật thành công!";
        header('Location: ../index.php');  // Redirect to home page
        exit();
    } else {
        $_SESSION['message'] = "❌ Lỗi khi cập nhật thông tin đăng nhập!";
        header('Location: _change_credentials.php');
        exit();
    }}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thay đổi tên người dùng và mật khẩu</title>
    <link rel="icon" type="image/jpg" href="../images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<?php include "../Partials/db_connection.php"; ?>
<?php include "../Partials/login_modal.php"; ?>
<?php include "../Partials/signup_modal.php"; ?>

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-10 shadow">
    <div class="container mx-auto flex items-center justify-between px-4 py-3">
        <a class="font-bold text-lg" href="../index.php">IT Forum</a>
        <div class="flex gap-2">
            <a href="#" class="hover:text-blue-400 transition" data-modal-toggle="loginModal">Đăng nhập người dùng</a>
            <a href="#" class="hover:text-blue-400 transition" data-modal-toggle="signupModal">Đăng ký</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="flex-1 flex flex-col justify-center items-center pt-28 pb-8">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2 p-6">
        <h2 class="text-2xl font-bold text-center mb-4">Thay đổi tên người dùng và mật khẩu</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-2 rounded mb-3 text-center text-sm">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form action="_change_credentials.php" method="post" class="space-y-4">
            <div>
                <label for="new_username" class="block font-semibold mb-1 text-gray-700">Tên đăng nhập mới:</label>
                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="new_username" name="new_username" required>
            </div>
            <div>
                <label for="new_password" class="block font-semibold mb-1 text-gray-700">Mật khẩu mới:</label>
                <input type="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="new_password" name="new_password" required>
            </div>
            <div>
                <label for="confirm_password" class="block font-semibold mb-1 text-gray-700">Xác nhận mật khẩu:</label>
                <input type="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold">Cập nhật tên người dùng và mật khẩu</button>
        </form>
    </div>
</div>

<div class="mt-auto">
    <?php include '../Partials/_footer.php'; ?>
</div>

<script>
    let alert = document.querySelector('.bg-blue-100');
    if (alert) {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    }
</script>
</body>
</html>
