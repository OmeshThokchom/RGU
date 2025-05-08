<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Handle delete operation
if (isset($_POST['delete']) && isset($_POST['event_id'])) {
    $event_id = sanitize_input($conn, $_POST['event_id']);
    mysqli_query($conn, "DELETE FROM events WHERE event_id = '$event_id'");
    header('Location: events.php');
    exit();
}

// Handle add/edit operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $title = sanitize_input($conn, $_POST['title']);
    $description = sanitize_input($conn, $_POST['description']);
    $event_date = sanitize_input($conn, $_POST['event_date']);
    $venue = sanitize_input($conn, $_POST['venue']);
    $organizer = sanitize_input($conn, $_POST['organizer']);
    
    if (isset($_POST['event_id'])) {
        // Edit operation
        $event_id = sanitize_input($conn, $_POST['event_id']);
        $query = "UPDATE events SET 
                  title = '$title',
                  description = '$description',
                  event_date = '$event_date',
                  venue = '$venue',
                  organizer = '$organizer'
                  WHERE event_id = '$event_id'";
    } else {
        // Add operation
        $query = "INSERT INTO events (title, description, event_date, venue, organizer)
                  VALUES ('$title', '$description', '$event_date', '$venue', '$organizer')";
    }
    
    mysqli_query($conn, $query);
    header('Location: events.php');
    exit();
}

// Get event for editing if edit_id is set
$edit_event = null;
if (isset($_GET['edit_id'])) {
    $edit_id = sanitize_input($conn, $_GET['edit_id']);
    $result = mysqli_query($conn, "SELECT * FROM events WHERE event_id = '$edit_id'");
    $edit_event = mysqli_fetch_assoc($result);
}

// Handle filters
$show = isset($_GET['show']) ? sanitize_input($conn, $_GET['show']) : 'upcoming';
$search = isset($_GET['search']) ? sanitize_input($conn, $_GET['search']) : '';

// Build query with filters
$query = "SELECT * FROM events WHERE 1=1";

if ($show == 'upcoming') {
    $query .= " AND event_date >= CURDATE()";
} elseif ($show == 'past') {
    $query .= " AND event_date < CURDATE()";
}

if ($search) {
    $query .= " AND (title LIKE '%$search%' 
                OR description LIKE '%$search%' 
                OR venue LIKE '%$search%'
                OR organizer LIKE '%$search%')";
}

$query .= " ORDER BY event_date " . ($show == 'past' ? 'DESC' : 'ASC');
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h1><i class="fi fi-sr-calendar"></i> <?php echo $edit_event ? 'Edit Event' : 'Manage Events'; ?></h1>

    <!-- Add/Edit Form -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-<?php echo $edit_event ? 'pencil' : 'plus'; ?>"></i> <?php echo $edit_event ? 'Edit Event' : 'Add New Event'; ?></h3>
        <form method="POST" data-type="events">
            <?php if ($edit_event): ?>
                <input type="hidden" name="event_id" value="<?php echo $edit_event['event_id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="title"><i class="fi fi-sr-text"></i> Event Title</label>
                <input type="text" id="title" name="title" required
                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description"><i class="fi fi-sr-document-signed"></i> Description</label>
                <textarea id="description" name="description" rows="4" required><?php echo $edit_event ? htmlspecialchars($edit_event['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="event_date"><i class="fi fi-sr-calendar-day"></i> Event Date</label>
                <input type="date" id="event_date" name="event_date" required
                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['event_date']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="venue"><i class="fi fi-sr-marker"></i> Venue</label>
                <input type="text" id="venue" name="venue" required
                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['venue']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="organizer"><i class="fi fi-sr-user"></i> Organizer</label>
                <input type="text" id="organizer" name="organizer" required
                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['organizer']) : ''; ?>">
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-<?php echo $edit_event ? 'disk' : 'plus'; ?>"></i>
                    <?php echo $edit_event ? 'Update Event' : 'Add Event'; ?>
                </button>
                <?php if ($edit_event): ?>
                    <a href="events.php" class="btn btn-danger">
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
                <label for="show"><i class="fi fi-sr-filter"></i> Show Events</label>
                <select id="show" name="show">
                    <option value="upcoming" <?php echo $show == 'upcoming' ? 'selected' : ''; ?>>
                        <i class="fi fi-sr-calendar-clock"></i> Upcoming Events
                    </option>
                    <option value="past" <?php echo $show == 'past' ? 'selected' : ''; ?>>
                        <i class="fi fi-sr-calendar-day"></i> Past Events
                    </option>
                    <option value="all" <?php echo $show == 'all' ? 'selected' : ''; ?>>
                        <i class="fi fi-sr-calendar"></i> All Events
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="search"><i class="fi fi-sr-search"></i> Search</label>
                <input type="text" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Search events...">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fi fi-sr-check"></i> Apply Filters
            </button>
            <?php if ($show != 'upcoming' || $search): ?>
                <a href="events.php" class="btn btn-danger">
                    <i class="fi fi-sr-cross-circle"></i> Clear Filters
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Events List -->
    <div class="dashboard-card">
        <h3>
            <i class="fi fi-sr-<?php echo $show == 'past' ? 'calendar-day' : ($show == 'all' ? 'calendar' : 'calendar-clock'); ?>"></i>
            <?php
            if ($show == 'upcoming') echo 'Upcoming Events';
            elseif ($show == 'past') echo 'Past Events';
            else echo 'All Events';
            ?>
        </h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><i class="fi fi-sr-star"></i> Title</th>
                        <th><i class="fi fi-sr-document-signed"></i> Description</th>
                        <th><i class="fi fi-sr-calendar"></i> Date</th>
                        <th><i class="fi fi-sr-marker"></i> Venue</th>
                        <th><i class="fi fi-sr-user"></i> Organizer</th>
                        <th><i class="fi fi-sr-settings"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = mysqli_fetch_assoc($result)): ?>
                        <tr data-event_id="<?php echo $event['event_id']; ?>"
                            class="<?php echo strtotime($event['event_date']) < strtotime('today') ? 'past-event' : ''; ?>">
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($event['description'])); ?></td>
                            <td>
                                <i class="fi fi-sr-calendar"></i>
                                <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                            </td>
                            <td>
                                <i class="fi fi-sr-marker"></i>
                                <?php echo htmlspecialchars($event['venue']); ?>
                            </td>
                            <td>
                                <i class="fi fi-sr-user"></i>
                                <?php echo htmlspecialchars($event['organizer']); ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $event['event_id']; ?>" 
                                       class="btn btn-primary">
                                       <i class="fi fi-sr-pencil"></i> Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger"
                                            onclick="deleteRecord('events', 'event_id', '<?php echo $event['event_id']; ?>')">
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