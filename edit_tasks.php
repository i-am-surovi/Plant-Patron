<?php
include 'db.php';
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the TASK_ID from the URL
$task_id = $_GET['task_id'];

// Fetch the task details from the TO_DO_LIST table
$stmt = $conn->prepare("SELECT * FROM TO_DO_LIST WHERE TASK_ID = ?");
$stmt->bind_param('i', $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Task not found!";
    exit();
}

$task = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plant_id = $_POST['plant_id'];
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $task_date = $_POST['task_date'];
    $task_time = $_POST['task_time'];
    $done_tasks = isset($_POST['done_tasks']) ? 1 : 0;

    // Update the task in the database
    $update_stmt = $conn->prepare("UPDATE TO_DO_LIST SET PLANT_ID = ?, TASK_NAME = ?, TASK_DESCRIPTION = ?, TASK_DATE = ?, TASK_TIME = ?, DONE_TASKS = ? WHERE TASK_ID = ?");
    $update_stmt->bind_param('issssii', $plant_id, $task_name, $task_description, $task_date, $task_time, $done_tasks, $task_id);

    if ($update_stmt->execute()) {
        header("Location: view_tasks.php");
        exit();
    } else {
        echo "Error updating task: " . $update_stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <header class="d-flex justify-content-between my-4">
            <h1>Edit Task</h1>
            <div>
                <a href="view_tasks.php" class="btn btn-primary">Back to Task List</a>
            </div>
        </header>

        <form action="edit_task.php?task_id=<?php echo $task_id; ?>" method="POST">
            <div class="form-group my-4">
                <label for="plant_id">Plant ID:</label>
                <input type="number" class="form-control" name="plant_id" value="<?php echo $task['PLANT_ID']; ?>" required>
            </div>
            <div class="form-group my-4">
                <label for="task_name">Task Name:</label>
                <input type="text" class="form-control" name="task_name" value="<?php echo $task['TASK_NAME']; ?>" required>
            </div>
            <div class="form-group my-4">
                <label for="task_description">Task Description:</label>
                <textarea class="form-control" name="task_description" required><?php echo $task['TASK_DESCRIPTION']; ?></textarea>
            </div>
            <div class="form-group my-4">
                <label for="task_date">Task Date:</label>
                <input type="date" class="form-control" name="task_date" value="<?php echo $task['TASK_DATE']; ?>" required>
            </div>
            <div class="form-group my-4">
                <label for="task_time">Task Time:</label>
                <input type="time" class="form-control" name="task_time" value="<?php echo $task['TASK_TIME']; ?>" required>
            </div>
            <div class="form-group my-4">
                <label for="done_tasks">Mark as Completed:</label>
                <input type="checkbox" name="done_tasks" <?php echo $task['DONE_TASKS'] ? 'checked' : ''; ?>>
            </div>
            <div class="form-group my-4">
                <input type="submit" value="Update Task" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
