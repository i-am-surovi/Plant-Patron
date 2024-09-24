<?php
include('db.php');
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user from the `plant_owner` table by email
    $stmt = $conn->prepare("SELECT ID, NAME, PASSWORD, PHONE, DATE_JOINED FROM plant_owner WHERE EMAIL = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Assuming plain text passwords (but you should use password hashing)
        if ($user['PASSWORD'] === $password) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['username'] = $user['NAME'];
            $_SESSION['date_joined'] = $user['DATE_JOINED'];
            header("Location: plant_list.php"); // Redirect to plant list
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No user found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form_container">
        <div class="overlay"></div>

        <div class="titleDiv">
            <h1 class="title">Login</h1>
            <span class="subTitle">Welcome back!</span>
        </div>

        <!-- Display error message if login fails -->
        <?php
        if (isset($error)) {
            echo '<span class="fail">' . $error . '</span>';
        }
        ?>

        <!-- Login form -->
        <form action="" method="POST">
            <div class="rows grid">
                <!-- Email -->
                <div class="row">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <!-- Password -->
                <div class="row">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <!-- Submit Button -->
                <div class="row">
                    <input type="submit" id="submitBtn" name="submit" value="Login">
                    <span class="registerLink">Don't have an account? <a href="register.php">Register</a></span>
                </div>
            </div>
        </form>
    </div>
</body>
</html>





