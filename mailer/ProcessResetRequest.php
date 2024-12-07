<?php
include('smtp/PHPMailerAutoload.php');
require __DIR__ . '/../config/db.php';
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    $db = getDBConnection();

    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result(); // Fetch the result set
    $user = $result->fetch_assoc();
    $stmt->close(); // Close the statement

    if ($user) {
        // Generate token and expiry
        $reset_token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save token to the database
        $stmt = $db->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $reset_token, $token_expiry, $email);
        $stmt->execute();
        $stmt->close(); // Close the statement

        $wifi_ip = shell_exec("hostname -I | awk '{print $1}'");
        // echo "Wi-Fi IP Address: " . trim($wifi_ip);

        // Prepare the email
        $reset_link = "http://". urldecode(trim($wifi_ip)) ."/financeTracker/views/resetPasswdForm.php?token=". urlencode($reset_token);
        $subject = "FineTrack - Password Reset Request";
        $message = "
            <h1>FineTrack - Password Reset Link</h1>
            <h3>Click the link below to reset your password:</h3>
            <h3>Link : <a href='$reset_link'>$reset_link</a></h3>
            <h3>This link will expire in 1 hour.</h3>
        ";

        // Send the email
        echo smtp_mailer($email, $subject, $message);
    } else {
        die("No account found with that email address.");
    }

    // Close the database connection
    $db->close();
}

function smtp_mailer($to, $subject, $msg)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "fine.track.23@gmail.com";
    $mail->Password = "jypuggasqpfuzbge";
    $mail->SetFrom("fine.track.23@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));
    if (!$mail->Send()) {
        return $mail->ErrorInfo;
    } else {
        return 'Sent';
    }
}
