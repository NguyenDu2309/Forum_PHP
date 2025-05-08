<?php
require_once '../app/controllers/AdminController.php';
require_once '../app/controllers/PostController.php';

$adminController = new AdminController();
$postController = new PostController();

// Get search term if any
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get posts based on search
if (!empty($search)) {
    $posts = $adminController->searchPosts($search);
} else {
    $posts = $postController->getAllPosts();
}
?>


    <?php include '../app/views/components/header.php'; ?>
    
    <div class="container">
        <h1 class="h2 mb-4 mt-3">Manage Posts</h1>
        
        <!-- Include alerts component -->
        <?php include '../app/views/components/alerts.php'; ?> 
        
        <!-- Search Controls -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="posts.php" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="searchInput" class="mr-2">Search:</label>
                        <input type="text" id="searchInput" name="search" class="form-control" 
                               placeholder="Search in title or content" value="<?= htmlspecialchars($search); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary mr-2">Search</button>
                    
                    <?php if (!empty($search)): ?>
                        <a href="posts.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <?php if (!empty($search)): ?>
                        Search Results: <?= count($posts) ?> post(s) found
                    <?php else: ?>
                        Total Posts: <?= count($posts) ?>
                    <?php endif; ?>
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Module</th>
                                <th>Content Preview</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($posts)): ?>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td><?= $post['post_id']; ?></td>
                                        <td><?= $post['title']; ?></td>
                                        <td><?= $post['username']; ?></td>
                                        <td><?= $post['module_name']; ?></td>
                                        <td class="post-content"><?= strip_tags(substr($post["content"], 0, 50)) . (strlen($post["content"]) > 50 ? '...' : ''); ?></td>
                                        <td><?= date('M j, Y', strtotime($post['created_at'])); ?></td>
                                        <td>
                                            <a href="/student_forum/app/views/post/post_detail.php?id=<?= $post['post_id']; ?>" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#deletePostModal<?= $post['post_id']; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            
                                            <!-- Delete Post Modal -->
                                            <div class="modal fade" id="deletePostModal<?= $post['post_id']; ?>" 
                                                 tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Post</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete this post?</p>
                                                            <p><strong>Title:</strong> <?= $post['title']; ?></p>
                                                            <p><strong>Author:</strong> <?= $post['username']; ?></p>
                                                            <p class="text-danger">This action cannot be undone. All comments on this post will also be deleted.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <form action="/student_forum/app/controllers/AdminController.php?action=deletePost" method="post">
                                                                <input type="hidden" name="post_id" value="<?= $post['post_id']; ?>">
                                                                <button type="submit" class="btn btn-danger">Delete Post</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No posts found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
    
    <?php include '../app/views/components/footer.php'; ?>
