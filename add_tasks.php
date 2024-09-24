<?php
// Include the database connection file
include 'db.php';
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve the form data
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $task_date = $_POST['task_date'];
    $task_time = $_POST['task_time'];
    $plant_id = $_POST['plant_id'];

    // Retrieve the user ID from the session (assuming the user is logged in and their ID is stored in the session)
    $user_id = $_SESSION['user_id'];

    // Prepare the SQL query to insert the task into the TO_DO_LIST table
    $stmt = $conn->prepare("INSERT INTO TO_DO_LIST 
        (ID, PLANT_ID, TASK_TIME, TASK_DATE, TASK_NAME, TASK_DESCRIPTION, DONE_TASKS, DUE_TASKS) 
        VALUES 
        (?, ?, ?, ?, ?, ?, 0, 1)");

    // Bind parameters to the SQL query
    $stmt->bind_param('iissss', $user_id, $plant_id, $task_time, $task_date, $task_name, $task_description);

    // Execute the query and check for errors
    if ($stmt->execute()) {
        echo "Task scheduled successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <header class="d-flex justify-content-between my-4">
            <h1>Schedule Task</h1>
            <div>
                <a href="task_list.php" class="btn btn-primary">Back to Task List</a>
            </div>
        </header>

        <form action="add_task.php" method="POST">
            <div class="form-group my-4">
                <label for="plant_id">Plant ID:</label>
                <input type="text" class="form-control" name="plant_id" required>
            </div>
            <div class="form-group my-4">
                <label for="task_name">Task Name:</label>
                <input type="text" class="form-control" name="task_name" required>
            </div>
            <div class="form-group my-4">
                <label for="task_description">Task Description:</label>
                <textarea class="form-control" name="task_description" rows="4" required></textarea>
            </div>
            <div class="form-group my-4">
                <label for="task_date">Task Date:</label>
                <input type="date" class="form-control" name="task_date" required>
            </div>
            <div class="form-group my-4">
                <label for="task_time">Task Time:</label>
                <input type="time" class="form-control" name="task_time" required>
            </div>
            <div class="form-group my-4">
                <input type="submit" value="Schedule Task" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
