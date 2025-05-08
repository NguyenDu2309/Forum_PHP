<div class="card forum-card mb-3">
    <div class="card-body p-3">
        <div class="d-flex flex-column">
            <!-- Post Title -->
            <h5 class="mb-2">
                <a href="/student_forum/app/views/post/post_detail.php?id=<?= $post['post_id']; ?>" class="text-primary font-weight-bold">
                    <?= $post["title"]; ?>
                </a>
            </h5>
            
            <!-- Post Content Preview -->
            <p class="text-secondary mb-2">
                <?= strip_tags(substr($post["content"], 0, 100)) . (strlen($post["content"]) > 100 ? '...' : ''); ?>
            </p>
            
            <!-- Post Image Preview (if exists) -->
            <?php if (!empty($post["image"])): ?>
                <div class="post-image-preview mb-2">
                    <a href="/student_forum/app/views/post/post_detail.php?id=<?= $post['post_id']; ?>">
                        <img src="/student_forum/<?= $post["image"]; ?>" alt="Post image" style="max-height: 150px; width: auto;">
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- Post Metadata -->
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="post-meta text-muted small">
                    <span data-toggle="tooltip" title="Author">
                        <i class="fas fa-user-circle mr-1"></i> <?= htmlspecialchars($post["username"]); ?>
                    </span>
                    <span class="mx-1">•</span>
                    <span data-toggle="tooltip" title="Module">
                        <i class="fas fa-folder mr-1"></i> <?= htmlspecialchars($post["module_name"]); ?>
                    </span>
                    <span class="mx-1">•</span>
                    <span data-toggle="tooltip" title="Posted date">
                        <i class="far fa-calendar-alt mr-1"></i> <?= date('M j, Y', strtotime($post["created_at"])); ?>
                    </span>
                    
                    <?php
                    // Get comment count
                    require_once __DIR__ . '/../../controllers/CommentController.php';
                    $commentController = new CommentController();
                    $commentCount = $commentController->countCommentsByPostId($post['post_id']);
                    ?>
                    <span class="mx-1">•</span>
                    <span data-toggle="tooltip" title="Comments">
                        <i class="far fa-comment-alt mr-1"></i> <?= $commentCount ?> comments
                    </span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-end mt-3">
                <a href="/student_forum/app/views/post/post_detail.php?id=<?= $post['post_id']; ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-book-reader mr-1"></i> Read more
                </a>

                <?php if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $post["user_id"]) : ?>
                    <a href="/student_forum/app/views/post/edit_post.php?id=<?= $post['post_id']; ?>" class="btn btn-sm btn-outline-primary ml-2">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    
                    <form method="POST" action="/student_forum/app/controllers/PostController.php?action=delete_post" class="d-inline ml-2">
                        <input type="hidden" name="post_id" value="<?= $post["post_id"]; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                onclick="return confirm('Are you sure you want to delete this post?')">
                            <i class="fas fa-trash-alt mr-1"></i> Delete
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>