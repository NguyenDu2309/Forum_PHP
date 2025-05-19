<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$query = "SELECT username, password FROM admin_users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username, $admin_password);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($new_password)) {
        if ($new_password === $confirm_password) {
            // Update username and password
            $update_query = "UPDATE admin_users SET username = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->bind_param("ssi", $new_username, $hashed_password, $admin_id);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('Profile updated successfully!'); window.location.href = 'admin_profile.php';</script>";
        } else {
            echo "<script>alert('Passwords do not match!'); window.location.href = 'admin_profile.php'; </script>";
        }
    } else {
        // Update only the username
        $update_query = "UPDATE admin_users SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_username, $admin_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Username updated successfully!'); window.location.href = 'admin_profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 100px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #5f6368;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            border-radius: 10px 10px 0 0;
        }
    </style>
    <script>
        function toggleEditMode() {
            document.getElementById('profile-view').classList.add('d-none');
            document.getElementById('profile-edit').classList.remove('d-none');
        }
    </script>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Back to Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Admin Profile</h3>
                    </div>
                    <div class="card-body">
                        <!-- Profile View -->
                        <div id="profile-view">
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($admin_username); ?></p>
                            <p><strong>Password:</strong> <?php echo htmlspecialchars($admin_password); ?></p>
                            <button class="btn btn-primary" onclick="toggleEditMode()">Change</button>
                        </div>

                        <!-- Profile Edit -->
                        <div id="profile-edit" class="d-none">
                            <form method="POST" action="admin_profile.php">
                                <div class="mb-3">
                                    <label for="new_username" class="form-label">New Username</label>
                                    <input type="text" class="form-control" id="new_username" name="new_username" value="<?php echo htmlspecialchars($admin_username); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password (optional)</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                                <button type="submit" name="update" class="btn btn-success">Save Changes</button>
                                <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
