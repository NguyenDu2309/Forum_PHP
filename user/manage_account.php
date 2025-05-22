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
    <title>Update Profile</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="user_profile.php">Bảng điều khiển người dùng</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="user_profile.php">Trở về bảng điều khiển</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Partials/_handle_logout.php">Đăng xuất</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5" style="margin-top: 200px;">
    <div class="row ">
        <div class="col-md-6">
            <div class="p-4 border rounded mt-5 mb-3">
                <h2>Đổi tên đăng nhập và mật khẩu</h2>
                <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
                <form method="post">
                    <input type="hidden" name="update_credentials">
                    <div class="mb-3">
                        <label>Tên đăng nhập mới</label>
                        <input type="text" class="form-control" name="new_username" value="<?php echo htmlspecialchars($current_username); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Mật khẩu cũ</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label>Mật khẩu mới</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="p-4 border rounded mt-5 mb-3">
                <h2>Cập nhật hình ảnh hồ sơ</h2>
                <?php if ($image_error) echo "<div class='alert alert-danger'>$image_error</div>"; ?>
                <?php if ($image_success) echo "<div class='alert alert-success'>$image_success</div>"; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_image">
                    <div class="mb-3">
                        <label>Tải lên hình ảnh mới</label>
                        <input type="file" class="form-control" name="user_image" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>

            <!-- Update Email Form -->
            <div class="p-4 border rounded mt-1 mb-3">
                <h2>Thay đổi Email</h2>
                <?php if ($email_error) echo "<div class='alert alert-danger'>$email_error</div>"; ?>
                <?php if ($email_success) echo "<div class='alert alert-success'>$email_success</div>"; ?>
                <form method="post">
                    <input type="hidden" name="update_email">
                    <div class="mb-3">
                        <label>Email mới</label>
                        <input type="email" class="form-control" name="new_email" value="<?php echo htmlspecialchars($current_email); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Remove alert after 3 seconds
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (value) {
        setTimeout(() => {
            value.style.display = "none";
        }, 3000);
    })
</script>

<?php include '../Partials/_footer.php'; ?>

</body>
</html>
