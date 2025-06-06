<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/jpg" href="images/favicon1.jpg">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Kết quả tìm kiếm</title>
  </head>
  <body class="bg-gray-50 min-h-screen flex flex-col">
<!-- included the _header file where is my navbar  -->
    <?php include "Partials/db_connection.php";?>
    <?php include "Partials/_header.php";?>
    <?php include "Partials/login_modal.php"; ?>
    <?php include "Partials/signup_modal.php"; ?>
    <?php include "Partials/admin_login_modal.php"; ?>

    <div class="container mx-auto my-6 flex-1 px-4">
        <?php 
        if(isset($_GET["query"])){
            $search = $_GET['query'];
            echo '<h3 class="text-center bg-red-500 text-white p-3 rounded my-4 text-xl font-semibold shadow">Kết quả tìm kiếm cho "<span class=\'italic\'>'.$search.'</span>"</h3>';

            $sql = "SELECT * FROM thread WHERE thread_title LIKE  '%$search%' 
                    OR thread_desc LIKE '%$search%'";

            $result = mysqli_query($conn, $sql);
            $row = mysqli_num_rows($result);
            if($row > 0){
                while ($fetch = mysqli_fetch_assoc($result)) {
                    $user = $fetch['thread_user_name'];
                    $title = $fetch['thread_title'];
                    $desc = $fetch['thread_desc'];
                    $time = $fetch['time'];
                    echo '
                    <div class="bg-white rounded-lg shadow mb-5 p-5">
                        <div class="flex items-center mb-2">
                            <img src="images/user.png" alt="User Avatar" class="rounded-full border-2 border-red-500 mr-3" style="width:40px; height:40px;">
                            <div>
                                <h6 class="text-blue-600 font-bold mb-1">' . htmlspecialchars($user) . '</h6>
                                <span class="text-gray-500 text-xs">' . htmlspecialchars($time) . '</span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <h5 class="text-lg font-semibold mt-2">
                            <a class="text-gray-900 hover:text-blue-600 transition" href="threads.php?id=' . $fetch['thread_id'] . '">
                                ' . htmlspecialchars($title) . '
                            </a>
                        </h5>
                        <p class="text-gray-700 mt-2">' . htmlspecialchars($desc) . '</p>
                    </div>';
                }
            } else {
                echo '
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-lg my-8 shadow min-h-[300px] flex flex-col justify-center items-center">
                    <h1 class="text-2xl font-bold mb-2">Không có kết quả cho "<em>'.$search.'</em>"</h1>
                    <p class="mb-3">Không có kết quả nào tương ứng với tìm kiếm của bạn.</p>
                    <ul class="list-disc list-inside text-left text-sm text-gray-700">
                        <li>Có vẻ như chúng tôi không thể tìm thấy bất kỳ kết quả nào cho <strong>'.$search.'</strong>. Hãy thử khám phá các danh mục hoặc kiểm tra trang chủ.</li>
                        <li>Kiểm tra chính tả của bạn.</li>
                    </ul>
                </div>';
            }
        }
        ?>
    </div>

    <?php include ('Partials/_footer.php'); ?>
  </body>
</html>