<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Forum - Connect and Learn</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/student_forum/public/assets/css/styles.css">
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/student_forum/public/index.php">
            <i class="fas fa-graduation-cap mr-2"></i>Student Forum
        </a>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
               
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/student_forum/public/my_question.php">
                        <i class="fas fa-list-alt mr-1"></i> My Posts
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link" href="/student_forum/public/contact.php">
                        <i class="fas fa-question-circle mr-1"></i> Contact Admin
                    </a>
                </li>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                    <a class="nav-link" href="/student_forum/public/assign_user.php">
                        <i class="fas fa-question-circle mr-1"></i> Assign Post
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student_forum/public/add_post.php">
                        <i class="fas fa-question-circle mr-1"></i> Add Post
                    </a>
                </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-tools mr-1"></i> Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/student_forum/admin/index.php">
                                <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                            </a>
                            <a class="dropdown-item" href="/student_forum/admin/users.php">
                                <i class="fas fa-users mr-1"></i> Users
                            </a>
                            <a class="dropdown-item" href="/student_forum/admin/posts.php">
                                <i class="fas fa-file-alt mr-1"></i> Posts
                            </a>
                            <a class="dropdown-item" href="/student_forum/admin/modules.php">
                                <i class="fas fa-book mr-1"></i> Modules
                            </a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="navbar-nav">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle mr-1"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <span class="badge badge-warning ml-1">Admin</span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="/student_forum/public/my_question.php">
                            <i class="fas fa-list-alt mr-1"></i> My Posts
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/student_forum/app/controllers/AuthController.php?action=logout">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/student_forum/app/views/auth/login.php" class="btn btn-outline-light mr-2">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
                <a href="/student_forum/app/views/auth/register.php" class="btn btn-light">
                    <i class="fas fa-user-plus mr-1"></i> Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>