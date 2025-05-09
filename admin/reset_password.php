<?php
// Start output buffering
ob_start();
session_start();
require_once('../config.php'); // Add main config file
require_once('../db_config.php');
require_once('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';
$error = '';
$valid_token = false;
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!$token) {
    header('Location: forgot_password.php');
    exit();
}

try {
    $conn = get_db_connection();
    
    // Check if token exists and is not expired
    $query = "SELECT admin_id, username, email FROM admins 
              WHERE reset_token = '" . sanitize_input($conn, $token) . "' 
              AND reset_token_expiry > NOW()";
    $result = mysqli_query($conn, $query);
    
    if ($admin = mysqli_fetch_assoc($result)) {
        $valid_token = true;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if (strlen($password) < 8) {
                $error = "Password must be at least 8 characters long";
            } elseif ($password !== $confirm_password) {
                $error = "Passwords do not match";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update = "UPDATE admins SET 
                          password = '$hashed_password',
                          reset_token = NULL,
                          reset_token_expiry = NULL 
                          WHERE admin_id = {$admin['admin_id']}";
                
                if (mysqli_query($conn, $update)) {
                    $message = "Password has been reset successfully. You can now login with your new password.";
                    
                    // Send confirmation email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = SMTP_HOST;
                        $mail->SMTPAuth = true;
                        $mail->Username = SMTP_USERNAME;
                        $mail->Password = SMTP_PASSWORD;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = SMTP_PORT;
                        
                        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                        $mail->addAddress($admin['email'], $admin['username']);
                        
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Successful - RGU Portal';
                        $mail->Body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <div style='background: #6b46c1; color: white; padding: 20px; text-align: center;'>
                                <h2>Password Reset Successful</h2>
                            </div>
                            <div style='padding: 20px; background: #f9f9f9;'>
                                <p>Dear {$admin['username']},</p>
                                <p>Your password has been successfully reset.</p>
                                <p>If you did not make this change, please contact the administrator immediately.</p>
                            </div>
                        </div>";
                        
                        $mail->send();
                    } catch (Exception $e) {
                        // Just log the error, don't show to user since password reset was successful
                        error_log("Failed to send confirmation email: " . $mail->ErrorInfo);
                    }
                } else {
                    $error = "Failed to reset password. Please try again.";
                    error_log("Failed to update password: " . mysqli_error($conn));
                }
            }
        }
    } else {
        $error = "Invalid or expired reset link. Please request a new one.";
    }
} catch (Exception $e) {
    error_log("Reset password error: " . $e->getMessage());
    $error = "An error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - RGU Portal</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/glass.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            padding: 20px;
        }
        
        .reset-container {
            width: 100%;
            max-width: 360px;
            margin: auto;
        }
        
        .reset-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px 32px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .message {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error {
            background: rgba(220, 38, 38, 0.1);
            color: #fca5a5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .password-requirements {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-box">
            <h1><i class="fi fi-sr-lock"></i> Reset Password</h1>
            
            <?php if ($message): ?>
                <div class="message">
                    <i class="fi fi-sr-check"></i> <?php echo $message; ?>
                    <div style="margin-top: 15px;">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fi fi-sr-sign-in-alt"></i> Go to Login
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error">
                    <i class="fi fi-sr-exclamation"></i> <?php echo $error; ?>
                    <?php if (strpos($error, 'expired') !== false): ?>
                        <div style="margin-top: 15px;">
                            <a href="forgot_password.php" class="btn btn-primary">
                                <i class="fi fi-sr-redo"></i> Request New Reset Link
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($valid_token && !$message): ?>
                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="password"><i class="fi fi-sr-lock"></i> New Password</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Enter new password">
                        <div class="password-requirements">
                            Must be at least 8 characters long
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><i class="fi fi-sr-lock"></i> Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               placeholder="Confirm new password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fi fi-sr-check"></i> Reset Password
                    </button>
                </form>
            <?php endif; ?>
            
            <?php if (!$valid_token && !$message): ?>
                <a href="login.php" class="return-link">
                    <i class="fi fi-sr-arrow-left"></i> Back to Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>