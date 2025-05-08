<?php
include_once __DIR__ . '/../../../config/database.php'; 

$database = new Database();
$pdo = $database->connect();
?>

<!-- New Discussion Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <form action="../app/controllers/PostController.php?action=create_post" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="questionModalLabel">
                        <i class="fas fa-plus-circle mr-2"></i>Create New Discussion
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-4">
                    <!-- Title Input -->
                    <div class="form-group">
                        <label for="questionTitle"><i class="fas fa-heading mr-1"></i>Title</label>
                        <input type="text" class="form-control" id="questionTitle" name="title" 
                               placeholder="Enter a descriptive title for your discussion" required />
                        <small class="form-text text-muted">A good title helps others understand your question at a glance.</small>
                    </div>

                    <!-- Module Selection -->
                    <div class="form-group">
                        <label for="moduleSelect"><i class="fas fa-folder mr-1"></i>Select Module</label>
                        <select class="form-control" id="moduleSelect" name="module_id" required>
                            <option value="">Select a Module</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM modules ORDER BY module_name ASC");
                            while ($module = $stmt->fetch()) {
                                echo "<option value='{$module["module_id"]}'>{$module["module_name"]}</option>";
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted">Choose the module that best fits your discussion topic.</small>
                    </div>

                    <!-- Content Input -->
                    <div class="form-group">
                        <label for="questionContent"><i class="fas fa-file-alt mr-1"></i>Content</label>
                        <textarea class="form-control" id="questionContent" name="content" rows="6" 
                                  placeholder="Provide details, context, and any relevant information..." required></textarea>
                        <small class="form-text text-muted">Be clear and detailed to get the best responses.</small>
                    </div>

                    <!-- File Upload -->
                    <div class="form-group">
                        <label for="postImage"><i class="fas fa-image mr-1"></i>Add Image (Optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="postImage" name="image" accept="image/jpeg,image/jpg,image/png">
                            <label class="custom-file-label" for="postImage">Choose image...</label>
                        </div>
                        <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG (Max: 5MB)</small>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i>Post Discussion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Update file input label with filename when file is selected
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var label = e.target.nextElementSibling;
        label.innerHTML = fileName;
    });
});
</script>
