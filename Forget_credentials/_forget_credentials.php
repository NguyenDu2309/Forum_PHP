<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
    <?php session_start(); ?>
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
                   <a class=" dropdown-item m text-white " href="#" data-bs-toggle="modal" data-bs-target="#loginModal"> Đăng nhập người dùng</a>
                </li>
                <li class="nav-item">
                    <a class=" dropdown-item m text-white " href="#" data-bs-toggle="modal" data-bs-target="#loginModal"> Đăng ký</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 mt-5">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4 ">Đặt lại thông tin xác thực</h2>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <form action="_all_otp_process.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Nhập địa chỉ email đã đăng ký của bạn:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="send_otp">Gửi OTP</button>
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
</script>
</body>
</body>
</html>
