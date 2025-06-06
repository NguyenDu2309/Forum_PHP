<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="Partials/style.css">
  <link rel="icon" type="image/jpg" href="images/favicon1.jpg">
  <title>Liên hệ với chúng tôi</title>

  <style>
    /* Contact Page Custom Styles */
    .contact-container {
      background: linear-gradient(to right, #ff7e5f, #feb47b);
      padding: 50px 15px;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .contact-form {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .contact-form h2 {
      color: #ff7e5f;
      font-size: 2.5rem;
      margin-bottom: 20px;
      text-align: center;
    }

    .contact-form input,
    .contact-form textarea {
      border-radius: 8px;
      box-shadow: none;
      border: 1px solid #ddd;
      margin-bottom: 20px;
    }

    .contact-form button {
      background-color: #ff7e5f;
      border: none;
      color: white;
      padding: 12px 30px;
      font-size: 1.1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .contact-form button:hover {
      background-color: #feb47b;
    }

    .contact-info {
      color: white;
      text-align: center;
      margin-top: 40px;
    }

    .contact-info h4 {
      font-size: 1.5rem;
    }

    .contact-info p {
      font-size: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 767px) {
      .contact-container {
        padding: 30px 15px;
      }

      .contact-form {
        padding: 20px;
      }

      .contact-form h2 {
        font-size: 2rem;
      }
    }

    a {
      display: inline-block;
      transition: transform 0.3s ease, color 0.8s ease;
    }

    a:hover {
      text-decoration: underline;
      color: black;
      transform: scale(1.1);
    }

    /* Prevent horizontal overflow */
    body,
    html {
      overflow-x: hidden;
    }
  </style>
</head>

<!-- Include the header -->
<?php include "Partials/_header.php"; ?>
<?php include "Partials/login_modal.php"; ?>
<?php include "Partials/signup_modal.php"; ?>
<?php include "Partials/admin_login_modal.php"; ?>

<?php
// Include database connection
include 'Partials/db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Retrieve form data
  $name = $_POST['name'];
  $email = $_POST['email'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  // Prepare the SQL query to insert data into the feedback table
  $sql = "INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)";

  // Prepare the statement
  if ($stmt = $conn->prepare($sql)) {
    // Bind parameters
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute the statement
    if ($stmt->execute()) {
      // Redirect to a thank you page or show success message
      echo "<script>alert('Your message has been sent successfully.'); window.location.href='contact_form.php';</script>";
    } else {
      echo "<script>alert('Error: Could not send message. Please try again later.');</script>";
    }

    // Close the statement
    $stmt->close();
  } else {
    echo "<script>alert('Error: Unable to prepare the statement.');</script>";
  }

  // Close the connection
  $conn->close();
}
?>

<div class="container-fluid">
  <div class="contact-container mt-5">
    <div class="flex justify-center">
      <div class="w-full lg:w-8/12 md:w-10/12">
        <div class="contact-form">
          <h2>Liên hệ với chúng tôi</h2>
          <form action="contact_form.php" method="POST">
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700">Họ và Tên</label>
              <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="name" name="name" required>
            </div>
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="email" name="email" required>
            </div>
            <div class="mb-4">
              <label for="subject" class="block text-sm font-medium text-gray-700">Môn học</label>
              <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="subject" name="subject" required>
            </div>
            <div class="mb-4">
              <label for="message" class="block text-sm font-medium text-gray-700">Vấn đề</label>
              <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="w-full bg-red-500 text-white px-12 py-3 rounded-lg hover:bg-red-600 transition">Gửi</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="contact-info bg-blue-500 text-white p-3 mb-3 rounded-lg">
    <h4 class="text-xl font-semibold">Cần thêm sự hỗ trợ?</h4>
    <p class="bg-gray-600 rounded-lg p-2">Vui lòng liên hệ  
      <a class="text-white hover:text-black transition-colors duration-300" href="mailto:nguyendu2004.anhuu@gmail.com">
        nguyendu2004.anhuu@gmail.com
      </a> (clickable).
    </p>
  </div>
</div>

<!-- Include the footer -->
<?php include "Partials/_footer.php"; ?>

</html>