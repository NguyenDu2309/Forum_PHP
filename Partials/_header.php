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
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Brand -->
            <a href="/Forum_website/index.php" class="text-xl md:text-2xl font-bold hover:text-blue-400 transition flex-shrink-0">IT Forum</a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-6 flex-grow justify-center">
                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <a href="/Forum_website/index.php" class="py-2 px-3 text-white hover:text-blue-400 transition font-medium whitespace-nowrap">Trang chủ</a>
                    
                    <div class="relative group">
                        <button class="py-2 px-3 text-white hover:text-blue-400 transition font-medium flex items-center whitespace-nowrap">
                            Danh mục
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute hidden group-hover:block bg-white text-gray-800 rounded-lg shadow-lg mt-1 z-50 w-48">
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
                    
                    <a href="contact_form.php" class="py-2 px-3 text-white hover:text-blue-400 transition font-medium whitespace-nowrap">Liên hệ</a>
                </div>

                <!-- Search Form -->
                <form action="/Forum_website/search.php" method="GET" class="flex items-center">
                    <input type="search" name="query" placeholder="Tìm kiếm..." required
                        class="bg-gray-700 text-white rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40 xl:w-56 text-sm">
                    <button type="submit" class="bg-blue-500 text-white px-3 py-2 rounded-r-lg hover:bg-blue-600 transition text-sm whitespace-nowrap">Tìm</button>
                </form>
            </div>

            <!-- User Actions (Desktop) -->
            <div class="hidden lg:flex items-center space-x-3 flex-shrink-0">
                <?php if (isset($_SESSION['username'])): ?>
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2">
                        <img src="<?= $user_image ?>" alt="User Image" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-blue-400 font-medium text-sm"><?= substr($username, 0, 8) ?></span>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button id="profileDropdown" class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition flex items-center text-sm">
                            Hồ sơ
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="profileDropdownMenu" class="absolute hidden right-0 bg-white text-gray-800 rounded-lg shadow-lg mt-1 z-50 w-48">
                            <a href="/Forum_website/user/user_profile.php" class="block px-4 py-2 hover:bg-gray-100">Xem hồ sơ</a>
                            <a href="Partials/_handle_logout.php" class="block px-4 py-2 hover:bg-gray-100">Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login and Signup Buttons -->
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <button id="loginDropdown" class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition flex items-center text-sm">
                                Đăng nhập
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="loginDropdownMenu" class="absolute hidden right-0 bg-white text-gray-800 rounded-lg shadow-lg mt-1 z-50 w-48">
                                <a href="#" data-modal-target="#loginModal" class="block px-4 py-2 hover:bg-gray-100">Đăng nhập người dùng</a>
                                <a href="#" data-modal-target="#adminLoginModal" class="block px-4 py-2 hover:bg-gray-100">Đăng nhập quản trị</a>
                            </div>
                        </div>
                        <button data-modal-target="#signupModal" class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition text-sm">Đăng ký</button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button id="navbarToggler" class="lg:hidden text-white focus:outline-none p-2" aria-label="Toggle navigation">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="navbarContent" class="lg:hidden hidden mt-4 pb-4 border-t border-gray-700">
            <div class="flex flex-col space-y-3 mt-4">
                <!-- Navigation Links -->
                <a href="/Forum_website/index.php" class="py-2 px-3 text-white hover:text-blue-400 transition font-medium rounded">Trang chủ</a>
                
                <div class="relative">
                    <button id="mobileCategoryBtn" class="w-full text-left py-2 px-3 text-white hover:text-blue-400 transition font-medium flex items-center justify-between rounded">
                        Danh mục
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="mobileCategoryMenu" class="hidden bg-gray-700 rounded-lg mt-2 ml-4">
                        <?php
                        $sql2 = "SELECT * FROM `category` LIMIT 6";
                        $result = mysqli_query($conn, $sql2);
                        if ($result) {
                            while ($fetch = mysqli_fetch_assoc($result)) {
                                $title = $fetch["category_name"];
                                $id = $fetch["category_id"];
                                echo '<a href="/Forum_website/thread_list.php?id=' . $id . '" class="block px-4 py-2 text-white hover:text-blue-400 transition">' . $title . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <a href="contact_form.php" class="py-2 px-3 text-white hover:text-blue-400 transition font-medium rounded">Liên hệ</a>

                <!-- Search Form -->
                <form action="/Forum_website/search.php" method="GET" class="flex items-center mt-3">
                    <input type="search" name="query" placeholder="Tìm kiếm chủ đề..." required
                        class="bg-gray-700 text-white rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 flex-grow">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition">Tìm</button>
                </form>

                <!-- User Actions -->
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="flex items-center space-x-3 pt-3 border-t border-gray-700">
                        <img src="<?= $user_image ?>" alt="User Image" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-grow">
                            <div class="text-blue-400 font-medium"><?= $username ?></div>
                            <div class="flex space-x-2 mt-2">
                                <a href="/Forum_website/user/user_profile.php" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">Hồ sơ</a>
                                <a href="Partials/_handle_logout.php" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition">Đăng xuất</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pt-3 border-t border-gray-700 space-y-2">
                        <button data-modal-target="#loginModal" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">Đăng nhập người dùng</button>
                        <button data-modal-target="#adminLoginModal" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Đăng nhập quản trị</button>
                        <button data-modal-target="#signupModal" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">Đăng ký</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
// Mobile menu toggle
document.getElementById('navbarToggler').addEventListener('click', function () {
    const navbarContent = document.getElementById('navbarContent');
    navbarContent.classList.toggle('hidden');
});

// Mobile category menu toggle
document.getElementById('mobileCategoryBtn').addEventListener('click', function () {
    const menu = document.getElementById('mobileCategoryMenu');
    menu.classList.toggle('hidden');
});

// Desktop dropdown handling
document.querySelectorAll('.relative').forEach(dropdown => {
    const button = dropdown.querySelector('button');
    const menu = dropdown.querySelector('div[class*="absolute"]');
    
    if (button && menu) {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Close other dropdowns
            document.querySelectorAll('.relative div[class*="absolute"]').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.add('hidden');
                }
            });
            
            menu.classList.toggle('hidden');
        });
    }
});

// Close dropdowns when clicking outside
document.addEventListener('click', (event) => {
    document.querySelectorAll('.relative div[class*="absolute"]').forEach(menu => {
        const dropdown = menu.closest('.relative');
        if (!dropdown.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
});

// Handle modal toggling
document.querySelectorAll('[data-modal-target]').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
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

// Close mobile menu when clicking on links
document.querySelectorAll('#navbarContent a').forEach(link => {
    link.addEventListener('click', () => {
        document.getElementById('navbarContent').classList.add('hidden');
    });
});

// Handle window resize
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        document.getElementById('navbarContent').classList.add('hidden');
    }
});
</script>