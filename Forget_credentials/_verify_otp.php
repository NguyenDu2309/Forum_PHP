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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" type="image/jpg" href="../images/favicon1.jpg">
   <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures full height */
            background-color: #f4f4f9;
        }
        .nav-item .m {
                color: white !important;  /* Ensures text color stays white */
                background-color: transparent !important;  /* Ensures no background change */
            }

        .nav-item .m:hover {
            color: white !important;  /* Keeps text color white on hover */
            background-color: transparent !important;  /* Prevents background color change */
        }

        .container {
                    flex: 1; /* Pushes footer down */
                }
        .footer {
            margin-top: auto;
        }

    </style>
</head>
<body>
<!-- including files for laod login and sign up modal -->
  <?php include "../Partials/db_connection.php"; ?>
  <?php include "../Partials/login_modal.php"; ?>
  <?php include "../Partials/signup_modal.php"; ?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">IT Forum</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                   <a class=" dropdown-item m text-white " href="#" data-bs-toggle="modal" data-bs-target="#loginModal"> Quên mật khẩu</a>
                </li>
                <li class="nav-item">
                    <a class=" dropdown-item m text-white " href="#" data-bs-toggle="modal" data-bs-target="#loginModal"> Đăng ký</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<body>

<div class="container mt-5">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Nhập OTP</h2>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info text-center"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Nhập OTP được gửi đến email của bạn:</label>
                        <input type="text" class="form-control" id="otp" name="otp" required <?php echo !$otp_valid ? 'disabled' : ''; ?>>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" <?php echo !$otp_valid ? 'disabled' : ''; ?>>Xác minh OTP</button>
                </form>

                <!-- Resend OTP Button -->
                <form action="_resend_otp.php" method="post" class="mt-3">
                    <button type="submit" class="btn btn-secondary w-100" id="resendBtn" <?php echo $otp_valid ? 'disabled' : ''; ?>>Gửi lại OTP</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Footer -->
<div class="footer">
    <?php include '../Partials/_footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

<script>
    let alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    }

    // Reload page when OTP expires
    let remainingTime = <?php echo $remaining_time > 0 ? $remaining_time : 0; ?>;
    
    if (remainingTime > 0) {
        setTimeout(() => {
            location.reload(); // Reload page when OTP expires
        }, remainingTime * 1000); // Reload exactly when OTP expires
    }
</script>
</body>
</body>
</html>
