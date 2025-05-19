<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Pagination setup
$limit = 10; // Number of posts per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$offset = ($page - 1) * $limit;

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE thread_title LIKE '%$search%' OR thread_user_name LIKE '%$search%'";
}

// Query to get posts with pagination and search functionality
$query = "SELECT * FROM thread $search_query ORDER BY time DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of posts for pagination
$total_query = "SELECT COUNT(*) FROM thread $search_query";
$total_result = $conn->query($total_query);
$total_posts = $total_result->fetch_row()[0];
$total_pages = ceil($total_posts / $limit);

$stmt->close();

// Handle post deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM thread WHERE thread_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_posts.php'); // Redirect to avoid resubmitting form
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
        }

        .container {
            margin-top: 80px;
        }

        .btn {
            padding: 8px 15px;
        }

        .search-bar {
            margin-bottom: 20px;
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
                        <a class="nav-link" href="admin_dashboard.php">Back to Dashboard</a>
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
        <h2>Manage Posts</h2>

        <!-- Search Form -->
        <form class="search-bar" method="GET" action="manage_posts.php">
            <input type="text" name="search" class="form-control" placeholder="Search by title or author"
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-primary mt-2">Search</button>
        </form>

        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
        <a href="manage_posts.php" class="btn btn-secondary mb-3">Back to All Posts</a>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Post Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serial = $offset + 1; // Serial number starts from 1 on each page
                while ($post = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $serial++; ?></td>
                    <td><?= htmlspecialchars($post['thread_title']); ?></td>
                    <td><?= htmlspecialchars($post['thread_user_name']); ?></td>
                    <td><?= htmlspecialchars($post['thread_cat_id']); ?></td>
                    <td><?= htmlspecialchars($post['time']); ?></td>
                    <td>
                        <a href="manage_posts.php?delete_id=<?= $post['thread_id']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="manage_posts.php?page=<?= $page - 1; ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="manage_posts.php?page=<?= $i; ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>"><?= $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="manage_posts.php?page=<?= $page + 1; ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
