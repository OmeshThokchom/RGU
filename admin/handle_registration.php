<?php
session_start();
require_once('../db_config.php');
require_once('../config.php');
require_once('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

$conn = get_db_connection();
$request_id = sanitize_input($conn, $_POST['request_id']);
$action = sanitize_input($conn, $_POST['action']);

if (!in_array($action, ['approve', 'reject'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid action']));
}

// Get request details
$query = "SELECT * FROM admin_registration_requests WHERE request_id = '$request_id' AND status = 'pending'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    http_response_code(404);
    die(json_encode(['success' => false, 'message' => 'Request not found or already processed']));
}

$request = mysqli_fetch_assoc($result);

if ($action === 'approve') {
    // Insert new admin
    $query = "INSERT INTO admins (username, password, email) 
              VALUES ('{$request['username']}', '{$request['password']}', '{$request['email']}')";
    
    if (!mysqli_query($conn, $query)) {
        error_log("Failed to create admin: " . mysqli_error($conn));
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Failed to create admin account']));
    }
}

// Update request status
$status = $action === 'approve' ? 'approved' : 'rejected';
$update = "UPDATE admin_registration_requests SET status = '$status' WHERE request_id = '$request_id'";

if (!mysqli_query($conn, $update)) {
    error_log("Failed to update request status: " . mysqli_error($conn));
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Failed to update request status']));
}

// Get admin email for sending notifications
$admin_query = "SELECT email FROM admins WHERE username = 'admin' LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin_email = mysqli_fetch_assoc($admin_result)['email'];

// Send email notification
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;
    
    // Set from email to the default admin email
    $mail->setFrom($admin_email, SMTP_FROM_NAME);
    $mail->addAddress($request['email'], $request['username']);

    $mail->isHTML(true);
    
    // Get absolute URL to login page
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $login_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/login.php';

    if ($action === 'approve') {
        $mail->Subject = 'Admin Registration Approved';
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #6b46c1; color: white; padding: 20px; text-align: center;'>
                <h2>Registration Approved</h2>
            </div>
            <div style='padding: 20px; background: #f9f9f9;'>
                <p>Dear {$request['username']},</p>
                <p>Your admin registration request has been approved. You can now login using your credentials.</p>
                <p><strong>Username:</strong> {$request['username']}</p>
                <p>Click the button below to login:</p>
                <p style='text-align: center;'>
                    <a href='{$login_url}' 
                       style='display: inline-block; padding: 12px 24px; background: #6b46c1; color: white; text-decoration: none; border-radius: 6px;'>
                        Login to Admin Panel
                    </a>
                </p>
            </div>
        </div>";
    } else {
        $mail->Subject = 'Admin Registration Rejected';
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #dc2626; color: white; padding: 20px; text-align: center;'>
                <h2>Registration Not Approved</h2>
            </div>
            <div style='padding: 20px; background: #f9f9f9;'>
                <p>Dear {$request['username']},</p>
                <p>We regret to inform you that your admin registration request has not been approved at this time.</p>
                <p>If you believe this is an error, please contact the system administrator at {$admin_email}.</p>
            </div>
        </div>";
    }

    $mail->AltBody = strip_tags($mail->Body);
    $mail->send();
    
    error_log("Successfully sent " . ($action === 'approve' ? 'approval' : 'rejection') . " email to: " . $request['email']);
} catch (Exception $e) {
    error_log("Failed to send notification email: " . $mail->ErrorInfo);
}

echo json_encode(['success' => true, 'message' => 'Request processed successfully']);
mysqli_close($conn);