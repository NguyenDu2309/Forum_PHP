<?php
    echo '
        <!-- Modal -->
        <div class="modal fade" id="signupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Đăng ký IT Forum</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body align-item-center">
                        <h3 class="text-center mb-4 p-2 bg-danger rounded">Đăng ký tại đây</h3>
                        <form class="w-50 mx-auto" action="Partials/_handle_signup.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong>Tên đăng nhập </strong></label>
                                <input type="text" class="form-control" maxlength="15" id="username" aria-describedby="emailHelp" name="username" placeholder="Nhập tên đăng nhập" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><strong>Email</strong></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
                                <div id="emailHelp" class="form-text text-danger"><strong>Vui lòng điền thông tin này một cách cẩn thận vì nó sẽ được sử dụng cho những thông tin liên lạc quan trọng.</strong></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><strong>Mật khẩu</strong></label>
                                <input type="password" class="form-control" maxlength="20" id="password" name="password" placeholder="Tạo mật khẩu mạnh" required>
                            </div>
                            <div class="mb-3">
                                <label for="cpassword" class="form-label"><strong>Xác nhận mật khẩu </strong></label>
                                <input type="password" class="form-control" maxlength="20" id="cpassword" name="cpassword" placeholder="Xác nhận mật khẩu của bạn" required>
                                <div id="emailHelp" class="form-text"><strong>Vui lòng nhập mật khẩu xác nhận giống như mật khẩu </strong></div>
                            </div>
                            <div class="mb-3">
                                <label for="user_image" class="form-label"><strong>Ảnh đại diện</strong></label>
                                <input type="file" id="user_image" name="user_image" accept="image/*">
                                <div class="form-text">Tải lên ảnh đại diện (tùy chọn).</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    ';
?>
