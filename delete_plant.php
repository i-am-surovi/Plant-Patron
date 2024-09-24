<?php
session_start();
include('db.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['plant_id'])) {
        $plant_id = $_POST['plant_id'];
        $user_id = $_SESSION['user_id'];

        // Verify that the plant belongs to the user
        $verify_stmt = $conn->prepare("SELECT PLANT_ID FROM PLANTS WHERE PLANT_ID = ? AND OWNER_ID = ?");
        $verify_stmt->bind_param('ii', $plant_id, $user_id);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();

        if ($verify_result->num_rows > 0) {
            // Plant exists and belongs to the user, proceed to delete

            // First, delete the associated plant photos from PLANT_PHOTOS table
            $delete_photos_stmt = $conn->prepare("DELETE FROM PLANT_PHOTOS WHERE PLANT_ID = ?");
            $delete_photos_stmt->bind_param('i', $plant_id);
            $delete_photos_stmt->execute();
            $delete_photos_stmt->close();

            // Now delete the plant from the PLANTS table
            $delete_stmt = $conn->prepare("DELETE FROM PLANTS WHERE PLANT_ID = ?");
            $delete_stmt->bind_param('i', $plant_id);
            if ($delete_stmt->execute()) {
                // Deletion successful
                $delete_stmt->close();
                $verify_stmt->close();
                header("Location: plant_list.php?msg=Plant+and+associated+photos+deleted+successfully");
                exit();
            } else {
                // Deletion failed
                $delete_stmt->close();
                $verify_stmt->close();
                header("Location: plant_list.php?error=Failed+to+delete+plant");
                exit();
            }
        } else {
            // Plant does not exist or does not belong to the user
            header("Location: plant_list.php?error=Plant+not+found");
            exit();
        }
    } else {
        // plant_id not set
        header("Location: plant_list.php?error=Invalid+request");
        exit();
    }
} else {
    // Invalid request method
    header("Location: plant_list.php?error=Invalid+request");
    exit();
}
?>


