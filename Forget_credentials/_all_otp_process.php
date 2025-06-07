<?php
//if you are using localhost then you have to config the php.init and email.init files in you xampp server.(to enable smtp service)
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../library/PHPMailer/PHPMailer.php'; // PHPMailer
require '../library/PHPMailer/SMTP.php'; // PHPMailer
require '../library/PHPMailer/Exception.php'; // PHPMailer
require '../Partials/db_connection.php'; 
require '../library/PHPMailer/POP3.php'; // PHPMailer

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email_id = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $_SESSION['message'] = "❌ Email không tồn tại trong hệ thống.";
        header('Location: _forget_credentials.php');
        exit();
    }

    // Generate OTP (4-digit)
    $otp = rand(1000, 9999);
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    // Send OTP via Email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '   '; // Admin Gmail
        $mail->Password = '   '; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('youremail.com', 'IT Forum Support');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Mã OTP xác thực IT Forum';
        $mail->Body = '
            <div style="max-width: 420px; margin:auto; font-family: Arial, sans-serif; border-radius: 12px; background: #f3f4f6; box-shadow: 0 2px 12px #0001;">
                <div style="background: #1e293b; color: #fff; border-radius: 12px 12px 0 0; padding: 18px 0; text-align: center;">
                    <h2 style="margin:0;font-size:1.25rem;letter-spacing:1px;">🔒 Yêu cầu đặt lại mật khẩu</h2>
                </div>
                <div style="background: #fff; border-radius: 0 0 12px 12px; padding: 24px 20px 20px 20px; text-align: center;">
                    <h3 style="color: #2563eb; margin-bottom: 10px; font-size: 1.1rem;">Mã OTP của bạn:</h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #dc2626; margin: 10px 0 18px 0; letter-spacing: 4px;">' . $otp . '</div>
                    <p style="color: #64748b; font-size: 14px;">OTP này có hiệu lực trong <b>3 phút</b>. Không chia sẻ mã này với bất kỳ ai.</p>
                    <div class="my-4"></div>
                    <p style="color: #666; font-size: 13px; margin-top: 18px;">Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này hoặc liên hệ với bộ phận hỗ trợ.</p>
                    <p style="color: #94a3b8; font-size: 12px; margin-top: 10px;">© 2025 IT Forum. All rights reserved.</p>
                </div>
            </div>
        ';

        $mail->send();
        $_SESSION['message'] = "✅ OTP đã được gửi tới $email!";
        header('Location: _verify_otp.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "❌ Gửi OTP thất bại. Lỗi: {$mail->ErrorInfo}";
        header('Location: _forget_credentials.php');
        exit();
    }
}
