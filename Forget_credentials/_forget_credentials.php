<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại thông tin xác thực</title>
    <link rel="icon" type="image/jpg" href="../images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<?php session_start(); ?>
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
        <h2 class="text-2xl font-bold text-center mb-4">Đặt lại thông tin xác thực</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-2 rounded mb-3 text-center text-sm">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form action="_all_otp_process.php" method="post" class="space-y-4">
            <div>
                <label for="email" class="block font-semibold mb-1 text-gray-700">Nhập địa chỉ email đã đăng ký của bạn:</label>
                <input type="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" id="email" name="email" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold" name="send_otp">Gửi OTP</button>
        </form>
    </div>
</div>

<div class="mt-auto">
    <?php include '../Partials/_footer.php'; ?>
</div>

<script>
    // Ẩn alert sau 5s
    let alert = document.querySelector('.bg-blue-100');
    if (alert) {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    }
</script>
</body>
</html>
