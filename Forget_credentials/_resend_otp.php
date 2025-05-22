<?php
session_start();
include "../Partials/db_connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../library/PHPMailer/PHPMailer.php'; // PHPMailer 
require '../library/PHPMailer/SMTP.php'; // PHPMailer
require '../library/PHPMailer/Exception.php'; // PHPMailer
require '../Partials/db_connection.php'; // 
require '../library/PHPMailer/POP3.php'; // PHPMailer

// Function to generate a 6-digit OTP
function generateOTP() {
    return rand(1000, 9999);
}

// Check if email is set
if (!isset($_SESSION['email'])) {
    $_SESSION['message'] = "Please request an OTP first.";
    header("Location: _reset_credentials.php");
    exit();
}

$email = $_SESSION['email'];
$otp = generateOTP();
$_SESSION['otp'] = $otp;

$mail = new PHPMailer(true);
try {

    $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Fake SMTP host used here for demonstration
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Replace with your email address securely
        $mail->Password = 'your-app-password'; // Replace with your app password securely (never hard-code)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // From address - use a generic email here, such as a support address
        $mail->setFrom('your-email@gmail.com', 'iDiscuss Support'); // Replace with a secure email address
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "
            <div style='max-width: 600px; margin: auto; font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; border-radius: 10px; background-color: #f9f9f9;'>
                <div style='text-align: center; padding-bottom: 10px;'>
                    <h2 style='color: #333; margin-bottom: 5px;'>🔒 Yêu cầu đặt lại mật khẩu</h2>
                    <p style='color: #555;'>Bạn nhận được email này vì có yêu cầu đặt lại mật khẩu của bạn.</p>
                </div>
                <div style='text-align: center; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
                    <h3 style='color: #007bff; margin-bottom: 10px;'>Mã OTP của bạn:</h3>
                    <p style='font-size: 24px; font-weight: bold; color: #d9534f; margin: 10px 0;'>$otp</p>
                    <p style='color: #777; font-size: 14px;'>OTP này có hiệu lực trong <b>3 phút</b>. Không chia sẻ nó với bất kỳ ai.</p>
                </div>
                <div style='margin-top: 20px; text-align: center;'>
                    <p style='color: #666; font-size: 14px;'>Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này hoặc liên hệ với bộ phận hỗ trợ.</p>
                    <p style='color: #999; font-size: 12px;'>© 2025 iDiscuss. All rights reserved.</p>
                </div>
            </div>
        ";

    // Check if the email was sent
    if ($mail->send()) {
        $_SESSION['message'] = "📩 Mã OTP mới đã được gửi tới email của bạn!";
    } else {
        $_SESSION['message'] = "❌ Không gửi được OTP mới. Hãy thử lại sau.";
    }
} catch (Exception $e) {
    $_SESSION['message'] = "❌ Email không được gửi: " . $mail->ErrorInfo;
    // Log the detailed error for debugging (never expose this in production)
    error_log("PHPMailer Error: " . $mail->ErrorInfo);
}

// Redirect to OTP verification page
header("Location: _verify_otp.php");
exit();
?>
