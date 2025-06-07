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
    <title>Thêm danh mục mới</title>
    <link rel="icon" type="image/jpg" href="/Forum_website/images/favicon1.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-gray-800 text-white z-10 shadow">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <a class="font-bold text-lg" href="admin_dashboard.php">Bảng điều khiển quản trị</a>
            <div class="flex gap-4">
                <a class="hover:text-blue-400 transition" href="admin_dashboard.php">Quay lại Bảng điều khiển</a>
                <a class="hover:text-blue-400 transition" href="admin_logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto pt-28 px-2 max-w-xl">
        <h2 class="mb-4 text-2xl font-bold text-gray-800">Thêm danh mục mới</h2>
        <a href="manage_category.php" class="inline-block bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900 transition text-sm mb-4">← Quay lại danh mục</a>

        <form action="add_category.php" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
            <div>
                <label for="category_name" class="block font-semibold mb-1 text-gray-700">Tên danh mục</label>
                <input type="text" name="category_name" id="category_name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="category_desc" class="block font-semibold mb-1 text-gray-700">Mô tả danh mục</label>
                <textarea name="category_desc" id="category_desc" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div>
                <label for="category_image" class="block font-semibold mb-1 text-gray-700">Hình ảnh danh mục</label>
                <input type="file" name="category_image" id="category_image" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white" accept="image/*" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold">Thêm danh mục</button>
        </form>
    </div>
</body>
</html>
