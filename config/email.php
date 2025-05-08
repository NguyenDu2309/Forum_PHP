<?php
use PHPMailer\PHPMailer\PHPMailer;

return [
    'smtp' => [
        'host' => 'smtp.gmail.com', // Your SMTP host
        'port' => 587,
        'username' => $_ENV['EMAIL'] ?? null,
        'password' => $_ENV['EMAIL_APP_PASSWORD'] ?? null,
        'encryption' => PHPMailer::ENCRYPTION_STARTTLS,
        'from_email' => $_ENV['EMAIL'] ?? null, // Use the same email as username
        'from_name' => 'Student Forum'
    ]
];
?>