<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: /student_forum/public/index.php");
    exit;
}

require_once __DIR__ . '/../app/models/Post.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Module.php';

$postModel = new Post();
$userModel = new User();
$moduleModel = new Module();

// Get unassigned posts (posts without user or module)
$unassignedPosts = $postModel->getUnassignedPosts();

// Handle post assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["assign_post"])) {
    $post_id = $_POST["post_id"];
    $user_id = $_POST["user_id"];
    $module_id = $_POST["module_id"];
    
    if ($postModel->assignPost($post_id, $user_id, $module_id)) {
        $_SESSION["success_message"] = "Post assigned successfully!";
    } else {
        $_SESSION["error_message"] = "Failed to assign post.";
    }
    
    header("Location: /student_forum/public/assign_user.php");
    exit;
}

// Get all users and modules for dropdown
$users = $userModel->getAllUsers();
$modules = $moduleModel->getAllModules();

// Include header
include_once __DIR__ . '/../app/views/components/header.php';
?>

<div class="container mt-4">
    <h2>Assign Posts to Users and Modules</h2>
    
    <?php if (isset($_SESSION["success_message"])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION["success_message"]; 
                unset($_SESSION["success_message"]);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION["error_message"])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION["error_message"]; 
                unset($_SESSION["error_message"]);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($unassignedPosts)): ?>
        <div class="alert alert-info">There are no unassigned posts at the moment.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Date</th>
                        <th>Assign To</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unassignedPosts as $post): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post["title"]); ?></td>
                            <td><?php echo substr(htmlspecialchars($post["content"]), 0, 100) . '...'; ?></td>
                            <td><?php echo date('M d, Y', strtotime($post["created_at"])); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?php echo $post["post_id"]; ?>">
                                    
                                    <div class="form-group">
                                        <select name="user_id" class="form-control mb-2" required>
                                            <option value="">Select User</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?php echo $user["user_id"]; ?>">
                                                    <?php echo htmlspecialchars($user["username"]); ?> (<?php echo $user["email"]; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <select name="module_id" class="form-control mb-2" required>
                                            <option value="">Select Module</option>
                                            <?php foreach ($modules as $module): ?>
                                                <option value="<?php echo $module["module_id"]; ?>">
                                                    <?php echo htmlspecialchars($module["module_name"]); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" name="assign_post" class="btn btn-primary">Assign</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
</div>

<?php include_once __DIR__ . '/../app/views/components/footer.php'; ?>
