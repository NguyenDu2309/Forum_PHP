<?php
include("db_connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim input values to remove unwanted spaces
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);
    $email = trim($_POST['email']); // Get the email from the form

    // Check if the username or email already exists
    $sql = "SELECT * FROM `users` WHERE user_name = '$username' OR email_id = '$email'";
    $result = mysqli_query($conn, $sql);
    $rows = mysqli_num_rows($result);

    if ($rows > 0) {
        // Check if it's the username or the email that's already taken
        $userExists = false;
        $emailExists = false;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['user_name'] == $username) {
                $userExists = true;
            }
            if ($row['email_id'] == $email) {
                $emailExists = true;
            }
        }

        // Redirect to index.php with an appropriate message
        if ($userExists && $emailExists) {
            header("Location: /Forum_website/index.php?signupsuccess=false&exist=true&email=true&username=true");
        } elseif ($userExists) {
            header("Location: /Forum_website/index.php?signupsuccess=false&exist=true&username=true");
        } elseif ($emailExists) {
            header("Location: /Forum_website/index.php?signupsuccess=false&exist=true&email=true");
        }
        exit();
    } else {
        // No existing username or email, proceed with signup
        if ($password === $cpassword) { // Passwords match

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Handle image upload (if any)
            $imageName = null;
            if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
                // Move the uploaded image to the 'uploads' folder
                $imageName = trim($_FILES['user_image']['name']);
                $targetDir = "../uploads/user_images/";
                $targetPath = $targetDir . basename($imageName);

                // Only proceed if the image is moved successfully
                if (move_uploaded_file($_FILES['user_image']['tmp_name'], $targetPath)) {
                    // Image uploaded successfully
                } else {
                    // Failed to upload image, set imageName to null
                    $imageName = null;
                }
            }

            // Insert the new user into the database
            $sql = "INSERT INTO `users` (`user_name`, `user_password`, `login_time`, `email_id`, `user_image`) 
                    VALUES ('$username', '$hashedPassword', current_timestamp(), '$email', '$imageName')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                // Signup successful
                header("Location: /Forum_website/index.php?signupsuccess=true&exist=false");
                exit();
            } else {
                // Signup failed (database error)
                header("Location: /Forum_website/index.php?signupsuccess=false&exist=false");
                exit();
            }
        } else {
            // Passwords don't match
            header("Location: /Forum_website/index.php?signupsuccess=false&exist=false");
            exit();
        }
    }
}
?>
 