<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $plant_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Fetch the plant details
    $stmt = $conn->prepare("SELECT * FROM plants WHERE PLANT_ID = ? AND OWNER_ID = ?");
    $stmt->bind_param('ii', $plant_id, $user_id);
    $stmt->execute();
    $plant = $stmt->get_result()->fetch_assoc();

    if (!$plant) {
        echo "Plant not found or you don't have permission to view this plant.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Plant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1>Plant Details: <?= htmlspecialchars($plant['NAME']); ?></h1>
        <p><strong>Category:</strong> <?= htmlspecialchars($plant['CATEGORY']); ?></p>
        <p><strong>Sunlight Info:</strong> <?= htmlspecialchars($plant['SUNLIGHT_INFO']); ?></p>
        <p><strong>Pot Size:</strong> <?= htmlspecialchars($plant['POT_SIZE']); ?></p>
        <p><strong>Fertilizer:</strong> <?= htmlspecialchars($plant['FERTILIZER']); ?></p>
        <p><strong>Leaf Condition:</strong> <?= htmlspecialchars($plant['LEAF_CONDITION']); ?></p>
        <p><strong>Watering Status:</strong> <?= htmlspecialchars($plant['WATERING_STATUS']); ?></p>
        <p><strong>Expected Life Span:</strong> <?= htmlspecialchars($plant['EXPECTED_LIFE_SPAN']); ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($plant['AGE']); ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($plant['LOCATION']); ?></p>
        <p><strong>Species:</strong> <?= htmlspecialchars($plant['SPECIES']); ?></p>
        <a href="plant_list.php" class="btn btn-primary">Back to Plant List</a>
    </div>
</body>
</html>
