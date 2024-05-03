<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php'; // Include PHPMailer autoload file

$env = parse_ini_file('.env');

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['contact-name']);
    $phone = trim($_POST['contact-phone']);
    $email = trim($_POST['contact-email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['contact-message']);

    if ($name == "") {
        $msg['err'] = "\n Name can not be empty!";
        $msg['field'] = "contact-name";
        $msg['code'] = FALSE;
    } else if ($phone != "" && !preg_match("/^[0-9 \\-\\+]{8,13}$/i", $phone)) {
        $msg['err'] = "\n Please enter a valid phone number!";
        $msg['field'] = "contact-phone";
        $msg['code'] = FALSE;
    } else if ($email == "") {
        $msg['err'] = "\n Email can not be empty!";
        $msg['field'] = "contact-email";
        $msg['code'] = FALSE;
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $msg['err'] = "\n Please enter a valid email address!";
        $msg['field'] = "contact-email";
        $msg['code'] = FALSE;
    } else if ($subject == "") {
        $msg['err'] = "\n Subject can not be empty!";
        $msg['field'] = "subject";
        $msg['code'] = FALSE;
    } else if ($message == "") {
        $msg['err'] = "\n Message can not be empty!";
        $msg['field'] = "contact-message";
        $msg['code'] = FALSE;
    } else {
        // SMTP configuration

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $env["MAIL_USERNAME"]; // Your Gmail email address
        $mail->Password = $env["MAIL_PASSWORD"]; // Your Gmail password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587; // SMTP port for TLS/STARTTLS

        // Email content
        $replyToEmail = $email; // Use the email provided by the user as the reply-to email
        $replyToName = $name; // Use the name provided by the user as the reply-to name

        if ($phone == "") {
            $phone = "Not Provided";
        }
        
        $mail->setFrom($replyToEmail, $replyToName);
        $mail->addReplyTo($replyToEmail, $replyToName);
        $mail->addAddress($env["MY_EMAIL"]);
        $mail->Subject = "New Message from your Portfolio - " . $subject;
        $mail->Body = "Sender Information\n\n" . "Name: $name\n" . "Email: $email\n" . "Phone: $phone\n\n" . $message;

        // Send email
        if ($mail->send()) {
            $msg['success'] = "\n Email has been sent successfully.";
            $msg['code'] = TRUE;
        } else {
            $msg['err'] = "\n Error sending email. Please try again later.";
            $msg['code'] = FALSE;
        }    
    }
    echo json_encode($msg);
}
