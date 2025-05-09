<?php
session_start();
require_once('../db_config.php');

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = get_db_connection();
    $username = sanitize_input($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username = '$username' OR email = '$username'";
    $result = mysqli_query($conn, $query);

    if ($admin = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['is_superuser'] = $admin['is_superuser'];
            header('Location: index.php');
            exit();
        }
    }
    $error = 'Invalid username/email or password';
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RGU Portal</title>
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
        
        .login-container {
            width: 100%;
            max-width: 360px;
            margin: auto;
        }
        
        .login-box {
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
        
        .error-message {
            background: rgba(220, 38, 38, 0.1);
            color: #fca5a5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }
        
        .form-group input {
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
        
        .btn-primary {
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
            margin-top: 8px;
        }
        
        .return-link:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .forgot-password {
            text-align: right;
            margin-top: -12px;
        }
        
        .forgot-password a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .additional-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }
        
        .register-link {
            text-align: center;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px;
            border-radius: 8px;
            background: rgba(107, 70, 193, 0.1);
            transition: all 0.3s ease;
        }
        
        .register-link:hover {
            background: rgba(107, 70, 193, 0.2);
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }
            
            .login-box {
                padding: 32px 24px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .form-group input,
            .btn-primary {
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1><i class="fi fi-sr-user"></i> Admin Login</h1>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fi fi-sr-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="login-form">
                <div class="form-group">
                    <label for="username"><i class="fi fi-sr-user"></i> Username or Email</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Enter your username or email"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fi fi-sr-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Enter your password">
                </div>
                
                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-sign-in-alt"></i> Login
                </button>
                
                <div class="additional-links">
                    <a href="register.php" class="register-link">
                        <i class="fi fi-sr-user-add"></i> New Registration
                    </a>
                    <a href="<?php echo $relative_path; ?>/" class="return-link">
                        <i class="fi fi-sr-arrow-left"></i> Back to Main Site
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>