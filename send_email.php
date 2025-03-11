<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // 

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $subject = htmlspecialchars($_POST["subject"]);
    $message = htmlspecialchars($_POST["message"]);

    if (isset($_COOKIE["last_submission"]) && $_COOKIE["last_submission"] == $email) {
        echo json_encode(["status" => "error", "message" => "You can only submit once per day."]);
        exit;
    }

    setcookie("last_submission", $email, time() + 86400, "/");

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP host (e.g., Gmail, Outlook, etc.)
        $mail->SMTPAuth = true;
        $mail->Username = 'chibiis088@gmail.com'; // Your SMTP email
        $mail->Password = 'fxqe rqsh fzui qvex'; // App password (not your actual email password!)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS encryption
        $mail->Port = 587; // SMTP port for TLS

        // Email headers
        $mail->setFrom($email, $name);
        $mail->addAddress('chibiis088@gmail.com'); // Where you want to receive emails
        $mail->addReplyTo($email, $name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission from $name";
        $mail->Body = "<strong>Nimi:</strong> $name <br> 
                       <strong>Sähköposti:</strong> $email <br> 
                       <strong>Aihe:</strong> $subject <br> 
                       <strong>Viesti:</strong> <br> $message";

        // Send email
        $mail->send();
        header('Location: index.html');
        echo json_encode(["status" => "success", "message" => "Your message has been sent!"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Message could not be sent. Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Form submission failed."]);
}
?>