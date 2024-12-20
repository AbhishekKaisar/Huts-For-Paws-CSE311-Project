<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Huts_for_Paws_2";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $conn->real_escape_string($_POST["user_name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $password = $conn->real_escape_string($_POST["password"]);

    // SQL query to fetch user or admin
    $stmt = $conn->prepare("
        SELECT 'User' AS role, User_Name, Email, Password 
        FROM users 
        WHERE User_Name = ? AND Email = ?
        UNION 
        SELECT 'Admin' AS role, User_Name, Email, Password 
        FROM admin 
        WHERE User_Name = ? AND Email = ?
    ");
    $stmt->bind_param("ssss", $user_name, $email, $user_name, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user["role"] == "User") {
            // Verify hashed password for users
            if (password_verify($password, $user["Password"])) {
                $_SESSION["user_id"] = $user["User_Name"];
                $_SESSION["user_role"] = "User";
                // Redirect to User Dashboard
                header("Location: /htp2/user_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } elseif ($user["role"] == "Admin") {
            // Plain-text password comparison for admins
            if ($password === $user["Password"]) {
                $_SESSION["user_id"] = $user["User_Name"];
                $_SESSION["user_role"] = "Admin";
                // Redirect to Admin Dashboard
                header("Location: /htp2/admin_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        }
    } else {
        $error_message = "User not found. Please check your credentials.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            width: 620px;
            height: 535px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .image-section {
            width: 315px;
            background: url("Images/login Now with Logo.png") no-repeat center center;

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

        .form-section button {
            width: 100%;
            padding: 12px;
            background-color: #6a5acd;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-section button:hover {
            background-color: #5845c7;
        }

        .form-section p {
            color: red;
            margin-top: 10px;
        }
    </style>
    <script>
        if (localStorage.getItem('authToken')) {
            const role = localStorage.getItem('userRole');
            if (role === 'User') {
                window.location.href = "/htp2/user_dashboard.php";
            } else if (role === 'Admin') {
                window.location.href = "/htp2/admin_dashboard.php";
            }
        }

        // Login function
        async function login(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                // Store auth token and role in localStorage
                localStorage.setItem('authToken', result.authToken);
                localStorage.setItem('userRole', result.role);

                // Redirect based on role
                if (result.role === 'User') {
                    window.location.href = "/htp2/user_dashboard.php";
                } else if (result.role === 'Admin') {
                    window.location.href = "/htp2/admin_dashboard.php";
                }
            } else {
                // Display error message
                document.getElementById('error-message').textContent = result.message || "An error occurred.";
            }
        }
    </script>
</head>

<body>
    <div class="create-account-widget">
        <div class="image-section">
            <div class="overlay-text">Log In</div>
        </div>
        <div class="form-section">
            <h2>Log In to your Account</h2>
            <form method="post" action="">
                <input type="text" name="user_name" placeholder="User Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <?php if (!empty($error_message)): ?>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
