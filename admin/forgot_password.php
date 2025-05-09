<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn = get_db_connection();
        $email = sanitize_input($conn, $_POST['email']);
        
        error_log("Password reset attempt for email: " . $email);
        
        // Check if email exists
        $query = "SELECT admin_id, username, email FROM admins WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if ($admin = mysqli_fetch_assoc($result)) {
            error_log("Found admin account for email: " . $email);
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $update = "UPDATE admins SET reset_token = '$token', reset_token_expiry = '$expiry' 
                       WHERE admin_id = {$admin['admin_id']}";
            
            if (mysqli_query($conn, $update)) {
                error_log("Reset token stored in database for admin: " . $admin['username']);
                
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);
                
                try {
                    // Enable debugging
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->Debugoutput = function($str, $level) {
                        error_log("PHPMailer [$level] : $str");
                    };
                    
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = SMTP_PORT;
                    
                    error_log("Configured SMTP settings");
                    
                    // Recipients
                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                    $mail->addAddress($admin['email'], $admin['username']);
                    
                    error_log("Set email recipients");
                    
                    // Generate reset link with proper path
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                    $reset_link = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                    
                    error_log("Generated reset link: " . $reset_link);
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Your RGU Portal Password';
                    $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <div style='background: #6b46c1; color: white; padding: 20px; text-align: center;'>
                            <h2>Reset Your Password</h2>
                        </div>
                        <div style='padding: 20px; background: #f9f9f9;'>
                            <p>Dear {$admin['username']},</p>
                            <p>We received a request to reset your password for your RGU Portal account.</p>
                            <p>Here is your password reset link:<br>
                            <a href='$reset_link'>$reset_link</a></p>
                            <p style='font-size: 13px; color: #666;'>
                                This link will expire in 1 hour for security reasons.<br>
                                If you did not request this reset, please ignore this email.
                            </p>
                        </div>
                    </div>";
                    
                    error_log("Attempting to send email...");
                    $mail->send();
                    error_log("Email sent successfully!");
                    
                    $message = "Password reset link has been sent to your email: " . $admin['email'];
                } catch (Exception $e) {
                    error_log("PHPMailer error: " . $e->getMessage());
                    error_log("Full error info: " . $mail->ErrorInfo);
                    $error = "An error occurred while sending the email. Please try again later.";
                }
            } else {
                error_log("Failed to store reset token: " . mysqli_error($conn));
                $error = "System error: Failed to generate reset token.";
            }
        } else {
            error_log("No admin found with email: " . $email);
            $error = "No admin account found with this email address.";
        }
        mysqli_close($conn);
    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        $error = "An error occurred. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - RGU Portal</title>
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
        
        .forgot-container {
            width: 100%;
            max-width: 360px;
            margin: auto;
        }
        
        .forgot-box {
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
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-box">
            <h1><i class="fi fi-sr-key"></i> Forgot Password</h1>
            
            <?php if ($message): ?>
                <div class="message">
                    <i class="fi fi-sr-check"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error">
                    <i class="fi fi-sr-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email"><i class="fi fi-sr-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                           placeholder="Enter your registered email">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-paper-plane"></i> Generate Reset Link
                </button>
                
                <a href="login.php" class="return-link">
                    <i class="fi fi-sr-arrow-left"></i> Back to Login
                </a>
            </form>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>