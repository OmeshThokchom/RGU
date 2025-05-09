<?php
// Buffer output to prevent headers already sent error
ob_start();

session_start();
require_once(__DIR__ . '/../../db_config.php');

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// For AJAX requests, don't output the header
if ($isAjax) {
    return;
}

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: login.php');
    exit();
}

$base_path = dirname(dirname(__DIR__));
$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Admin Panel - RGU Portal</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $relative_path; ?>/assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css'>
    <!-- Admin-specific styles and scripts -->
    <script src="<?php echo $relative_path; ?>/assets/js/admin.js"></script>
    <!-- Admin-specific styles only -->
    <style>
    :root {
        --primary-color: #6b46c1;
        --secondary-color: #553c9a;
        --text-color: #fff;
        --bg-color: #1a1a2e;
        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    * {
        box-sizing: border-box;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    html, body {
        min-height: 100vh;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        line-height: 1.5;
        zoom: 1;
        background-color: var(--bg-color);
        color: var(--text-color);
    }

    img {
        max-width: 100%;
        height: auto;
    }

    .logo {
        width: 200px;
        height: auto;
        max-width: 100%;
    }

    .container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .admin-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 15px 0;
        box-shadow: var(--glass-shadow);
        backdrop-filter: blur(10px);
    }
    
    .admin-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
        padding: 0 20px;
    }
    
    .logo-container {
        flex: 0 0 auto;
    }
    
    .nav-wrapper {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
    }
    
    .admin-nav {
        flex: 1;
    }
    
    .admin-nav ul {
        display: flex;
        gap: 20px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .admin-nav a {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }
    
    .admin-nav a:hover,
    .admin-nav a.active {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .admin-nav a i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
        font-size: 16px;
        vertical-align: middle;
    }
    
    .admin-user {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-left: auto;
    }
    
    .admin-user .return-link {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .admin-user .return-link:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .admin-user .return-link i {
        font-size: 16px;
        transition: transform 0.3s ease;
    }
    
    .admin-user .return-link:hover i {
        transform: translateX(-2px);
    }
    
    .admin-user .user-name {
        color: rgba(255,255,255,0.9);
        padding: 0 10px;
        border-left: 1px solid rgba(255,255,255,0.2);
        border-right: 1px solid rgba(255,255,255,0.2);
    }
    
    .admin-user i {
        margin-right: 5px;
        font-size: 16px;
        vertical-align: middle;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(107, 70, 193, 0.2);
        border-color: rgba(255, 255, 255, 0.2);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .table-responsive {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .admin-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }
    
    .admin-table th {
        background: rgba(255, 255, 255, 0.1);
        font-weight: 500;
        color: rgba(255, 255, 255, 0.95);
        padding: 15px 20px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .admin-table td {
        padding: 15px 20px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        line-height: 1.6;
    }
    
    .admin-table tr:last-child td {
        border-bottom: none;
    }
    
    .form-group {
        position: relative;
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 500;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.9);
        pointer-events: none;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.9);
        font-family: inherit;
        font-size: 14px;
        line-height: 1.5;
        transition: all 0.3s ease;
        z-index: 1;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.2);
    }

    .form-group input::placeholder,
    .form-group select::placeholder,
    .form-group textarea::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-group select {
        cursor: pointer;
        appearance: none;
        padding-right: 40px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='rgba(255, 255, 255, 0.5)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
    }

    .form-group select option {
        background-color: #1a1a2e;
        color: rgba(255, 255, 255, 0.9);
    }

    /* Date input specific styles */
    .form-group input[type="date"] {
        appearance: none;
        padding-right: 40px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='rgba(255, 255, 255, 0.5)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
    }

    .form-group input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    /* Checkbox styling */
    .form-group input[type="checkbox"] {
        width: auto;
        margin-right: 8px;
        cursor: pointer;
    }

    .form-group input[type="checkbox"] + i {
        color: rgba(255, 255, 255, 0.7);
        transition: color 0.3s ease;
    }

    .form-group input[type="checkbox"]:checked + i {
        color: var(--primary-color);
    }

    .admin-body {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: rgba(255, 255, 255, 0.9);
        min-height: 100vh;
    }

    .dashboard-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: var(--glass-shadow);
        margin-bottom: 25px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        transform: rotate(45deg);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .dashboard-card:hover::before {
        opacity: 1;
    }

    .dashboard-card h3 {
        margin-bottom: 20px;
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dashboard-card .count {
        font-size: 2.5rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
        margin: 15px 0;
    }

    .dashboard-card .btn {
        margin-top: 15px;
        width: 100%;
        justify-content: center;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }

    @media (max-width: 768px) {
        .admin-header-content {
            flex-direction: column;
            gap: 20px;
        }

        .nav-wrapper {
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        .admin-nav ul {
            flex-direction: column;
            width: 100%;
        }

        .admin-nav a {
            width: 100%;
            justify-content: center;
        }

        .admin-user {
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }

        .table-responsive {
            margin: 0 -20px;
            width: calc(100% + 40px);
            border-radius: 0;
        }
    }

    .glass-confirm {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 9999;
    }

    .glass-confirm.active {
        opacity: 1;
        visibility: visible;
    }

    .glass-confirm-content {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        transform: scale(0.9);
        transition: transform 0.3s ease;
        max-width: 90%;
        width: 400px;
    }

    .glass-confirm.active .glass-confirm-content {
        transform: scale(1);
    }

    .glass-confirm i {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .glass-confirm h3 {
        color: rgba(255, 255, 255, 0.95);
        margin-bottom: 10px;
    }

    .glass-confirm p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 20px;
    }

    .glass-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 9999;
    }

    .glass-toast.active {
        transform: translateX(0);
    }

    .glass-toast.success {
        border-left: 4px solid #10b981;
    }

    .glass-toast.error {
        border-left: 4px solid #ef4444;
    }

    .glass-toast i {
        font-size: 1.25rem;
    }

    .glass-toast.success i {
        color: #10b981;
    }

    .glass-toast.error i {
        color: #ef4444;
    }

    .glass-toast span {
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
    }

    .admin-user .profile-link {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .admin-user .profile-link:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
    }
    
    .admin-user .profile-link i {
        margin-right: 0;
    }
    </style>
</head>
<body class="admin-body">
    <?php if (isset($_SESSION['admin_id'])): ?>
        <header class="admin-header glass">
            <div class="container">
                <div class="admin-header-content">
                    <div class="logo-container">
                        <img src="<?php echo $relative_path; ?>/assets/images/logo-dark.png" alt="RGU Logo" class="logo">
                    </div>
                    <div class="nav-wrapper">
                        <nav class="admin-nav">
                            <ul>
                                <li><a href="<?php echo $relative_path; ?>/admin/index.php" <?php echo $current_page === 'index' ? 'class="active"' : ''; ?>><i class="fi fi-sr-apps"></i>Dashboard</a></li>
                                <li><a href="<?php echo $relative_path; ?>/admin/departments.php" <?php echo $current_page === 'departments' ? 'class="active"' : ''; ?>><i class="fi fi-sr-building"></i>Departments</a></li>
                                <li><a href="<?php echo $relative_path; ?>/admin/students.php" <?php echo $current_page === 'students' ? 'class="active"' : ''; ?>><i class="fi fi-sr-graduation-cap"></i>Students</a></li>
                                <li><a href="<?php echo $relative_path; ?>/admin/faculty.php" <?php echo $current_page === 'faculty' ? 'class="active"' : ''; ?>><i class="fi fi-sr-chalkboard-user"></i>Faculty</a></li>
                                <li><a href="<?php echo $relative_path; ?>/admin/notices.php" <?php echo $current_page === 'notices' ? 'class="active"' : ''; ?>><i class="fi fi-sr-megaphone"></i>Notices</a></li>
                                <li><a href="<?php echo $relative_path; ?>/admin/events.php" <?php echo $current_page === 'events' ? 'class="active"' : ''; ?>><i class="fi fi-sr-calendar"></i>Events</a></li>
                                <li><a href="<?php echo $relative_path; ?>/admin/admins.php" <?php echo $current_page === 'admins' ? 'class="active"' : ''; ?>><i class="fi fi-sr-users-gear"></i>Administrators</a></li>
                            </ul>
                        </nav>
                        <div class="admin-user">
                            <a href="<?php echo $relative_path; ?>/index.php" class="return-link"><i class="fi fi-sr-arrow-left"></i>Main Site</a>
                            <span class="user-name">
                                <a href="<?php echo $relative_path; ?>/admin/profile.php" class="profile-link" title="Profile Settings">
                                    <i class="fi fi-sr-user"></i><?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                                </a>
                            </span>
                            <a href="<?php echo $relative_path; ?>/admin/logout.php" class="btn btn-danger"><i class="fi fi-sr-sign-out-alt"></i>Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <?php require_once('ui-components.php'); ?>
    <?php endif; ?>