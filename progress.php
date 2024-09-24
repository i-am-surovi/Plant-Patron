<?php
session_start();
include 'db.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$plant_id = $_GET['plant_id'];

// Debugging: Check if plant_id exists and belongs to the user
$stmt = $conn->prepare("SELECT * FROM plants WHERE PLANT_ID = ? AND OWNER_ID = ?");
$stmt->bind_param('ii', $plant_id, $user_id);
$stmt->execute();
$plant_result = $stmt->get_result();

if ($plant_result->num_rows == 0) {
    die("Error: The plant does not exist or does not belong to you.");
}

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notes = $_POST['notes'];
    $target_dir = "uploads/";
    $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES["plant_image"]["name"]));
    $target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES["plant_image"]["tmp_name"]);
    if ($check !== false) {
        // Move the uploaded file
        if (move_uploaded_file($_FILES["plant_image"]["tmp_name"], $target_file)) {
            // Insert photo details into the database
            $stmt = $conn->prepare("INSERT INTO PLANT_PHOTOS (plant_id, plant_photo_url, notes, date_of_photo_taken) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param('iss', $plant_id, $target_file, $notes);
            if ($stmt->execute()) {
                echo "Photo uploaded successfully!";
            } else {
                echo "Error uploading photo: " . $stmt->error;
            }
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "File is not an image.";
    }
}

// Fetch all progress photos for this plant
$stmt = $conn->prepare("SELECT * FROM PLANT_PHOTOS WHERE PLANT_ID = ? ORDER BY DATE_OF_PHOTO_TAKEN DESC");
$stmt->bind_param('i', $plant_id);
$stmt->execute();
$photos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Progress</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1>Plant Progress</h1>
        <a href="plant_list.php" class="btn btn-secondary mb-3">Back to Plant List</a>

        <h3>Upload New Progress Photo</h3>
        <form action="progress.php?plant_id=<?= $plant_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="plant_image" class="form-label">Upload Plant Image:</label>
                <input type="file" name="plant_image" id="plant_image" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes (optional):</label>
                <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Upload Photo</button>
        </form>

        <h3 class="mt-5">Progress Photos</h3>
        <?php if ($photos->num_rows > 0): ?>
            <div class="row">
               <?php while ($photo = $photos->fetch_assoc()): ?>
                  <div class="col-md-4">
                     <div class="card mb-4">
                        <!-- Assuming plant_photo_url stores the relative path -->
                        <img src="<?= htmlspecialchars($photo['PLANT_PHOTO_URL']); ?>" class="card-img-top" alt="Plant Photo">
                        <div class="card-body">
                           <p class="card-text"><?= htmlspecialchars($photo['NOTES']); ?></p>
                           <p class="text-muted">Date: <?= htmlspecialchars($photo['DATE_OF_PHOTO_TAKEN']); ?></p>
                        </div>
                     </div>
                  </div>
               <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No progress photos available for this plant.</p>
        <?php endif; ?>
    </div>
</body>
</html>

