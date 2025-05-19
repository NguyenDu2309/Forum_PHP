<?php
// Include database connection
include('Partials/db_connection.php');
// Start the session
session_start();

// Check if a message is set in the session from previous submission
if (isset($_SESSION['message'])) {
    // Assign the message and unset it from the session
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $thread_category_id = $_GET['id'];
        $titleq = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($_POST['desc'], ENT_QUOTES, 'UTF-8');
        $threadUser = $_SESSION['username'];
        $emailID = $_SESSION['email_id'];

        // Xử lý upload ảnh
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

        // SQL query to insert new thread (thêm trường thread_image)
        $sql = "INSERT INTO `thread` (`thread_title`, `thread_desc`, `thread_cat_id`, `thread_user_name`, `email_id`, `thread_image`, `time`) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);";
        $smt = $conn->prepare($sql);
        $smt->bind_param("ssisss", $titleq, $desc, $thread_category_id, $threadUser, $emailID, $imageFileName);
        $result = $smt->execute();
        if ($result) {
            $_SESSION['message'] = "success";
        } else {
            $_SESSION['message'] = "failed";
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
        exit();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
     <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (सही ऑर्डर में) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <link rel="stylesheet" href="Partials/style.css">
        <!-- this is the link of bs-4.5 . i used media object component from it -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
                integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <link rel="icon" type="image/jpg" href="images/favicon1.jpg">

        <title>threads</title>
        <style>
                 .media-body p {
                        word-wrap: break-word; /* Allow long words to break */
                        white-space: normal;   /* Allow text to wrap normally */
                }
                     .media-body h5 a {
                         word-wrap: break-word; /* Allow long words to break */
                            white-space: normal; /* Allow text to wrap normally */
                            display: block; /* ensure the link take the entire block */
                }
        </style>
</head>
<body>
        <!-- included the _header file where is my navbar  -->
        <?php include "Partials/_header.php"; ?>
        <?php include "Partials/login_modal.php"; ?>
        <?php include "Partials/signup_modal.php"; ?>
        <?php include "Partials/admin_login_modal.php"; ?>

        <!-- Display the alert message if it exists in the session -->
        <?php if (isset($message)) : ?>
                <?php if($message == "success"): ?>
                            <div class="alert alert-success alert-dismissible fade show " role="alert">
                                     <strong class="ms-1">✔successfully! </strong> posted wait for others reply.
                                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                    <?php elseif ($message == "failed"): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                         <strong class="ms-1">Error!</strong> Your question did not post due to some reason please try again.
                                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                         </div>
                    <?php endif; ?>
        <?php endif; ?>

        <?php
        // get the clicked card id by get function and store it in $threadID
        if (isset($_GET['id']) && !empty($_GET['id'])) {
                $threadID = $_GET['id'];
                $sql = "SELECT * FROM `category`WHERE category_id = $threadID";
                $result = mysqli_query($conn, $sql);
                if ($result == true) {
                        while ($fetch = mysqli_fetch_assoc($result)) {
                                echo '<div class="container my-4">
                                                                        <div class="col-lg-12">
                                                                                        <div class="h-50 p-3 bg-light border rounded-3">
                                                                                            <h2> Welcome to ' . $fetch['category_name'] . ' </h2>
                                                                                            <p class="p-3"> ' . $fetch['category_desc'] . ' </p>
                                                                                            <hr>';

                                if (isset($_SESSION["username"])) {
                                        echo '<p> Welcome : <span class ="fw-bold text-danger">' . $_SESSION["username"] . '</span></p> ';
                                        echo '<hr>';
                                }

                                echo '
                                                                                            <h4 class="p-0"> Rules: </h4>
                                                                                            <p class="p-3">  Be Respectful and Courteous || Stay On Topic || No Spamming or Advertising || Use Appropriate Language || No Illegal Activities || No Offensive Content</p>
                                                                                        </div>
                                                                                    </div>
                                                                            </div>';

                        }
                }
        }


        ?>


        <!-- here is the form form bs in which user will ask its question -->
        <div class="container">
                <hr>
                <?php
                if (isset($_SESSION['username'])) {
                        echo '
            <h3 class="p-2 bg-danger rounded"> Ask questions here </h3>
<!-- we use here php self in action this mean it will post this request in the same page where the form located -- -->
             <form class="my-3" action="' . ($_SERVER["PHP_SELF"]) . '?id=' . $_GET["id"] . '" method="POST" enctype="multipart/form-data">

             <div class="mb-3">
                        <label for="titles" class="form-label">Question title</label>
                        <input type="text" class="form-control" placeholder="Enter your question title" id="titles" aria-describedby="emailHelp" name="title">
                        <div id="emailHelp" class="form-text" name="title">Question title should be understable and simple-short way</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Full explaination</label>
                        <div class="form-floating">
                        <textarea class="form-control"  id="floatingTextarea2" style="height: 100px" name="desc"  ></textarea>
                        <label for="floatingTextarea2"  id="description">Explain your question in detail</label>
                    </div>
                    </div>
                    <div class="mb-3">
        <label for="thread_image" class="form-label">Upload Image (optional)</label>
        <input type="file" class="form-control" id="thread_image" name="thread_image" accept="image/*">
    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                </div>
            ';
                } else {
                        echo ' <h3 class = "bg-success p-2 text-center rounded-pill"> Please login to post question!  </h3>';
                }

                ?>
        </div>
        <hr>

        <!-- using media object using bs 4.5 by which we will show users queries -->
        <div class="container my-3">
                <h3 class="p-2 my-3 bg-danger rounded"> User queries </h3>

                <!-- now we will show thread queries using the thread table and here we are fetching it -->
                <?php
                /*  getting id of clicked category by $_GET function. we achieve this because we save
                id of card category to the url of thread.php while clicking it. and then get it by
                this function */
                if (isset($_GET['id']) && !empty($_GET['id'])) {
                        $threadID = $_GET['id'];
                        $noResultFound = true;
                        $sql = "SELECT * FROM `thread` WHERE thread_cat_id = $threadID";
                        $result = mysqli_query($conn, $sql);
                        if ($result == true) {
                                while ($fetch = mysqli_fetch_assoc($result)) {
                                        $noResultFound = false;
                                        $title = $fetch['thread_title'];
                                        $desc = $fetch['thread_desc'];
                                        $time = $fetch['time'];
                                        $threaduser = $fetch['thread_user_name'];
                                        $newTime = date('d/m/y  g:i a', strtotime($time));
                                        
                                        // Fetch user image
                                        $userImageSql = "SELECT user_image FROM `users` WHERE user_name = '$threaduser'";
                                        $userImageResult = mysqli_query($conn, $userImageSql);
                                        $userImageRow = mysqli_fetch_assoc($userImageResult);
                                        $userImage = !empty($userImageRow['user_image']) ? "uploads/user_images/" . $userImageRow['user_image'] : 'images/user.png';

                                        $imageHtml = '';
                                        if (!empty($fetch['thread_image'])) {
                                            $imageHtml = '<img src="uploads/thread_images/' . htmlspecialchars($fetch['thread_image']) . '" class="img-fluid mb-2" style="max-width:200px;max-height:200px;" alt="Thread Image">';
                                        }
                                        echo '
                            <div class="card mb-3">
                                    <div class="card-body">
                                            ' . $imageHtml . '
                                            <div class="d-flex align-items-center">
                                                    <img src="' . $userImage . '" alt="User Avatar" class="rounded-circle me-3 border border-danger" style="width:40px; height:40px;">
                                                    <div>
                                                            <h6 class="card-subtitle mb-1 text-primary fw-bold">' . $threaduser . '</h6>
                                                            <span class="text-muted small">' . $newTime . '</span>
                                                    </div>
                                            </div>
                                            <hr class="my-2">
                                                <h5 class="card-title mt-2">
                                                    <a class="text-dark stretched-link text-decoration-none" href="threads.php?id=' . $fetch['thread_id'] . '">
                                                            ' . $title . '
                                                    </a>
                                            </h5>
                                            <p class="card-text text-wrap">' . $desc . '</p>
                                            
                                    </div>
                            </div>';


                                }
                        }


                        if ($noResultFound == true) {
                                echo ' <div class="jumbotron jumbotron-fluid">
                                                    <div class="container">
                                                        <h3 class="display-4">No question found </h3>
                                                        <p class="lead">There is no question related to this category be the first person to ask.</p>
                                                    </div>
                                                </div>';
                        }


                }

                ?>
        </div>

        <!-- Optional JavaScript; choose one of the two! -->

        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
                crossorigin="anonymous">
        </script>

        <script>
                //removing alert after 3 seconds --
                let alerts = document.querySelectorAll('.alert');
                alerts.forEach(function (value) {
                        setTimeout(() => {
                                value.style.display = "none";
                        }, 3000);
                })
        </script>

        <!-- Option 2: Separate Popper and Bootstrap JS -->
        
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        
        <?php include "Partials/_footer.php" ?>
</body>
</html>
