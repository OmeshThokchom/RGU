<?php 
$base_path = dirname(__DIR__);
$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
require_once(__DIR__ . '/../db_config.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Assam Royal Global University</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $relative_path; ?>/assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css'>
    <link rel="stylesheet" href="<?php echo $relative_path; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $relative_path; ?>/assets/css/glass.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo-container">
                    <a href="<?php echo $relative_path; ?>/">
                        <img src="<?php echo $relative_path; ?>/assets/images/logo-dark.png" alt="RGU Logo" class="logo">
                    </a>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo $relative_path; ?>/"><i class="fi fi-sr-home"></i>Home</a></li>
                        <li><a href="<?php echo $relative_path; ?>/pages/departments.php"><i class="fi fi-sr-building"></i>Departments</a></li>
                        <li><a href="<?php echo $relative_path; ?>/pages/notices.php"><i class="fi fi-sr-megaphone"></i>Notices</a></li>
                        <li><a href="<?php echo $relative_path; ?>/pages/events.php"><i class="fi fi-sr-calendar"></i>Events</a></li>
                    </ul>
                </nav>
                <div class="admin-link-container">
                    <a href="<?php echo $relative_path; ?>/admin/login.php" class="admin-link"><i class="fi fi-sr-user"></i>Admin</a>
                </div>
            </div>
        </div>
    </header>
    <main class="container">