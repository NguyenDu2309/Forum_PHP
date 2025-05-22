<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ensure this meta tag is correct -->

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <link rel="stylesheet" href="Partials/style.css">
  <link rel="icon" type="image/jpg" href="images/favicon1.jpg">
  <title>Liên hệ với chúng tôi</title>

  <style>
    /* Contact Page Custom Styles */
    .contact-container {
      background: linear-gradient(to right, #ff7e5f, #feb47b);
      padding: 50px 15px;
      /* Reduced padding to avoid overflow */
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

    .contact-form .form-control {
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
        display: inline-block;  /* Make the link an inline-block element */
        /* transition: transform 0.3s ease, color 0.8s ease !important;  Smooth transition for both transform and color */
    }

    a:hover {
        text-decoration: underline !important;  /* Ensure underline is applied */
        /* color: black !important;  Change the color of the link on hover */
        /* transform: scale(1.1);  Zoom in the link on hover */
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


<div class="container-fluid"> <!-- Use container-fluid to cover full width -->
  <div class="contact-container mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10 col-12">
        <div class="contact-form">
          <h2>Liên hệ với chúng tôi</h2>
          <form action="contact_form.php" method="POST">
            <div class="mb-3">
              <label for="name" class="form-label">Họ và Tên</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="subject" class="form-label">Môn học</label>
              <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Vấn đề</label>
              <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-block">Gửi</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="contact-info rounded bg-info text-white p-3 mb-3">
    <h4>Cần thêm sự hỗ trợ?</h4>
    <p class="bg-secondary rounded-3 p-2 ">Vui lòng liên hệ  
      <a class="text-decoration-none text-white" href="mailto:nguyendu2004.anhuu@gmail.com">
        nguyendu2004.anhuu@gmail.com
      </a> (clickable).
    </p>



  </div>
</div>

<!-- Include the footer -->
<?php include "Partials/_footer.php"; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</html>