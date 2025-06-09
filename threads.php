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
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    $user_id = $_SESSION['user_id'];
    // Chỉ cần kiểm tra có tồn tại dòng like
    $sql = "SELECT 1 FROM comment_likes WHERE comment_id = ? AND user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? 1 : 0;
}

function checkIfReplyLiked($reply_id) {
    global $conn;
    if (!isset($_SESSION['user_id'])) return 0;
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT 1 FROM reply_likes WHERE reply_id = ? AND user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reply_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? 1 : 0;
}

function getReplyLikes($reply_id) {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM reply_likes WHERE reply_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reply_id);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();
    return $total;
}

// Helper function to moderate content using AI
function moderateContent($content) {
    $payload = json_encode([
        "text" => $content,
        "prefix" => "hate-speech-detection"
    ]);

    $ch = curl_init('http://localhost:5000/analyze');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for valid response
    if ($response === false || $http_code !== 200) {
        return [
            'success' => false,
            'message' => "Không thể kết nối tới máy chủ kiểm duyệt AI."
        ];
    }

    $result = json_decode($response, true);
    $ai_output = strtoupper($result["result"] ?? "");

    // Check if content is offensive, hate speech, or toxic
    if (strpos($ai_output, "OFFENSIVE") !== false || strpos($ai_output, "HATE") !== false || strpos($ai_output, "TOXIC") !== false) {
        $reason = "";
        if (strpos($ai_output, "OFFENSIVE") !== false) {
            $reason = "Bình luận bị phát hiện là có nội dung <strong>xúc phạm</strong>.";
        } elseif (strpos($ai_output, "TOXIC") !== false) {
            $reason = "Bình luận bị phát hiện là có nội dung <strong>độc hại</strong>.";
        } elseif (strpos($ai_output, "HATE") !== false) {
            $reason = "Bình luận bị phát hiện là có nội dung <strong>thù ghét</strong>.";
        }
        
        return [
            'success' => false,
            'message' => $reason
        ];
    }

    return ['success' => true];
}

