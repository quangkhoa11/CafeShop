<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khoa110303@gmail.com'; 
        $mail->Password   = 'moim dwoq jhot dptx'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8'; 

        $mail->setFrom('khoa110303@gmail.com', 'CafeShop System');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;  
        $mail->Body    = $body;    

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
