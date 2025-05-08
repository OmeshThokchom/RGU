<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Handle delete operation
if (isset($_POST['delete']) && isset($_POST['notice_id'])) {
    $notice_id = sanitize_input($conn, $_POST['notice_id']);
    mysqli_query($conn, "DELETE FROM notices WHERE notice_id = '$notice_id'");
    header('Location: notices.php');
    exit();
}

// Handle add/edit operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $title = sanitize_input($conn, $_POST['title']);
    $content = sanitize_input($conn, $_POST['content']);
    $expiry_date = sanitize_input($conn, $_POST['expiry_date']);
    // Modified checkbox handling
    $important = isset($_POST['important']) && $_POST['important'] == '1' ? 1 : 0;
    
    if (isset($_POST['notice_id'])) {
        // Edit operation
        $notice_id = sanitize_input($conn, $_POST['notice_id']);
        $query = "UPDATE notices SET 
                  title = '$title',
                  content = '$content',
                  expiry_date = " . ($expiry_date ? "'$expiry_date'" : "NULL") . ",
                  important = $important
                  WHERE notice_id = '$notice_id'";
    } else {
        // Add operation
        $query = "INSERT INTO notices (title, content, expiry_date, important, created_at)
                  VALUES ('$title', '$content', " . ($expiry_date ? "'$expiry_date'" : "NULL") . ", $important, NOW())";
    }
    
    mysqli_query($conn, $query);
    header('Location: notices.php');
    exit();
}

// Get notice for editing if edit_id is set
$edit_notice = null;
if (isset($_GET['edit_id'])) {
    $edit_id = sanitize_input($conn, $_GET['edit_id']);
    $result = mysqli_query($conn, "SELECT * FROM notices WHERE notice_id = '$edit_id'");
    $edit_notice = mysqli_fetch_assoc($result);
}

// Handle filters
$show = isset($_GET['show']) ? sanitize_input($conn, $_GET['show']) : 'active';
$search = isset($_GET['search']) ? sanitize_input($conn, $_GET['search']) : '';

