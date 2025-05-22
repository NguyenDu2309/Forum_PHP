<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Fetch all feedbacks from the database
$query = "SELECT * FROM feedback ORDER BY submitted_at DESC";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem phản hồi</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Custom Navbar Styling */
        .navbar-custom {
            background-color: #343a40;
        }

        .navbar-custom .navbar-brand {
            color: #fff;
        }

        .navbar-custom .nav-link {
            color: #fff;
        }

        .navbar-custom .nav-link:hover {
            color: #ffc107;
        }

        /* Main Content Styling */
        .main-content {
            margin-top: 100px; /* Added margin from top */
            padding-left: 50px; /* Left-side padding */
        }

        .table th, .table td {
            text-align: center;
        }

        .table-container {
            margin-top: 30px;
        }

        /* Styling for Back and Logout Buttons */
        .navbar-custom .nav-item {
            margin-left: 15px;
        }

        .btn-custom {
            background-color: #ffc107;
            color: #fff;
            border-radius: 5px;
            padding: 10px 20px;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #e0a800;
        }

        .navbar-custom .navbar-toggler {
            border-color: transparent;
        }
    </style>
</head>

<body>

   <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">Bảng điều khiển quản trị</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">Quay lại Bảng điều khiển</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container">
            <h2 class=" mb-4">Tất cả phản hồi</h2>

            <div class="table-container">
                <!-- Feedback Table -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Môn học</th>
                            <th>Tin nhắn</th>
                            <th>Đã nộp lúc</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display feedbacks
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>" . $row['feedback_id'] . "</td>
                                        <td>" . $row['name'] . "</td>
                                        <td>" . $row['email'] . "</td>
                                        <td>" . $row['subject'] . "</td>
                                        <td>" . nl2br($row['message']) . "</td>
                                        <td>" . $row['submitted_at'] . "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No feedbacks available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
