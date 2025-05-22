//if you are using localhost then you have to config the php.init and email.init files in you xampp server.(to enable smtp service)
<?php
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
        $_SESSION['message'] = "❌ Email not found in our records.";
        header('Location: _forget_credentials.php');
        exit();
    }

    // Generate OTP (6-digit)
    $otp = rand(1000, 9999);
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    // Send OTP via Email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '   '; // Admin Gmail (enter enail id from which you want to send otps to the user) .
        $mail->Password = '   '; // create App Password for your email account which will god practice instead of hardcore the actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('youremail.com', 'IT Forum Support');
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
                    <p style='color: #999; font-size: 12px;'>© 2025 IT Forum. All rights reserved.</p>
                </div>
            </div>
        ";

        $mail->send();
        $_SESSION['message'] = "✅ OTP sent successfully to $email!";
        header('Location: _verify_otp.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "❌ OTP sending failed. Error: {$mail->ErrorInfo}";
        header('Location: _forget_credentials.php');
        exit();
    }
}
