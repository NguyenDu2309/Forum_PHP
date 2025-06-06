<?php
echo '
<div class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50" id="loginModal" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h5 class="text-xl font-bold text-gray-800" id="loginModalLabel">Đăng nhập vào IT Forum</h5>
            <button type="button" class="text-gray-600 hover:text-gray-800" data-bs-dismiss="modal" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <h3 class="text-lg font-semibold text-center mb-4 p-2 bg-red-500 text-white rounded-lg">Đăng nhập tại đây</h3>
            <form class="w-full max-w-xs mx-auto" action="Partials/_handle_login.php" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700"><strong>Tên đăng nhập</strong></label>
                    <input type="text" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="15" id="username" name="username" placeholder="Nhập tên đăng nhập">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700"><strong>Mật khẩu</strong></label>
                    <input type="password" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="20" id="password" name="password" placeholder="Nhập mật khẩu">
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Đăng nhập</button>
            </form>
        </div>
        <div class="flex justify-between items-center p-4 border-t border-gray-200">
            <a href="/Forum_website/Forget_credentials/_forget_credentials.php" class="text-blue-500 hover:text-blue-600 font-medium"><strong>Quên mật khẩu?</strong></a>
            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
';
?>