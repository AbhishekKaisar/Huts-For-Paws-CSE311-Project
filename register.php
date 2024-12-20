<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
ob_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Huts_for_Paws_2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $full_name = $conn->real_escape_string($_POST["full_name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $password = $conn->real_escape_string($_POST["password"]);
    $user_name = $conn->real_escape_string($_POST["user_name"]);
    $phone = $conn->real_escape_string($_POST["phone"]);
    $address = $conn->real_escape_string($_POST["address"]);

    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        echo "Passwords do not match";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $email_check = "SELECT * FROM `users` WHERE Email = '$email' OR User_Name = '$user_name'";
    if (($conn->query($email_check))->num_rows > 0) {
        echo "Error: Email or Username aready exists.";
        exit;
    }

    $sql = "INSERT INTO users (Full_name, Email, Password, User_Name, Phone, Address)
            VALUES ('$full_name', '$email', '$hashed_password', '$user_name', '$phone', '$address')";

    if ($conn->query($sql) === TRUE) {
        header("Location: /htp2/user_dashboard.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #e8f0fe;
        }

        .create-account-widget {
            display: flex;
            width: 800px;
            height: auto;
            background-color: whitesmoke;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .image-section {
            width: 315px;
            background: url("Images/Register Now with Logo.png") no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .image-section .overlay-text {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .form-section {
            flex: 1;
            padding: 40px;
        }

        .form-section h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-section input[type="text"],
        .form-section input[type="email"],
        .form-section input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-section input[type="checkbox"] {
            margin-right: 10px;
        }

        .form-section label {
            font-size: 14px;
            color: #554;
        }

        .form-section .terms {
            margin-bottom: 20px;
        }

        .form-section button {
            width: 100%;
            padding: 12px;
            background-color: #6a5acd;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-section .social-login {
            margin-top: 20px;
            text-align: center;
        }

        .form-section .social-login img {
            margin: 0 10px;
            cursor: pointer;
            width: 30px;
            height: 30px;
        }

        .form-section p {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }

        .form-section a {
            color: #6a5acd;
            text-decoration: none;
        }

        .name-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .name-container input {
            width: 48%;
        }

        .password-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .password-container input {
            width: 48%;
        }
    </style>
</head>

<body>
    <div class="create-account-widget">
        <div class="image-section">
            <div class="overlay-text">Register Now</div>
        </div>
        <div class="form-section">
            <h2>Create Your Account</h2>
            <form method="post" action="">
                <div class="name-container">
                    <input type="text" name="full_name" placeholder="Full_Name" required>
                    <input type="text" name="user_name" placeholder="User_Name" required>
                </div>
                <div class = "password-container">
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="text" name="address" placeholder="Address" required>
                <div class="terms">
                    <input type="checkbox" name="terms" required>
                    <label for="terms">I agree to all <a href="#terms">Terms and Conditions</a></label>
                </div>
                <button type="submit">Create Account</button>
            </form>
            <p>Already have an account? <br> <a href="http://localhost/htp2/login.php">Sign in</a></p>
        </div>
    </div>
</body>

</html>