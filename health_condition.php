<?php
session_start();
include 'db.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID
$plant_id = $_GET['plant_id'] ?? null; // Plant ID from query string

// Check if the plant ID is provided
if (!$plant_id) {
    echo "Error: Plant ID not provided.";
    exit();
}

// Fetch the plant's health conditions for the logged-in user
$stmt = $conn->prepare("
    SELECT * FROM HEALTH_CONDITION 
    WHERE PLANT_ID = ? 
    AND PLANT_ID IN (SELECT PLANT_ID FROM PLANTS WHERE OWNER_ID = ?)
");
$stmt->bind_param('ii', $plant_id, $user_id);
$stmt->execute();
$health_conditions = $stmt->get_result();

// Fetch the plant's name for display purposes
$plant_stmt = $conn->prepare("SELECT NAME FROM PLANTS WHERE PLANT_ID = ? AND OWNER_ID = ?");
$plant_stmt->bind_param('ii', $plant_id, $user_id);
$plant_stmt->execute();
$plant = $plant_stmt->get_result()->fetch_assoc();

if (!$plant) {
    echo "Error: The plant does not exist or does not belong to you.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Condition - <?= htmlspecialchars($plant['NAME']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1>Health Condition for <?= htmlspecialchars($plant['NAME']); ?></h1>
        <a href="plant_list.php" class="btn btn-secondary mb-3">Back to Plant List</a>

        <!-- Button to add a new health condition -->
        <a href="add_health.php?plant_id=<?= $plant_id; ?>" class="btn btn-success mb-3">Add Health Condition</a>

        <?php if ($health_conditions->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Disease</th>
                        <th>Treatment Status</th>
                        <th>Ongoing Medication</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($health = $health_conditions->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($health['DISEASE']); ?></td>
                            <td><?= htmlspecialchars($health['TREATMENT_STATUS']); ?></td>
                            <td><?= htmlspecialchars($health['ONGOING_MEDICATION']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No health conditions recorded for this plant.</p>
        <?php endif; ?>
    </div>
</body>
</html>