// Helper function to display nested replies recursively
function displayReplies($conn, $comment_id, $parent_reply_id = null, $depth = 0)
{
    // Get replies for the current level
    $reply_sql = "SELECT r.*, rp.user_name AS replied_to_user 
                 FROM replies r 
                 LEFT JOIN replies rp ON r.parent_reply_id = rp.reply_id 
                 WHERE r.comment_id = ? AND r.parent_reply_id ";
    $reply_sql .= $parent_reply_id === null ? "IS NULL" : "= ?";
    $reply_sql .= " ORDER BY r.reply_time ASC";
    
    $reply_stmt = $conn->prepare($reply_sql);
    
    if ($parent_reply_id === null) {
        $reply_stmt->bind_param("i", $comment_id);
    } else {
        $reply_stmt->bind_param("ii", $comment_id, $parent_reply_id);
    }
    
    $reply_stmt->execute();
    $replies = $reply_stmt->get_result();
    
    $output = '';
    while ($reply = $replies->fetch_assoc()) {
        $margin_left = ($depth * 20) + 20;
        $user_name = $reply['user_name'];
        $user_sql = "SELECT user_image FROM users WHERE user_name = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("s", $user_name);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_image = 'images/user.png';
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            if (!empty($user_row['user_image'])) {
                $user_image = "uploads/user_images/" . $user_row['user_image'];
            }
        }

        $output .= '<div class="reply-container" style="margin-left:' . $margin_left . 'px; margin-top:8px; background:#f8fafc; border-radius:0.5rem; padding:12px;">';
        $output .= '<div class="flex items-start">';
        $output .= '<img src="' . htmlspecialchars($user_image) . '" class="rounded-full mr-2" alt="User" style="width:30px; height:30px;">';
        $output .= '<div class="flex-1">';
        $output .= '<div class="flex justify-between items-center">';
        $output .= '<strong class="text-blue-600" style="font-size:0.95em;">' . htmlspecialchars($reply['user_name']) . '</strong>';
        // Đưa tim và nút reply ra ngoài cùng bên phải
        $output .= '<div class="flex items-center space-x-2 ml-2">';
        $replyLikes = getReplyLikes($reply['reply_id']);
        $replyLiked = checkIfReplyLiked($reply['reply_id']) ? 'liked' : '';
        $output .= '<p class="m-0" id="reply-likes-count-' . $reply['reply_id'] . '">' . $replyLikes . '</p>';
        $output .= '<button class="reply-like-btn ml-1" id="reply-like-' . $reply['reply_id'] . '" data-reply-id="' . $reply['reply_id'] . '" data-liked="' . ($replyLiked ? '1' : '0') . '">';
        $output .= '<i class="heart-icon ' . $replyLiked . ' fas fa-heart"></i>';
        $output .= '</button>';
        if (isset($_SESSION['username'])) {
            $output .= '<button class="reply-btn ml-2" type="button" title="Reply" onclick="toggleNestedReplyForm(' . $reply['reply_id'] . ',' . $comment_id . ')">';
            $output .= '<i class="fas fa-reply"></i>';
            $output .= '</button>';
        }
        $output .= '</div>'; // end flex items-center space-x-2
        $output .= '</div>'; // end flex justify-between

        // Nội dung reply
        $output .= '<p class="mb-1">' . htmlspecialchars($reply['reply_text']) . '</p>';
        $output .= '<small class="text-gray-500 italic" style="font-size:0.8em;">' . date('d-m-Y h:i A', strtotime($reply['reply_time'])) . '</small>';

        // Form trả lời lồng nhau
        if (isset($_SESSION['username'])) {
            $output .= '<form method="POST" class="mt-2" style="display:none;" id="nested-reply-form-' . $reply['reply_id'] . '">';
            $output .= '<div class="flex gap-2">';
            $output .= '<input type="text" name="nested_reply_text" class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Viết câu trả lời..." required>';
            $output .= '<input type="hidden" name="reply_comment_id" value="' . $comment_id . '">';
            $output .= '<input type="hidden" name="parent_reply_id" value="' . $reply['reply_id'] . '">';
            $output .= '<button type="submit" name="post_nested_reply" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">Trả lời</button>';
            $output .= '</div>';
            $output .= '</form>';
        }

        $output .= '</div>'; // flex-1
        $output .= '</div>'; // flex

        // Đệ quy hiển thị reply con
        $output .= displayReplies($conn, $comment_id, $reply['reply_id'], $depth + 1);

        $output .= '</div>'; // reply-container
    }
    return $output;
}

