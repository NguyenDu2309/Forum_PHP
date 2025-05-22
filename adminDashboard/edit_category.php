<?php
session_start();
include '../Partials/db_connection.php';

// Verify if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Get category data
if (isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    $query = "SELECT * FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
}

// Update category
if (isset($_POST['update_category'])) {
    $category_name = $_POST['category_name'];
    $category_desc = $_POST['category_desc'];
    $category_image = $_FILES['category_image'];

    // Default image if no new image is uploaded
    $image_path = $category['category_image']; // existing image path

    // Check if a new image is uploaded
    if ($category_image['size'] > 0) {
        // Handle file upload
        $target_dir = "../uploads/category_images/";
        $target_file = $target_dir . basename($category_image["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type (allow only images)
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($imageFileType, $allowed_types)) {
            // Upload the new image
            if (move_uploaded_file($category_image["tmp_name"], $target_file)) {
                $image_path = $target_file; // new image path
            } else {
                echo "<script>alert('Xin lỗi, đã có lỗi khi tải hình ảnh lên.');</script>";
            }
        } else {
            echo "<script>alert('Chỉ cho phép các tệp JPG, JPEG, PNG và GIF.');</script>";
        }
    }

    // Update the category in the database
    $updateQuery = "UPDATE category SET category_name = ?, category_desc = ?, category_image = ? WHERE category_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $category_name, $category_desc, $image_path, $category_id);

    if ($stmt->execute()) {
        echo "<script>alert('Đã cập nhật danh mục thành công!'); window.location.href = 'manage_category.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật danh mục!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa danh mục</title>
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

        .form-group img {
            max-width: 200px;
            margin-top: 10px;
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
                        <a class="nav-link" href="admin_dashboard.php">Quay lại Bảng điều khiển</a>
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
        <h2>Edit Category</h2>
        <form action="edit_category.php?category_id=<?= htmlspecialchars($category['category_id']); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="category_name" class="form-label">Tên danh mục</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required
                    value="<?= htmlspecialchars($category['category_name']); ?>">
            </div>
            <div class="mb-3">
                <label for="category_desc" class="form-label">Mô tả</label>
                <textarea class="form-control" id="category_desc" name="category_desc" rows="3" required><?= htmlspecialchars($category['category_desc']); ?></textarea>
            </div>

            <div class="mb-3 form-group">
                <label for="category_image" class="form-label">Hình ảnh danh mục</label>
                <input type="file" class="form-control" id="category_image" name="category_image">
                <!-- Display current image -->
                <?php if ($category['category_image']): ?>
                    <img src="<?= htmlspecialchars($category['category_image']); ?>" alt="Current Image">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary" name="update_category">Cập nhật danh mục</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
