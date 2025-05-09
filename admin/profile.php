<?php
require_once('includes/header.php');
$conn = get_db_connection();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Get current admin info
$admin_id = $_SESSION['admin_id'];
$query = "SELECT username, email FROM admins WHERE admin_id = $admin_id";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_username = sanitize_input($conn, $_POST['username']);
        $new_email = sanitize_input($conn, $_POST['email']);
        
        // Check if username is already taken
        $check_username = mysqli_query($conn, "SELECT admin_id FROM admins WHERE username = '$new_username' AND admin_id != $admin_id");
        if (mysqli_num_rows($check_username) > 0) {
            $error = "Username is already taken";
        } else {
            $update = "UPDATE admins SET username = '$new_username', email = '$new_email' WHERE admin_id = $admin_id";
            if (mysqli_query($conn, $update)) {
                $_SESSION['admin_username'] = $new_username;
                $message = "Profile updated successfully";
                $admin['username'] = $new_username;
                $admin['email'] = $new_email;
            } else {
                $error = "Failed to update profile";
            }
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        $check_pass = mysqli_query($conn, "SELECT password FROM admins WHERE admin_id = $admin_id");
        $current_hash = mysqli_fetch_assoc($check_pass)['password'];
        
        if (!password_verify($current_password, $current_hash)) {
            $error = "Current password is incorrect";
        } elseif (strlen($new_password) < 8) {
            $error = "New password must be at least 8 characters long";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = "UPDATE admins SET password = '$hashed_password' WHERE admin_id = $admin_id";
            if (mysqli_query($conn, $update)) {
                $message = "Password changed successfully";
            } else {
                $error = "Failed to change password";
            }
        }
    }
}
?>

<div class="container">
    <h1><i class="fi fi-sr-user"></i> Profile Settings</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fi fi-sr-check"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fi fi-sr-exclamation"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-user-pen"></i> Update Profile</h3>
        <form method="POST">
            <div class="form-group">
                <label for="username"><i class="fi fi-sr-user"></i> Username</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($admin['username']); ?>">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fi fi-sr-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($admin['email']); ?>">
            </div>
            
            <div class="action-buttons">
                <button type="submit" name="update_profile" class="btn btn-primary">
                    <i class="fi fi-sr-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
    
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-lock"></i> Change Password</h3>
        <form method="POST">
            <div class="form-group">
                <label for="current_password"><i class="fi fi-sr-lock"></i> Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password"><i class="fi fi-sr-lock"></i> New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <div class="password-requirements">
                    Must be at least 8 characters long
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fi fi-sr-lock"></i> Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="action-buttons">
                <button type="submit" name="change_password" class="btn btn-primary">
                    <i class="fi fi-sr-key"></i> Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<?php
mysqli_close($conn);
require_once('includes/footer.php');
?>