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
        $mail->Subject = 'Mã OTP xác thực IT Forum';
        $mail->Body = "
            <div style='max-width: 420px; margin:auto; font-family: Arial, sans-serif; border-radius: 12px; background: #f3f4f6; box-shadow: 0 2px 12px #0001;'>
                <div style='background: #1e293b; color: #fff; border-radius: 12px 12px 0 0; padding: 18px 0; text-align: center;'>
                    <h2 style='margin:0;font-size:1.25rem;letter-spacing:1px;'>🔒 Yêu cầu đặt lại mật khẩu</h2>
                </div>
                <div style='background: #fff; border-radius: 0 0 12px 12px; padding: 24px 20px 20px 20px; text-align: center;'>
                    <h3 style='color: #2563eb; margin-bottom: 10px; font-size: 1.1rem;'>Mã OTP của bạn:</h3>
                    <div style='font-size: 2rem; font-weight: bold; color: #dc2626; margin: 10px 0 18px 0; letter-spacing: 4px;'>$otp</div>
                    <p style='color: #64748b; font-size: 14px;'>OTP này có hiệu lực trong <b>3 phút</b>. Không chia sẻ mã này với bất kỳ ai.</p>
                    <div style='margin: 18px 0;'></div>
                    <p style='color: #666; font-size: 13px; margin-top: 18px;'>Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này hoặc liên hệ với bộ phận hỗ trợ.</p>
                    <p style='color: #94a3b8; font-size: 12px; margin-top: 10px;'>© 2025 IT Forum. All rights reserved.</p>
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
