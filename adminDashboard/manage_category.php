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
    <title>Quản lý danh mục</title>
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
    <div class="container mx-auto pt-28 px-2 max-w-4xl">
        <h2 class="mb-4 text-2xl font-bold text-gray-800">Quản lý danh mục</h2>
        <a href="add_category.php" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition text-sm mb-4">+ Thêm danh mục mới</a>

        <div class="overflow-x-auto rounded shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">#</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tên danh mục</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Mô tả</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if ($result->num_rows > 0): ?>
                        <?php 
                        $serial_number = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="text-center"><?= $serial_number++; ?></td>
                                <td class="font-medium text-blue-700"><?= htmlspecialchars($row['category_name']); ?></td>
                                <td class="text-gray-700"><?= htmlspecialchars($row['category_desc']); ?></td>
                                <td class="text-center space-y-2">
                                    <a href="edit_category.php?category_id=<?= htmlspecialchars($row['category_id']); ?>"
                                       class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded text-sm font-semibold transition mb-1">
                                        Chỉnh sửa
                                    </a>
                                    <a href="manage_category.php?delete=<?= htmlspecialchars($row['category_id']); ?>"
                                       class="inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold transition mb-1"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');">
                                        Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 text-lg">Không tìm thấy danh mục nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
