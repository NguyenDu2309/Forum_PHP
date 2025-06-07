<?php
session_start();

// Set OTP expiration time (180 seconds).
$otp_expiration_time = 180;

// Ensure OTP time is set
if (!isset($_SESSION['otp_time'])) {
    $_SESSION['otp_time'] = time(); // Store OTP generation time
}

// Check if OTP is still valid
$remaining_time = $otp_expiration_time - (time() - $_SESSION['otp_time']);
$otp_valid = $remaining_time > 0;

// Expire OTP if time is over
if (!$otp_valid) {
    unset($_SESSION['otp']);
    unset($_SESSION['otp_time']);
    $_SESSION['message'] = "❌ OTP đã hết hạn. Vui lòng gửi lại OTP.";
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $user_otp = $_POST['otp'];

    if ($otp_valid && isset($_SESSION['otp']) && $_SESSION['otp'] == $user_otp) {
        $_SESSION['message'] = "✅ Đã xác minh OTP thành công!";
        unset($_SESSION['otp']); 
        unset($_SESSION['otp_time']);
        header("Location: _change_credentials.php");
        exit();
    } else {
        $_SESSION['message'] = "❌ Invalid OTP! Please try again.";
        header("Location: _verify_otp.php"); // Redirect back to OTP page
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
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
            <a href="#" class="hover:text-blue-400 transition" data-modal-toggle="loginModal">Quên mật khẩu</a>
            <a href="#" class="hover:text-blue-400 transition" data-modal-toggle="signupModal">Đăng ký</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="flex-1 flex flex-col justify-center items-center pt-28 pb-8">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2 p-6">
        <h2 class="text-2xl font-bold text-center mb-4">Nhập OTP</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-2 rounded mb-3 text-center text-sm">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="space-y-4">
            <div>
                <label for="otp" class="block font-semibold mb-1 text-gray-700">Nhập OTP được gửi đến email của bạn:</label>
                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="otp" name="otp" required <?php echo !$otp_valid ? 'disabled' : ''; ?>>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold" <?php echo !$otp_valid ? 'disabled' : ''; ?>>Xác minh OTP</button>
        </form>

        <!-- Resend OTP Button -->
        <form action="_resend_otp.php" method="post" class="mt-3">
            <button type="submit" class="w-full bg-gray-400 text-white py-2 rounded hover:bg-gray-500 transition font-semibold" id="resendBtn" <?php echo $otp_valid ? 'disabled' : ''; ?>>Gửi lại OTP</button>
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

    // Reload page when OTP expires
    let remainingTime = <?php echo $remaining_time > 0 ? $remaining_time : 0; ?>;
    if (remainingTime > 0) {
        setTimeout(() => {
            location.reload();
        }, remainingTime * 1000);
    }
</script>
</body>
</html>
