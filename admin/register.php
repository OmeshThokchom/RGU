<?php
session_start();
require_once('../db_config.php');
require_once('../config.php');
require_once('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = get_db_connection();
    $username = sanitize_input($conn, $_POST['username']);
    $email = sanitize_input($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if username or email already exists in admins or pending requests
        $check_query = "SELECT username, email FROM admins 
                       WHERE username = '$username' OR email = '$email'
                       UNION
                       SELECT username, email FROM admin_registration_requests 
                       WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $existing = mysqli_fetch_assoc($result);
            if ($existing['username'] === $username) {
                $error = "Username is already taken";
            } else {
                $error = "Email is already registered";
            }
        } else {
            // Insert registration request
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO admin_registration_requests (username, password, email)
                      VALUES ('$username', '$hashed_password', '$email')";

            if (mysqli_query($conn, $query)) {
                // Send notification email to existing admins
                $admin_query = "SELECT email FROM admins";
                $admin_result = mysqli_query($conn, $admin_query);
                
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

                    while ($admin = mysqli_fetch_assoc($admin_result)) {
                        $mail->addAddress($admin['email']);
                    }

                    $mail->isHTML(true);
                    $mail->Subject = 'New Admin Registration Request';
                    $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <div style='background: #6b46c1; color: white; padding: 20px; text-align: center;'>
                            <h2>New Admin Registration Request</h2>
                        </div>
                        <div style='padding: 20px; background: #f9f9f9;'>
                            <p>A new admin registration request has been submitted:</p>
                            <p><strong>Username:</strong> {$username}</p>
                            <p><strong>Email:</strong> {$email}</p>
                            <p>Please login to the admin panel to review this request.</p>
                        </div>
                    </div>";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Failed to send admin notification email: " . $mail->ErrorInfo);
                }

                $message = "Registration request submitted successfully. Please wait for approval from an existing administrator.";
            } else {
                $error = "Failed to submit registration request. Please try again.";
                error_log("Registration error: " . mysqli_error($conn));
            }
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - RGU Portal</title>
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
        
        .register-container {
            width: 100%;
            max-width: 400px;
            margin: auto;
        }
        
        .register-box {
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
            gap: 12px;
        }
        
        .message {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error-message {
            background: rgba(220, 38, 38, 0.1);
            color: #fca5a5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            height: 44px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: rgba(107, 70, 193, 0.5);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .password-requirements {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 4px;
        }
        
        .btn-primary {
            width: 100%;
            height: 44px;
            background: linear-gradient(135deg, #6b46c1 0%, #553c9a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(107, 70, 193, 0.4);
        }
        
        .return-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .return-link:hover {
            color: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h1><i class="fi fi-sr-user-add"></i> Admin Registration</h1>
            
            <?php if ($message): ?>
                <div class="message">
                    <i class="fi fi-sr-check"></i> <?php echo $message; ?>
                    <div style="margin-top: 15px;">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fi fi-sr-sign-in-alt"></i> Back to Login
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fi fi-sr-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$message): ?>
                <form method="POST" class="register-form">
                    <div class="form-group">
                        <label for="username"><i class="fi fi-sr-user"></i> Username</label>
                        <input type="text" id="username" name="username" required
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                               placeholder="Choose a username">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fi fi-sr-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="Enter your email">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fi fi-sr-lock"></i> Password</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Create a password">
                        <div class="password-requirements">
                            Must be at least 8 characters long
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><i class="fi fi-sr-lock"></i> Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               placeholder="Confirm your password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fi fi-sr-user-add"></i> Register
                    </button>
                    
                    <a href="login.php" class="return-link">
                        <i class="fi fi-sr-arrow-left"></i> Back to Login
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>