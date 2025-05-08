<?php 
session_start();
?>

<?php include '../components/header.php'; ?>

<!-- Import Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<style>
    .password-container {
        position: relative;
    }
    .password-toggle {
        position: absolute;
        right: 0.75rem;
        top: 0.625rem;
        cursor: pointer;
        z-index: 10;
    }
</style>

<div class="max-w-md mx-auto mt-12 bg-white shadow-md rounded-lg p-6">
    <h3 class="text-2xl font-semibold text-center mb-6">Login</h3>

    <form action="/student_forum/app/controllers/AuthController.php?action=login" method="POST">
        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" name="email" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium">Password</label>
            <div class="password-container">
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <span class="password-toggle" onclick="togglePassword()">
                    <i class="fa fa-eye" id="eye-icon"></i>
                </span>
            </div>
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-300">Login</button>
    </form>

    <!-- Display alerts -->
    <?php include '../components/alerts.php'; ?>

    <p class="mt-4 text-center text-sm">Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register here</a></p>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

<?php include '../components/footer.php'; ?>
