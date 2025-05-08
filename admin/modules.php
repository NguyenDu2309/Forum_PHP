<?php
require_once '../app/controllers/AdminController.php';
require_once '../app/models/Module.php';

$adminController = new AdminController();
$moduleModel = new Module();
$modules = $moduleModel->getAllModules();

?>


    <?php include '../app/views/components/header.php'; ?>
    
    <div class="container">
        <h1 class="h2 mb-4 mt-3">Manage Modules</h1>
        
        <!-- Include alerts component -->
        <?php include '../app/views/components/alerts.php'; ?> 
        
        <!-- Add New Module -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Module</h5>
            </div>
            <div class="card-body">
                <form action="/student_forum/app/controllers/AdminController.php?action=addModule" method="post">
                    <div class="form-group">
                        <label for="moduleName">Module Name</label>
                        <input type="text" class="form-control" id="moduleName" name="module_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Module</button>
                </form>
            </div>
        </div>
        
        <!-- Modules List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Modules</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Module Name</th>
                                <th>Post Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($modules)): ?>
                                <?php foreach ($modules as $module): ?>
                                    <tr>
                                        <td><?= $module['module_id']; ?></td>
                                        <td>
                                            <span id="module-name-<?= $module['module_id']; ?>"><?= $module['module_name']; ?></span>
                                            <form id="edit-form-<?= $module['module_id']; ?>" style="display: none;" 
                                                  action="/student_forum/app/controllers/AdminController.php?action=updateModule" method="post">
                                                <div class="input-group">
                                                    <input type="hidden" name="module_id" value="<?= $module['module_id']; ?>">
                                                    <input type="text" class="form-control" name="module_name" 
                                                           value="<?= $module['module_name']; ?>" required>
                                                    <div class="input-group-append">
                                                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                                                        <button type="button" class="btn btn-secondary btn-sm" 
                                                                onclick="toggleEditForm(<?= $module['module_id']; ?>, false)">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <?php 
                                            $post_count = $adminController->getPostCountByModule($module['module_id']);
                                            echo $post_count;
                                            ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="toggleEditForm(<?= $module['module_id']; ?>, true)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#deleteModuleModal<?= $module['module_id']; ?>"
                                                    >
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            
                                            <!-- Delete Module Modal -->
                                            <div class="modal fade" id="deleteModuleModal<?= $module['module_id']; ?>" 
                                                 tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Module</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete this module?</p>
                                                            <p><strong>Module Name:</strong> <?= $module['module_name']; ?></p>
                                                            <?php if ($post_count > 0): ?>
                                                                <div class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> 
                                                                    This module has <?= $post_count ?> posts associated with it. 
                                                                    You must delete or reassign those posts before deleting this module.
                                                                </div>
                                                            <?php else: ?>
                                                                <p class="text-danger">This action cannot be undone.</p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <form action="/student_forum/app/controllers/AdminController.php?action=deleteModule" method="post">
                                                                <input type="hidden" name="module_id" value="<?= $module['module_id']; ?>">
                                                                <button type="submit" class="btn btn-danger" >
                                                                    Delete Module
                                                                </button>
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
                                    <td colspan="4" class="text-center">No modules found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
    <?php include '../app/views/components/footer.php'; ?>

    
    <script>
    function toggleEditForm(moduleId, show) {
        var nameElement = document.getElementById('module-name-' + moduleId);
        var formElement = document.getElementById('edit-form-' + moduleId);
        
        if (show) {
            nameElement.style.display = 'none';
            formElement.style.display = 'block';
        } else {
            nameElement.style.display = 'inline';
            formElement.style.display = 'none';
        }
    }
    </script>
