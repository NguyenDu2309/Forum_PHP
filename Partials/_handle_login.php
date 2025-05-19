<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connection.php';
    
    $login = false;
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM `users` WHERE user_name = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_num_rows($result);
    
    if ($row == 1) {
        while ($fetch = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $fetch['user_password'])) {
                $username = true;
                $password = true;

                session_start();
                $_SESSION['username'] = $fetch['user_name'];
                $_SESSION['user_id'] = $fetch['user_id'];
                $_SESSION['email_id'] = $fetch['email_id'];

                header("location: /Forum_website/index.php?username=true&password=true");
                exit();
            } else {
                $password = false;
                header("location: /Forum_website/index.php?username=true&password=false");
                exit();
            }
        }
    } else {
        $username = false;
        header("location: /Forum_website/index.php?username=false&password=false");
        exit();
    }
}
?>
