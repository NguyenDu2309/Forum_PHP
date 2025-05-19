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
                <li class="nav-item"><a class="nav-link active" href="/Forum_website/index.php">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
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
                <li class="nav-item"><a class="nav-link" href="contact_form.php">Contact</a></li>
            </ul>

            <form class="d-flex align-items-center" action="/Forum_website/search.php" method="GET">
                <input class="form-control me-2 ms-2 px-2 py-1" name="query" type="search" placeholder="Search threads" required>
                <button class="btn btn-outline-success ms-2 px-2" type="submit">Search</button>

                <?php if (isset($_SESSION['username'])): ?>
                    <!-- User Profile -->
                    <div class="d-flex align-items-center ms-4">
                        <img src="<?= $user_image ?>" alt="User Image" width="40" height="40" class="rounded-circle image-fluid" style="object-fit: cover;">
                        <p class="mb-0 text-primary ms-2"><?= substr($username, 0, 7) ?></p>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="btn-group ms-3">
                        <button class="btn btn-outline-success dropdown-toggle px-2" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            Profile
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="/Forum_website/user/user_profile.php">View Profile</a></li>
                            <li><a class="dropdown-item" href="Partials/_handle_logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Login and Signup Buttons -->
                    <div class="btn-group ms-2">
                        <button class="btn btn-outline-success dropdown-toggle px-2" type="button" id="loginDropdown" data-bs-toggle="dropdown">
                            Login
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginModal"> User Login</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adminLoginModal"> Admin Login</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-success ms-2 px-2" data-bs-toggle="modal" type="button" data-bs-target="#signupModal">Signup</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</nav>
