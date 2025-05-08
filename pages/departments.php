<?php
require_once('../includes/header.php');
$conn = get_db_connection();

// Get department code from URL if present
$dept_code = isset($_GET['dept']) ? sanitize_input($conn, $_GET['dept']) : '';

if ($dept_code) {
    // Show specific department details
    $query = "SELECT * FROM departments WHERE dept_code = '$dept_code'";
    $result = mysqli_query($conn, $query);
    $department = mysqli_fetch_assoc($result);

    if ($department) {
        ?>
        <div class="container">
            <h1><i class="fi fi-sr-building"></i> <?php echo htmlspecialchars($department['dept_name']); ?></h1>
            <div class="department-details">
                <p class="description"><?php echo htmlspecialchars($department['description']); ?></p>
                <div class="hod-info">
                    <i class="fi fi-sr-user"></i>
                    <div>
                        <strong>Head of Department</strong><br>
                        <?php echo htmlspecialchars($department['hod_name']); ?>
                    </div>
                </div>
            </div>

            <div class="department-sections">
                <section class="faculty-section">
                    <h2><i class="fi fi-sr-chalkboard-user"></i> Faculty Members</h2>
                    <div class="faculty-grid">
                        <?php
                        $query = "SELECT f.*, 
                                 COUNT(DISTINCT s.student_id) as student_count
                                 FROM faculties f 
                                 LEFT JOIN students s ON s.dept_id = f.dept_id 
                                 WHERE f.dept_id = {$department['dept_id']}
                                 GROUP BY f.faculty_id";
                        $faculty_result = mysqli_query($conn, $query);
                        
                        while ($faculty = mysqli_fetch_assoc($faculty_result)) {
                            echo '<div class="faculty-card">';
                            echo '<div class="faculty-info">';
                            echo '<h3><i class="fi fi-sr-user"></i> ' . htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']) . '</h3>';
                            echo '<p class="designation"><i class="fi fi-sr-briefcase"></i> ' . htmlspecialchars($faculty['designation']) . '</p>';
                            
                            echo '<div class="qualification">';
                            echo '<strong><i class="fi fi-sr-graduation-cap"></i> Qualification</strong>';
                            echo '<p>' . nl2br(htmlspecialchars($faculty['qualification'])) . '</p>';
                            echo '</div>';
                            
                            echo '<div class="contact-info">';
                            if ($faculty['email']) {
                                echo '<p><i class="fi fi-sr-envelope"></i> ';
                                echo '<a href="mailto:' . htmlspecialchars($faculty['email']) . '">' . htmlspecialchars($faculty['email']) . '</a></p>';
                            }
                            if ($faculty['phone']) {
                                echo '<p><i class="fi fi-sr-phone-call"></i> ' . htmlspecialchars($faculty['phone']) . '</p>';
                            }
                            echo '</div>';
                            
                            echo '</div>';
                            echo '</div>';
                        }
                        
                        if (mysqli_num_rows($faculty_result) == 0) {
                            echo '<p>No faculty members found.</p>';
                        }
                        ?>
                    </div>
                </section>

                <section class="students-section">
                    <h2><i class="fi fi-sr-graduation-cap"></i> Students</h2>
                    
                    <div class="students-header">
                        <?php
                        $student_count_query = "SELECT COUNT(*) as count FROM students WHERE dept_id = {$department['dept_id']}";
                        $student_count_result = mysqli_query($conn, $student_count_query);
                        $student_count = mysqli_fetch_assoc($student_count_result)['count'];
                        
                        echo '<div class="student-count">';
                        echo '<i class="fi fi-sr-users"></i>';
                        echo '<div>';
                        echo '<strong>Total Students:</strong> ' . $student_count;
                        echo '</div>';
                        echo '</div>';
                        ?>
                        
                        <!-- Search form -->
                        <form method="GET" action="students.php" class="search-form">
                            <input type="hidden" name="dept" value="<?php echo htmlspecialchars($dept_code); ?>">
                            <div class="search-input-wrapper">
                                <i class="fi fi-sr-search"></i>
                                <input type="text" name="search" placeholder="Search students by name or roll number...">
                            </div>
                            <button type="submit" class="btn-glass primary">
                                <i class="fi fi-sr-search"></i> Search
                            </button>
                        </form>
                        
                        <div class="view-all">
                            <a href="students.php?dept=<?php echo urlencode($dept_code); ?>" class="btn-glass">
                                <i class="fi fi-sr-list"></i> View All Department Students
                            </a>
                        </div>
                    </div>

                    <?php
                    // Get recent students with more details
                    $recent_students_query = "SELECT s.*, 
                                            (SELECT COUNT(*) FROM students WHERE dept_id = s.dept_id AND semester = s.semester) as semester_count
                                            FROM students s 
                                            WHERE s.dept_id = {$department['dept_id']} 
                                            ORDER BY s.student_id DESC LIMIT 6";
                    $recent_students_result = mysqli_query($conn, $recent_students_query);
                    
                    if (mysqli_num_rows($recent_students_result) > 0):
                    ?>
                        <div class="recent-students">
                            <h3><i class="fi fi-sr-user-add"></i> Recent Students</h3>
                            <div class="student-cards">
                                <?php while ($student = mysqli_fetch_assoc($recent_students_result)): ?>
                                    <div class="student-card">
                                        <div class="student-name">
                                            <i class="fi fi-sr-user"></i>
                                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                        </div>
                                        <div class="roll-number">
                                            <i class="fi fi-sr-id-card"></i>
                                            <?php echo htmlspecialchars($student['roll_number']); ?>
                                        </div>
                                        <div class="semester">
                                            <i class="fi fi-sr-book"></i>
                                            Semester <?php echo htmlspecialchars($student['semester']); ?>
                                            <span class="semester-count">(<?php echo $student['semester_count']; ?> students)</span>
                                        </div>
                                        <?php if ($student['email']): ?>
                                            <div class="student-contact">
                                                <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>">
                                                    <i class="fi fi-sr-envelope"></i>
                                                    <?php echo htmlspecialchars($student['email']); ?>
                                                </a>
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

                            <div class="student-stats">
                                <?php
                                // Get semester-wise counts
                                $semester_query = "SELECT semester, COUNT(*) as count 
                                                 FROM students 
                                                 WHERE dept_id = {$department['dept_id']}
                                                 GROUP BY semester 
                                                 ORDER BY semester";
                                $semester_result = mysqli_query($conn, $semester_query);
                                
                                if (mysqli_num_rows($semester_result) > 0):
                                ?>
                                    <h3><i class="fi fi-sr-chart-pie"></i> Students by Semester</h3>
                                    <div class="semester-distribution">
                                        <?php while ($sem = mysqli_fetch_assoc($semester_result)): ?>
                                            <div class="semester-stat">
                                                <span class="semester-label">Semester <?php echo $sem['semester']; ?></span>
                                                <span class="semester-bar" style="--percentage: <?php echo ($sem['count'] / $student_count) * 100; ?>%">
                                                    <span class="semester-count"><?php echo $sem['count']; ?></span>
                                                </span>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <i class="fi fi-sr-info"></i>
                            <p>No students found in this department.</p>
                            <a href="../admin/students.php" class="btn-glass primary">Add Students</a>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="container"><p>Department not found.</p></div>';
    }
} else {
    // Show list of all departments
    ?>
    <div class="container">
        <h1><i class="fi fi-sr-building"></i> Our Departments</h1>
        <div class="departments-grid">
            <?php
            $query = "SELECT * FROM departments ORDER BY dept_name";
            $result = mysqli_query($conn, $query);
            
            while ($dept = mysqli_fetch_assoc($result)) {
                echo '<div class="department-card">';
                echo '<h3><i class="fi fi-sr-building"></i> ' . htmlspecialchars($dept['dept_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($dept['description']) . '</p>';
                echo '<p><i class="fi fi-sr-user"></i> <strong>HOD:</strong> ' . htmlspecialchars($dept['hod_name']) . '</p>';
                echo '<div class="department-links">';
                echo '<a href="?dept=' . urlencode($dept['dept_code']) . '"><i class="fi fi-sr-info"></i> Department Details</a>';
                echo '<a href="students.php?dept=' . urlencode($dept['dept_code']) . '"><i class="fi fi-sr-graduation-cap"></i> View Students</a>';
                echo '<a href="faculties.php?dept=' . urlencode($dept['dept_code']) . '"><i class="fi fi-sr-chalkboard-user"></i> View Faculty</a>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

mysqli_close($conn);
require_once('../includes/footer.php');
?>