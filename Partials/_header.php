<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db_connection.php');

$user_image = "images/user.png"; // Default image
$username = "";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Query to get user image
    $sql = "SELECT user_image FROM users WHERE user_name = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['user_image'])) {
            $user_image = "uploads/user_images/" . $row['user_image'];
        }
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/Forum_website/index.php">IT Forum</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="/Forum_website/index.php">Trang chủ</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Danh mục
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php
                        $sql2 = "SELECT * FROM `category` LIMIT 6";
                        $result = mysqli_query($conn, $sql2);
                        if ($result) {
                            while ($fetch = mysqli_fetch_assoc($result)) {
                                $title = $fetch["category_name"];
                                $id = $fetch["category_id"];
                                echo '<li><a class="dropdown-item" href="/Forum_website/thread_list.php?id=' . $id . '">' . $title . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="contact_form.php">Liên hệ</a></li>
            </ul>

            <div class="d-flex align-items-center flex-nowrap">
                <!-- Search Form -->
                <form class="d-flex align-items-center me-3" action="/Forum_website/search.php" method="GET">
                    <input class="form-control me-2" name="query" type="search" placeholder="Tìm kiếm chủ đề..." required style="min-width: 200px;">
                    <button class="btn btn-outline-success text-nowrap" type="submit">Tìm kiếm</button>
                </form>

                <?php if (isset($_SESSION['username'])): ?>
                    <!-- User Profile -->
                    <div class="d-flex align-items-center me-3">
                        <img src="<?= $user_image ?>" alt="User Image" width="40" height="40" class="rounded-circle" style="object-fit: cover;">
                        <span class="text-primary ms-2 text-nowrap"><?= substr($username, 0, 7) ?></span>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-outline-success dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            Hồ sơ
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="/Forum_website/user/user_profile.php">Xem hồ sơ</a></li>
                            <li><a class="dropdown-item" href="Partials/_handle_logout.php">Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Login and Signup Buttons -->
                    <div class="d-flex align-items-center">
                        <div class="btn-group me-2">
                            <button class="btn btn-outline-success dropdown-toggle" type="button" id="loginDropdown" data-bs-toggle="dropdown">
                                Đăng nhập
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập người dùng</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adminLoginModal">Đăng nhập quản trị</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-outline-success" data-bs-toggle="modal" type="button" data-bs-target="#signupModal">Đăng ký</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>