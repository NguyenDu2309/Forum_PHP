<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Delete user account along with their comments, threads, and likes
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get username before deleting
        $stmt = $conn->prepare("SELECT user_name FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($user_name);
        $stmt->fetch();
        $stmt->close();

        if (!empty($user_name)) {
            // Delete user's likes
            $deleteLikes = "DELETE FROM comment_likes WHERE user_id = ?";
            $stmt = $conn->prepare($deleteLikes);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            // Delete user's comments
            $deleteComments = "DELETE FROM comments WHERE user_name = ?";
            $stmt = $conn->prepare($deleteComments);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->close();

            // Delete user's threads
            $deleteThreads = "DELETE FROM thread WHERE thread_user_name = ?";
            $stmt = $conn->prepare($deleteThreads);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->close();
        }

        // Finally, delete user
        $deleteUser = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($deleteUser);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo "<script>alert('User and all related data deleted successfully!'); window.location.href = 'manage_users.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error deleting user data: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Fetch all users
$query = "SELECT user_id, user_name, login_time FROM users";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Error fetching users: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
        }
        .container {
            margin-top: 80px;
        }
        .table {
            border: 2px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            border: 2px solid #ddd;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .btn {
            padding: 8px 15px;
        }
        @media (max-width: 576px) {
            .table th, .table td {
                font-size: 14px;
            }
            .navbar-brand {
                font-size: 16px;
            }
            .navbar-toggler {
                border-color: #fff;
            }
        }
    </style>
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
                        <a class="nav-link active" href="admin_dashboard.php">Back to Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2 class="mb-4">Manage Users</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-4">Back to Dashboard</a>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php 
                        $serial_number = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $serial_number++; ?></td>
                                <td><?= htmlspecialchars($row['user_name']); ?></td>
                                <td><?= htmlspecialchars($row['login_time']); ?></td>
                                <td>
                                    <a href="user_activity.php?user=<?= htmlspecialchars($row['user_name']); ?>" class="btn btn-primary btn-sm mt-1">View Posts & Comments</a>
                                    <a href="manage_users.php?delete=<?= htmlspecialchars($row['user_id']); ?>" class="btn btn-danger btn-sm mt-1" onclick="return confirmDelete(<?= $row['user_id']; ?>);">Delete Account</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No users found!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function confirmDelete(userId) {
            if (confirm("Are you sure you want to delete this account?")) {
                window.location.href = "manage_users.php?delete=" + userId;
            }
            return false;
        }
    </script>

</body>
</html>
