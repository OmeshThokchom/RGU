<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Get counts for dashboard
$counts = array();

$queries = [
    'departments' => "SELECT COUNT(*) as count FROM departments",
    'students' => "SELECT COUNT(*) as count FROM students",
    'faculty' => "SELECT COUNT(*) as count FROM faculties",
    'notices' => "SELECT COUNT(*) as count FROM notices",
    'events' => "SELECT COUNT(*) as count FROM events",
    'pending_registrations' => "SELECT COUNT(*) as count FROM admin_registration_requests WHERE status = 'pending'"
];

foreach ($queries as $key => $query) {
    $result = mysqli_query($conn, $query);
    $counts[$key] = mysqli_fetch_assoc($result)['count'];
}

// Get recent notices
$recent_notices = mysqli_query($conn, "SELECT * FROM notices ORDER BY created_at DESC LIMIT 5");

// Get upcoming events
$upcoming_events = mysqli_query($conn, "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 5");

// Get pending registration requests
$pending_requests = mysqli_query($conn, "SELECT * FROM admin_registration_requests WHERE status = 'pending' ORDER BY created_at DESC");
?>

<div class="container">
    <h1><i class="fi fi-sr-dashboard"></i> Dashboard Overview</h1>

    <!-- Search Box -->
    <div class="dashboard-search">
        <input type="text" id="dashboardSearch" placeholder="Search across all sections..." />
        <i class="fi fi-sr-search search-icon"></i>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card" data-section="departments">
            <h3><i class="fi fi-sr-building"></i> Departments</h3>
            <p class="count"><?php echo $counts['departments']; ?></p>
            <div class="search-results"></div>
            <a href="departments.php" class="btn btn-primary">Manage</a>
        </div>
        
        <div class="dashboard-card" data-section="students">
            <h3><i class="fi fi-sr-graduation-cap"></i> Students</h3>
            <p class="count"><?php echo $counts['students']; ?></p>
            <div class="search-results"></div>
            <a href="students.php" class="btn btn-primary">Manage</a>
        </div>
        
        <div class="dashboard-card" data-section="faculty">
            <h3><i class="fi fi-sr-chalkboard-user"></i> Faculty Members</h3>
            <p class="count"><?php echo $counts['faculty']; ?></p>
            <div class="search-results"></div>
            <a href="faculty.php" class="btn btn-primary">Manage</a>
        </div>
        
        <div class="dashboard-card" data-section="notices">
            <h3><i class="fi fi-sr-megaphone"></i> Notices</h3>
            <p class="count"><?php echo $counts['notices']; ?></p>
            <div class="search-results"></div>
            <a href="notices.php" class="btn btn-primary">Manage</a>
        </div>
        
        <div class="dashboard-card" data-section="events">
            <h3><i class="fi fi-sr-calendar"></i> Events</h3>
            <p class="count"><?php echo $counts['events']; ?></p>
            <div class="search-results"></div>
            <a href="events.php" class="btn btn-primary">Manage</a>
        </div>
    </div>

    <div class="dashboard-grid" style="margin-top: 40px;">
        <div class="dashboard-card">
            <h3><i class="fi fi-sr-bell"></i> Recent Notices</h3>
            <?php if (mysqli_num_rows($recent_notices) > 0): ?>
                <ul class="dashboard-list">
                    <?php while ($notice = mysqli_fetch_assoc($recent_notices)): ?>
                        <li>
                            <i class="fi fi-sr-document-signed"></i> 
                            <div class="list-content">
                                <strong><?php echo htmlspecialchars($notice['title']); ?></strong>
                                <small><i class="fi fi-sr-calendar"></i> Posted: <?php echo date('F j, Y', strtotime($notice['created_at'])); ?></small>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No recent notices</p>
            <?php endif; ?>
        </div>

        <div class="dashboard-card">
            <h3><i class="fi fi-sr-calendar-clock"></i> Upcoming Events</h3>
            <?php if (mysqli_num_rows($upcoming_events) > 0): ?>
                <ul class="dashboard-list">
                    <?php while ($event = mysqli_fetch_assoc($upcoming_events)): ?>
                        <li>
                            <i class="fi fi-sr-star"></i>
                            <div class="list-content">
                                <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                <small><i class="fi fi-sr-calendar"></i> Date: <?php echo date('F j, Y', strtotime($event['event_date'])); ?></small>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No upcoming events</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add this after the main dashboard grid -->
    <?php if ($counts['pending_registrations'] > 0): ?>
        <div class="dashboard-card pending-registrations">
            <h3><i class="fi fi-sr-user-time"></i> Pending Admin Registration Requests</h3>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><i class="fi fi-sr-user"></i> Username</th>
                            <th><i class="fi fi-sr-envelope"></i> Email</th>
                            <th><i class="fi fi-sr-calendar"></i> Requested</th>
                            <th><i class="fi fi-sr-settings"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($request = mysqli_fetch_assoc($pending_requests)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['username']); ?></td>
                                <td><?php echo htmlspecialchars($request['email']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($request['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-primary" 
                                                onclick="handleRegistrationRequest('<?php echo $request['request_id']; ?>', 'approve')">
                                            <i class="fi fi-sr-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                                onclick="handleRegistrationRequest('<?php echo $request['request_id']; ?>', 'reject')">
                                            <i class="fi fi-sr-cross"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.dashboard-search {
    position: relative;
    margin: 20px 0;
}

.dashboard-search input {
    width: 100%;
    padding: 15px 20px;
    padding-left: 50px;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.dashboard-search input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.dashboard-search .search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-color);
    opacity: 0.7;
}

.dashboard-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dashboard-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.dashboard-list li:last-child {
    border-bottom: none;
}

.dashboard-list .list-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.dashboard-list small {
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
}

.search-results {
    margin: 10px 0;
    max-height: 200px;
    overflow-y: auto;
}

.search-results:empty {
    display: none;
}

.search-item {
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.search-item:last-child {
    border-bottom: none;
}

.pending-registrations {
    margin-top: 25px;
}

.pending-registrations .action-buttons {
    display: flex;
    gap: 8px;
}

.pending-registrations .btn {
    padding: 6px 12px;
    font-size: 13px;
}
</style>

<script>
let searchTimeout;

document.getElementById('dashboardSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const searchTerm = e.target.value;

    // Clear results if search is empty
    if (!searchTerm) {
        document.querySelectorAll('.search-results').forEach(div => div.innerHTML = '');
        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.querySelector('.count').style.display = 'block';
        });
        return;
    }

    // Hide counts when showing search results
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.querySelector('.count').style.display = 'none';
    });

    // Debounce the search
    searchTimeout = setTimeout(() => {
        fetch('ajax_handlers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=search&term=${encodeURIComponent(searchTerm)}`
        })
        .then(response => response.json())
        .then(data => {
            // Update each section with its results
            Object.keys(data).forEach(section => {
                const resultsDiv = document.querySelector(`[data-section="${section}"] .search-results`);
                if (resultsDiv) {
                    resultsDiv.innerHTML = data[section].map(item => `
                        <div class="search-item">
                            ${item.title || item.name || item.first_name + ' ' + item.last_name}
                        </div>
                    `).join('');
                }
            });
        })
        .catch(error => console.error('Error:', error));
    }, 300);
});

async function handleRegistrationRequest(requestId, action) {
    if (!confirm(`Are you sure you want to ${action} this registration request?`)) {
        return;
    }

    try {
        const response = await fetch('handle_registration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `request_id=${requestId}&action=${action}`
        });

        const result = await response.json();
        if (result.success) {
            // Reload the page to reflect changes
            location.reload();
        } else {
            alert(result.message || 'Failed to process request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to process request');
    }
}
</script>

<?php
mysqli_close($conn);
require_once('includes/footer.php');
?>