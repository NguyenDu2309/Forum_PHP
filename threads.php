<?php
// Include database connection
include('Partials/db_connection.php');
// Start the session
session_start();

// Get the thread ID from the URL
$thread_id = $_GET['id'];

// Pagination logic
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$comments_per_page = 10;
$offset = ($page - 1) * $comments_per_page;

// SQL query to fetch comments related to the thread, ordered by likes (most likes first)
$sql = "SELECT * FROM comments WHERE thread_comment_id = ? ORDER BY likes DESC LIMIT ? OFFSET ?";
// Prepare the SQL query
$stmt = $conn->prepare($sql);
// Bind the thread ID to the prepared statement
$stmt->bind_param("iii", $thread_id, $comments_per_page, $offset);
// Execute the prepared statement
$stmt->execute();
// Get the result from the executed statement
$comments = $stmt->get_result();

// Helper function to check if a comment is liked by the current user
function checkIfLiked($comment_id)
{
    // Use the global database connection
    global $conn;
    // Check if the user is logged in, if not return 0 (not liked)
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // SQL query to check if the user has liked the specific comment
    $sql = "SELECT liked FROM comment_likes WHERE comment_id = ? AND user_id = ?";
    // Prepare the SQL query
    $stmt = $conn->prepare($sql);
    // Bind the comment ID and user ID to the prepared statement
    $stmt->bind_param("ii", $comment_id, $user_id);
    // Execute the prepared statement
    $stmt->execute();
    // Get the result from the executed statement
    $result = $stmt->get_result();

    // If there is result fetch it from the database
    if ($result->num_rows > 0) {
        // return like status
        return $result->fetch_assoc()['liked'];
    }
    // If the user has not liked the comment, return 0
    return 0;
}

// Initialize $post variable
$post = null; // or false depending on if you want to use it for a default alert
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['comment']) && isset($_GET['id'])) {
        $threadID = $_GET['id'];
        $comment = $_POST['comment'];
        $username = $_SESSION['username'];
        $emailID = $_SESSION['email_id'];
        //SQL query to insert new comments
        $sql = "INSERT INTO `comments` (`comment`, `thread_comment_id`, `user_name`, `email_id`, `comment_time`) VALUES (?, ?, ?, ?, current_timestamp())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $comment, $threadID, $username, $emailID);
        $result2 = $stmt->execute();

        // check if the query successfully executed then set $result variable for further use
        if ($result2) {
            // set a variable in $_SESSION to show the success message and it will only be true once when user posts comment
             $_SESSION['alert_success'] = true;
             header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id);// this code will stop the reposting the comment in order to reload
             exit();  // Don't forget to call exit() after header to stop further code execution


        }
         // if the query is failed then echo this message and die
          else {
              // set a variable in $_SESSION to show the fail message and it will only be true once when user posts comment
             $_SESSION['alert_fail'] = true;
             header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id);// this code will stop the reposting the comment in order to reload
             exit();  // Don't forget to call exit() after header to stop further code execution

         }
    }
    // Xử lý reply nếu có
    if (isset($_POST['post_reply']) && isset($_POST['reply_text']) && isset($_POST['reply_comment_id'])) {
        $reply_text = trim($_POST['reply_text']);
        $reply_comment_id = intval($_POST['reply_comment_id']);
        $reply_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
        if ($reply_text !== '') {
            $reply_sql = "INSERT INTO replies (comment_id, user_name, reply_text) VALUES (?, ?, ?)";
            $reply_stmt = $conn->prepare($reply_sql);
            $reply_stmt->bind_param("iss", $reply_comment_id, $reply_user, $reply_text);
            $reply_stmt->execute();
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page);
            exit();
        }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel = "stylesheet" href ="Partials/style.css">
    <!-- this is the link of bs-4.5 . i used media object component from it -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/jpg" href="images/favicon1.jpg">
    
    <title>threads</title>
    <style>
      /* Custom style for date and time */
      .text-attractive-color {
          color: #6c757d; /* A soft gray color */
          font-size: 0.9rem; /* Slightly smaller font size */
          font-style: italic; /* To differentiate it from the main text */
      }

       /* Heart button style */
        .like-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .heart-icon {
            color: gray;
            font-size: 24px;
        }
        .heart-icon.liked {
            color: red;
        }
      
      /* Container for comment text and time */
      .comment-container {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
      }

      .comment-text {
          margin-bottom: 10px;
      }
      
      /* Additional spacing to ensure comments don't overlap header */
      .media {
          margin-top: 20px; /* Give some space from the top */
      }
    </style>
  </head>
  <body>
  <!-- included the _header file where is my navbar  -->
    <?php  include"Partials/_header.php"?>
    <?php  include"Partials/db_connection.php"?>
    <?php  include"Partials/login_modal.php"?>
    <?php  include"Partials/signup_modal.php"?>

    <?php
      //Check if $_SESSION variable is true then show the message only once
        if (isset($_SESSION['alert_success']) && $_SESSION['alert_success'] === true) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong class="ms-1">✔Success!</strong> Comment posted successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                     //Unset the variable to avoid showing the message again
                    unset($_SESSION['alert_success']);
        }
         //Check if $_SESSION variable is true then show the message only once
       elseif (isset($_SESSION['alert_fail']) && $_SESSION['alert_fail'] === true) {
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                             <strong class="ms-1">Error!</strong> you can not post comment right now try later.
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                    //Unset the variable to avoid showing the message again
                    unset($_SESSION['alert_fail']);
        }
    ?>
     <?php
    // Get the clicked thread's id, and fetch the thread details from the database and display it
          if(isset($_GET['id'])) {
              $threadsID = $_GET['id'];
              // SQL query to fetch thread details by its ID
              $sql = "SELECT * FROM `thread` WHERE thread_id = $threadsID";
              $result = mysqli_query($conn, $sql);
              // If the query is successfully executed
              if($result == true) {
                  // Loop through the fetched threads to display the threads in HTML format
                  while ($fetch = mysqli_fetch_assoc($result)) {
                      echo '<div class="container my-4">
                                <div class="col-lg-12">
                                    <div class="h-50 p-3 bg-light border rounded-3">';
                                    // Hiển thị ảnh nếu có
                                    if (!empty($fetch['thread_image'])) {
                                        echo '<img src="uploads/thread_images/' . htmlspecialchars($fetch['thread_image']) . '" alt="Thread Image" class="img-fluid mb-3" style="max-width:300px;max-height:300px;border-radius:8px;">';
                                    }
                      echo '          <h4 style="word-wrap: break-word;"> <span>🔹Q :- </span'. $fetch['thread_title'] .'</h4>
                                        <p class="py-1" style="word-wrap: break-word; white-space: normal;" <span>🔻 </span> '. $fetch['thread_desc'] .' </p>
                                        <hr>';
                                        if(isset($_SESSION["username"])) {
                                            echo '<p> posted by  : <span class ="fw-bold text-danger">'.$fetch["thread_user_name"].'</span></p> ';
                                            echo '<hr>';
                                        }
                      echo '
                                        
                                    </div>
                                </div>
                            </div>';
                  }
              }
          }
     ?>

