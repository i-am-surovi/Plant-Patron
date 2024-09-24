<?php
include 'db.php';  // Including database connection
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login page if not logged in
    exit();
}

// Check if TASK_ID is provided in the URL
if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];

    // Prepare DELETE SQL statement
    $stmt = $conn->prepare("DELETE FROM TO_DO_LIST WHERE TASK_ID = ? AND ID = ?");
    $stmt->bind_param('ii', $task_id, $_SESSION['user_id']);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to task view page after successful deletion
        header("Location: view_tasks.php");
        exit();
    } else {
        echo "Error deleting task: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "No task ID provided for deletion.";
}

$conn->close();
?>
