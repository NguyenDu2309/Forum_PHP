<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Navigation bar -->
<?php include '../app/views/components/header.php'; ?>
    
<!-- New discussion modal -->
<?php include '../app/views/post/new_post.php' ?>
    
<div class="container">
    <div class="main-body p-0"> 
        <div class="inner-wrapper">
            <!-- Include sidebar component -->
            <?php include '../app/views/components/sidebar.php'; ?>
            
            <!-- Inner main -->
            <div class="inner-main">
                <!-- Include main header component -->
                <?php include '../app/views/components/main_header.php'; ?>

                <!-- Include alerts component -->
                <?php include '../app/views/components/alerts.php'; ?>

                <!-- Inner main body -->
                <div class="inner-main-body p-2 p-sm-3 forum-content fade-in">
                    <?php
                    if (!isset($postController)) {
                        require_once __DIR__ . '/../app/controllers/PostController.php';
                        $postController = new PostController();
                    }

                    // Get search term from URL parameter (if exists)
                    $searchTerm = '';
                    if(isset($_GET['search'])) {
                        $searchTerm = trim($_GET['search']);
                    }
                    
                    // Check if user is searching
                    if (!empty($searchTerm)) {
                        // Get filtered posts matching the search term
                        $postResults = $postController->searchPosts($searchTerm);
                        
                        // Show search results information
                        echo '<div class="mb-3 p-3 bg-light rounded">';
                        echo '<strong><i class="fas fa-search mr-1"></i> Search Results:</strong> ' . 
                             count($postResults) . ' discussion(s) found for "' . htmlspecialchars($searchTerm) . '"';
                        echo ' <a href="index.php" class="btn btn-sm btn-outline-secondary ml-2">
                                <i class="fas fa-times mr-1"></i>Clear
                               </a>';
                        echo '</div>';
                        
                        // Use the search results for display
                        $posts = $postResults;
                    } else {
                        // No search, get all posts
                        $posts = $postController->getAllPosts();
                    }

                    // Display posts
                    if (!empty($posts)) {
                        foreach ($posts as $post) {
                            include '../app/views/post/post_item.php';
                        }
                    } elseif (empty($searchTerm)) { // Only show "No discussions" if not searching
                        echo '<div class="text-center p-5 bg-white rounded shadow-sm">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <h5>No discussions found</h5>
                                <p class="text-muted">Be the first to start a discussion!</p>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#questionModal">
                                    <i class="fas fa-plus-circle mr-1"></i> Create Discussion
                                </button>
                              </div>';
                    }
                    ?>
                </div>
                <!-- /Inner main body -->
            </div>
            <!-- /Inner main -->
        </div>
    </div>
</div>

<?php include '../app/views/components/footer.php'; ?>