<!-- here is the php code for comment post -->
 <div class="container">
     <?php
        /*  Here we have removed the $post variable from this section 
         as we are now going to check it in the starting section only */
     ?>
  </div>

<!-- Display comments in media object format -->
<div class="container my-3" style="max-height: 500px; overflow-y: auto;">
    <h2 class="bg-danger p-2 my-3 rounded"> Discussion </h2>

    <?php
    // Loop through each comment fetched from the database
    while ($comment = $comments->fetch_assoc()):
        // Fetch user image from the users table using the user_name
        $user_name = $comment['user_name']; // Get the user_name from the comment
        $user_sql = "SELECT user_image FROM users WHERE user_name = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("s", $user_name); // Bind the user_name as a string
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_image = 'images/user.png'; // Default user image

        // Check if user image exists and update the $user_image variable
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc(); // Fetch the row with user_image
            if (!empty($user_row['user_image'])) {
                $user_image = "uploads/user_images/" . $user_row['user_image'];
            }
        }
        
    ?>
        <div class="media mb-3">
            <!-- User Image (Making it round using CSS) -->
            <img src="<?php echo htmlspecialchars($user_image); ?>" class="mr-3 rounded-circle" alt="User" style="width:40px; height:40px;">
            <div class="media-body comment-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="mt-0 text-primary"><?php echo htmlspecialchars($comment['user_name']); ?></h5>
                    <!-- Like button and likes count section -->
                    <div class="d-flex align-items-center mt-2">
                    <p class="m-0" id="likes-count-<?php echo $comment['comment_id']; ?>"><?php echo $comment['likes']; ?></p>
                    <button class="like-btn" 
                        id="like-<?php echo $comment['comment_id']; ?>" 
                        data-comment-id="<?php echo $comment['comment_id']; ?>" 
                        data-liked="<?php echo checkIfLiked($comment['comment_id']) ? '1' : '0'; ?>">
                        <i class="heart-icon <?php echo checkIfLiked($comment['comment_id']) ? 'liked' : ''; ?> fas fa-heart"></i>
                    </button>
                    <!-- Reply icon button -->
                    <button class="btn btn-sm btn-link p-0 ms-2 align-middle" type="button" title="Reply"
                        onclick="toggleReplyForm(<?php echo $comment['comment_id']; ?>)">
                        <i class="fas fa-reply"></i>
                    </button>
                </div>
                </div>
                <!-- Comment text and comment time section -->
                <div class="comment-container">
                    <p class="comment-text"><?php echo htmlspecialchars($comment['comment']); ?></p>
                    <small class="text-muted text-attractive-color">
                        <?php echo date('d-m-Y h:i A', strtotime($comment['comment_time'])); ?>
                    </small>
                </div>
               
                <form method="POST" class="mt-2" style="display:none;" id="reply-form-<?php echo $comment['comment_id']; ?>">
                    <div class="input-group">
                        <input type="text" name="reply_text" class="form-control form-control-sm" placeholder="Write a reply..." required>
                        <input type="hidden" name="reply_comment_id" value="<?php echo $comment['comment_id']; ?>">
                        <button type="submit" name="post_reply" class="btn btn-sm btn-success">Send</button>
                    </div>
                </form>
                <!-- Hiển thị replies -->
                <?php
                $reply_sql = "SELECT * FROM replies WHERE comment_id = ? ORDER BY reply_time ASC";
                $reply_stmt = $conn->prepare($reply_sql);
                $reply_stmt->bind_param("i", $comment['comment_id']);
                $reply_stmt->execute();
                $reply_result = $reply_stmt->get_result();
                while ($reply = $reply_result->fetch_assoc()):
                ?>
                    <div class="ms-4 mt-2 p-2 bg-light rounded">
                        <strong class="text-secondary" style="font-size:0.95em;"><?php echo htmlspecialchars($reply['user_name']); ?>:</strong>
                        <span style="font-size:0.95em;"><?php echo htmlspecialchars($reply['reply_text']); ?></span>
                        <br>
                        <small class="text-muted" style="font-size:0.8em;"><?php echo date('d-m-Y h:i A', strtotime($reply['reply_time'])); ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- here is the form for post comments -->
