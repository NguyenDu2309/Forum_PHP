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
    <title>Update credentials</title>
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
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Thay đổi tên người dùng và mật khẩu</h2>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <form action="_change_credentials.php" method="post">
                    <div class="mb-3">
                        <label for="new_username" class="form-label">Tên đăng nhập mới:</label>
                        <input type="text" class="form-control" id="new_username" name="new_username" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật tên người dùng và mật khẩu</button>
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
