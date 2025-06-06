<?php
echo '
<div class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50" id="signupModal" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h5 class="text-xl font-bold text-gray-800" id="signupModalLabel">Đăng ký IT Forum</h5>
            <button type="button" class="text-gray-600 hover:text-gray-800" data-bs-dismiss="modal" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <h3 class="text-lg font-semibold text-center mb-4 p-2 bg-red-500 text-white rounded-lg">Đăng ký tại đây</h3>
            <form class="w-full max-w-xs mx-auto" action="Partials/_handle_signup.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700"><strong>Tên đăng nhập</strong></label>
                    <input type="text" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="15" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700"><strong>Email</strong></label>
                    <input type="email" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="email" name="email" placeholder="Nhập email" required>
                    <div class="text-sm text-red-500 mt-1"><strong>Vui lòng điền thông tin này một cách cẩn thận vì nó sẽ được sử dụng cho những thông tin liên lạc quan trọng.</strong></div>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700"><strong>Mật khẩu</strong></label>
                    <input type="password" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="20" id="password" name="password" placeholder="Tạo mật khẩu mạnh" required>
                </div>
                <div class="mb-4">
                    <label for="cpassword" class="block text-sm font-medium text-gray-700"><strong>Xác nhận mật khẩu</strong></label>
                    <input type="password" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="20" id="cpassword" name="cpassword" placeholder="Xác nhận mật khẩu của bạn" required>
                    <div class="text-sm text-gray-600 mt-1"><strong>Vui lòng nhập mật khẩu xác nhận giống như mật khẩu</strong></div>
                </div>
                <div class="mb-4">
                    <label for="user_image" class="block text-sm font-medium text-gray-700"><strong>Ảnh đại diện</strong></label>
                    <input type="file" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="user_image" name="user_image" accept="image/*">
                    <div class="text-sm text-gray-600 mt-1">Tải lên ảnh đại diện (tùy chọn).</div>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Đăng ký</button>
                </form>
            </div>
            <div class="flex justify-end items-center p-4 border-t border-gray-200">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
';
?>