<?php
session_start();
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Module.php';

class AdminController {
    private $userModel;
    private $postModel;
    private $moduleModel;
    
    public function __construct() {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "You don't have permission to access this page.";
            header("Location: /student_forum/public/index.php");
            exit;
        }
        
        $this->userModel = new User();
        $this->postModel = new Post();
        $this->moduleModel = new Module();
    }
    
    // User Management Methods
    public function getAllUsers() {
        return $this->userModel->getAllUsers();
    }
    
    // Post Management Methods
    public function getAllPosts() {
        return $this->postModel->getAllPosts(); 
    }
    
    public function getPostsByModule($module_id) {
        return $this->postModel->getPostsByModule($module_id);
    }
    
    public function searchPosts($search) {
        return $this->postModel->searchPosts($search);
    }
    
    public function deletePost() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"])) {
            $post_id = $_POST["post_id"];
            
            if ($this->postModel->deletePostFromAdmin($post_id)) {
                $_SESSION["admin_success"] = "Post deleted successfully.";
            } else {
                $_SESSION["admin_error"] = "Failed to delete post.";
            }
            
            header("Location: /student_forum/admin/posts.php");
            exit;
        }
    }
    
    public function deleteUser() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"])) {
            $user_id = $_POST["user_id"];
            
            // Don't allow deleting yourself
            if ($user_id == $_SESSION["user_id"]) {
                $_SESSION["admin_error"] = "You cannot delete your own account.";
                header("Location: /student_forum/admin/users.php");
                exit;
            }
            
            if ($this->userModel->deleteUser($user_id)) {
                $_SESSION["admin_success"] = "User deleted successfully.";
            } else {    
                $_SESSION["admin_error"] = "Failed to delete user.";
            }
            
            header("Location: /student_forum/admin/users.php");
            exit;
        }
    }
    
    public function updateUserRole() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"]) && isset($_POST["role"])) {
            $user_id = $_POST["user_id"];
            $role = $_POST["role"];
            
            // Don't allow changing your own role
            if ($user_id == $_SESSION["user_id"]) {
                $_SESSION["admin_error"] = "You cannot change your own role.";
                header("Location: /student_forum/admin/users.php");
                exit;
            }
            
            // Validate role
            if (!in_array($role, ['student', 'admin'])) {
                $_SESSION["admin_error"] = "Invalid role specified.";
                header("Location: /student_forum/admin/users.php");
                exit;
            }
            
            if ($this->userModel->updateUserRole($user_id, $role)) {
                $_SESSION["admin_success"] = "User role updated successfully.";
            } else {
                $_SESSION["admin_error"] = "Failed to update user role.";
            }
            
            header("Location: /student_forum/admin/users.php");
            exit;
        }
    }
    
    // Dashboard statistics
    public function getDashboardStats() {
        $stats = [
            'total_users' => $this->userModel->countUsers(),
            'total_posts' => $this->postModel->countAllPosts(),
            'total_modules' => $this->moduleModel->countModules()
        ];
        
        return $stats;
    }
    
    // Module Management Methods
    public function getAllModules() {
        return $this->moduleModel->getAllModules();
    }
    
    public function getPostCountByModule($module_id) {
        // Use the Post model to count posts in a module
        return $this->postModel->countPostsByModule($module_id);
    }
    
    public function addModule() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["module_name"])) {
            $module_name = trim($_POST["module_name"]);
            
            if (empty($module_name)) {
                $_SESSION["admin_error"] = "Module name cannot be empty.";
                header("Location: /student_forum/admin/modules.php");
                exit;
            }
            
            if ($this->moduleModel->createModule($module_name)) {
                $_SESSION["admin_success"] = "Module created successfully.";
            } else {
                $_SESSION["admin_error"] = "Failed to create module.";
            }
            
            header("Location: /student_forum/admin/modules.php");
            exit;
        }
    }
    
    public function updateModule() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["module_id"]) && isset($_POST["module_name"])) {
            $module_id = $_POST["module_id"];
            $module_name = trim($_POST["module_name"]);
            
            if (empty($module_name)) {
                $_SESSION["admin_error"] = "Module name cannot be empty.";
                header("Location: /student_forum/admin/modules.php");
                exit;
            }
            
            if ($this->moduleModel->updateModule($module_id, $module_name)) {
                $_SESSION["admin_success"] = "Module updated successfully.";
            } else {
                $_SESSION["admin_error"] = "Failed to update module.";
            }
            
            header("Location: /student_forum/admin/modules.php");
            exit;
        }
    }
    
    public function deleteModule() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["module_id"])) {
            $module_id = $_POST["module_id"];
            
            // Check if there are posts in this module
            // NO NEED THIS FEATURE
            
            if ($this->moduleModel->deleteModule($module_id)) {
                $_SESSION["admin_success"] = "Module deleted successfully.";
            } else {
                $_SESSION["admin_error"] = "Failed to delete module.";
            }
            
            header("Location: /student_forum/admin/modules.php");
            exit;
        }
    }
}

// Handle admin actions
$adminController = new AdminController();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteUser':
            $adminController->deleteUser();
            break;
        case 'updateUserRole':
            $adminController->updateUserRole();
            break;
        case 'deletePost':
            $adminController->deletePost();
            break;
        case 'addModule':
            $adminController->addModule();
            break;
        case 'updateModule':
            $adminController->updateModule();
            break;
        case 'deleteModule':
            $adminController->deleteModule();
            break;
        default:
            header("Location: /student_forum/admin/index.php");
            exit;
    }
}
?>