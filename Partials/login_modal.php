<?php
echo '
                <div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Login to iDiscuss</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    
                        <h3 class="text-center mb-4 p-2 bg-danger rounded">Please Log in Here</h3>
                        <form class="w-50 mx-auto" action="Partials/_handle_login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong> Username </strong></label>
                                <input type="text" class="form-control" maxlength="15" id="username" aria-describedby="emailHelp" name="username" placeholder="Enter your username">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><strong> Password </strong></label>
                                <input type="password" class="form-control" maxlength="20" id="password" name="password" placeholder="Enter your password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Log in</button>
                        </form>
                        </div>
                        <div class="modal-footer">
                        <a href="/Forum_website/Forget_credentials/_forget_credentials.php" class="text-primary me-auto"><strong>Forgot credentials?</strong></a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                    </div>
                </div>
                </div>
            ';
?>