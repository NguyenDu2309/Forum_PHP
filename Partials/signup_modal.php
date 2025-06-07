<?php
echo '
<div class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50 p-2" id="signupModal" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="bg-white rounded w-full max-w-xs mx-auto max-h-[75vh] overflow-y-auto">
        <div class="flex justify-between items-center p-2 border-b border-gray-200 sticky top-0 bg-white rounded-t">
            <h5 class="text-sm font-bold text-gray-800" id="signupModalLabel">Đăng ký IT Forum</h5>
            <button type="button" class="text-gray-600 hover:text-gray-800" data-bs-dismiss="modal" aria-label="Close">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-2">
            <h3 class="text-sm font-semibold text-center mb-2 p-1 bg-red-500 text-white rounded">Đăng ký</h3>
            <form action="Partials/_handle_signup.php" method="POST" enctype="multipart/form-data">
                <div class="mb-2">
                    <label for="username" class="block text-xs text-gray-700"><strong>Tên đăng nhập</strong></label>
                    <input type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" maxlength="15" id="username" name="username" placeholder="Tên đăng nhập" required>
                </div>
                <div class="mb-2">
                    <label for="email" class="block text-xs text-gray-700"><strong>Email</strong></label>
                    <input type="email" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" id="email" name="email" placeholder="Email" required>
                    <div class="text-xs text-red-500">Điền cẩn thận</div>
                </div>
                <div class="mb-2">
                    <label for="password" class="block text-xs text-gray-700"><strong>Mật khẩu</strong></label>
                    <input type="password" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" maxlength="20" id="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <div class="mb-2">
                    <label for="cpassword" class="block text-xs text-gray-700"><strong>Xác nhận</strong></label>
                    <input type="password" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" maxlength="20" id="cpassword" name="cpassword" placeholder="Nhập lại" required>
                </div>
                <div class="mb-2">
                    <label for="user_image" class="block text-xs text-gray-700"><strong>Ảnh</strong></label>
                    <input type="file" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none" id="user_image" name="user_image" accept="image/*">
                </div>
                <div class="flex gap-1">
                    <button type="submit" class="flex-1 bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 text-xs font-medium">Đăng ký</button>
                    <button type="button" class="bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600 text-xs" data-bs-dismiss="modal">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>
';
?>