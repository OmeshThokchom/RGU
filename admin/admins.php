<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Check if current admin is superuser
$current_admin_id = $_SESSION['admin_id'];
$superuser_check = mysqli_query($conn, "SELECT is_superuser FROM admins WHERE admin_id = $current_admin_id");
$is_superuser = mysqli_fetch_assoc($superuser_check)['is_superuser'];

// Handle superuser promotion/demotion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_superuser']) && $is_superuser) {
    $target_admin_id = sanitize_input($conn, $_POST['admin_id']);
    
    // Count current superusers
    $superuser_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM admins WHERE is_superuser = 1"))['count'];
    
    // Get target admin's current status
    $target_status = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_superuser FROM admins WHERE admin_id = '$target_admin_id'"))['is_superuser'];
    
    // Prevent removing last superuser
    if (!($superuser_count === 1 && $target_status == 1)) {
        $new_status = $target_status ? 0 : 1;
        mysqli_query($conn, "UPDATE admins SET is_superuser = $new_status WHERE admin_id = '$target_admin_id'");
    }
}

// Delete admin functionality (only for superusers)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_admin']) && $is_superuser) {
    $target_admin_id = sanitize_input($conn, $_POST['admin_id']);
    
    // Prevent deleting last superuser
    $superuser_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_superuser FROM admins WHERE admin_id = '$target_admin_id'"))['is_superuser'];
    $superuser_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM admins WHERE is_superuser = 1"))['count'];
    
    if (!($superuser_count === 1 && $superuser_check == 1)) {
        mysqli_query($conn, "DELETE FROM admins WHERE admin_id = '$target_admin_id'");
    }
}

// Get all admins
$query = "SELECT * FROM admins ORDER BY username";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h1><i class="fi fi-sr-users-gear"></i> System Administrators</h1>

    <div class="dashboard-search">
        <input type="text" id="adminSearch" placeholder="Search administrators..." />
        <i class="fi fi-sr-search search-icon"></i>
    </div>

    <div class="dashboard-card">
        <h3><i class="fi fi-sr-list"></i> All Administrators</h3>
        <?php if ($is_superuser): ?>
        <div class="alert alert-info">
            <i class="fi fi-sr-info"></i> You are a superuser admin. You can promote/demote other admins and delete accounts.
        </div>
        <?php endif; ?>
        
        <div class="admins-grid">
            <?php while ($admin = mysqli_fetch_assoc($result)): ?>
                <div class="admin-card" data-search="<?php echo htmlspecialchars($admin['username'] . ' ' . $admin['email']); ?>">
                    <div class="admin-header">
                        <div class="admin-icon">
                            <i class="fi fi-sr-<?php echo $admin['is_superuser'] ? 'shield-check' : 'user'; ?>"></i>
                        </div>
                        <div class="admin-role">
                            <span class="badge <?php echo $admin['is_superuser'] ? 'superuser-badge' : 'admin-badge'; ?>">
                                <?php echo $admin['is_superuser'] ? 'Superuser' : 'Admin'; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="admin-info">
                        <h4 class="admin-name"><?php echo htmlspecialchars($admin['username']); ?></h4>
                        <div class="admin-email">
                            <i class="fi fi-sr-envelope"></i>
                            <?php echo htmlspecialchars($admin['email']); ?>
                        </div>
                        <div class="admin-joined">
                            <i class="fi fi-sr-calendar"></i>
                            Joined <?php echo date('F j, Y', strtotime($admin['created_at'])); ?>
                        </div>
                    </div>
                    
                    <?php if ($is_superuser && $admin['admin_id'] != $current_admin_id): ?>
                    <div class="admin-actions">
                        <form method="POST">
                            <input type="hidden" name="admin_id" value="<?php echo $admin['admin_id']; ?>">
                            <button type="submit" name="toggle_superuser" class="btn btn-primary">
                                <i class="fi fi-sr-<?php echo $admin['is_superuser'] ? 'user' : 'shield-check'; ?>"></i>
                                <?php echo $admin['is_superuser'] ? 'Remove Superuser' : 'Make Superuser'; ?>
                            </button>
                            <button type="submit" name="delete_admin" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this admin account?');">
                                <i class="fi fi-sr-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
.admins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.admin-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.admin-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(107, 70, 193, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.admin-card:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 8px 30px rgba(107, 70, 193, 0.15);
}

.admin-card:hover::before {
    opacity: 1;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(107, 70, 193, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #a78bfa;
}

.superuser-badge {
    background: rgba(107, 70, 193, 0.2);
    color: #a78bfa;
}

.admin-badge {
    background: rgba(59, 130, 246, 0.2);
    color: #93c5fd;
}

.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.admin-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.admin-name {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.95);
    margin: 0;
}

.admin-email, .admin-joined {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.admin-actions {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.admin-actions form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.admin-actions .btn {
    width: 100%;
    justify-content: center;
}

.dashboard-search {
    position: relative;
    margin: 20px 0;
}

.dashboard-search input {
    width: 100%;
    padding: 1rem 3rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.dashboard-search input:focus {
    outline: none;
    border-color: rgba(107, 70, 193, 0.5);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 20px rgba(107, 70, 193, 0.15);
}

.dashboard-search .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(107, 70, 193, 0.7);
    font-size: 1.1rem;
}

.admin-card.hidden {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('adminSearch');
    const adminCards = document.querySelectorAll('.admin-card');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        adminCards.forEach(card => {
            const searchData = card.dataset.search.toLowerCase();
            if (searchData.includes(searchTerm)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    });
});
</script>

<?php
mysqli_close($conn);
require_once('includes/footer.php');
?>