<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    // Database connection
    include "../Partials/db_connection.php";
    $user_name = $_SESSION['username'];
  


    // Begin transaction to ensure all deletions are processed together
    $conn->begin_transaction();

    try {

        // Delete user from the users table
         $sql_user = "DELETE FROM users WHERE user_name = ?";
         $stmt_user = $conn->prepare($sql_user);
         $stmt_user->bind_param("s", $user_name);
         $stmt_user->execute();

        // Delete user's comments
        $sql_comments = "DELETE FROM comments WHERE user_name = ?";
        $stmt_comments = $conn->prepare($sql_comments);
        $stmt_comments->bind_param("s", $user_name);
        $stmt_comments->execute();

        // Delete user's likes (Assuming there's a table for likes)
        $sql_likes = "DELETE FROM comment_likes WHERE user_id = ?";
        $stmt_likes = $conn->prepare($sql_likes);
        $stmt_likes->bind_param("s", $user_name);
        $stmt_likes->execute();

        // Delete user's posts/threads
        $sql_threads = "DELETE FROM thread WHERE thread_user_name = ?";
        $stmt_threads = $conn->prepare($sql_threads);
        $stmt_threads->bind_param("s", $user_name);
        $stmt_threads->execute();

        // Commit the transaction
        $conn->commit();

        // Destroy session and redirect
        session_destroy();
        header("Location: http://localhost/index.php"); // Redirect after deletion
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $message = "Failed to delete your account. Please try again.";
        
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../Partials/style.css">
    <title>Delete Account</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
       
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex: 1;
            padding: 20px;
        }

        .confirmation-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .footer {
            margin-top: auto;
            
            background-color: #f8f9fa;
            text-align: center;
            width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .confirmation-box {
                padding: 20px;
                max-width: 90%;
            }

            .container {
                padding: 10px;
            }
           
            a{
                margin-top:3px;
            }
        }
    </style>
</head>
<body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="user_profile.php">User Panel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="user_profile.php">Back to Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../Partials/_handle_logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    <div class="wrapper">
        <div class="container">
            <div class="confirmation-box">
                <h2 class="text-center mb-4">Delete Your Account</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger"><?= $message ?></div>
                <?php endif; ?>
                <p>Are you sure you want to delete your account? This action is irreversible and will permanently remove your profile and all your forum posts and comments.</p>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="text-center">
                        <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete My Account</button>
                        <a href="user_profile.php" class="btn btn-secondary">No, Go Back</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <?php include "../Partials/_footer.php"; ?>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>
</html>
