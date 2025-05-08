<?php
require_once __DIR__ . '/../../controllers/PostController.php';
require_once __DIR__ . '/../../controllers/CommentController.php';

// Kiểm tra ID bài viết
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /student_forum/public/my_question.php');  
    exit();
}

$post_id = $_GET['id'];
$postController = new PostController();
$post = $postController->getPostById($post_id);

// Kiểm tra bài viết tồn tại
if (!$post) {
    header('Location: /student_forum/public/my_question.php');
    exit();
}

// Lấy comments
$commentController = new CommentController();
$comments = $commentController->getCommentsByPostId($post_id);

// Include header
include '../components/header.php';
?>

<style>
    .post-content img {
        width: 688px;
        height: 474px;
        object-fit: contain;
        max-width: 100%;
    }
</style>

<div class="container mt-4">
    <!-- Include alerts component -->
    <?php include '../components/alerts.php'; ?> 

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title"><?= $post["title"]; ?></h3>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <span class="text-muted">Posted by </span>
                            <a href="#" class="font-weight-bold"><?= $post["username"]; ?></a>
                            <span class="text-muted"> in </span>
                            <span class="badge badge-secondary"><?= $post["module_name"]; ?></span>
                            <span class="text-muted"> · <?= date('F j, Y, g:i a', strtotime($post["created_at"])); ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                        <div class="ml-auto">
                            <a href="/student_forum/app/views/post/edit_post.php?id=<?= $post_id ?>" class="btn btn-sm btn-outline-primary">Edit Post</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-content mb-4">
                        <p><?= nl2br($post["content"]); ?></p>
                        
                        <?php if (!empty($post["image"])): ?>
                            <div class="mt-3">
                                <img width="688" height="474" src="/student_forum/<?= $post["image"]; ?>" class="img-fluid rounded" alt="Post Image">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <!-- Comment Section -->
                    <div class="comments-section">
                        <h5 class="mb-3">Comments (<?= count($comments); ?>)</h5>
                        
                        <!-- Display comments -->
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment card mb-2" id="comment-<?= $comment['comment_id']; ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <?= $comment["username"]
                                                
                                                
                                                ; ?> 
                                                <small><?= date('M j, Y g:i a', strtotime($comment["created_at"])); ?></small>
                                            </h6>
                                            
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-primary edit-comment-btn" 
                                                            data-comment-id="<?= $comment['comment_id']; ?>">
                                                        Edit
                                                    </button>
                                                    <form action="/student_forum/app/controllers/CommentController.php?action=delete" method="post" class="d-inline">
                                                        <input type="hidden" name="comment_id" value="<?= $comment['comment_id']; ?>">
                                                        <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this comment?')">Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Phần nội dung comment -->
                                        <div id="comment-content-<?= $comment['comment_id']; ?>">
                                            <p class="card-text mt-2"><?= nl2br($comment["content"]); ?></p>
                                        </div>
                                        
                                        <!-- Form chỉnh sửa comment (ẩn mặc định) -->
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                            <div class="comment-edit-form" id="comment-edit-form-<?= $comment['comment_id']; ?>" style="display: none;">
                                                <form action="/student_forum/app/controllers/CommentController.php?action=edit" method="post">
                                                    <input type="hidden" name="comment_id" value="<?= $comment['comment_id']; ?>">
                                                    <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="content" rows="3" required><?= $comment['content'] ?></textarea>
                                                    </div>
                                                    <div class="mt-2">
                                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                        <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn" 
                                                                data-comment-id="<?= $comment['comment_id']; ?>">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                        <?php endif; ?>
                        
                        <!-- Comment Form -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="comment-form mt-4">
                                <h6>Add a Comment</h6>
                                <form action="/student_forum/app/controllers/CommentController.php?action=create" method="post">
                                    <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                                    <div class="form-group">
                                        <textarea class="form-control" name="content" rows="3" placeholder="Write your comment here..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Comment</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mt-4">
                                Please <a href="/student_forum/app/views/auth/login.php">login</a> to comment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="/student_forum/public/index.php  " class="btn btn-outline-secondary">&larr; Back to Discussions</a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle comment editing -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-comment-btn');
    const cancelButtons = document.querySelectorAll('.cancel-edit-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            
            document.getElementById(`comment-content-${commentId}`).style.display = 'none';
            
            document.getElementById(`comment-edit-form-${commentId}`).style.display = 'block';
        });
    });
    
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            
            document.getElementById(`comment-content-${commentId}`).style.display = 'block';
            
            document.getElementById(`comment-edit-form-${commentId}`).style.display = 'none';
        });
    });
});
</script>

<?php include '../components/footer.php'; ?>

