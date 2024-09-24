<?php
// plant_list.php

session_start();
include('db.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: index.php");
    exit();
}

// Get user ID and name from session
$user_id = $_SESSION['user_id'];
$name = $_SESSION['username'];

// Fetch plants for the logged-in user
$stmt = $conn->prepare("SELECT * FROM PLANTS WHERE OWNER_ID = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$plants = $result->fetch_all(MYSQLI_ASSOC); // Fetch all plants
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant List</title>
    <link rel="stylesheet" href="plantStyle.css"> <!-- Link to external CSS -->
</head>
<body>
    <!-- Navbar with logo, logout, and add plant buttons -->
    <div class="navbar">
        <img src="logo.png" alt="Logo">
        <div class="nav-buttons">
            <a href="add_plant.php" class="add-plant">Add Plant</a>
            <a href="welcome.php" class="logout">Logout</a>
        </div>
    </div>

    <!-- Welcome message and content -->
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>

        <?php
        // Display success or error messages
        if (isset($_GET['msg'])) {
            echo '<p class="success-message">' . htmlspecialchars($_GET['msg']) . '</p>';
        }
        if (isset($_GET['error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <!-- Display plant list or message if no plants -->
        <?php if (count($plants) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Plant Name</th>
                        <th>Category</th>
                        <th>Sunlight Info</th>
                        <th>Pot Size</th>
                        <th>Fertilizer</th>
                        <th>Leaf Condition</th>
                        <th>Watering Status</th>
                        <th>Location</th>
                        <th>Species</th>
                        <th>Age</th>
                        <th>Action</th> <!-- Column for Delete button -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plants as $plant): ?>
                        <tr>
                            <td>
                                <a href="plant_photos.php?plant_id=<?php echo urlencode($plant['PLANT_ID']); ?>">
                                    <?php echo htmlspecialchars($plant['NAME']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($plant['CATEGORY']); ?></td>
                            <td><?php echo htmlspecialchars($plant['SUNLIGHT_INFO']); ?></td>
                            <td><?php echo htmlspecialchars($plant['POT_SIZE']); ?></td>
                            <td><?php echo htmlspecialchars($plant['FERTILIZER']); ?></td>
                            <td><?php echo htmlspecialchars($plant['LEAF_CONDITION']); ?></td>
                            <td><?php echo htmlspecialchars($plant['WATERING_STATUS']); ?></td>
                            <td><?php echo htmlspecialchars($plant['LOCATION']); ?></td>
                            <td><?php echo htmlspecialchars($plant['SPECIES']); ?></td>
                            <td><?php echo htmlspecialchars($plant['AGE']); ?></td>
                            <td>
                                <form action="delete_plant.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this plant?');">
                                    <input type="hidden" name="plant_id" value="<?php echo htmlspecialchars($plant['PLANT_ID']); ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-plants">No plant is added yet. Add your plants to track.</p>
        <?php endif; ?>
    </div>
</body>
</html>

