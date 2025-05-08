<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/User.php';

class PostController {
    private $postModel;

    public function __construct() {
        $this->postModel = new Post();
    }

    public function create_post() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!isset($_SESSION["user_id"])) {
                $_SESSION["login_error"] = "You have to login first to post a question.";
                header("Location: /student_forum/app/views/auth/login.php");
                exit;
            }

            $user_id = $_SESSION["user_id"];
            $title = htmlspecialchars(trim($_POST["title"]));
            $content = htmlspecialchars(trim($_POST["content"]));
            $module_id = $_POST["module_id"];
            $image_path = null;

            // Verify user exists
            $userModel = new User();
            if (!$userModel->getUserById($user_id)) {
                $_SESSION["post_error"] = "User not found. Please login again.";
                header("Location: /student_forum/app/views/auth/login.php");
                exit;
            }

            // Validate inputs
            if (empty($title) || empty($content) || empty($module_id)) {
                $_SESSION["post_error"] = "All fields are required.";
                header("Location: /student_forum/public/index.php");
                exit;
            }

            // Handle Image Upload
            if (!empty($_FILES["image"]["name"])) {
                // Validate file type
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
                $file_type = $_FILES["image"]["type"];
                
                if (!in_array($file_type, $allowed_types)) {
                    $_SESSION["post_error"] = "Only JPG, JPEG, and PNG files are allowed.";
                    header("Location: /student_forum/public/index.php");
                    exit;
                }
                
                $target_dir = "../../public/uploads/";
                $image_name = time() . "_" . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $image_name;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = "public/uploads/" . $image_name;
                }
            }

            // Call the model to create post
            if ($this->postModel->createPost($user_id, $module_id, $title, $content, $image_path)) {
                header("Location: /student_forum/public/index.php");
                exit;
            } else {
                $_SESSION["post_error"] = "Error creating post.";
                header("Location: /student_forum/public/index.php");
                exit;
            }
        }
    }
    
    public function updatePost() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!isset($_SESSION["user_id"])) {
                $_SESSION["login_error"] = "You have to login first to edit a post.";
                header("Location: /student_forum/app/views/auth/login.php");
                exit;
            }

            $user_id = $_SESSION["user_id"];
            $post_id = $_POST["post_id"];
            $title = htmlspecialchars(trim($_POST["title"]));
            $content = htmlspecialchars(trim($_POST["content"]));
            $module_id = $_POST["module_id"];
            
            // Get current post data
            $current_post = $this->postModel->getPostById($post_id);
            
            // Validate that the post belongs to this user
            if (!$current_post || $current_post["user_id"] != $user_id) {
                $_SESSION["post_error"] = "You can only edit your own posts.";
                header("Location: /student_forum/public/my_question.php");
                exit;
            }
            
            // Validate inputs
            if (empty($title) || empty($content) || empty($module_id)) {
                $_SESSION["post_error"] = "All fields are required.";
                header("Location: /student_forum/app/views/post/edit_post.php?id=" . $post_id);
                exit;
            }

            // Initialize image path with current one
            $image_path = $current_post["image"];
            
            // Check if remove image is checked
            if (isset($_POST["remove_image"]) && $_POST["remove_image"] == 1) {
                // If physical file exists, delete it
                if (!empty($image_path) && file_exists("../../" . $image_path)) {
                    unlink("../../" . $image_path);
                }
                $image_path = null;
            }
            
            // Handle New Image Upload
            if (!empty($_FILES["image"]["name"])) {
                // Validate file type
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
                $file_type = $_FILES["image"]["type"];
                
                if (!in_array($file_type, $allowed_types)) {
                    $_SESSION["post_error"] = "Only JPG, JPEG, and PNG files are allowed.";
                    header("Location: /student_forum/app/views/post/edit_post.php?id=" . $post_id);
                    exit;
                }
                
                $target_dir = "../../public/uploads/";
                $image_name = time() . "_" . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $image_name;

                // If there's already an image and we're uploading a new one, delete the old one
                if (!empty($current_post["image"]) && file_exists("../../" . $current_post["image"])) {
                    unlink("../../" . $current_post["image"]);
                }

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = "public/uploads/" . $image_name;
                }
            }

            // Call model to update the post
            if ($this->postModel->updatePost($post_id, $title, $content, $module_id, $image_path, $user_id)) {
                header("Location: /student_forum/app/views/post/post_detail.php?id=" . $post_id);
                exit;
            } else {
                $_SESSION["post_error"] = "Error updating post.";
                header("Location: /student_forum/app/views/post/edit_post.php?id=" . $post_id);
                exit;
            }
        }
    }
    
    public function deletePost() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"])) {
            $post_id = $_POST["post_id"];
            $user_id = $_SESSION["user_id"];
    
            if ($this->postModel->deletePost($post_id, $user_id)) {
                header("Location: /student_forum/public/my_question.php");
                exit;
            } else {
                echo "Error deleting post.";
            }
        }
    }
    

    public function getAllPosts() {
        return $this->postModel->getAllPosts();
    }

    public function getPostById($post_id) {
        return $this->postModel->getPostById($post_id);
    }

    public function getUserPost() {
        return $this->postModel->getUserPost($_SESSION["user_id"]);
        
    }
    

    public function countAllPosts() {
        return $this->postModel->countAllPosts();
    }
    public function countAllPostsByUser($user_id) {
        return $this->postModel->countAllPostsByUser($user_id);
    }

    public function searchPosts($search) {
        // Strip HTML tags from the search term before passing to the model
        $plainSearch = strip_tags($search);
        return $this->postModel->searchPosts($plainSearch);
    }

}

$postController = new PostController();

if (isset($_GET["action"])) {
    if ($_GET["action"] == "create_post") {
        $postController->create_post();
    } else if ($_GET["action"] == "delete_post") {
        $postController->deletePost();
    } else if ($_GET["action"] == "update_post") {
        $postController->updatePost();
    }
}
?>
