<?php
// plant_photos.php

session_start();
include('db.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if plant_id is provided
if (isset($_GET['plant_id'])) {
    $plant_id = intval($_GET['plant_id']);
    $user_id = $_SESSION['user_id'];

    // Verify that the plant belongs to the user
    $verify_stmt = $conn->prepare("SELECT NAME FROM PLANTS WHERE PLANT_ID = ? AND OWNER_ID = ?");
    if (!$verify_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $verify_stmt->bind_param('ii', $plant_id, $user_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();

    if ($verify_result->num_rows > 0) {
        $plant = $verify_result->fetch_assoc();
        $plant_name = $plant['NAME'];

        // Fetch photos related to the plant
        $photos_stmt = $conn->prepare("SELECT PLANT_PHOTO_URL, NOTES, DATE_OF_PHOTO_TAKEN FROM PLANT_PHOTOS WHERE PLANT_ID = ?");
        if (!$photos_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $photos_stmt->bind_param('i', $plant_id);
        $photos_stmt->execute();
        $photos_result = $photos_stmt->get_result();
        $photos = $photos_result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Plant not found or does not belong to the user
        header("Location: plant_list.php?error=Plant+not+found");
        exit();
    }
} else {
    // plant_id not provided
    header("Location: plant_list.php?error=Invalid+plant+ID");
    exit();
}

// Handle form submission for photo upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['plant_photo'])) {
    $upload_dir = 'uploads/';
    
    // Ensure the uploads directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $image_file_type = strtolower(pathinfo($_FILES['plant_photo']['name'], PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES['plant_photo']['tmp_name']);
    if ($check === false) {
        $error = "File is not an image.";
    }

    // Check file size (limit to 5MB)
    if ($_FILES['plant_photo']['size'] > 5 * 1024 * 1024) {
        $error = "Sorry, your file is too large.";
    }

    // Allow only certain file formats
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($image_file_type, $allowed_types)) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    // Check if there was an error
    if (isset($error)) {
        // Redirect back with error
        header("Location: plant_photos.php?plant_id={$plant_id}&error=" . urlencode($error));
        exit();
    }

    // Generate a unique file name to prevent overwriting
    $new_filename = uniqid('plant_', true) . '.' . $image_file_type;
    $target_file = $upload_dir . $new_filename;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['plant_photo']['tmp_name'], $target_file)) {
        // Insert into database
        $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        $date_of_photo_taken = isset($_POST['date_of_photo']) ? $_POST['date_of_photo'] : null;

        $insert_stmt = $conn->prepare("INSERT INTO PLANT_PHOTOS (PLANT_ID, PLANT_PHOTO_URL, NOTES, DATE_OF_PHOTO_TAKEN) VALUES (?, ?, ?, ?)");
        if (!$insert_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $insert_stmt->bind_param('isss', $plant_id, $target_file, $notes, $date_of_photo_taken);
        if ($insert_stmt->execute()) {
            // Success - Redirect to plant list page with success message
            header("Location: plant_list.php?msg=" . urlencode("Photo uploaded successfully for {$plant_name}."));
            exit();
        } else {
            // Database insertion failed
            header("Location: plant_photos.php?plant_id={$plant_id}&error=" . urlencode("Failed to upload photo."));
            exit();
        }
    } else {
        // File upload failed
        header("Location: plant_photos.php?plant_id={$plant_id}&error=" . urlencode("Failed to upload photo."));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($plant_name); ?> - Photos</title>
    <link rel="stylesheet" href="photos.css"> <!-- Link to external CSS -->
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <img src="logo.png" alt="Logo" class="logo">
        <div class="nav-buttons">
            <a href="growth.php?plant_id=<?php echo urlencode($plant_id); ?>" class="view-growth-button">View Growth Record</a>
            <a href="plant_photos.php?plant_id=<?php echo urlencode($plant_id); ?>" class="add-photo-button">Add Photo</a>
        </div>
    </div>

    <!-- Container for content -->
    <div class="container">
        <!-- Plant Name -->
        <h1><?php echo htmlspecialchars($plant_name); ?></h1>

        <!-- Display plant details in a table -->
        <table class="plant-details">
            <tr>
                <th>Plant ID</th>
                <td><?php echo htmlspecialchars($plant_id); ?></td>
            </tr>
            <!-- Add more plant details as needed -->
            <!-- Example:
            <tr>
                <th>Category</th>
                <td><?php echo htmlspecialchars($plant['CATEGORY']); ?></td>
            </tr>
            -->
        </table>

        <?php
        // Display success or error messages
        if (isset($_GET['msg'])) {
            echo '<p class="success-message">' . htmlspecialchars($_GET['msg']) . '</p>';
        }
        if (isset($_GET['error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <!-- Display existing photos -->
        <?php if (count($photos) > 0): ?>
            <div class="photos-gallery">
                <?php foreach ($photos as $photo): ?>
                    <div class="photo-card">
                        <img src="<?php echo htmlspecialchars($photo['PLANT_PHOTO_URL']); ?>" alt="Plant Photo">
                        <?php if (!empty($photo['NOTES'])): ?>
                            <p class="photo-notes"><?php echo htmlspecialchars($photo['NOTES']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($photo['DATE_OF_PHOTO_TAKEN'])): ?>
                            <p class="photo-date">Photo taken on: <?php echo htmlspecialchars($photo['DATE_OF_PHOTO_TAKEN']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-photos">No Photos is added yet. Add one to track growth.</p>
        <?php endif; ?>

        <!-- Upload Photo Form -->
        <div class="upload-form">
            <h2>Upload a New Photo</h2>
            <form action="plant_photos.php?plant_id=<?php echo urlencode($plant_id); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="plant_photo">Select Image:</label>
                    <input type="file" name="plant_photo" id="plant_photo" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="notes">Notes (optional):</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Enter any notes about the photo..."></textarea>
                </div>
                <div class="form-group">
                    <label for="date_of_photo">Date Photo Was Taken:</label>
                    <input type="date" name="date_of_photo" id="date_of_photo">
                </div>
                <button type="submit" class="upload-button">Upload Photo</button>
            </form>
        </div>
    </div>
</body>
</html>
