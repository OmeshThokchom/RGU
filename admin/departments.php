<?php
require_once('includes/header.php');
$conn = get_db_connection();

// Handle delete operation
if (isset($_POST['delete']) && isset($_POST['dept_id'])) {
    $dept_id = sanitize_input($conn, $_POST['dept_id']);
    mysqli_query($conn, "DELETE FROM departments WHERE dept_id = '$dept_id'");
    header('Location: departments.php');
    exit();
}

// Handle add/edit operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $dept_name = sanitize_input($conn, $_POST['dept_name']);
    $dept_code = sanitize_input($conn, $_POST['dept_code']);
    $description = sanitize_input($conn, $_POST['description']);
    $hod_name = sanitize_input($conn, $_POST['hod_name']);
    
    if (isset($_POST['dept_id'])) {
        // Edit operation
        $dept_id = sanitize_input($conn, $_POST['dept_id']);
        $query = "UPDATE departments SET 
                  dept_name = '$dept_name',
                  dept_code = '$dept_code',
                  description = '$description',
                  hod_name = '$hod_name'
                  WHERE dept_id = '$dept_id'";
    } else {
        // Add operation
        $query = "INSERT INTO departments (dept_name, dept_code, description, hod_name)
                  VALUES ('$dept_name', '$dept_code', '$description', '$hod_name')";
    }
    
    mysqli_query($conn, $query);
    header('Location: departments.php');
    exit();
}

// Get department for editing if edit_id is set
$edit_department = null;
if (isset($_GET['edit_id'])) {
    $edit_id = sanitize_input($conn, $_GET['edit_id']);
    $result = mysqli_query($conn, "SELECT * FROM departments WHERE dept_id = '$edit_id'");
    $edit_department = mysqli_fetch_assoc($result);
}

// Fetch all departments
$result = mysqli_query($conn, "SELECT * FROM departments ORDER BY dept_name");
?>

<div class="container">
    <h1><i class="fi fi-sr-building"></i> <?php echo $edit_department ? 'Edit Department' : 'Manage Departments'; ?></h1>

    <!-- Add/Edit Form -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-<?php echo $edit_department ? 'pencil' : 'plus'; ?>"></i> <?php echo $edit_department ? 'Edit Department' : 'Add New Department'; ?></h3>
        <form method="POST" data-type="departments">
            <?php if ($edit_department): ?>
                <input type="hidden" name="dept_id" value="<?php echo $edit_department['dept_id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="dept_name"><i class="fi fi-sr-building"></i> Department Name</label>
                <input type="text" id="dept_name" name="dept_name" required
                       value="<?php echo $edit_department ? htmlspecialchars($edit_department['dept_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="dept_code"><i class="fi fi-sr-hastag"></i> Department Code</label>
                <input type="text" id="dept_code" name="dept_code" required
                       value="<?php echo $edit_department ? htmlspecialchars($edit_department['dept_code']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description"><i class="fi fi-sr-document-signed"></i> Description</label>
                <textarea id="description" name="description" rows="4"><?php echo $edit_department ? htmlspecialchars($edit_department['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="hod_name"><i class="fi fi-sr-user"></i> Head of Department</label>
                <input type="text" id="hod_name" name="hod_name" required
                       value="<?php echo $edit_department ? htmlspecialchars($edit_department['hod_name']) : ''; ?>">
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fi fi-sr-<?php echo $edit_department ? 'disk' : 'plus'; ?>"></i>
                    <?php echo $edit_department ? 'Update Department' : 'Add Department'; ?>
                </button>
                <?php if ($edit_department): ?>
                    <a href="departments.php" class="btn btn-danger">
                        <i class="fi fi-sr-cross-circle"></i> Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Departments List -->
    <div class="dashboard-card">
        <h3><i class="fi fi-sr-list"></i> All Departments</h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><i class="fi fi-sr-building"></i> Department Name</th>
                        <th><i class="fi fi-sr-hastag"></i> Code</th>
                        <th><i class="fi fi-sr-document-signed"></i> Description</th>
                        <th><i class="fi fi-sr-user"></i> Head of Department</th>
                        <th><i class="fi fi-sr-settings"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dept = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dept['dept_name']); ?></td>
                            <td><?php echo htmlspecialchars($dept['dept_code']); ?></td>
                            <td><?php echo htmlspecialchars($dept['description']); ?></td>
                            <td><?php echo htmlspecialchars($dept['hod_name']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $dept['dept_id']; ?>" 
                                       class="btn btn-primary">
                                       <i class="fi fi-sr-pencil"></i> Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger"
                                            onclick="deleteRecord('departments', 'dept_id', '<?php echo $dept['dept_id']; ?>')">
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