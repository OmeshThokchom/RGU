<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Handle delete operation
if (isset($_POST['delete']) && isset($_POST['student_id'])) {
    $student_id = sanitize_input($conn, $_POST['student_id']);
    mysqli_query($conn, "DELETE FROM students WHERE student_id = '$student_id'");
    header('Location: students.php');
    exit();
}

// Handle add/edit operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $roll_number = sanitize_input($conn, $_POST['roll_number']);
    $first_name = sanitize_input($conn, $_POST['first_name']);
    $last_name = sanitize_input($conn, $_POST['last_name']);
    $dept_id = sanitize_input($conn, $_POST['dept_id']);
    $semester = sanitize_input($conn, $_POST['semester']);
    $email = sanitize_input($conn, $_POST['email']);
    $phone = sanitize_input($conn, $_POST['phone']);
    
    if (isset($_POST['student_id'])) {
        // Edit operation
        $student_id = sanitize_input($conn, $_POST['student_id']);
        $query = "UPDATE students SET 
                  roll_number = '$roll_number',
                  first_name = '$first_name',
                  last_name = '$last_name',
                  dept_id = '$dept_id',
                  semester = '$semester',
                  email = '$email',
                  phone = '$phone'
                  WHERE student_id = '$student_id'";
    } else {
        // Add operation
        $query = "INSERT INTO students (roll_number, first_name, last_name, dept_id, semester, email, phone)
                  VALUES ('$roll_number', '$first_name', '$last_name', '$dept_id', '$semester', '$email', '$phone')";
    }
    
    mysqli_query($conn, $query);
    header('Location: students.php');
    exit();
}

// Get departments for dropdown
$departments = mysqli_query($conn, "SELECT * FROM departments ORDER BY dept_name");
$dept_list = [];
while ($dept = mysqli_fetch_assoc($departments)) {
    $dept_list[$dept['dept_id']] = $dept['dept_name'];
}

// Get student for editing if edit_id is set
$edit_student = null;
if (isset($_GET['edit_id'])) {
    $edit_id = sanitize_input($conn, $_GET['edit_id']);
    $result = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$edit_id'");
    $edit_student = mysqli_fetch_assoc($result);
}

// Handle filters
$filter_dept = isset($_GET['dept_id']) ? sanitize_input($conn, $_GET['dept_id']) : '';
$search = isset($_GET['search']) ? sanitize_input($conn, $_GET['search']) : '';

// Build query with filters
$query = "SELECT s.*, d.dept_name 
          FROM students s 
          JOIN departments d ON s.dept_id = d.dept_id 
          WHERE 1=1";

if ($filter_dept) {
    $query .= " AND s.dept_id = '$filter_dept'";
}

if ($search) {
    $query .= " AND (s.first_name LIKE '%$search%' 
                OR s.last_name LIKE '%$search%' 
                OR s.roll_number LIKE '%$search%')";
}

$query .= " ORDER BY s.roll_number";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h1><i class="fi fi-sr-graduation-cap"></i> <?php echo $edit_student ? 'Edit Student' : 'Manage Students'; ?></h1>

    <!-- Add/Edit Form -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-<?php echo $edit_student ? 'pencil' : 'plus'; ?>"></i> <?php echo $edit_student ? 'Edit Student' : 'Add New Student'; ?></h3>
        <form method="POST" data-type="students">
            <?php if ($edit_student): ?>
                <input type="hidden" name="student_id" value="<?php echo $edit_student['student_id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="roll_number"><i class="fi fi-sr-id-card"></i> Roll Number</label>
                <input type="text" id="roll_number" name="roll_number" required
                       value="<?php echo $edit_student ? htmlspecialchars($edit_student['roll_number']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="first_name"><i class="fi fi-sr-user"></i> First Name</label>
                <input type="text" id="first_name" name="first_name" required
                       value="<?php echo $edit_student ? htmlspecialchars($edit_student['first_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name"><i class="fi fi-sr-user"></i> Last Name</label>
                <input type="text" id="last_name" name="last_name" required
                       value="<?php echo $edit_student ? htmlspecialchars($edit_student['last_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="dept_id"><i class="fi fi-sr-building"></i> Department</label>
                <select id="dept_id" name="dept_id" required>
                    <option value="">Select Department</option>
                    <?php foreach ($dept_list as $id => $name): ?>
                        <option value="<?php echo $id; ?>" <?php echo ($edit_student && $edit_student['dept_id'] == $id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester"><i class="fi fi-sr-book"></i> Semester</label>
                <input type="number" id="semester" name="semester" min="1" max="8" required
                       value="<?php echo $edit_student ? htmlspecialchars($edit_student['semester']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fi fi-sr-envelope"></i> Email</label>
                <input type="email" id="email" name="email"
                       value="<?php echo $edit_student ? htmlspecialchars($edit_student['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="phone"><i class="fi fi-sr-phone-call"></i> Phone</label>
                <input type="text" id="phone" name="phone"
                       value="<?php echo $edit_student ? htmlspecialchars($edit_student['phone']) : ''; ?>">
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-<?php echo $edit_student ? 'disk' : 'plus'; ?>"></i>
                    <?php echo $edit_student ? 'Update Student' : 'Add Student'; ?>
                </button>
                <?php if ($edit_student): ?>
                    <a href="students.php" class="btn btn-danger">
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
                       placeholder="Search by name or roll number">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fi fi-sr-check"></i> Apply Filters
            </button>
            <?php if ($filter_dept || $search): ?>
                <a href="students.php" class="btn btn-danger">
                    <i class="fi fi-sr-cross-circle"></i> Clear Filters
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Students List -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-list"></i> All Students</h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><i class="fi fi-sr-id-card"></i> Roll Number</th>
                        <th><i class="fi fi-sr-user"></i> Name</th>
                        <th><i class="fi fi-sr-building"></i> Department</th>
                        <th><i class="fi fi-sr-book"></i> Semester</th>
                        <th><i class="fi fi-sr-address-book"></i> Contact</th>
                        <th><i class="fi fi-sr-settings"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = mysqli_fetch_assoc($result)): ?>
                        <tr data-student_id="<?php echo $student['student_id']; ?>">
                            <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['dept_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['semester']); ?></td>
                            <td>
                                <?php if ($student['email']): ?>
                                    <i class="fi fi-sr-envelope"></i> <?php echo htmlspecialchars($student['email']); ?><br>
                                <?php endif; ?>
                                <?php if ($student['phone']): ?>
                                    <i class="fi fi-sr-phone-call"></i> <?php echo htmlspecialchars($student['phone']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $student['student_id']; ?>" 
                                       class="btn btn-primary">
                                       <i class="fi fi-sr-pencil"></i> Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger"
                                            onclick="deleteRecord('students', 'student_id', '<?php echo $student['student_id']; ?>')">
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