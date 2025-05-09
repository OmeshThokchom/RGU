<?php
require_once('includes/header.php');
$conn = get_db_connection();
?>

<div class="hero">
    <h1>Welcome to The Assam Royal Global University</h1>
    <p><i class="fi fi-sr-book-open-cover"></i> Empowering Minds, Building Futures</p>
</div>

<section class="about">
    <div class="container">
        <h2><i class="fi fi-sr-info"></i> About RGU</h2>
        <p>The Assam Royal Global University, established under The Assam Royal Global University Act, 2013 is dedicated to serving as a guiding light of innovation in education, research, and professional excellence. Located in Guwahati, Assam, we foster an environment of intellectual curiosity and academic rigor.</p>
        <p>With state-of-the-art infrastructure and dedicated faculty members, RGU is committed to providing quality education and producing industry-ready professionals who can contribute meaningfully to society.</p>
    </div>
</section>

<section class="departments">
    <div class="container">
        <h2><i class="fi fi-sr-building"></i> Our Departments</h2>
        <div class="departments-grid">
            <?php
            $query = "SELECT d.*, 
                     (SELECT COUNT(*) FROM students s WHERE s.dept_id = d.dept_id) as student_count,
                     (SELECT COUNT(*) FROM faculties f WHERE f.dept_id = d.dept_id) as faculty_count
                     FROM departments d ORDER BY d.dept_name";
            $result = mysqli_query($conn, $query);
            
            while ($dept = mysqli_fetch_assoc($result)) {
                echo '<div class="department-card">';
                echo '<h3><i class="fi fi-sr-building"></i> ' . htmlspecialchars($dept['dept_name']) . '</h3>';
                
                $description = strlen($dept['description']) > 150 ? 
                             substr($dept['description'], 0, 150) . '...' : 
                             $dept['description'];
                echo '<p>' . htmlspecialchars($description) . '</p>';
                
                echo '<div class="department-stats">';
                echo '<p><i class="fi fi-sr-user"></i> <strong>HOD:</strong> ' . htmlspecialchars($dept['hod_name']) . '</p>';
                echo '<p><i class="fi fi-sr-users"></i> <strong>Students:</strong> ' . $dept['student_count'] . '</p>';
                echo '<p><i class="fi fi-sr-chalkboard-user"></i> <strong>Faculty:</strong> ' . $dept['faculty_count'] . '</p>';
                echo '</div>';
                
                echo '<a href="pages/departments.php?dept=' . urlencode($dept['dept_code']) . '" class="view-dept-link">';
                echo '<i class="fi fi-sr-arrow-right"></i> View Department Details';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<section class="notices">
    <div class="container">
        <h2><i class="fi fi-sr-megaphone"></i> Latest Notices</h2>
        <div class="notice-grid">
            <?php
            // Get latest active notices, prioritizing important ones
            $query = "SELECT * FROM notices 
                     WHERE (expiry_date IS NULL OR expiry_date >= CURDATE())
                     ORDER BY important DESC, created_at DESC 
                     LIMIT 5";
            $result = mysqli_query($conn, $query);

            while ($notice = mysqli_fetch_assoc($result)) {
                $content_preview = strlen($notice['content']) > 200 ? 
                                substr($notice['content'], 0, 200) . '...' : 
                                $notice['content'];
                                
                echo '<div class="notice-card' . ($notice['important'] ? ' important' : '') . '">';
                if ($notice['important']) {
                    echo '<div class="notice-badge important"><i class="fi fi-sr-star"></i> Important</div>';
                }
                echo '<div class="notice-header">';
                echo '<h3><i class="fi fi-sr-document-signed"></i> ' . htmlspecialchars($notice['title']) . '</h3>';
                echo '<div class="notice-date">';
                echo '<i class="fi fi-sr-calendar"></i> Posted: ' . date('F j, Y', strtotime($notice['created_at']));
                echo '</div>';
                echo '</div>';
                echo '<div class="notice-content">' . nl2br(htmlspecialchars($content_preview)) . '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <p class="view-all"><a href="pages/notices.php" class="btn-glass"><i class="fi fi-sr-list"></i> View All Notices</a></p>
    </div>
</section>

<section class="events">
    <div class="container">
        <h2><i class="fi fi-sr-calendar"></i> Upcoming Events</h2>
        <div class="events-grid">
            <?php
            $query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 3";
            $result = mysqli_query($conn, $query);
            
            while ($event = mysqli_fetch_assoc($result)) {
                echo '<div class="event-card">';
                echo '<div class="event-date"><i class="fi fi-sr-calendar"></i> ' . date('F j, Y', strtotime($event['event_date'])) . '</div>';
                echo '<h3><i class="fi fi-sr-star"></i> ' . htmlspecialchars($event['title']) . '</h3>';
                echo '<p>' . htmlspecialchars($event['description']) . '</p>';
                echo '<p><i class="fi fi-sr-marker"></i> <strong>Venue:</strong> ' . htmlspecialchars($event['venue']) . '</p>';
                echo '</div>';
            }
            ?>
        </div>
        <p class="view-all"><a href="pages/events.php"><i class="fi fi-sr-list"></i> View All Events</a></p>
    </div>
</section>

<?php
mysqli_close($conn);
require_once('includes/footer.php');
?>
