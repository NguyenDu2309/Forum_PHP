<?php
echo '
<!-- Admin Login Modal -->
<div class="modal fade" id="adminLoginModal" tabindex="-1" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminLoginModalLabel">Quản trị viên đăng nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Admin Login Form -->
                <h3 class="text-center mb-4 p-2 bg-danger text-white rounded">Đăng nhập trang quản trị</h3>
                <form action="Partials/_handle_admin_login.php" method="POST">
                    <div class="mb-3">
                        <label for="adminUsername" class="form-label"><strong>Tên đăng nhập</strong></label>
                        <input type="text" class="form-control" id="adminUsername" name="adminUsername" placeholder="Nhập tên đăng nhập" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminPassword" class="form-label"><strong>Mật khẩu</strong></label>
                        <input type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="Nhập mật khẩu" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>
</div>
'
?>