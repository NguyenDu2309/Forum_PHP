<?php
session_start();
include '../Partials/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user details
$user_image = "images/user.png"; // Default image
$username = "";
$time = ""; // Thêm dòng này để khởi tạo biến $time

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Query to get user image
    $sql = "SELECT * FROM users WHERE user_name = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['user_image'])) {
            $user_image = "../uploads/user_images/" . $row['user_image'];
        }
        // Lấy thời gian đăng ký tài khoản (hoặc trường phù hợp, ví dụ: created_at)
        $time = !empty($row['created_at']) ? $row['created_at'] : (isset($row['login_time']) ? $row['login_time'] : '');
    }
}

// Fetch total posts and comments count
$post_query = "SELECT COUNT(*) AS total_posts FROM thread WHERE thread_user_name = ?";
$comment_query = "SELECT COUNT(*) AS total_comments FROM comments WHERE user_name = ?";
$stmt = $conn->prepare($post_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$post_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare($comment_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$comment_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
                    background-color: #f8f9fa;
                    display: flex;
                    flex-direction: column;
                    min-height: 100vh;
                }

                .profile-card {
                    max-width: 450px;
                    margin: auto;
                    border-radius: 10px;
                }

                .profile-icon {
                    font-size: 100px;
                    color: #6c757d;
                }

                .btn-back {
                    display: inline-flex;
                    align-items: center;
                }

                .footer {
                    margin-top: auto; /* Push the footer to the bottom */
                }

    </style>
</head>
<body>

 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_profile.php">Bảng điều khiển người dùng</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                         <a class="nav-link" href="user_profile.php">Về bảng điều khiển</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Partials/_handle_logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<!-- Profile Section -->
<div class="container " style="margin-top: 100px;">
    <div class="card shadow-lg profile-card">
        <div class="card-body text-center">
          <img src="<?= $user_image ?>"  width="100" height="100" class="rounded-circle">
            <h3 class="mt-2"><?= htmlspecialchars($username) ?></h3>
            <p><strong>Joined:</strong> <?= date('F j, Y', strtotime($time)) ?></p>
            
            <div class="row">
                <div class="col">
                    <h5><?= $post_result['total_posts'] ?></h5>
                    <p class="text-muted">Tổng số bài đăng</p>
                </div>
                <div class="col">
                    <h5><?= $comment_result['total_comments'] ?></h5>
                    <p class="text-muted">Tổng số bình luận</p>
                </div>
            </div>

            <a href="manage_account.php" class="btn btn-primary w-100">
                <i class="bi bi-pencil-square"></i> Chỉnh sửa thông tin cá nhân
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary mt-2 w-100 btn-back">
                <i class="bi bi-arrow-left me-1"></i> Trở về
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<div class="footer">
<?php include '../Partials/_footer.php'; ?>
</div>
</body>
</html>
