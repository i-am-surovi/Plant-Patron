<?php
include 'db.php';
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $sunlight = $_POST['sunlight'];
    $pot_size = $_POST['pot_size'];
    $fertilizer = $_POST['fertilizer'];
    $leaf_condition = $_POST['leaf_condition'];
    $watering_status = $_POST['watering_status'];
    $expected_life_span = $_POST['expected_life_span'];
    $age = $_POST['age'];
    $location = $_POST['location'];
    $species = $_POST['species'];
    $owner_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO plants 
        (OWNER_ID, NAME, CATEGORY, SUNLIGHT_INFO, POT_SIZE, FERTILIZER, LEAF_CONDITION, WATERING_STATUS, EXPECTED_LIFE_SPAN, AGE, LOCATION, SPECIES) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param('isssssssssss', $owner_id, $name, $category, $sunlight, $pot_size, $fertilizer, $leaf_condition, $watering_status, $expected_life_span, $age, $location, $species);

    if ($stmt->execute()) {
        echo "Plant added successfully!";
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
    <title>Add New Plant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <header class="d-flex justify-content-between my-4">
            <h1>Add New Plant</h1>
            <div>
                <a href="plant_list.php" class="btn btn-primary">Back to Plant List</a>
            </div>
        </header>

        <form action="add_plant.php" method="POST">
            <div class="form-group my-4">
                <label for="name">Plant Name:</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group my-4">
                <label for="category">Category:</label>
                <input type="text" class="form-control" name="category" required>
            </div>
            <div class="form-group my-4">
                <label for="sunlight">Sunlight Info:</label>
                <input type="text" class="form-control" name="sunlight" required>
            </div>
            <div class="form-group my-4">
                <label for="pot_size">Pot Size:</label>
                <input type="text" class="form-control" name="pot_size" required>
            </div>
            <div class="form-group my-4">
                <label for="fertilizer">Fertilizer:</label>
                <input type="text" class="form-control" name="fertilizer" required>
            </div>
            <div class="form-group my-4">
                <label for="leaf_condition">Leaf Condition:</label>
                <input type="text" class="form-control" name="leaf_condition" required>
            </div>
            <div class="form-group my-4">
                <label for="watering_status">Watering Status:</label>
                <input type="text" class="form-control" name="watering_status" required>
            </div>
            <div class="form-group my-4">
                <label for="expected_life_span">Expected Life Span:</label>
                <input type="text" class="form-control" name="expected_life_span" required>
            </div>
            <div class="form-group my-4">
                <label for="age">Age of Plant:</label>
                <input type="text" class="form-control" name="age" required>
            </div>
            <div class="form-group my-4">
                <label for="location">Location:</label>
                <input type="text" class="form-control" name="location" required>
            </div>
            <div class="form-group my-4">
                <label for="species">Species:</label>
                <input type="text" class="form-control" name="species" required>
            </div>
            <div class="form-group my-4">
                <input type="submit" value="Add Plant" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
