<?php
// Include database connection
include('Partials/db_connection.php');
session_start();

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $thread_category_id = $_GET['id'];
        $titleq = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($_POST['desc'], ENT_QUOTES, 'UTF-8');
        $threadUser = $_SESSION['username'];
        $emailID = $_SESSION['email_id'];

        $imageFileName = null;
        if (isset($_FILES['thread_image']) && $_FILES['thread_image']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/thread_images/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $imageFileName = uniqid() . '_' . basename($_FILES["thread_image"]["name"]);
            $targetFile = $targetDir . $imageFileName;
            move_uploaded_file($_FILES["thread_image"]["tmp_name"], $targetFile);
        }

        $sql = "INSERT INTO `thread` (`thread_title`, `thread_desc`, `thread_cat_id`, `thread_user_name`, `email_id`, `thread_image`, `time`) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);";
        $smt = $conn->prepare($sql);
        $smt->bind_param("ssisss", $titleq, $desc, $thread_category_id, $threadUser, $emailID, $imageFileName);
        $result = $smt->execute();
        $_SESSION['message'] = $result ? "success" : "failed";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
        exit();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Partials/style.css">
    <link rel="icon" type="image/jpg" href="images/favicon1.jpg">
    <title>Bài đăng</title>
</head>
<body class="bg-gray-100">
    <?php include "Partials/_header.php"; ?>
    <?php include "Partials/login_modal.php"; ?>
    <?php include "Partials/signup_modal.php"; ?>
    <?php include "Partials/admin_login_modal.php"; ?>

    <?php if (isset($message)) : ?>
        <?php if($message == "success"): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-4 rounded" role="alert">
                <p class="font-bold">Thành công!</p>
                <p> Đã đăng chờ người khác trả lời.</p>
            </div>
        <?php elseif ($message == "failed"): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 m-4 rounded" role="alert">
                <p class="font-bold">Lỗi!</p>
                <p> Vui lòng thử lại.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $threadID = $_GET['id'];
        $sql = "SELECT * FROM `category` WHERE category_id = $threadID";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($fetch = mysqli_fetch_assoc($result)) {
                echo '<div class="max-w-4xl mx-auto p-4 bg-white rounded shadow">
                        <h2 class="text-2xl font-bold">Chào mừng đến ' . $fetch['category_name'] . '</h2>
                        <p class="mt-2">' . $fetch['category_desc'] . '</p>
                        <hr class="my-4">';
                if (isset($_SESSION["username"])) {
                    echo '<p>Xin chào: <span class="font-semibold text-red-600">' . $_SESSION["username"] . '</span></p><hr class="my-2">';
                }
                echo '<h4 class="font-bold">Quy định:</h4>
                      <p class="mt-1">Tôn trọng, lịch sự || Giữ đúng chủ đề || Không spam || Ngôn ngữ phù hợp || Không hoạt động phi pháp || Không xúc phạm</p>
                    </div>';
            }
        }
    }
    ?>

    <div class="max-w-4xl mx-auto p-4">
        <h3 class="text-xl font-semibold text-white bg-red-500 p-2 rounded">Các bài đăng</h3>

        <?php
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $threadID = $_GET['id'];
            $noResultFound = true;
            $sql = "SELECT * FROM `thread` WHERE thread_cat_id = $threadID";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                while ($fetch = mysqli_fetch_assoc($result)) {
                    $noResultFound = false;
                    $title = $fetch['thread_title'];
                    $desc = $fetch['thread_desc'];
                    $time = $fetch['time'];
                    $threaduser = $fetch['thread_user_name'];
                    $newTime = date('d/m/y  g:i a', strtotime($time));
                    $userImageSql = "SELECT user_image FROM `users` WHERE user_name = '$threaduser'";
                    $userImageResult = mysqli_query($conn, $userImageSql);
                    $userImageRow = mysqli_fetch_assoc($userImageResult);
                    $userImage = !empty($userImageRow['user_image']) ? "uploads/user_images/" . $userImageRow['user_image'] : 'images/user.png';

                    $imageHtml = '';
                    if (!empty($fetch['thread_image'])) {
                        $imageHtml = '<img src="uploads/thread_images/' . htmlspecialchars($fetch['thread_image']) . '" class="w-40 h-40 object-cover rounded mb-2">';
                    }

                    echo '<div class="bg-white rounded shadow p-4 my-4">
                            ' . $imageHtml . '
                            <div class="flex items-center mb-2">
                                <img src="' . $userImage . '" alt="User Avatar" class="w-10 h-10 rounded-full border border-red-500 mr-3">
                                <div>
                                    <p class="text-blue-600 font-semibold">' . $threaduser . '</p>
                                    <span class="text-gray-500 text-sm">' . $newTime . '</span>
                                </div>
                            </div>
                            <h5 class="text-lg font-bold">
                                <a href="threads.php?id=' . $fetch['thread_id'] . '" class="text-black hover:underline">' . $title . '</a>
                            </h5>
                            <p class="text-gray-700">' . $desc . '</p>
                          </div>';
                }
            }
            if ($noResultFound) {
                echo '<div class="bg-white p-4 rounded shadow text-center">
                        <h3 class="text-xl font-bold">Chưa có bài đăng</h3>
                        <p class="text-gray-600">Hãy là người đầu tiên đặt câu hỏi!</p>
                      </div>';
            }
        }
        ?>
    </div>

    <div class="max-w-4xl mx-auto p-4">
        <hr>
        <?php if (isset($_SESSION['username'])): ?>
            <h3 class="text-xl font-semibold text-white bg-red-500 p-2 rounded"> Đăng bài tại đây </h3>
            <form class="my-3" action="<?= $_SERVER["PHP_SELF"] . '?id=' . $_GET["id"] ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="titles" class="block text-sm font-medium">Tiêu đề</label>
                    <input type="text" id="titles" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <p class="text-sm text-gray-500">Tiêu đề ngắn gọn, rõ ràng</p>
                </div>
                <div class="mb-4">
                    <label for="floatingTextarea2" class="block text-sm font-medium">Giải thích đầy đủ</label>
                    <textarea id="floatingTextarea2" name="desc" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm h-32"></textarea>
                </div>
                <div class="mb-4">
                    <label for="thread_image" class="block text-sm font-medium">Tải hình ảnh (tuự chọn)</label>
                    <input type="file" id="thread_image" name="thread_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"> Đăng </button>
            </form>
        <?php else: ?>
            <h3 class="bg-green-200 text-center p-2 rounded"> Vui lòng đăng nhập để đăng bài! </h3>
        <?php endif; ?>
    </div>

    <?php include "Partials/_footer.php" ?>
</body>
</html>
