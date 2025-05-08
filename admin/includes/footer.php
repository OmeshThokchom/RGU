<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define relative path if not already set
if (!isset($relative_path)) {
    $relative_path = './';  // Adjust if necessary
}
?>

<script src="<?php echo $relative_path; ?>/assets/js/glass-ui.js"></script>

<footer class="admin-footer">
    <div class="container">
        <?php if (isset($_SESSION['admin_username'])): ?>
            <p>
                <i class="fi fi-sr-info-circle"></i> 
                Logged in as: <?php echo htmlspecialchars($_SESSION['admin_username']); ?> | 
                <a href="<?php echo $relative_path; ?>/admin/logout.php"><i class="fi fi-sr-sign-out-alt"></i> Logout</a>
            </p>
        <?php else: ?>
            <p>Please log in</p>
        <?php endif; ?>
        
        <p class="copyright">
            <i class="fi fi-sr-copyright"></i> <?php echo date('Y'); ?> The Assam Royal Global University
            <span class="divider">|</span>
            <a href="<?php echo $relative_path; ?>/"><i class="fi fi-sr-home"></i> Back to Main Site</a>
        </p>
    </div>
</footer>

<style>
.admin-footer {
    background: rgba(255, 255, 255, 0.07);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding: 20px 0;
    margin-top: 60px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
}

.admin-footer .container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    text-align: center;
}

.admin-footer p {
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.admin-footer a {
    color: rgba(107, 70, 193, 0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.admin-footer a:hover {
    color: rgba(107, 70, 193, 1);
    transform: translateY(-1px);
}

.admin-footer .divider {
    margin: 0 10px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .admin-footer p {
        flex-direction: column;
        gap: 15px;
    }
    
    .admin-footer .divider {
        display: none;
    }
}
</style>

</body>
</html>
