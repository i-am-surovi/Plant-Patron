<?php
// delete_plantPhoto.php

session_start();
include('db.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if plant_id and photo_id are set
    if (isset($_POST['plant_id']) && isset($_POST['photo_id'])) {
        $plant_id = intval($_POST['plant_id']);
        $photo_id = intval($_POST['photo_id']);
        $user_id = $_SESSION['user_id'];

        // Verify that the plant belongs to the user
        $verify_plant_stmt = $conn->prepare("SELECT PLANT_ID FROM PLANTS WHERE PLANT_ID = ? AND OWNER_ID = ?");
        if (!$verify_plant_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $verify_plant_stmt->bind_param('ii', $plant_id, $user_id);
        $verify_plant_stmt->execute();
        $verify_plant_result = $verify_plant_stmt->get_result();

        if ($verify_plant_result->num_rows > 0) {
            // Verify that the photo belongs to the plant
            $verify_photo_stmt = $conn->prepare("SELECT PLANT_PHOTO_URL FROM PLANT_PHOTOS WHERE PHOTO_ID = ? AND PLANT_ID = ?");
            if (!$verify_photo_stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $verify_photo_stmt->bind_param('ii', $photo_id, $plant_id);
            $verify_photo_stmt->execute();
            $verify_photo_result = $verify_photo_stmt->get_result();

            if ($verify_photo_result->num_rows > 0) {
                $photo = $verify_photo_result->fetch_assoc();
                $photo_url = $photo['PLANT_PHOTO_URL'];

                // Begin transaction
                $conn->begin_transaction();

                try {
                    // Delete the photo record from the database
                    $delete_photo_stmt = $conn->prepare("DELETE FROM PLANT_PHOTOS WHERE PHOTO_ID = ? AND PLANT_ID = ?");
                    if (!$delete_photo_stmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }
                    $delete_photo_stmt->bind_param('ii', $photo_id, $plant_id);
                    if (!$delete_photo_stmt->execute()) {
                        throw new Exception("Execute failed: " . $delete_photo_stmt->error);
                    }
                    $delete_photo_stmt->close();

                    // Delete the photo file from the server
                    if (file_exists($photo_url)) {
                        unlink($photo_url);
                    }

                    // Commit transaction
                    $conn->commit();

                    // Redirect back with success message
                    header("Location: plant_photos.php?plant_id={$plant_id}&msg=" . urlencode("Photo deleted successfully."));
                    exit();
                } catch (Exception $e) {
                    // Rollback transaction
                    $conn->rollback();
                    // Redirect back with error message
                    header("Location: plant_photos.php?plant_id={$plant_id}&error=" . urlencode("Failed to delete photo. Error: " . $e->getMessage()));
                    exit();
                }
            } else {
                // Photo does not exist or does not belong to the plant
                header("Location: plant_photos.php?plant_id={$plant_id}&error=" . urlencode("Photo not found."));
                exit();
            }
        } else {
            // Plant does not exist or does not belong to the user
            header("Location: plant_list.php?error=Plant+not+found");
            exit();
        }
    } else {
        // plant_id or photo_id not set
        header("Location: plant_list.php?error=Invalid+request");
        exit();
    }
} else {
    // Invalid request method
    header("Location: plant_list.php?error=Invalid+request");
    exit();
}
?>
