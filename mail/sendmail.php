<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // --- Cấu hình Gmail SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khoa110303@gmail.com'; // Gmail của bạn
        $mail->Password   = 'moim dwoq jhot dptx'; // Mật khẩu ứng dụng
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8'; // ⚠️ Thêm dòng này để gửi tiếng Việt chuẩn

        // --- Thông tin người gửi / nhận ---
        $mail->setFrom('khoa110303@gmail.com', 'CafeShop System');
        $mail->addAddress($to);

        // --- Nội dung ---
        $mail->isHTML(true);
        $mail->Subject = $subject;  // ví dụ: "Mã xác nhận đăng ký tài khoản CafeShop"
        $mail->Body    = $body;     // ví dụ: "Xin chào, mã xác nhận của bạn là 123456"

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
