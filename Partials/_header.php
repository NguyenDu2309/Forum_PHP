<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db_connection.php');

$user_image = "images/user.png"; // Default image
$username = "";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Query to get user image
    $sql = "SELECT user_image FROM users WHERE user_name = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['user_image'])) {
            $user_image = "Uploads/user_images/" . $row['user_image'];
        }
    }
}
?>

<nav class="bg-gray-800 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <!-- Brand -->
        <a href="/Forum_website/index.php" class="text-2xl font-bold hover:text-blue-400 transition">IT Forum</a>

        <!-- Toggler for mobile -->
        <button id="navbarToggler" class="md:hidden text-white focus:outline-none" aria-label="Toggle navigation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Navbar Links and Forms -->
        <div id="navbarContent" class="hidden md:flex md:items-center md:space-x-6 w-full md:w-auto">
            <!-- Navigation Links -->
            <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                <a href="/Forum_website/index.php" class="py-2 px-3 text-white hover:text-blue-400 transition font-medium">Trang chủ</a>
                <div class="relative group">
                    <button class="py-2 px-3 text-white hover:text-blue-400 transition font-medium flex items-center">
                        Danh mục
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden group-hover:block bg-white text-gray-800 rounded-lg shadow-lg mt-1 z-10 w-48">
                        <?php
                        $sql2 = "SELECT * FROM `category` LIMIT 6";
                        $result = mysqli_query($conn, $sql2);
                        if ($result) {
                            while ($fetch = mysqli_fetch_assoc($result)) {
                                $title = $fetch["category_name"];
                                $id = $fetch["category_id"];
                                echo '<a href="/Forum_website/thread_list.php?id=' . $id . '" class="block px-4 py-2 hover:bg-gray-100">' . $title . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <a href="contact_form.php" class="py-2 px-3 text-white hover:text-blue-400 transition font-medium">Liên hệ</a>
            </div>

            <!-- Search Form and User Actions -->
            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                <!-- Search Form -->
                <form action="/Forum_website/search.php" method="GET" class="flex items-center">
                    <input type="search" name="query" placeholder="Tìm kiếm chủ đề..." required class="bg-gray-700 text-white rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-48 md:w-64">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition">Tìm kiếm</button>
                </form>

                <?php if (isset($_SESSION['username'])): ?>
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2">
                        <img src="<?= $user_image ?>" alt="User Image" class="w-10 h-10 rounded-full object-cover">
                        <span class="text-blue-400 font-medium"><?= substr($username, 0, 7) ?></span>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button id="profileDropdown" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition flex items-center">
                            Hồ sơ
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="profileDropdownMenu" class="absolute hidden right-0 bg-white text-gray-800 rounded-lg shadow-lg mt-1 z-10 w-48">
                            <a href="/Forum_website/user/user_profile.php" class="block px-4 py-2 hover:bg-gray-100">Xem hồ sơ</a>
                            <a href="Partials/_handle_logout.php" class="block px-4 py-2 hover:bg-gray-100">Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login and Signup Buttons -->
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <button id="loginDropdown" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition flex items-center">
                                Đăng nhập
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="loginDropdownMenu" class="absolute hidden right-0 bg-white text-gray-800 rounded-lg shadow-lg mt-1 z-10 w-48">
                                <a href="#" data-modal-target="#loginModal" class="block px-4 py-2 hover:bg-gray-100">Đăng nhập người dùng</a>
                                <a href="#" data-modal-target="#adminLoginModal" class="block px-4 py-2 hover:bg-gray-100">Đăng nhập quản trị</a>
                            </div>
                        </div>
                        <button data-modal-target="#signupModal" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Đăng ký</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
// Toggle navbar on mobile
document.getElementById('navbarToggler').addEventListener('click', function () {
    const navbarContent = document.getElementById('navbarContent');
    navbarContent.classList.toggle('hidden');
});

// Handle dropdowns (click for both desktop and mobile)
document.querySelectorAll('.relative').forEach(dropdown => {
    const button = dropdown.querySelector('button');
    const menu = dropdown.querySelector('div');
    
    button.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
        if (!dropdown.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
});

// Handle modal toggling
document.querySelectorAll('[data-modal-target]').forEach(button => {
    button.addEventListener('click', () => {
        const target = button.getAttribute('data-modal-target');
        const modal = document.querySelector(target);
        if (modal) {
            modal.classList.remove('hidden');
        }
    });
});

// Handle modal closing
document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
    button.addEventListener('click', () => {
        const modal = button.closest('[id="loginModal"], [id="signupModal"], [id="adminLoginModal"]');
        if (modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>