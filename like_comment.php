<?php
// like_comment.php (Simplified - Not Recommended for Production)
include('Partials/db_connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has liked the comment
    $sql = "SELECT liked FROM comment_likes WHERE comment_id = $comment_id AND user_id = $user_id";
    $result = $conn->query($sql);
    
     // Initialize variables before the if statement
    $new_liked_status = 1;

    if ($result->num_rows > 0) {
         // If the user has liked the comment, toggle their like
        $row = $result->fetch_assoc();
        $current_liked_status = $row['liked'];
        $new_liked_status = $current_liked_status == 1 ? 0 : 1;

        // Update the like state in the comment_likes table
         $sql = "UPDATE comment_likes SET liked = $new_liked_status WHERE comment_id = $comment_id AND user_id = $user_id";
        $conn->query($sql);

    } else {
           // If the user has not liked the comment, then insert a new record into comment_likes
       $sql = "INSERT INTO comment_likes (comment_id, user_id, liked) VALUES ($comment_id, $user_id, 1)";
         $conn->query($sql);
        
    }
    
     // Calculate total likes for the comment from the comment_likes table
    $sql = "SELECT COUNT(*) as total_likes FROM comment_likes WHERE comment_id = $comment_id AND liked = 1";
    $like_result = $conn->query($sql);
    $new_likes = $like_result->fetch_assoc()['total_likes'];

    // Update the likes count in the comments table
    $sql = "UPDATE comments SET likes = $new_likes WHERE comment_id = $comment_id";
    $conn->query($sql);

    echo json_encode(['success' => true, 'newLikes' => $new_likes, 'liked' => $new_liked_status]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>