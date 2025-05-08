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

    $query = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($admin = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit();
        }
    }
    $error = 'Invalid username or password';
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
            transform: translateY(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .login-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.15);
        }
        
        h1 {
            text-align: center;
            font-size: 24px;
            font-weight: 500;
            margin: 0;
            color: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding-bottom: 8px;
        }

        h1 i {
            font-size: 26px;
            color: rgba(107, 70, 193, 0.9);
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        
        .form-group {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            font-weight: 400;
            padding-left: 2px;
            margin: 0;
        }
        
        .form-group input {
            width: 100%;
            height: 44px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: rgba(107, 70, 193, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(107, 70, 193, 0.15);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6b46c1 0%, #553c9a 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 44px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(107, 70, 193, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: rgba(220, 38, 38, 0.1);
            color: #fca5a5;
            height: 44px;
            border-radius: 12px;
            text-align: center;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid rgba(220, 38, 38, 0.2);
            margin: 0;
            padding: 0 16px;
        }
        
        .return-link {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 0;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 13px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }
        
        .return-link:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
            color: rgba(255, 255, 255, 0.9);
        }

        .return-link:active {
            transform: translateY(0);
        }
        
        .return-link i {
            transition: transform 0.3s ease;
            font-size: 16px;
        }
        
        .return-link:hover i {
            transform: translateX(-4px);
        }

        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
            }

            .login-box {
                padding: 32px 24px;
                gap: 24px;
            }

            h1 {
                font-size: 22px;
            }

            .form-group input,
            .btn-primary,
            .return-link {
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1><i class="fi fi-sr-user"></i> Admin Login</h1>
            
            <div class="welcome-text" style="text-align: center; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.5; margin-top: -16px;">
                Welcome to the Admin Portal. Please login with your credentials to access the dashboard and manage the system.
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fi fi-sr-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="login-form">
                <div class="form-group">
                    <label for="username"><i class="fi fi-sr-user"></i> Username</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Enter your username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fi fi-sr-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-sign-in-alt"></i> Login
                </button>
                
                <a href="<?php echo $relative_path; ?>/" class="return-link">
                    <i class="fi fi-sr-arrow-left"></i> Back to Main Site
                </a>
            </form>
        </div>
    </div>
</body>
</html>