<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="Partials/style.css">
  <link rel="icon" type="image/jpg" href="images/favicon1.jpg">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <title>IT Forum</title>
  <style>
    .card-img-top {
      height: 200px;
      width: 100%;
      object-fit: cover;
      border-radius: 0.5rem;
    }
  </style>
</head>
<body class="bg-gray-100 font-sans">
  <!-- Included header and database connection -->
  <?php include "Partials/_header.php"; ?>
  <?php include "Partials/db_connection.php"; ?>

  <!-- Login Modal -->
  <div class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50" id="loginModal" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
      <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h5 class="text-xl font-bold text-gray-800" id="loginModalLabel">Đăng nhập vào IT Forum</h5>
        <button type="button" class="text-gray-600 hover:text-gray-800" data-modal-dismiss="loginModal" aria-label="Close">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="p-6">
        <h3 class="text-lg font-semibold text-center mb-4 p-2 bg-red-500 text-white rounded-lg">Đăng nhập tại đây</h3>
        <form class="w-full max-w-xs mx-auto" action="Partials/_handle_login.php" method="POST" id="loginForm">
          <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700"><strong>Tên đăng nhập</strong></label>
            <input type="text" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="15" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
          </div>
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700"><strong>Mật khẩu</strong></label>
            <input type="password" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="20" id="password" name="password" placeholder="Nhập mật khẩu" required>
          </div>
          <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Đăng nhập</button>
        </form>
      </div>
      <div class="flex justify-between items-center p-4 border-t border-gray-200">
        <a href="/Forum_website/Forget_credentials/_forget_credentials.php" class="text-blue-500 hover:text-blue-600 font-medium"><strong>Quên mật khẩu?</strong></a>
        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition" data-modal-dismiss="loginModal">Close</button>
      </div>
    </div>
  </div>

  <!-- Signup Modal -->
  <div class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50" id="signupModal" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-2">
      <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h5 class="text-base font-bold text-gray-800" id="signupModalLabel">Đăng ký IT Forum</h5>
        <button type="button" class="text-gray-600 hover:text-gray-800" data-modal-dismiss="signupModal" aria-label="Close">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="p-4">
        <h3 class="text-base font-semibold text-center mb-3 p-2 bg-red-500 text-white rounded-lg">Đăng ký tại đây</h3>
        <form class="w-full max-w-sm mx-auto" action="Partials/_handle_signup.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="username" class="block text-xs font-medium text-gray-700"><strong>Tên đăng nhập</strong></label>
            <input type="text" class="mt-1 w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs" maxlength="15" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
          </div>
          <div class="mb-3">
            <label for="email" class="block text-xs font-medium text-gray-700"><strong>Email</strong></label>
            <input type="email" class="mt-1 w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs" id="email" name="email" placeholder="Nhập email" required>
            <div class="text-xs text-red-500 mt-1"><strong>Vui lòng điền thông tin này một cách cẩn thận.</strong></div>
          </div>
          <div class="mb-3">
            <label for="password" class="block text-xs font-medium text-gray-700"><strong>Mật khẩu</strong></label>
            <input type="password" class="mt-1 w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs" maxlength="20" id="password" name="password" placeholder="Tạo mật khẩu mạnh" required>
          </div>
          <div class="mb-3">
            <label for="cpassword" class="block text-xs font-medium text-gray-700"><strong>Xác nhận mật khẩu</strong></label>
            <input type="password" class="mt-1 w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs" maxlength="20" id="cpassword" name="cpassword" placeholder="Xác nhận mật khẩu của bạn" required>
            <div class="text-xs text-gray-600 mt-1"><strong>Vui lòng nhập mật khẩu xác nhận giống như mật khẩu</strong></div>
          </div>
          <div class="mb-3">
            <label for="user_image" class="block text-xs font-medium text-gray-700"><strong>Ảnh đại diện</strong></label>
            <input type="file" class="mt-1 w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs" id="user_image" name="user_image" accept="image/*">
            <div class="text-xs text-gray-600 mt-1">Tải lên ảnh đại diện (tùy chọn).</div>
          </div>
          <button type="submit" class="w-full bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition text-xs font-semibold">Đăng ký</button>
        </form>
      </div>
      <div class="flex justify-end items-center p-3 border-t border-gray-200">
        <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 transition text-xs" data-modal-dismiss="signupModal">Đóng</button>
      </div>
    </div>
  </div>

  <!-- Admin Login Modal -->
  <div class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50" id="adminLoginModal" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
      <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h5 class="text-xl font-bold text-gray-800" id="adminLoginModalLabel">Quản trị viên đăng nhập</h5>
        <button type="button" class="text-gray-600 hover:text-gray-800" data-modal-dismiss="adminLoginModal" aria-label="Close">
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
      <div class="flex justify-end items-center p-4 border-t border-gray-200">
        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition" data-modal-dismiss="adminLoginModal">Đóng</button>
      </div>
    </div>
  </div>

  <!-- Success/Error Alerts -->
  <?php
  // Alert for session messages
  if (isset($_SESSION['message'])) {
    echo '<div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between w-80 z-50" role="alert">
            <div class="flex items-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span><strong>Thành công!</strong> '.htmlspecialchars($_SESSION['message']).'</span>
            </div>
            <button type="button" class="text-white hover:text-gray-200" data-alert-dismiss>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>';
    unset($_SESSION['message']);
  }

  // Login alerts
  if (isset($_GET['username']) && isset($_GET['password'])) {
    if ($_GET['username'] === "true" && $_GET['password'] === "true") {
      // Success handled by session message
    } elseif ($_GET['username'] === "true" && $_GET['password'] === "false") {
      echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between w-80 z-50" role="alert">
              <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Lỗi!</strong> Mật khẩu không đúng. Vui lòng thử lại.</span>
              </div>
              <button type="button" class="text-white hover:text-gray-200" data-alert-dismiss>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>';
    } elseif ($_GET['username'] === "false") {
      echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between w-80 z-50" role="alert">
              <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Lỗi!</strong> Không tìm thấy tên người dùng. Vui lòng kiểm tra lại.</span>
              </div>
              <button type="button" class="text-white hover:text-gray-200" data-alert-dismiss>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>';
    }
  }

  // Signup alerts
  if (isset($_GET['signupsuccess']) && isset($_GET['exist'])) {
    if ($_GET['signupsuccess'] === "true" && $_GET['exist'] === "false") {
      echo '<div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between w-80 z-50" role="alert">
              <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><strong>Thành công!</strong> Tài khoản của bạn đã được tạo thành công.</span>
              </div>
              <button type="button" class="text-white hover:text-gray-200" data-alert-dismiss>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>';
    } elseif ($_GET['signupsuccess'] === "false" && $_GET['exist'] === "false") {
      echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between w-80 z-50" role="alert">
              <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Lỗi!</strong> Mật khẩu không khớp. Vui lòng thử lại.</span>
              </div>
              <button type="button" class="text-white hover:text-gray-200" data-alert-dismiss>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>';
    } elseif ($_GET['signupsuccess'] === "false" && $_GET['exist'] === "true") {
      if (isset($_GET['username']) && $_GET['username'] === "true") {
        echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between w-80 z-50" role="alert">
                <div class="flex items-center">
                  <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span><strong>Lỗi!</strong> Tên người dùng đã tồn tại. Vui lòng chọn tên khác.</span>
                </div>
                <button type="button" class="text-white hover:text-gray-200" data-alert-dismiss>
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>';
      } elseif (isset($_GET['email']) && $_GET['email'] === "true") {
        echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between w-80 z-50" role="alert">
                <div class="flex items-center">
                  <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span><strong>Lỗi!</strong> Email đã được đăng ký. Vui lòng chọn email khác.</span>
                </div>
                <button type="button" class="text-white hover:text-gray-200" data-alert-dismiss>
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>';
      }
    }
  }
  ?>

  <!-- Categories Section -->
  <div class="container mx-auto mt-8 px-4">
    <h3 class="text-2xl font-bold text-center bg-red-500 text-white p-3 rounded-lg shadow-md mb-6">Danh mục</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
      // SQL Query to fetch categories
      $sql = "SELECT * FROM `category`";
      $result = mysqli_query($conn, $sql);

      // Loop through categories and display each category
      while ($fetch = mysqli_fetch_assoc($result)) {
        $image_path = "Uploads/" . $fetch['category_image'];
        echo '
          <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
            <img src="' . $image_path . '" class="card-img-top" alt="' . $fetch['category_name'] . '">
            <div class="p-4">
              <h4 class="text-xl font-semibold mb-2">
                <a href="thread_list.php?id=' . $fetch['category_id'] . '" class="text-gray-800 hover:text-blue-600 no-underline">' . $fetch['category_name'] . '</a>
              </h4>
              <p class="text-gray-600 mb-4">' . substr($fetch['category_desc'], 0, 90) . '...</p>
              <a href="thread_list.php?id=' . $fetch['category_id'] . '" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Xem chủ đề</a>
            </div>
          </div>';
      }
      ?>
    </div>
  </div>

  <!-- JavaScript for modals and alerts -->
  <script>
    // Handle modal toggling
    document.querySelectorAll('[data-modal-target]').forEach(button => {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        const target = button.getAttribute('data-modal-target');
        const modal = document.querySelector(target);
        if (modal) {
          modal.classList.remove('hidden');
          modal.style.display = 'flex';
        }
      });
    });

    // Handle modal closing
    document.querySelectorAll('[data-modal-dismiss]').forEach(button => {
      button.addEventListener('click', () => {
        const modalId = button.getAttribute('data-modal-dismiss');
        const modal = document.getElementById(modalId);
        if (modal) {
          modal.classList.add('hidden');
          modal.style.display = 'none';
        }
      });
    });

    // Close modals when clicking outside
    document.querySelectorAll('#loginModal, #signupModal, #adminLoginModal').forEach(modal => {
      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          modal.classList.add('hidden');
          modal.style.display = 'none';
        }
      });
    });

    // Check login state and close modal on successful login
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('username') === 'true' && urlParams.get('password') === 'true' && document.getElementById('loginModal')) {
        document.getElementById('loginModal').classList.add('hidden');
        document.getElementById('loginModal').style.display = 'none';
      }
      // Ensure navbar reflects logged-in state
      const username = '<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>';
      if (username) {
        const navbarContent = document.getElementById('navbarContent');
        if (navbarContent) {
          navbarContent.classList.remove('hidden');
        }
      }
    });

    // Handle alert dismissal
    document.querySelectorAll('[data-alert-dismiss]').forEach(button => {
      button.addEventListener('click', () => {
        const alert = button.closest('[role="alert"]');
        if (alert) {
          alert.style.display = 'none';
        }
      });
    });

    // Auto-hide alerts after 3 seconds
    document.querySelectorAll('[role="alert"]').forEach(alert => {
      setTimeout(() => {
        alert.style.display = 'none';
      }, 3000);
    });
  </script>

  <!-- Included footer -->
  <?php include "Partials/_footer.php"; ?>
</body>
</html>