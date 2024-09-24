<?php
include('db.php');

// Start session and set error reporting
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$feedback = ""; // Variable to hold success or fail message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['userName']; // Retrieving 'userName' from the form
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $date_joined = date("Y-m-d H:i:s"); // Current date and time

    // Check if the email or username already exists
    $check_sql = "SELECT * FROM plant_owner WHERE EMAIL = ? OR NAME = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $feedback = '<div class="fail">Username or email already exists</div>';
    } else {
        // Insert the user data into the `plant_owner` table
        $stmt = $conn->prepare("INSERT INTO plant_owner (NAME, EMAIL, PHONE, PASSWORD, DATE_JOINED) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $username, $email, $phone, $password, $date_joined);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id; // Store user ID in session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['date_joined'] = $date_joined;
            $feedback = '<div class="success">Account created successfully!</div>';
            header("Location: index.php"); // Redirect to a welcome page
            exit();
        } else {
            $feedback = '<div class="fail">Error: ' . $stmt->error . '</div>';
        }
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body> 

    <div class="register_container">
        <div class="overlay">
            <!-- No content -->
        </div>

        <div class="titleDiv">
            <h1 class="title">Register</h1>
            <span class="subTitle">Thanks for choosing us!</span>
        </div>

        <!-- formSection -->
         <form action="" method="POST">
            <div class="rows grid">
                <!-- User Name -->
                <div class="row">
                    <label for="username">User Name</label>
                    <input type="text" id="username" name="userName" placeholder="Enter User Name" required>
                </div>
                <!-- Email Address -->
                <div class="row">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter Your Email" required>
                </div>
                <!-- Phone Number -->
                <div class="row">
                    <label for="phone">Phone Number</label>
                    <input type="phone" id="phone" name="phone" placeholder="Enter Your Phone Number" required>
                </div>
                <!-- Password -->
                <div class="row">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
                </div>
                <!-- Submit Button -->
                <div class="row">
                    <input type="submit" id="submitBtn" name="submit" value="Register" required>
                    <span class="registerLink">Have an account already? <a href="index.php">Login</a></span>
                </div>
            </div>
         </form>

         <!-- Display Feedback (success or fail) -->
         <?php echo $feedback; ?>

    </div>

</body>
</html>
