<?php
    echo '
        <!-- Modal -->
        <div class="modal fade" id="signupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Signup to iDiscuss</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body align-item-center">
                        <h3 class="text-center mb-4 p-2 bg-danger rounded">Please Sign Up Here</h3>
                        <form class="w-50 mx-auto" action="Partials/_handle_signup.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong>Username </strong></label>
                                <input type="text" class="form-control" maxlength="15" id="username" aria-describedby="emailHelp" name="username" placeholder="Enter your username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><strong>Email</strong></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                <div id="emailHelp" class="form-text text-danger"><strong>Please fill this carefully as it will be used for important communications.</strong></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><strong>Password</strong></label>
                                <input type="password" class="form-control" maxlength="20" id="password" name="password" placeholder="Create a strong password" required>
                            </div>
                            <div class="mb-3">
                                <label for="cpassword" class="form-label"><strong>Confirm Password </strong></label>
                                <input type="password" class="form-control" maxlength="20" id="cpassword" name="cpassword" placeholder="Confirm your password" required>
                                <div id="emailHelp" class="form-text"><strong>Please enter the confirm password same as password </strong></div>
                            </div>
                            <div class="mb-3">
                                <label for="user_image" class="form-label"><strong>Profile Image</strong></label>
                                <input type="file" id="user_image" name="user_image" accept="image/*">
                                <div class="form-text">Upload a profile image (optional).</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Signup</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    ';
?>
