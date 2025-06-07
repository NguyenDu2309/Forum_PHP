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
    <div class="container mx-auto pt-28 px-2 max-w-5xl">
        <h2 class="mb-4 text-2xl font-bold text-gray-800">Tất cả phản hồi</h2>
        <div class="overflow-x-auto rounded shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">ID</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tên</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Email</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Môn học</th>
                        <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Tin nhắn</th>
                        <th class="py-3 px-3 text-center font-semibold whitespace-nowrap">Đã nộp lúc</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr class='hover:bg-gray-50'>
                                    <td class='text-center'>" . $row['feedback_id'] . "</td>
                                    <td class='font-medium text-blue-700'>" . htmlspecialchars($row['name']) . "</td>
                                    <td class='text-gray-700'>" . htmlspecialchars($row['email']) . "</td>
                                    <td class='text-green-700'>" . htmlspecialchars($row['subject']) . "</td>
                                    <td class='text-gray-800'>" . nl2br(htmlspecialchars($row['message'])) . "</td>
                                    <td class='text-center text-gray-500'>" . htmlspecialchars($row['submitted_at']) . "</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-6 text-gray-500 text-lg'>Không có phản hồi nào</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
