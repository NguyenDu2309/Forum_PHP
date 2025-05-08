<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Get module filter param if set
$module_id = isset($_GET['module_id']) ? $_GET['module_id'] : '';

// Get all modules from database
require_once __DIR__ . '/../../models/Module.php';
$moduleModel = new Module();
$modules = $moduleModel->getAllModules();
?>
<!-- Inner sidebar -->
<div class="inner-sidebar">
    <!-- Inner sidebar header -->
    <div class="inner-sidebar-header justify-content-center">
        <button class="btn btn-primary has-icon btn-block" type="button" data-toggle="modal" data-target="#questionModal">
            <i class="fas fa-plus-circle mr-2"></i>
            NEW DISCUSSION
        </button>
    </div>
    <!-- /Inner sidebar header -->

    <!-- Inner sidebar body -->
    <div class="inner-sidebar-body p-0">
        <div class="p-3">
            <nav class="nav nav-pills nav-gap-y-1 flex-column">
                <a href="/student_forum/public/index.php" class="nav-link nav-link-faded has-icon <?php echo ($current_page === 'index.php' && empty($_GET['module_id'])) ? 'active' : ''; ?>">
                    <i class="fas fa-home mr-2"></i>All Discussions
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/student_forum/public/my_question.php" class="nav-link nav-link-faded has-icon <?php echo ($current_page === 'my_question.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user-edit mr-2"></i>My Discussions
                </a>
                <?php endif; ?>
            </nav>
            
            <!-- Filter Section -->
            <div class="mt-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-filter mr-2"></i>Filter by Module
                </h6>
                
                <form action="index.php" method="GET" id="filterForm">
                    <?php if(isset($_GET['search'])): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                    <?php endif; ?>
                    
                    <!-- Module Filter -->
                    <div class="form-group">
                        <select class="form-control form-control-sm" id="module_id" name="module_id" onchange="this.form.submit()">
                            <option value="">All Modules</option>
                            <?php foreach($modules as $module): ?>
                                <option value="<?php echo $module['module_id']; ?>" <?php echo ($module_id == $module['module_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($module['module_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if(!empty($module_id)): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-block" onclick="window.location='index.php'">
                            <i class="fas fa-times mr-1"></i>Clear Filter
                        </button>
                    <?php endif; ?>
                </form>
            </div>
            <!-- /Filter Section -->
        </div>
    </div>
    <!-- /Inner sidebar body -->
</div>
<!-- /Inner sidebar --> 