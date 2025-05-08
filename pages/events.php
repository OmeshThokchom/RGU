<?php
require_once('../includes/header.php');
$conn = get_db_connection();

// Get upcoming events
$upcoming_query = "SELECT * FROM events 
                  WHERE event_date >= CURDATE() 
                  ORDER BY event_date ASC";
$upcoming_result = mysqli_query($conn, $upcoming_query);

// Get past events
$past_query = "SELECT * FROM events 
              WHERE event_date < CURDATE() 
              ORDER BY event_date DESC";
$past_result = mysqli_query($conn, $past_query);
?>

<div class="container">
    <h1><i class="fi fi-sr-calendar"></i> University Events</h1>

    <!-- Upcoming Events Section -->
    <section class="events-section">
        <h2><i class="fi fi-sr-calendar-clock"></i> Upcoming Events</h2>
        <div class="events-grid">
            <?php if (mysqli_num_rows($upcoming_result) > 0): ?>
                <?php while ($event = mysqli_fetch_assoc($upcoming_result)): ?>
                    <div class="event-card">
                        <div class="event-date">
                            <i class="fi fi-sr-calendar"></i>
                            <?php 
                            $date = new DateTime($event['event_date']);
                            echo $date->format('M j, Y');
                            ?>
                        </div>
                        <h3><i class="fi fi-sr-star"></i> <?php echo htmlspecialchars($event['title']); ?></h3>
                        <div class="event-details">
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            <p><i class="fi fi-sr-marker"></i> <strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                            <p><i class="fi fi-sr-user"></i> <strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No upcoming events scheduled.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Past Events Section -->
    <section class="events-section past-events">
        <h2><i class="fi fi-sr-calendar-day"></i> Past Events</h2>
        <div class="events-grid">
            <?php if (mysqli_num_rows($past_result) > 0): ?>
                <?php while ($event = mysqli_fetch_assoc($past_result)): ?>
                    <div class="event-card past">
                        <div class="event-date">
                            <i class="fi fi-sr-calendar"></i>
                            <?php 
                            $date = new DateTime($event['event_date']);
                            echo $date->format('M j, Y');
                            ?>
                        </div>
                        <h3><i class="fi fi-sr-star"></i> <?php echo htmlspecialchars($event['title']); ?></h3>
                        <div class="event-details">
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            <p><i class="fi fi-sr-marker"></i> <strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                            <p><i class="fi fi-sr-user"></i> <strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No past events to display.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Print Instructions -->
    <div class="print-instructions">
        <p><i class="fi fi-sr-print"></i> To download events list as PDF:</p>
        <ol>
            <li>Press Ctrl+P (Windows) or Cmd+P (Mac)</li>
            <li>Select "Save as PDF" in the destination</li>
            <li>Click "Save" or "Print"</li>
        </ol>
    </div>
</div>

<?php
mysqli_close($conn);
require_once('../includes/footer.php');
?>