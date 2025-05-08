<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION["post_error"] = "You have to login to edit your question.";       
    header('Location: /student_forum/app/views/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/PostController.php';

$database = new Database();
$pdo = $database->connect();

// Check if post ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /student_forum/public/my_question.php');
    exit;
}

// Get post details
$post_id = $_GET['id'];
$postController = new PostController();
$post = $postController->getPostById($post_id);

// Verify that the post exists and belongs to the current user
if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    header('Location: /student_forum/public/my_question.php');
    exit;
}

// Include header
include '../components/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Edit Discussion</h5>
                </div>
                <div class="card-body">
                    <!-- Display alerts -->
                    <?php include '../components/alerts.php'; ?> 
                     <!-- Display alerts -->

                    <form action="/student_forum/app/controllers/PostController.php?action=update_post" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="post_id" value="<?= $post_id ?>">
                        
                        <!-- Title Input -->
                        <div class="form-group">
                            <label for="questionTitle">Title</label>
                            <input type="text" class="form-control" id="questionTitle" name="title" 
                                   value="<?= $post['title'] ?>" required />
                        </div>

                        <!-- Module Selection -->
                        <div class="form-group">
                            <label for="moduleSelect">Select Module</label>
                            <select class="form-control" id="moduleSelect" name="module_id" required>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM modules");
                                while ($module = $stmt->fetch()) {
                                    $selected = ($module["module_id"] == $post["module_id"]) ? 'selected' : '';
                                    echo "<option value='{$module["module_id"]}' $selected>{$module["module_name"]}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Content Input -->
                        <div class="form-group">
                            <label for="questionContent">Content</label>
                            <textarea class="form-control" id="questionContent" name="content" 
                                      rows="6" required><?= $post['content'] ?></textarea>
                        </div>

                        <!-- Current Image -->
                        <?php if (!empty($post["image"])): ?>
                            <div class="form-group">
                                <label>Current Image</label>
                                <div>
                                    <img src="/student_forum/<?= $post["image"]; ?>" 
                                         class="img-fluid rounded" style="max-height: 200px;" alt="Post Image">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="removeImage" name="remove_image" value="1">
                                    <label class="form-check-label" for="removeImage">Remove image</label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- File Upload -->
                        <div class="form-group">
                            <label for="customFile">New Attachment (optional)</label>
                            <input type="file" class="form-control-file" id="customFile" name="image">
                            <small class="form-text text-muted">Leave empty to keep the current image (if any)</small>
                        </div>

                        <div class="form-group mt-4">
                            <a href="/student_forum/public/my_question.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?> 