<div class="container">
  <hr>
  <?php
        //check if the user is logged in if logged in then show the form
        if(isset($_SESSION['username'])) {
           echo '
            <h2> Post comment for your response</h2>
            <form class="my-3" action="'.$_SERVER['PHP_SELF'].'?id='.$_GET['id'].'" method="POST">
                <div class="mb-3">
                  <label for="description" class="form-label">Write a solution </label>
                  <div class="form-floating">
                  <textarea class="form-control"  id="floatingTextarea2" style="height: 100px" name="comment"  ></textarea>
                  <label for="floatingTextarea2"  id="description">write the solution if you have</label>
                </div>
                </div>
                <button type="submit" class="btn btn-primary">Post</button>
              </form>
            ';
        }
        // if the user is not logged in then show this message
        else {
            echo '<h3 class="bg-success p-2 text-center rounded-pill"> Please login to reply ! </h3>';
        }
    ?>
</div>
  <hr>
  
<?php
$count_sql = "SELECT COUNT(*) as total FROM comments WHERE thread_comment_id = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $thread_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_comments = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_comments / $comments_per_page);
?>

<!-- Pagination for comments -->
<nav aria-label="Comment pagination">
  <ul class="pagination justify-content-center">
    <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=<?php echo $page-1; ?>">Previous</a>
      </li>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
        <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
      </li>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
      <li class="page-item">
        <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=<?php echo $page+1; ?>">Next</a>
      </li>
    <?php endif; ?>
  </ul>
</nav>
    
<script>

    // removing alert after 3 sec with js
    let alerts = document.querySelectorAll('.alert'); // Assuming multiple alerts with 'alert' class
    alerts.forEach(function(alert) {
        setTimeout(() => {
            alert.remove();
        }, 3000);
    });

       
    
     document.addEventListener('DOMContentLoaded', function () {
        // Select all like buttons on the page
            document.querySelectorAll('.like-btn').forEach(function(button) {
                 // Add a click event listener to each button
                button.addEventListener('click', function() {
                     // Get the comment ID from the button
                    const commentId = button.getAttribute('data-comment-id');
                     // Get the likes count paragraph
                    const likesCountElement = document.getElementById(`likes-count-${commentId}`);
                     // Get the heart icon element inside the button
                    const heartIcon = button.querySelector('.heart-icon');

                // Make an AJAX request to like_comment.php
                    fetch('like_comment.php', {
                         // Set request method to POST
                        method: 'POST',
                        // Set request body with comment id
                        body: new URLSearchParams({
                            'comment_id': commentId
                        }),
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    // Convert response to JSON
                    .then(response => response.json())
                    .then(function(data) {
    if (data.success) {
        button.setAttribute('data-liked', data.liked);

        // Toggle 'liked' class và cập nhật màu tim
        if (data.liked == 1) {
            heartIcon.classList.add('liked');
            heartIcon.style.color = 'red';
        } else {
            heartIcon.classList.remove('liked');
            heartIcon.style.color = 'gray';
        }

        likesCountElement.textContent = data.newLikes;
    }
})
                    // Handle errors during AJAX request
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
  
     <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->

    <?php include "Partials/_footer.php"  ?>
    <script>
function toggleReplyForm(commentId) {
    var form = document.getElementById('reply-form-' + commentId);
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}
</script>
    </body>
</html>
