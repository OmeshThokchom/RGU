<?php
require_once('../includes/header.php');
$conn = get_db_connection();

// Get filters
$search = isset($_GET['search']) ? sanitize_input($conn, $_GET['search']) : '';
$show = isset($_GET['show']) ? sanitize_input($conn, $_GET['show']) : 'active';

// Build query based on filters
$query = "SELECT * FROM notices WHERE 1=1";

if ($show == 'active') {
    $query .= " AND (expiry_date IS NULL OR expiry_date >= CURDATE())";
} elseif ($show == 'expired') {
    $query .= " AND expiry_date < CURDATE()";
} elseif ($show == 'important') {
    $query .= " AND important = 1";
}

if ($search) {
    $query .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
}

$query .= " ORDER BY important DESC, created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h1><i class="fi fi-sr-megaphone"></i> Notice Board</h1>

    <!-- Search Form -->
    <div class="search-controls">
        <form method="GET" class="search-form">
            <?php if ($show != 'active'): ?>
                <input type="hidden" name="show" value="<?php echo htmlspecialchars($show); ?>">
            <?php endif; ?>
            <div class="search-input-wrapper">
                <i class="fi fi-sr-search"></i>
                <input type="text" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search notices...">
            </div>
            <button type="submit" class="btn-glass primary">
                <i class="fi fi-sr-search"></i> Search
            </button>
        </form>

        <!-- Filter Options -->
        <div class="filter-options">
            <a href="?show=active" class="btn-glass <?php echo $show == 'active' ? 'active' : ''; ?>">
                <i class="fi fi-sr-bell"></i> Active Notices
            </a>
            <a href="?show=important" class="btn-glass <?php echo $show == 'important' ? 'active' : ''; ?>">
                <i class="fi fi-sr-star"></i> Important Notices
            </a>
            <a href="?show=expired" class="btn-glass <?php echo $show == 'expired' ? 'active' : ''; ?>">
                <i class="fi fi-sr-clock-past"></i> Expired Notices
            </a>
        </div>
    </div>

    <!-- Notices List -->
    <div class="notice-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($notice = mysqli_fetch_assoc($result)): ?>
                <?php 
                $is_expired = $notice['expiry_date'] && strtotime($notice['expiry_date']) < strtotime('today');
                $status_class = $is_expired ? 'expired' : ($notice['important'] ? 'important' : 'active');
                ?>
                <div class="notice-card <?php echo $status_class; ?>">
                    <?php if ($notice['important']): ?>
                        <div class="notice-badge important">
                            <i class="fi fi-sr-star"></i> Important
                        </div>
                    <?php endif; ?>

                    <div class="notice-header">
                        <h3>
                            <i class="fi fi-sr-document-signed"></i>
                            <?php echo htmlspecialchars($notice['title']); ?>
                        </h3>
                        <div class="notice-dates">
                            <span class="posted-date">
                                <i class="fi fi-sr-calendar"></i>
                                Posted: <?php echo date('F j, Y', strtotime($notice['created_at'])); ?>
                            </span>
                            <?php if ($notice['expiry_date']): ?>
                                <span class="expiry-date <?php echo $is_expired ? 'expired' : ''; ?>">
                                    <i class="fi fi-sr-calendar-clock"></i>
                                    <?php echo $is_expired ? 'Expired: ' : 'Expires: '; ?>
                                    <?php echo date('F j, Y', strtotime($notice['expiry_date'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="notice-content">
                        <?php echo nl2br(htmlspecialchars($notice['content'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fi fi-sr-info"></i>
                <p>No notices found matching your criteria.</p>
                <?php if ($search || $show != 'active'): ?>
                    <a href="notices.php" class="btn-glass">View All Notices</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.notice-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.notice-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.notice-card.important {
    border-color: var(--secondary-color);
}

.notice-card.expired {
    opacity: 0.7;
}

.notice-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.notice-badge.important {
    background: var(--secondary-color);
    color: white;
}

.notice-header {
    margin-bottom: 15px;
}

.notice-header h3 {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 10px 0;
    padding-right: 80px;
}

.notice-dates {
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-size: 13px;
    opacity: 0.8;
}

.notice-dates span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.expiry-date.expired {
    color: #ff4444;
}

.notice-content {
    white-space: pre-line;
    line-height: 1.6;
}

.search-controls {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.filter-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .notice-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
mysqli_close($conn);
require_once('../includes/footer.php');
?>
