<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Handle delete operation
if (isset($_POST['delete']) && isset($_POST['faculty_id'])) {
    $faculty_id = sanitize_input($conn, $_POST['faculty_id']);
    mysqli_query($conn, "DELETE FROM faculties WHERE faculty_id = '$faculty_id'");
    header('Location: faculty.php');
    exit();
}

// Handle add/edit operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $first_name = sanitize_input($conn, $_POST['first_name']);
    $last_name = sanitize_input($conn, $_POST['last_name']);
    $dept_id = sanitize_input($conn, $_POST['dept_id']);
    $designation = sanitize_input($conn, $_POST['designation']);
    $qualification = sanitize_input($conn, $_POST['qualification']);
    $email = sanitize_input($conn, $_POST['email']);
    $phone = sanitize_input($conn, $_POST['phone']);
    
    if (isset($_POST['faculty_id'])) {
        // Edit operation
        $faculty_id = sanitize_input($conn, $_POST['faculty_id']);
        $query = "UPDATE faculties SET 
                  first_name = '$first_name',
                  last_name = '$last_name',
                  dept_id = '$dept_id',
                  designation = '$designation',
                  qualification = '$qualification',
                  email = '$email',
                  phone = '$phone'
                  WHERE faculty_id = '$faculty_id'";
    } else {
        // Add operation
        $query = "INSERT INTO faculties (first_name, last_name, dept_id, designation, qualification, email, phone)
                  VALUES ('$first_name', '$last_name', '$dept_id', '$designation', '$qualification', '$email', '$phone')";
    }
    
    mysqli_query($conn, $query);
    header('Location: faculty.php');
    exit();
}

// Get departments for dropdown
$departments = mysqli_query($conn, "SELECT * FROM departments ORDER BY dept_name");
$dept_list = [];
while ($dept = mysqli_fetch_assoc($departments)) {
    $dept_list[$dept['dept_id']] = $dept['dept_name'];
}

// Get faculty member for editing if edit_id is set
$edit_faculty = null;
if (isset($_GET['edit_id'])) {
    $edit_id = sanitize_input($conn, $_GET['edit_id']);
    $result = mysqli_query($conn, "SELECT * FROM faculties WHERE faculty_id = '$edit_id'");
    $edit_faculty = mysqli_fetch_assoc($result);
}

// Handle filters
$filter_dept = isset($_GET['dept_id']) ? sanitize_input($conn, $_GET['dept_id']) : '';
$search = isset($_GET['search']) ? sanitize_input($conn, $_GET['search']) : '';

// Build query with filters
$query = "SELECT f.*, d.dept_name 
          FROM faculties f 
          JOIN departments d ON f.dept_id = d.dept_id 
          WHERE 1=1";

if ($filter_dept) {
    $query .= " AND f.dept_id = '$filter_dept'";
}

if ($search) {
    $query .= " AND (f.first_name LIKE '%$search%' 
                OR f.last_name LIKE '%$search%' 
                OR f.designation LIKE '%$search%')";
}

$query .= " ORDER BY f.first_name, f.last_name";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h1><i class="fi fi-sr-chalkboard-user"></i> <?php echo $edit_faculty ? 'Edit Faculty Member' : 'Manage Faculty'; ?></h1>

    <!-- Add/Edit Form -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-<?php echo $edit_faculty ? 'pencil' : 'plus'; ?>"></i> <?php echo $edit_faculty ? 'Edit Faculty Member' : 'Add New Faculty Member'; ?></h3>
        <form method="POST" data-type="faculties">
            <?php if ($edit_faculty): ?>
                <input type="hidden" name="faculty_id" value="<?php echo $edit_faculty['faculty_id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="first_name"><i class="fi fi-sr-user"></i> First Name</label>
                <input type="text" id="first_name" name="first_name" required
                       value="<?php echo $edit_faculty ? htmlspecialchars($edit_faculty['first_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name"><i class="fi fi-sr-user"></i> Last Name</label>
                <input type="text" id="last_name" name="last_name" required
                       value="<?php echo $edit_faculty ? htmlspecialchars($edit_faculty['last_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="dept_id"><i class="fi fi-sr-building"></i> Department</label>
                <select id="dept_id" name="dept_id" required>
                    <option value="">Select Department</option>
                    <?php foreach ($dept_list as $id => $name): ?>
                        <option value="<?php echo $id; ?>" <?php echo ($edit_faculty && $edit_faculty['dept_id'] == $id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="designation"><i class="fi fi-sr-briefcase"></i> Designation</label>
                <input type="text" id="designation" name="designation" required
                       value="<?php echo $edit_faculty ? htmlspecialchars($edit_faculty['designation']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="qualification"><i class="fi fi-sr-graduation-cap"></i> Qualification</label>
                <textarea id="qualification" name="qualification" rows="3"><?php echo $edit_faculty ? htmlspecialchars($edit_faculty['qualification']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fi fi-sr-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo $edit_faculty ? htmlspecialchars($edit_faculty['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="phone"><i class="fi fi-sr-phone-call"></i> Phone</label>
                <input type="text" id="phone" name="phone"
                       value="<?php echo $edit_faculty ? htmlspecialchars($edit_faculty['phone']) : ''; ?>">
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-<?php echo $edit_faculty ? 'disk' : 'plus'; ?>"></i>
                    <?php echo $edit_faculty ? 'Update Faculty Member' : 'Add Faculty Member'; ?>
                </button>
                <?php if ($edit_faculty): ?>
                    <a href="faculty.php" class="btn btn-danger">
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
                <label for="filter_dept"><i class="fi fi-sr-filter"></i> Filter by Department</label>
                <select id="filter_dept" name="dept_id">
                    <option value="">All Departments</option>
                    <?php foreach ($dept_list as $id => $name): ?>
                        <option value="<?php echo $id; ?>" <?php echo ($filter_dept == $id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="search"><i class="fi fi-sr-search"></i> Search</label>
                <input type="text" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Search by name or designation">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fi fi-sr-check"></i> Apply Filters
            </button>
            <?php if ($filter_dept || $search): ?>
                <a href="faculty.php" class="btn btn-danger">
                    <i class="fi fi-sr-cross-circle"></i> Clear Filters
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Faculty List -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-list"></i> All Faculty Members</h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><i class="fi fi-sr-user"></i> Name</th>
                        <th><i class="fi fi-sr-building"></i> Department</th>
                        <th><i class="fi fi-sr-briefcase"></i> Designation</th>
                        <th><i class="fi fi-sr-graduation-cap"></i> Qualification</th>
                        <th><i class="fi fi-sr-address-book"></i> Contact</th>
                        <th><i class="fi fi-sr-settings"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($faculty = mysqli_fetch_assoc($result)): ?>
                        <tr data-faculty_id="<?php echo $faculty['faculty_id']; ?>">
                            <td><?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($faculty['dept_name']); ?></td>
                            <td><?php echo htmlspecialchars($faculty['designation']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($faculty['qualification'])); ?></td>
                            <td>
                                <?php if ($faculty['email']): ?>
                                    <i class="fi fi-sr-envelope"></i> <?php echo htmlspecialchars($faculty['email']); ?><br>
                                <?php endif; ?>
                                <?php if ($faculty['phone']): ?>
                                    <i class="fi fi-sr-phone-call"></i> <?php echo htmlspecialchars($faculty['phone']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $faculty['faculty_id']; ?>" 
                                       class="btn btn-primary">
                                       <i class="fi fi-sr-pencil"></i> Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger"
                                            onclick="deleteRecord('faculties', 'faculty_id', '<?php echo $faculty['faculty_id']; ?>')">
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