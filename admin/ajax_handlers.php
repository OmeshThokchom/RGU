<?php
// Enable error reporting at the very top
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Add error logging
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/error.log');

session_start();
require_once('../db_config.php');

header('Content-Type: application/json');

// Debug log
error_log("Request received: " . print_r($_POST, true));

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    error_log("Session check failed - admin_id not set");
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

$conn = get_db_connection();
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

try {
    if (!isset($_POST['action'])) {
        throw new Exception('Action parameter is missing');
    }

    error_log("Processing action: " . $_POST['action']);
    
    switch($_POST['action']) {
        case 'create':
        case 'update':
            if (!isset($_POST['table'])) {
                throw new Exception('Table parameter is missing');
            }

            $table = sanitize_input($conn, $_POST['table']);
            $valid_tables = ['departments', 'students', 'faculties', 'notices', 'events'];
            
            if (!in_array($table, $valid_tables)) {
                throw new Exception('Invalid table name: ' . $table);
            }
            
            $fields = [];
            $values = [];
            $update_pairs = [];
            $post_data = $_POST;
            
            // Remove action and table from post data
            unset($post_data['action'], $post_data['table']);
            
            error_log("Processing fields for table $table: " . print_r($post_data, true));
            
            foreach ($post_data as $key => $value) {
                // Special handling for checkbox fields
                if ($key === 'important') {
                    $safe_key = 'important';
                    $safe_value = isset($_POST['important']) ? '1' : '0';
                    $fields[] = $safe_key;
                    $values[] = $safe_value;
                    $update_pairs[] = "$safe_key = $safe_value";
                    continue;
                }
                
                // Skip empty values for optional fields except for update operations where empty values should be allowed
                if ($value === '' && $_POST['action'] === 'create') continue;
                
                $safe_key = sanitize_input($conn, $key);
                $safe_value = sanitize_input($conn, $value);
                
                // For empty values in update operations, set NULL
                if ($value === '') {
                    $safe_value = "NULL";
                    $update_pairs[] = "$safe_key = NULL";
                } else {
                    $fields[] = $safe_key;
                    $values[] = "'$safe_value'";
                    $update_pairs[] = "$safe_key = '$safe_value'";
                }
            }

            if (empty($fields) && $_POST['action'] === 'create') {
                throw new Exception('No valid fields provided');
            }

            if ($_POST['action'] === 'create') {
                $query = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
            } else {
                // Get the correct ID field name based on table
                $id_field = $table === 'students' ? 'student_id' : 
                           ($table === 'faculties' ? 'faculty_id' : 
                           ($table === 'notices' ? 'notice_id' : 
                           ($table === 'events' ? 'event_id' : 'dept_id')));
                
                if (!isset($_POST[$id_field])) {
                    throw new Exception('ID field not provided for update');
                }
                
                $id = sanitize_input($conn, $_POST[$id_field]);
                $query = "UPDATE $table SET " . implode(', ', $update_pairs) . " WHERE $id_field = '$id'";
            }
            
            error_log("Executing query: " . $query);
            error_log("Parameters: " . json_encode($_POST));
            
            if (!mysqli_query($conn, $query)) {
                $error = mysqli_error($conn);
                error_log("MySQL Error: " . $error);
                throw new Exception('Database error: ' . $error);
            }

            $response = [
                'success' => true,
                'message' => $_POST['action'] === 'create' ? 'Record created successfully' : 'Record updated successfully',
                'id' => $_POST['action'] === 'create' ? mysqli_insert_id($conn) : $_POST[$id_field]
            ];
            break;

        case 'delete':
            $table = sanitize_input($conn, $_POST['table']);
            $id_field = sanitize_input($conn, $_POST['id_field']);
            $id = sanitize_input($conn, $_POST['id']);
            
            // Validate table name to prevent SQL injection
            $valid_tables = ['departments', 'students', 'faculties', 'notices', 'events'];
            if (!in_array($table, $valid_tables)) {
                die(json_encode(['success' => false, 'message' => 'Invalid table']));
            }
            
            $query = "DELETE FROM $table WHERE $id_field = '$id'";
            if (mysqli_query($conn, $query)) {
                $response = ['success' => true, 'message' => 'Record deleted successfully'];
            } else {
                $response = ['success' => false, 'message' => 'Error deleting record: ' . mysqli_error($conn)];
            }
            break;

        case 'search':
            $term = mysqli_real_escape_string($conn, $_POST['term']);
            $response = array();

            // Search departments
            $query = "SELECT dept_name as name FROM departments WHERE dept_name LIKE '%$term%' LIMIT 5";
            $result = mysqli_query($conn, $query);
            $response['departments'] = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $response['departments'][] = $row;
            }

            // Search students
            $query = "SELECT first_name, last_name FROM students WHERE first_name LIKE '%$term%' OR last_name LIKE '%$term%' LIMIT 5";
            $result = mysqli_query($conn, $query);
            $response['students'] = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $response['students'][] = $row;
            }

            // Search faculty
            $query = "SELECT first_name, last_name FROM faculties WHERE first_name LIKE '%$term%' OR last_name LIKE '%$term%' LIMIT 5";
            $result = mysqli_query($conn, $query);
            $response['faculty'] = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $response['faculty'][] = $row;
            }

            // Search notices
            $query = "SELECT title FROM notices WHERE title LIKE '%$term%' OR content LIKE '%$term%' LIMIT 5";
            $result = mysqli_query($conn, $query);
            $response['notices'] = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $response['notices'][] = $row;
            }

            // Search events
            $query = "SELECT title FROM events WHERE title LIKE '%$term%' OR description LIKE '%$term%' LIMIT 5";
            $result = mysqli_query($conn, $query);
            $response['events'] = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $response['events'][] = $row;
            }

            $response['success'] = true;
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log("Error occurred: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(400);
    $response = ['success' => false, 'message' => $e->getMessage()];
}

error_log("Sending response: " . json_encode($response));
mysqli_close($conn);
echo json_encode($response);