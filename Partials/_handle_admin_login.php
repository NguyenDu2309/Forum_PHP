

<?php
session_start();
include 'db_connection.php'; // Ensure the database connection file is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['adminUsername'];
    $password = $_POST['adminPassword'];

    // Query to verify admin credentials
    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $adminlogin = true;
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Compare the entered password with the stored password (plain text)
        if ($password === $admin['password']) {
            // Successful login
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: ../adminDashboard/admin_dashboard.php"); // Redirect to admin dashboard
            exit();
        } else {
            // Invalid password
            $adminlogin = false;
            header("Location: ../index.php?adminlogin=false"); // Redirect back to the login page
            exit();
        }
    } else {
        // Admin not found
        $adminlogin = false;
        header("Location: ../index.php?adminlogin=false"); // Redirect back to the login page
        exit();
    }
} else {
    // Redirect to login page if the request method is not POST
    $adminlogin = false;
    header("Location: ../index.php?adminlogin=false");
    exit();
}
?>
