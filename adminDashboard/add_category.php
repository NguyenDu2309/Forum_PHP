<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Handle the form submission for adding a category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $category_name = htmlspecialchars($_POST['category_name']);
    $category_desc = htmlspecialchars($_POST['category_desc']);
    
    // Handle image upload
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
        $image_name = $_FILES['category_image']['name'];
        $image_tmp = $_FILES['category_image']['tmp_name'];
        $image_size = $_FILES['category_image']['size'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_ext, $allowed_ext) && $image_size <= 5000000) { // Max size 5MB
            $image_new_name = uniqid('', true) . '.' . $image_ext;
            $image_upload_path = '../uploads/' . $image_new_name;
            move_uploaded_file($image_tmp, $image_upload_path);

            // Insert category into database
            $insertQuery = "INSERT INTO category (category_name, category_desc, category_image) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sss", $category_name, $category_desc, $image_new_name);
            if ($stmt->execute()) {
                echo "<script>alert('Đã thêm danh mục thành công!'); window.location.href = 'add_category.php';</script>";
            } else {
                echo "<script>alert('Lỗi khi thêm danh mục!');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Tệp hình ảnh không hợp lệ hoặc kích thước quá lớn!');</script>";
        }
    } else {
        echo "<script>alert('Vui lòng tải lên hình ảnh danh mục!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <style>
        body {
            background-color: #f4f4f9; /* Light grayish background */
        }

        .container {
            margin-top: 80px; /* Adjusting for navbar space */
        }

        /* Thicker borders for the form */
        .form-group {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
        }

        /* Button Styling */
        .btn {
            padding: 8px 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
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
    <div class="container">
        <h2 class="mb-4">Thêm danh mục mới</h2>
        <a href="manage_category.php" class="btn btn-secondary mb-4">Quay lại danh mục</a>

        <form action="add_category.php" method="POST" enctype="multipart/form-data" class="form-group">
            <div class="mb-3">
                <label for="category_name" class="form-label">Tên danh mục</label>
                <input type="text" name="category_name" id="category_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="category_desc" class="form-label">Mô tả danh mục</label>
                <textarea name="category_desc" id="category_desc" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="category_image" class="form-label">Hình ảnh danh mục</label>
                <input type="file" name="category_image" id="category_image" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-primary">Thêm danh mục</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
