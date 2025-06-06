<?php
echo '
<div class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50" id="adminLoginModal" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h5 class="text-xl font-bold text-gray-800" id="adminLoginModalLabel">Quản trị viên đăng nhập</h5>
            <button type="button" class="text-gray-600 hover:text-gray-800" data-bs-dismiss="modal" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <h3 class="text-lg font-semibold text-center mb-4 p-2 bg-red-500 text-white rounded-lg">Đăng nhập trang quản trị</h3>
            <form class="w-full max-w-xs mx-auto" action="Partials/_handle_admin_login.php" method="POST">
                <div class="mb-4">
                    <label for="adminUsername" class="block text-sm font-medium text-gray-700"><strong>Tên đăng nhập</strong></label>
                    <input type="text" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="adminUsername" name="adminUsername" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="mb-4">
                    <label for="adminPassword" class="block text-sm font-medium text-gray-700"><strong>Mật khẩu</strong></label>
                    <input type="password" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="adminPassword" name="adminPassword" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Đăng nhập</button>
            </form>
        </div>
    </div>
</div>
';
?>