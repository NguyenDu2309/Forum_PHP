<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Pagination setup
$limit = 10; // Number of comments per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$offset = ($page - 1) * $limit;

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE comment LIKE '%$search%' OR user_name LIKE '%$search%'";
}

// Query to get comments with pagination and search functionality
$query = "SELECT * FROM comments $search_query ORDER BY comment_time DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of comments for pagination
$total_query = "SELECT COUNT(*) FROM comments $search_query";
$total_result = $conn->query($total_query);
$total_comments = $total_result->fetch_row()[0];
$total_pages = ceil($total_comments / $limit);

$stmt->close();

// Handle comment deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Xóa reply trước
    $conn->query("DELETE FROM replies WHERE comment_id = $delete_id");
    // Xóa comment
    $delete_query = "DELETE FROM comments WHERE comment_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php'); // Redirect to avoid resubmitting form
}

// Handle reply deletion
if (isset($_GET['delete_reply_id'])) {
    $delete_reply_id = intval($_GET['delete_reply_id']);
    $delete_reply_query = "DELETE FROM replies WHERE reply_id = ?";
    $stmt = $conn->prepare($delete_reply_query);
    $stmt->bind_param("i", $delete_reply_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_comments.php' . (isset($_GET['page']) ? '?page=' . intval($_GET['page']) : ''));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
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

        .table-responsive {
            margin-bottom: 20px;
        }

        .table td,
        .table th {
            word-break: break-word;
            vertical-align: top;
            max-width: 350px;
        }

        /* To ensure the page works well on small screens */
        @media (max-width: 768px) {
            .table th,
            .table td {
                padding: 10px;
            }

            .btn {
                padding: 6px 12px;
            }

            .search-bar input {
                width: 100%;
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
        <h2>Manage Comments</h2>

        <!-- Search Form -->
        <form class="search-bar" method="GET" action="manage_comments.php">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by comment or author"
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
        <a href="manage_comments.php" class="btn btn-secondary mb-3">Back to All Comments</a>
        <?php endif; ?>

        <!-- Table of Comments -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Comment</th>
                        <th>Author</th>
                        <th>Post Title</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serial = $offset + 1; // Serial number starts from 1 on each page
                    while ($comment = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $serial++; ?></td>
                        <td><?= htmlspecialchars($comment['comment']); ?></td>
                        <td><?= htmlspecialchars($comment['user_name']); ?></td>
                        <td><?= htmlspecialchars($comment['thread_comment_id']); ?></td>
                        <td><?= htmlspecialchars($comment['comment_time']); ?></td>
                        <td>
                            <a href="manage_comments.php?delete_id=<?= $comment['comment_id']; ?>" class="btn btn-danger btn-sm mt-1"
                                onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                    // Lấy các reply cho comment này
                    $reply_query = "SELECT * FROM replies WHERE comment_id = " . intval($comment['comment_id']) . " ORDER BY reply_id ASC";
                    $reply_result = mysqli_query($conn, $reply_query);
                    if ($reply_result && mysqli_num_rows($reply_result) > 0):
                        while ($reply = mysqli_fetch_assoc($reply_result)):
                    ?>
                    <tr>
                        <td></td>
                        <td colspan="4" class="ps-5 border-start border-2 border-primary bg-light">
                            <span class="fw-bold text-success">Reply by <?= htmlspecialchars($reply['user_name']); ?>:</span>
                            <?= htmlspecialchars($reply['reply_text']); ?>
                            <span class="text-muted ms-2" style="font-size:0.9em;">
                                <?= isset($reply['reply_time']) ? htmlspecialchars($reply['reply_time']) : ''; ?>
                            </span>
                        </td>
                        <td>
                            <a href="manage_comments.php?delete_reply_id=<?= $reply['reply_id']; ?>&page=<?= $page; ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this reply?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    endif;
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="manage_comments.php?page=<?= $page - 1; ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="manage_comments.php?page=<?= $i; ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>"><?= $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="manage_comments.php?page=<?= $page + 1; ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
