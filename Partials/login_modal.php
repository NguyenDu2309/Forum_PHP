<?php
echo '
                <div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Đăng nhập vào IT Forum</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    
                        <h3 class="text-center mb-4 p-2 bg-danger rounded">Đăng nhập tại đây</h3>
                        <form class="w-50 mx-auto" action="Partials/_handle_login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong> Tên đăng nhập </strong></label>
                                <input type="text" class="form-control" maxlength="15" id="username" aria-describedby="emailHelp" name="username" placeholder="Nhập tên đăng nhập">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><strong> Mật khẩu </strong></label>
                                <input type="password" class="form-control" maxlength="20" id="password" name="password" placeholder="Nhập mật khẩu">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                        </form>
                        </div>
                        <div class="modal-footer">
                        <a href="/Forum_website/Forget_credentials/_forget_credentials.php" class="text-primary me-auto"><strong>Quên mật khẩu?</strong></a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                    </div>
                </div>
                </div>
            ';
?>