// Build query with filters
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
    <h1><i class="fi fi-sr-megaphone"></i> <?php echo $edit_notice ? 'Edit Notice' : 'Manage Notices'; ?></h1>

    <!-- Add/Edit Form -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-<?php echo $edit_notice ? 'pencil' : 'plus'; ?>"></i> <?php echo $edit_notice ? 'Edit Notice' : 'Add New Notice'; ?></h3>
        <form method="POST" data-type="notices">
            <?php if ($edit_notice): ?>
                <input type="hidden" name="notice_id" value="<?php echo $edit_notice['notice_id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="title"><i class="fi fi-sr-text"></i> Notice Title</label>
                <input type="text" id="title" name="title" required
                       value="<?php echo $edit_notice ? htmlspecialchars($edit_notice['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="content"><i class="fi fi-sr-document-signed"></i> Content</label>
                <textarea id="content" name="content" rows="4" required><?php echo $edit_notice ? htmlspecialchars($edit_notice['content']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="expiry_date"><i class="fi fi-sr-calendar-clock"></i> Expiry Date (optional)</label>
                <input type="date" id="expiry_date" name="expiry_date"
                       value="<?php echo $edit_notice && $edit_notice['expiry_date'] ? htmlspecialchars($edit_notice['expiry_date']) : ''; ?>">
            </div>
            
            <div class="form-group checkbox-group">
                <label class="checkbox-container">
                    <input type="checkbox" name="important" value="1"
                           <?php echo (isset($edit_notice) && $edit_notice['important'] == 1) ? 'checked' : ''; ?>>
                    <span class="checkmark"></span>
                    <span class="checkbox-label">
                        <i class="fi fi-sr-star"></i> Mark as Important
                    </span>
                </label>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-<?php echo $edit_notice ? 'disk' : 'plus'; ?>"></i>
                    <?php echo $edit_notice ? 'Update Notice' : 'Add Notice'; ?>
                </button>
                <?php if ($edit_notice): ?>
                    <a href="notices.php" class="btn btn-danger">
                        <i class="fi fi-sr-cross-circle"></i> Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Filters -->
    <div class="dashboard-card">
        <form method="GET" class="filters-form">
            <div class="form-group">
                <label for="show"><i class="fi fi-sr-filter"></i> Show Notices</label>
                <select id="show" name="show">
                    <option value="all" <?php echo $show == 'all' ? 'selected' : ''; ?>>
                        <i class="fi fi-sr-megaphone"></i> All Notices
                    </option>
                    <option value="active" <?php echo $show == 'active' ? 'selected' : ''; ?>>
                        <i class="fi fi-sr-bell"></i> Active Notices
                    </option>
                    <option value="expired" <?php echo $show == 'expired' ? 'selected' : ''; ?>>
                        <i class="fi fi-sr-clock-past"></i> Expired Notices
                    </option>
                    <option value="important" <?php echo $show == 'important' ? 'selected' : ''; ?>>
                        <i class="fi fi-sr-star"></i> Important Notices
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="search"><i class="fi fi-sr-search"></i> Search</label>
                <input type="text" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Search notices...">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fi fi-sr-check"></i> Apply Filters
            </button>
            <?php if ($show != 'active' || $search): ?>
                <a href="notices.php" class="btn btn-danger">
                    <i class="fi fi-sr-cross-circle"></i> Clear Filters
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Notices List -->
    <div class="dashboard-card">
        <h3>
            <i class="fi fi-sr-<?php 
                echo $show == 'expired' ? 'clock-past' : 
                    ($show == 'important' ? 'star' : 
                    ($show == 'all' ? 'megaphone' : 'bell')); 
            ?>"></i>
            <?php
            if ($show == 'active') echo 'Active Notices';
            elseif ($show == 'expired') echo 'Expired Notices';
            elseif ($show == 'important') echo 'Important Notices';
            else echo 'All Notices';
            ?>
        </h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><i class="fi fi-sr-megaphone"></i> Title</th>
                        <th><i class="fi fi-sr-document-signed"></i> Content</th>
                        <th><i class="fi fi-sr-calendar"></i> Posted Date</th>
                        <th><i class="fi fi-sr-calendar-clock"></i> Expires</th>
                        <th><i class="fi fi-sr-star"></i> Status</th>
                        <th><i class="fi fi-sr-settings"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($notice = mysqli_fetch_assoc($result)): ?>
                        <?php 
                        $is_expired = $notice['expiry_date'] && strtotime($notice['expiry_date']) < strtotime('today');
                        $status_class = $is_expired ? 'expired' : ($notice['important'] ? 'important' : 'active');
                        ?>
                        <tr data-notice_id="<?php echo $notice['notice_id']; ?>" class="notice-<?php echo $status_class; ?>">
                            <td>
                                <?php if ($notice['important']): ?>
                                    <span class="badge important-badge">
                                        <i class="fi fi-sr-star"></i> Important
                                    </span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($notice['title']); ?>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars($notice['content'])); ?></td>
                            <td>
                                <i class="fi fi-sr-calendar"></i>
                                <?php echo date('F j, Y', strtotime($notice['created_at'])); ?>
                            </td>
                            <td>
                                <?php if ($notice['expiry_date']): ?>
                                    <i class="fi fi-sr-calendar-clock"></i>
                                    <?php echo date('F j, Y', strtotime($notice['expiry_date'])); ?>
                                <?php else: ?>
                                    <i class="fi fi-sr-infinity"></i> No expiry
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($is_expired): ?>
                                    <span class="badge expired-badge">
                                        <i class="fi fi-sr-clock-past"></i> Expired
                                    </span>
                                <?php else: ?>
                                    <span class="badge <?php echo $notice['important'] ? 'important-badge' : 'active-badge'; ?>">
                                        <i class="fi fi-sr-<?php echo $notice['important'] ? 'star' : 'bell'; ?>"></i>
                                        <?php echo $notice['important'] ? 'Important' : 'Active'; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $notice['notice_id']; ?>" 
                                       class="btn btn-primary">
                                       <i class="fi fi-sr-pencil"></i> Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger"
                                            onclick="deleteRecord('notices', 'notice_id', '<?php echo $notice['notice_id']; ?>')">
                                        <i class="fi fi-sr-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
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