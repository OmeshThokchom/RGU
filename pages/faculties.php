<?php
require_once('../includes/header.php');
$conn = get_db_connection();

// Get department code from URL
$dept_code = isset($_GET['dept']) ? sanitize_input($conn, $_GET['dept']) : '';

// Build the query based on parameters
$query = "SELECT f.*, d.dept_name, d.dept_code 
          FROM faculties f 
          JOIN departments d ON f.dept_id = d.dept_id";

if ($dept_code) {
    $query .= " WHERE d.dept_code = '$dept_code'";
    
    // Get department name
    $dept_query = "SELECT dept_name FROM departments WHERE dept_code = '$dept_code'";
    $dept_result = mysqli_query($conn, $dept_query);
    $dept = mysqli_fetch_assoc($dept_result);
}

$query .= " ORDER BY f.first_name ASC, f.last_name ASC";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fi fi-sr-chalkboard-user"></i> <?php echo isset($dept) ? "Faculty - {$dept['dept_name']}" : "All Faculty Members"; ?></h1>
        <?php if ($dept_code): ?>
            <a href="departments.php?dept=<?php echo urlencode($dept_code); ?>" class="back-link">
                <i class="fi fi-sr-arrow-left"></i> Back to Department
            </a>
        <?php endif; ?>
    </div>

    <div class="faculty-filters">
        <?php if (!$dept_code): ?>
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search faculty by name or designation...">
                <button type="submit"><i class="fi fi-sr-search"></i> Search</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="faculty-grid">
        <?php while ($faculty = mysqli_fetch_assoc($result)): ?>
            <div class="faculty-card">
                <div class="faculty-info">
                    <h3><i class="fi fi-sr-user"></i> <?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?></h3>
                    <p class="designation"><i class="fi fi-sr-briefcase"></i> <?php echo htmlspecialchars($faculty['designation']); ?></p>
                    <?php if (!$dept_code): ?>
                        <p class="department">
                            <a href="?dept=<?php echo urlencode($faculty['dept_code']); ?>">
                                <i class="fi fi-sr-building"></i> <?php echo htmlspecialchars($faculty['dept_name']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    
                    <div class="qualification">
                        <strong><i class="fi fi-sr-graduation-cap"></i> Qualification</strong>
                        <p><?php echo nl2br(htmlspecialchars($faculty['qualification'])); ?></p>
                    </div>
                    
                    <div class="contact-info">
                        <?php if ($faculty['email']): ?>
                            <p>
                                <i class="fi fi-sr-envelope"></i>
                                <a href="mailto:<?php echo htmlspecialchars($faculty['email']); ?>">
                                    <?php echo htmlspecialchars($faculty['email']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <?php if ($faculty['phone']): ?>
                            <p>
                                <i class="fi fi-sr-phone-call"></i>
                                <?php echo htmlspecialchars($faculty['phone']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result) == 0): ?>
            <div class="no-results">
                <i class="fi fi-sr-info"></i>
                <p>No faculty members found.</p>
                <?php if ($dept_code): ?>
                    <a href="faculties.php" class="btn-glass">View All Faculty</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
mysqli_close($conn);
require_once('../includes/footer.php');
?>