<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Comment.php';

class CommentController {
    private $commentModel;
    
    public function __construct() {
        $this->commentModel = new Comment();
    }
    
    public function getCommentsByPostId($post_id) {
        return $this->commentModel->getCommentsByPostId($post_id);
    }
    
    public function createComment() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // check if user login already
            if (!isset($_SESSION["user_id"])) {
                $_SESSION["error"] = "You must be logged in to comment.";
                header("Location: /student_forum/app/views/auth/login.php");
                exit;
            }
            
            $user_id = $_SESSION["user_id"];
            $post_id = $_POST["post_id"];
            $content = htmlspecialchars(trim($_POST["content"]));
            
            // Validate
            if (empty($content)) {
                $_SESSION["error"] = "Comment cannot be empty.";
                header("Location: /student_forum/app/views/post/post_detail.php?id=$post_id");
                exit;
            }
            
            // submit comment
            if ($this->commentModel->createComment($user_id, $post_id, $content)) {
                $_SESSION["success"] = "Comment added successfully.";
            } else {
                $_SESSION["error"] = "Failed to add comment.";
            }
            
            header("Location: /student_forum/app/views/post/post_detail.php?id=$post_id");
            exit;
        }
    }
    
    public function deleteComment() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // check if user logined
            if (!isset($_SESSION["user_id"])) {
                $_SESSION["error"] = "You must be logged in to delete a comment.";
                header("Location: /student_forum/app/views/auth/login.php");
                exit;
            }
            
            $user_id = $_SESSION["user_id"];
            $comment_id = $_POST["comment_id"];
            $post_id = $_POST["post_id"];
            
            // delete comment
            if ($this->commentModel->deleteComment($comment_id, $user_id)) {
                $_SESSION["success"] = "Comment deleted successfully.";
            } else {
                $_SESSION["error"] = "Failed to delete comment.";
            }
            
            header("Location: /student_forum/app/views/post/post_detail.php?id=$post_id");
            exit;
        }
    }
    
    public function countCommentsByPostId($post_id) {
        return $this->commentModel->countCommentsByPostId($post_id);
    }
    
    public function editComment() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Kiểm tra người dùng đã đăng nhập
            if (!isset($_SESSION["user_id"])) {
                $_SESSION["error"] = "You must be logged in to edit a comment.";
                header("Location: /student_forum/app/views/auth/login.php");
                exit;
            }
            
            $user_id = $_SESSION["user_id"];
            $comment_id = $_POST["comment_id"];
            $post_id = $_POST["post_id"];
            $content = htmlspecialchars(trim($_POST["content"]));
            
            // Validate
            if (empty($content)) {
                $_SESSION["error"] = "Comment cannot be empty.";
                header("Location: /student_forum/app/views/post/post_detail.php?id=$post_id");
                exit;
            }
            
            // comment
            if ($this->commentModel->updateComment($comment_id, $user_id, $content)) {
                $_SESSION["success"] = "Comment updated successfully.";
            } else {
                $_SESSION["error"] = "Failed to update comment.";
            }
            
            header("Location: /student_forum/app/views/post/post_detail.php?id=$post_id");
            exit;
        }
    }
}

$commentController = new CommentController();

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "create":
            $commentController->createComment();
            break;
        case "delete":
            $commentController->deleteComment();
            break;
        case "edit":
            $commentController->editComment();
            break;
    }
}
?>