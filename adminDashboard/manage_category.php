<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Delete category
if (isset($_GET['delete'])) {
    $category_id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        echo "<script>alert('Category deleted successfully!'); window.location.href = 'manage_category.php';</script>";
    } else {
        echo "<script>alert('Error deleting category!');</script>";
    }
    $stmt->close();
}

// Fetch all categories
$query = "SELECT category_id, category_name, category_desc FROM category";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Error fetching categories: " . $conn->error);
}

// Edit category
if (isset($_POST['edit_category'])) {
    $category_id = intval($_POST['category_id']);
    $category_name = $_POST['category_name'];
    $category_desc = $_POST['category_desc'];

    $updateQuery = "UPDATE category SET category_name = ?, category_desc = ? WHERE category_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $category_name, $category_desc, $category_id);

    if ($stmt->execute()) {
        echo "<script>alert('Category updated successfully!'); window.location.href = 'manage_category.php';</script>";
    } else {
        echo "<script>alert('Error updating category!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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

        .table th,
        .table td {
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
            .table th,
            .table td {
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
        <h2 class="mb-4">Manage Categories</h2>
        <a href="add_category.php" class="btn btn-success mb-4">Add New Category</a>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Description</th>
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
                                <td><?= htmlspecialchars($row['category_name']); ?></td>
                                <td><?= htmlspecialchars($row['category_desc']); ?></td>
                                <td>
                                    <a href="edit_category.php?category_id=<?= htmlspecialchars($row['category_id']); ?>" class="btn btn-warning btn-sm mt-1">Edit</a>
                                    <a href="manage_category.php?delete=<?= htmlspecialchars($row['category_id']); ?>" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No categories found!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
