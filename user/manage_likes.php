<?php
session_start();
include '../Partials/db_connection.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Verify if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Pagination setup
$limit = 10; // Number of comments per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = ($page <= 0) ? 1 : $page; // If page is less than or equal to 0, reset to 1
$offset = ($page - 1) * $limit; // Offset for SQL query

// Query to get liked comments
$query = "SELECT lc.*, c.comment, c.comment_time, t.thread_title, t.thread_user_name
          FROM comment_likes lc
          INNER JOIN comments c ON lc.comment_id = c.comment_id
          INNER JOIN thread t ON c.thread_comment_id = t.thread_id
          WHERE lc.user_id = ? AND lc.liked = 1
          ORDER BY c.comment_time DESC
          LIMIT ?, ?";


$stmt = $conn->prepare($query);  // Prepare the query
$stmt->bind_param("iii", $user_id, $offset, $limit); // Bind parameters
$stmt->execute();  // Execute the query
$result = $stmt->get_result();  // Get the result



// Get total number of liked comments for pagination
$total_query = "SELECT COUNT(*) FROM comment_likes WHERE user_id = ?";
$stmt_total = $conn->prepare($total_query);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_liked_comments = $total_result->fetch_row()[0];
$total_pages = ceil($total_liked_comments / $limit); // Calculate total pages
$stmt_total->close();


// Handle unlike comment
if (isset($_GET['unlike_id'])) {
    $unlike_id = intval($_GET['unlike_id']);
    // 1. Bỏ like trong bảng comment_likes
    $unlike_query = "UPDATE comment_likes SET liked = 0 WHERE comment_id = ? AND user_id = ?";
    $stmt_unlike = $conn->prepare($unlike_query);
    $stmt_unlike->bind_param("ii", $unlike_id, $user_id);
    $stmt_unlike->execute();
    $stmt_unlike->close();

    // 2. Đếm lại tổng số like thật sự cho comment này
    $count_query = "SELECT COUNT(*) FROM comment_likes WHERE comment_id = ? AND liked = 1";
    $stmt_count = $conn->prepare($count_query);
    $stmt_count->bind_param("i", $unlike_id);
    $stmt_count->execute();
    $stmt_count->bind_result($new_likes);
    $stmt_count->fetch();
    $stmt_count->close();

    // 3. Cập nhật lại số like trong bảng comments
    $update_comment = $conn->prepare("UPDATE comments SET likes = ? WHERE comment_id = ?");
    $update_comment->bind_param("ii", $new_likes, $unlike_id);
    $update_comment->execute();
    $update_comment->close();

    header('Location: manage_likes.php');
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liked Comments</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
          body {
             background-color: #f4f4f9;
             display: flex;
             flex-direction: column;
             min-height: 100vh;
                
        }

        .container{
            margin-top:50px;
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

        /* Style for the scrollable td */
        .table-responsive tbody td {
            max-height: 150px;
            /* Adjust max-height as needed */
            overflow-y: auto;
            display: block;
            /* Make td a block-level element */
            padding: 10px;
            word-break: break-word;
            /* Allow long words to break */
            white-space: normal;
            /* Allow text to wrap normally */
        }

        .table th {
            text-align: center;
        }

        .table td {
            white-space: normal;
            word-break: break-word;
        }
        .footer{
            margin-top: auto;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_profile.php">User Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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

    <div class="container main-container">

    <div class="container1 mt-5">
    <button  class=" button rounded py-2 px-3 mt-2 text-white bg-dark border-0"> <a class="text-white text-decoration-none" href="user_profile.php">Back to Dashboard</a></button>
    </div>

    <!-- Main Content -->
    <div class="container mt-3">
        <h2>Liked Comments</h2>
       <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <tbody>
                    <?php
                    $serial = $offset + 1; // Serial number starts from 1 on each page
                    while ($like = $result->fetch_assoc()): ?>
                    <tr class="align-middle">
                        <td class="bg-secondary text-center fw-bold mt-3"><?= $serial++; ?></td>
                        <td class="bg-secondary-subtle px-3 w-auto text-start text-wrap" style="min-width: 150px;">
                             <span class="fw-bold text-primary"> Question Posted by: </span>
                            <?= htmlspecialchars($like['thread_user_name']); ?>
                        </td>
                        <td class="bg-light px-3 w-auto text-start text-wrap" style="min-width: 180px;">
                            <span class="fw-bold text-success"> Question: </span>
                                <?= htmlspecialchars($like['thread_title']); ?>
                           </td>
                        <td class="bg-warning-subtle px-3 w-auto text-start text-wrap" style="min-width: 200px;">
                             <span class="fw-bold text-danger"> Comment: </span>
                            <?= htmlspecialchars($like['comment']); ?>
                         </td>
                        <td class="bg-light text-muted text-center w-auto" style="min-width: 120px;">
                            <?= htmlspecialchars($like['comment_time']); ?>
                        </td>
                        <td class="text-center w-auto" style="min-width: 150px;">
                            <a href="manage_likes.php?unlike_id=<?= $like['comment_id']; ?>"
                                class="btn btn-danger btn-sm mt-1 px-3"
                                onclick="return confirm('Are you sure you want to unlike this comment?');">
                                ❌ Unlike
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </div>


        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link"
                        href="manage_likes.php?page=<?= max($page - 1, 1); ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="manage_likes.php?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link"
                        href="manage_likes.php?page=<?= min($page + 1, $total_pages); ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <div class="footer">
      <?php include '../Partials/_footer.php'; ?>
    </div>
</body>

</html>