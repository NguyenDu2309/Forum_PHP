<?php
echo '
<!-- Admin Login Modal -->
<div class="modal fade" id="adminLoginModal" tabindex="-1" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminLoginModalLabel">Admin Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Admin Login Form -->
                <h3 class="text-center mb-4 p-2 bg-danger text-white rounded">Login to Admin Panel</h3>
                <form action="Partials/_handle_admin_login.php" method="POST">
                    <div class="mb-3">
                        <label for="adminUsername" class="form-label"><strong>Username</strong></label>
                        <input type="text" class="form-control" id="adminUsername" name="adminUsername" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminPassword" class="form-label"><strong>Password</strong></label>
                        <input type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Log in</button>
                </form>
            </div>
        </div>
    </div>
</div>
'
?>