<?php
require_once('../includes/header.php');
$conn = get_db_connection();

// Get parameters from URL
$dept_code = isset($_GET['dept']) ? sanitize_input($conn, $_GET['dept']) : '';
$search = isset($_GET['search']) ? sanitize_input($conn, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize_input($conn, $_GET['sort']) : 'name_asc';

// Build the query based on parameters
$query = "SELECT s.*, d.dept_name, d.dept_code,
         (SELECT COUNT(*) FROM students WHERE dept_id = s.dept_id AND semester = s.semester) as semester_count
         FROM students s 
         JOIN departments d ON s.dept_id = d.dept_id 
         WHERE 1=1";

if ($dept_code) {
    $query .= " AND d.dept_code = '$dept_code'";
}

if ($search) {
    $query .= " AND (s.first_name LIKE '%$search%' 
                OR s.last_name LIKE '%$search%' 
                OR s.roll_number LIKE '%$search%')";
}

// Add sorting
switch ($sort) {
    case 'name_desc':
        $query .= " ORDER BY s.first_name DESC, s.last_name DESC";
        break;
    case 'roll_asc':
        $query .= " ORDER BY s.roll_number ASC";
        break;
    case 'roll_desc':
        $query .= " ORDER BY s.roll_number DESC";
        break;
    default: // name_asc
        $query .= " ORDER BY s.first_name ASC, s.last_name ASC";
}

$result = mysqli_query($conn, $query);

// Get department name if dept_code is provided
$dept_name = '';
if ($dept_code) {
    $dept_query = "SELECT dept_name FROM departments WHERE dept_code = '$dept_code'";
    $dept_result = mysqli_query($conn, $dept_query);
    if ($dept = mysqli_fetch_assoc($dept_result)) {
        $dept_name = $dept['dept_name'];
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fi fi-sr-graduation-cap"></i> <?php echo $dept_name ? "Students - $dept_name" : "All Students"; ?></h1>
        <?php if ($dept_code): ?>
            <a href="departments.php?dept=<?php echo urlencode($dept_code); ?>" class="back-link">
                <i class="fi fi-sr-arrow-left"></i> Back to Department
            </a>
        <?php endif; ?>
    </div>

    <div class="students-controls">
        <!-- Search Form -->
        <form method="GET" class="search-form">
            <?php if ($dept_code): ?>
                <input type="hidden" name="dept" value="<?php echo htmlspecialchars($dept_code); ?>">
            <?php endif; ?>
            <div class="search-input-wrapper">
                <i class="fi fi-sr-search"></i>
                <input type="text" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search by name or roll number...">
            </div>
            <button type="submit" class="btn-glass primary">
                <i class="fi fi-sr-search"></i> Search
            </button>
        </form>

        <!-- Sorting Options -->
        <div class="sort-options">
            <span><i class="fi fi-sr-sort"></i> Sort by:</span>
            <div class="sort-buttons">
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_asc'])); ?>" 
                   class="btn-glass <?php echo $sort == 'name_asc' ? 'active' : ''; ?>">
                   <i class="fi fi-sr-sort-alpha-up"></i> Name ↑
                </a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_desc'])); ?>"
                   class="btn-glass <?php echo $sort == 'name_desc' ? 'active' : ''; ?>">
                   <i class="fi fi-sr-sort-alpha-down"></i> Name ↓
                </a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'roll_asc'])); ?>"
                   class="btn-glass <?php echo $sort == 'roll_asc' ? 'active' : ''; ?>">
                   <i class="fi fi-sr-sort-numeric-up"></i> Roll No ↑
                </a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'roll_desc'])); ?>"
                   class="btn-glass <?php echo $sort == 'roll_desc' ? 'active' : ''; ?>">
                   <i class="fi fi-sr-sort-numeric-down"></i> Roll No ↓
                </a>
            </div>
        </div>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="students-grid">
            <?php while ($student = mysqli_fetch_assoc($result)): ?>
                <div class="student-card">
                    <div class="student-name">
                        <i class="fi fi-sr-user"></i>
                        <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                    </div>

                    <div class="roll-number">
                        <i class="fi fi-sr-id-card"></i>
                        <?php echo htmlspecialchars($student['roll_number']); ?>
                    </div>

                    <?php if (!$dept_code): ?>
                        <div class="department">
                            <a href="?dept=<?php echo urlencode($student['dept_code']); ?>">
                                <i class="fi fi-sr-building"></i>
                                <?php echo htmlspecialchars($student['dept_name']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="semester">
                        <i class="fi fi-sr-book"></i>
                        Semester <?php echo htmlspecialchars($student['semester']); ?>
                        <span class="semester-count">(<?php echo $student['semester_count']; ?> students)</span>
                    </div>

                    <?php if ($student['email'] || $student['phone']): ?>
                        <div class="student-contact">
                            <?php if ($student['email']): ?>
                                <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>">
                                    <i class="fi fi-sr-envelope"></i>
                                    <?php echo htmlspecialchars($student['email']); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($student['phone']): ?>
                                <a href="tel:<?php echo htmlspecialchars($student['phone']); ?>">
                                    <i class="fi fi-sr-phone-call"></i>
                                    <?php echo htmlspecialchars($student['phone']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Print Instructions -->
        <div class="print-instructions">
            <p><i class="fi fi-sr-print"></i> To download this list as PDF:</p>
            <ol>
                <li>Press Ctrl+P (Windows) or Cmd+P (Mac)</li>
                <li>Select "Save as PDF" in the destination</li>
                <li>Click "Save" or "Print"</li>
            </ol>
        </div>
    <?php else: ?>
        <div class="no-results">
            <i class="fi fi-sr-info"></i>
            <p>No students found matching your criteria.</p>
            <?php if ($search || $dept_code): ?>
                <a href="students.php" class="btn-glass">View All Students</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
mysqli_close($conn);
require_once('../includes/footer.php');
?>