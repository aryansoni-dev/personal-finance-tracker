<?php
require __DIR__ . '/../config/db.php';
// date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = trim($_POST['token']);
    $new_password = trim($_POST['passwd']);

    // var_dump($new_password);
    // echo "\n".date_default_timezone_get();
    // echo "\n".date('Y-m-d H:i:s');
    // echo "\n".date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Validate the password length
    if (strlen($new_password) < 8) {
        die("Password must be at least 8 characters.");
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    $db = getDBConnection();

    // Check if the token is valid and not expired
    // echo ("\nToken provided: " . $token);
    $stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    if (!$stmt->execute()) {
        echo ("Query execution failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    // echo ("\nDatabase result: " . print_r($user, true));


    if ($user) {
        // Update the user's password and clear the reset token and expiry
        $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user['id']);
        $stmt->execute();
        $stmt->close(); // Close the statement

        echo "Password has been reset!";
    } else {
        die("Invalid or expired token.");
    }

    // Close the database connection
    $db->close();
}