// Initialize $post variable
$post = null; // or false depending on if you want to use it for a default alert

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle main comments
    if (isset($_POST['comment']) && isset($_GET['id'])) {
        $threadID = $_GET['id'];
        $comment = $_POST['comment'];
        
        // AI Content Moderation for main comments
        $moderation_result = moderateContent($comment);
        
        if (!$moderation_result['success']) {
            $_SESSION['alert_fail'] = true;
            $_SESSION['fail_reason'] = $moderation_result['message'];
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page . "#discussion");
            exit();
        }
        
        $username = $_SESSION['username'];
        $emailID = $_SESSION['email_id'];
        //SQL query to insert new comments
        $image_path = null;
        if (isset($_FILES['comment_image']) && $_FILES['comment_image']['error'] == 0) {
            $image_name = uniqid() . '_' . basename($_FILES['comment_image']['name']);
            $target_dir = "uploads/comment_images/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed) && $_FILES['comment_image']['size'] <= 5*1024*1024) {
                move_uploaded_file($_FILES['comment_image']['tmp_name'], $target_file);
                $image_path = $target_file;
            }
        }
        $sql = "INSERT INTO `comments` (`comment`, `thread_comment_id`, `user_name`, `email_id`, `comment_time`, `comment_image`) VALUES (?, ?, ?, ?, current_timestamp(), ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $comment, $threadID, $username, $emailID, $image_path);
        $result2 = $stmt->execute();

        // check if the query successfully executed then set $result variable for further use
        if ($result2) {
            // set a variable in $_SESSION to show the success message and it will only be true once when user posts comment
             $_SESSION['alert_success'] = true;
             header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page . "#discussion");// this code will stop the reposting the comment in order to reload
             exit();  // Don't forget to call exit() after header to stop further code execution
        }
         // if the query is failed then echo this message and die
          else {
              // set a variable in $_SESSION to show the fail message and it will only be true once when user posts comment
             $_SESSION['alert_fail'] = true;
             $_SESSION['fail_reason'] = "Không thể đăng bình luận. Vui lòng thử lại sau.";
             header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page . "#discussion");// this code will stop the reposting the comment in order to reload
             exit();  // Don't forget to call exit() after header to stop further code execution
         }
    }
    
    // Handle regular replies
    if (isset($_POST['post_reply']) && isset($_POST['reply_text']) && isset($_POST['reply_comment_id'])) {
        $reply_text = trim($_POST['reply_text']);
        $reply_comment_id = intval($_POST['reply_comment_id']);
        $reply_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
        
        if ($reply_text !== '') {
            // AI Content Moderation for replies
            $moderation_result = moderateContent($reply_text);
            
            if (!$moderation_result['success']) {
                $_SESSION['reply_fail'] = true;
                $_SESSION['reply_fail_reason'] = $moderation_result['message'];
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page . "#discussion");
                exit();
            }
            
            $reply_sql = "INSERT INTO replies (comment_id, user_name, reply_text) VALUES (?, ?, ?)";
            $reply_stmt = $conn->prepare($reply_sql);
            $reply_stmt->bind_param("iss", $reply_comment_id, $reply_user, $reply_text);
            
            if ($reply_stmt->execute()) {
                $_SESSION['reply_success'] = true;
            } else {
                $_SESSION['reply_fail'] = true;
                $_SESSION['reply_fail_reason'] = "Không thể đăng phản hồi. Vui lòng thử lại sau.";
            }
            
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page . "#discussion");
            exit();
        }
    }
    
    // Handle nested replies
    if (isset($_POST['post_nested_reply']) && isset($_POST['nested_reply_text']) && isset($_POST['reply_comment_id']) && isset($_POST['parent_reply_id'])) {
        $reply_text = trim($_POST['nested_reply_text']);
        $reply_comment_id = intval($_POST['reply_comment_id']);
        $parent_reply_id = intval($_POST['parent_reply_id']);
        $reply_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
        
        if ($reply_text !== '') {
            // AI Content Moderation for nested replies
            $moderation_result = moderateContent($reply_text);
            
            if (!$moderation_result['success']) {
                $_SESSION['reply_fail'] = true;
                $_SESSION['reply_fail_reason'] = $moderation_result['message'];
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page . "#discussion");
                exit();
            }
            
            $nested_reply_sql = "INSERT INTO replies (comment_id, user_name, reply_text, parent_reply_id) VALUES (?, ?, ?, ?)";
            $nested_reply_stmt = $conn->prepare($nested_reply_sql);
            $nested_reply_stmt->bind_param("issi", $reply_comment_id, $reply_user, $reply_text, $parent_reply_id);
            
            if ($nested_reply_stmt->execute()) {
                $_SESSION['reply_success'] = true;
            } else {
                $_SESSION['reply_fail'] = true;
                $_SESSION['reply_fail_reason'] = "Không thể đăng phản hồi. Vui lòng thử lại sau.";
            }
            
            // Redirect to prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $thread_id . "&page=" . $page . "#discussion");
            exit();
        }
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="Partials/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/jpg" href="images/favicon1.jpg">
    <title>Bài đăng</title>
    <style>
      .heart-icon { color: gray; font-size: 24px; }
      .heart-icon.liked { color: red; }
      .comment-container { display: flex; flex-direction: column; justify-content: space-between; }
      .comment-text { margin-bottom: 10px; }
      .reply-container { border-left: 2px solid #e5e7eb; padding-left: 10px; }
      .reply-btn { color: #6b7280; transition: color 0.2s; }
      .reply-btn:hover { color: #2563eb; }
      .reply-like-btn { border: none; background: transparent; padding: 0; }
    </style>
  </head>
  <body class="bg-gray-50 min-h-screen flex flex-col">
  <!-- included the _header file where is my navbar  -->
    <?php  include"Partials/_header.php"?>
    <?php  include"Partials/db_connection.php"?>
    <?php  include"Partials/login_modal.php"?>
    <?php  include"Partials/signup_modal.php"?>

    <?php
      // Hiển thị chi tiết thread
      if(isset($_GET['id'])) {
          $threadsID = $_GET['id'];
          $sql = "SELECT * FROM `thread` WHERE thread_id = $threadsID";
          $result = mysqli_query($conn, $sql);
          if($result == true) {
              while ($fetch = mysqli_fetch_assoc($result)) {
                  echo '<div class="container mx-auto my-4 px-4">
                          <div class="bg-white rounded-lg shadow p-6">
                              '.(!empty($fetch['thread_image']) ? '<img src="uploads/thread_images/' . htmlspecialchars($fetch['thread_image']) . '" alt="Thread Image" class="mb-3 rounded-lg max-w-xs max-h-72">' : '').'
                              <h4 class="font-bold text-lg break-words mb-2"><span>🔹Q :- </span>' . htmlspecialchars($fetch['thread_title']) . '</h4>
                              <p class="mb-2 break-words whitespace-normal"><span>🔻 </span>' . htmlspecialchars($fetch['thread_desc']) . '</p>
                              <hr class="my-2">
                              '.(isset($_SESSION["username"]) ? '<p>Được đăng bởi: <span class="font-bold text-red-600">'.$fetch["thread_user_name"].'</span></p><hr class="my-2">' : '').'
                          </div>
                        </div>';
              }
          }
      }
    ?>

<!-- Display comments -->
<div class="container mx-auto my-3 px-4 max-h-[500px] overflow-y-auto" id="discussion">
    <h2 class="bg-red-500 text-white p-2 my-3 rounded text-lg font-semibold">Trao đổi</h2>

    <?php
    // Alerts
    if (isset($_SESSION['alert_success']) && $_SESSION['alert_success'] === true) {
        echo '<div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow z-50 flex items-center justify-between w-full max-w-md">
                <span class="font-semibold mr-2">✔Success!</span> Comment posted successfully.
                <button type="button" class="ml-4 text-green-700 hover:text-green-900" onclick="this.parentElement.style.display=\'none\'">&times;</button>
              </div>';
        unset($_SESSION['alert_success']);
    }
    elseif (isset($_SESSION['alert_fail']) && $_SESSION['alert_fail'] === true) {
        $reason = $_SESSION['fail_reason'] ?? 'Cannot post comment right now, try later.';
        echo '<div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 flex items-center justify-between w-full max-w-md">
                <span class="font-semibold mr-2">Error!</span> ' . $reason . '
                <button type="button" class="ml-4 text-red-700 hover:text-red-900" onclick="this.parentElement.style.display=\'none\'">&times;</button>
              </div>';
        unset($_SESSION['alert_fail']);
        unset($_SESSION['fail_reason']);
    }
    if (isset($_SESSION['reply_success']) && $_SESSION['reply_success'] === true) {
        echo '<div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow z-50 flex items-center justify-between w-full max-w-md">
                <span class="font-semibold mr-2">✔Success!</span> Reply posted successfully.
                <button type="button" class="ml-4 text-green-700 hover:text-green-900" onclick="this.parentElement.style.display=\'none\'">&times;</button>
              </div>';
        unset($_SESSION['reply_success']);
    }
    elseif (isset($_SESSION['reply_fail']) && $_SESSION['reply_fail'] === true) {
        $reason = $_SESSION['reply_fail_reason'] ?? 'Cannot post reply right now, try later.';
        echo '<div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 flex items-center justify-between w-full max-w-md">
                <span class="font-semibold mr-2">Error!</span> ' . $reason . '
                <button type="button" class="ml-4 text-red-700 hover:text-red-900" onclick="this.parentElement.style.display=\'none\'">&times;</button>
              </div>';
        unset($_SESSION['reply_fail']);
        unset($_SESSION['reply_fail_reason']);
    }
    ?>

    <?php
    // Loop through each comment
    while ($comment = $comments->fetch_assoc()):
        $user_name = $comment['user_name'];
        $user_sql = "SELECT user_image FROM users WHERE user_name = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("s", $user_name);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_image = 'images/user.png';
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            if (!empty($user_row['user_image'])) {
                $user_image = "uploads/user_images/" . $user_row['user_image'];
            }
        }
    ?>
        <div class="flex mb-3">
            <img src="<?php echo htmlspecialchars($user_image); ?>" class="rounded-full mr-3" alt="User" style="width:40px; height:40px;">
            <div class="flex-1 flex flex-col bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-start">
                    <h5 class="text-blue-600 font-semibold"><?php echo htmlspecialchars($comment['user_name']); ?></h5>
                    <div class="flex items-center mt-2">
                        <p class="m-0" id="likes-count-<?php echo $comment['comment_id']; ?>"><?php echo $comment['likes']; ?></p>
                        <button class="like-btn ml-2" 
                            id="like-<?php echo $comment['comment_id']; ?>" 
                            data-comment-id="<?php echo $comment['comment_id']; ?>" 
                            data-liked="<?php echo checkIfLiked($comment['comment_id']) ? '1' : '0'; ?>">
                            <i class="heart-icon <?php echo checkIfLiked($comment['comment_id']) ? 'liked' : ''; ?> fas fa-heart"></i>
                        </button>
                        <?php if(isset($_SESSION['username'])): ?>
                        <button class="reply-btn ml-3" type="button" title="Reply"
                            onclick="toggleReplyForm(<?php echo $comment['comment_id']; ?>)">
                            <i class="fas fa-reply"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="comment-container">
                    <p class="comment-text"><?php echo htmlspecialchars($comment['comment']); ?></p>
                    <?php if (!empty($comment['comment_image'])): ?>
    <div class="mt-2 flex justify-center">
        <img 
            src="<?= htmlspecialchars($comment['comment_image']) ?>" 
            alt="Comment Image" 
            class="rounded shadow object-cover cursor-zoom-in transition duration-200 hover:scale-105"
            style="max-width: 320px; max-height: 220px; width: auto; height: auto;"
            loading="lazy"
            onclick="showImageModal(this.src)"
        >
    </div>
<?php endif; ?>
                    <small class="text-gray-500 italic"><?php echo date('d-m-Y h:i A', strtotime($comment['comment_time'])); ?></small>
                </div>
                <?php if(isset($_SESSION['username'])): ?>
                <form method="POST" class="mt-2" style="display:none;" id="reply-form-<?php echo $comment['comment_id']; ?>">
                    <div class="flex gap-2">
                        <input type="text" name="reply_text" class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Viết câu trả lời..." required>
                        <input type="hidden" name="reply_comment_id" value="<?php echo $comment['comment_id']; ?>">
                        <button type="submit" name="post_reply" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">Gửi</button>
                    </div>
                </form>
                <?php endif; ?>
                <div class="replies-container mt-2">
                    <?php echo displayReplies($conn, $comment['comment_id']); ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Comment form -->
<div class="container mx-auto px-4">
  <hr>
  <?php if (isset($_SESSION['username'])): ?>
    <h2 class="text-lg font-semibold mb-2">Viết bình luận</h2>
    <form class="my-3" action="<?php echo $_SERVER['PHP_SELF'].'?id='.$_GET['id'].'&page='.$page; ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <textarea class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" style="height: 100px" name="comment" required></textarea>
        </div>
        <div class="mb-3">
          <input type="file" name="comment_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Đăng</button>
    </form>
  <?php else: ?>
    <h3 class="bg-green-500 text-white p-2 text-center rounded-full">Đăng nhập để bình luận</h3>
  <?php endif; ?>
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

<!-- Pagination -->
<nav aria-label="Comment pagination" class="my-4">
  <ul class="flex justify-center space-x-1">
    <?php if ($page > 1): ?>
      <li>
        <a class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300" href="?id=<?php echo $thread_id; ?>&page=<?php echo $page-1; ?>">Previous</a>
      </li>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <li>
        <a class="px-3 py-1 rounded <?php if ($i == $page) echo 'bg-blue-600 text-white'; else echo 'bg-gray-200 hover:bg-gray-300'; ?>" href="?id=<?php echo $thread_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
      </li>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
      <li>
        <a class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300" href="?id=<?php echo $thread_id; ?>&page=<?php echo $page+1; ?>">Next</a>
      </li>
    <?php endif; ?>
  </ul>
</nav>

<!-- Thêm modal phóng to ảnh vào cuối file trước </body> -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
    <span class="absolute top-4 right-8 text-white text-3xl cursor-pointer select-none" onclick="closeImageModal()">&times;</span>
    <img id="modal-img" src="" class="max-w-full max-h-[90vh] rounded shadow-lg border-4 border-white" alt="Zoomed Image">
</div>

<script>
function showImageModal(src) {
    document.getElementById('modal-img').src = src;
    document.getElementById('image-modal').classList.remove('hidden');
}
function closeImageModal() {
    document.getElementById('image-modal').classList.add('hidden');
    document.getElementById('modal-img').src = '';
}
// Đóng modal khi bấm nền đen
document.getElementById('image-modal').addEventListener('click', function(e) {
    if (e.target === this) closeImageModal();
});
</script>

<script>
    // Hide alerts after 3 seconds
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(() => {
            alert.remove();
        }, 3000);
    });

    // Toggle reply form for main comments
    function toggleReplyForm(commentId) {
        var form = document.getElementById('reply-form-' + commentId);
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
    // Toggle reply form for nested replies
    function toggleNestedReplyForm(replyId, commentId) {
        var form = document.getElementById('nested-reply-form-' + replyId);
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        // Like for comment
        document.querySelectorAll('.like-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const commentId = button.getAttribute('data-comment-id');
                const likesCountElement = document.getElementById(`likes-count-${commentId}`);
                const heartIcon = button.querySelector('.heart-icon');
                fetch('like_comment.php', {
                    method: 'POST',
                    body: new URLSearchParams({'comment_id': commentId}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(response => response.json())
                .then(function(data) {
                    if (data.success) {
                        button.setAttribute('data-liked', data.liked);
                        if (data.liked == 1) {
                            heartIcon.classList.add('liked');
                        } else {
                            heartIcon.classList.remove('liked');
                        }
                        likesCountElement.textContent = data.newLikes;
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
        // Like for reply
        document.querySelectorAll('.reply-like-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const replyId = button.getAttribute('data-reply-id');
                const likesCountElement = document.getElementById(`reply-likes-count-${replyId}`);
                const heartIcon = button.querySelector('.heart-icon');
                fetch('like_reply.php', {
                    method: 'POST',
                    body: new URLSearchParams({'reply_id': replyId}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(response => response.json())
                .then(function(data) {
                    if (data.success) {
                        button.setAttribute('data-liked', data.liked);
                        if (data.liked == 1) {
                            heartIcon.classList.add('liked');
                        } else {
                            heartIcon.classList.remove('liked');
                        }
                        likesCountElement.textContent = data.newLikes;
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
<?php include "Partials/_footer.php"  ?>
</body>
</html>