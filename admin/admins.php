<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Get all admins
$query = "SELECT * FROM admins ORDER BY username";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h1><i class="fi fi-sr-users-gear"></i> System Administrators</h1>

    <div class="dashboard-card">
        <h3><i class="fi fi-sr-list"></i> All Administrators</h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><i class="fi fi-sr-user"></i> Username</th>
                        <th><i class="fi fi-sr-envelope"></i> Email</th>
                        <th><i class="fi fi-sr-calendar"></i> Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($admin = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo date('F j, Y', strtotime($admin['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
require_once('includes/footer.php');
?>