<?php
ini_set('session.cookie_lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);
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

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "User") {
    header("Location: /htp2/login.php");
    exit();
}

// Get admin details
$admin_name = $_SESSION["user_id"];
$admin_data = null;

// Prepare query
$stmt = $conn->prepare("SELECT Full_Name, Email FROM users WHERE User_Name = ?");
if ($stmt === false) {
    // If the prepare statement fails, show the error and exit
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $admin_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin_data = $result->fetch_assoc();
} else {
    echo "User data not found!";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background-color: #f5f0fc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header .logo img {
            max-height: 50px;
        }

        header nav {
            display: flex;
            gap: 20px;
        }

        header nav a {
            text-decoration: none;
            color: #4a4a8a;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        header nav a:hover {
            background-color: #e6dbff;
        }

        header .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        header .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ccc;
        }

        /* Breadcrumb */
        .breadcrumb {
            padding: 10px 30px;
            font-size: 0.9em;
            color: #666;
            background-color: #f5f0fc;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #4a4a8a;
        }

        /* Main Section */
        main {
            padding: 40px 30px;
            text-align: center;
        }

        main h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container button {
            display: block;
            width: 300px;
            padding: 15px;
            margin: 10px auto;
            font-size: 1em;
            color: #fff;
            background-color: #5e3baf;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #7359b0;
            transform: scale(1.1);
        }

        /* Footer */
        footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px 30px;
            background-color: #f5f0fc;
            margin-top: 40px;
        }

        footer div {
            flex: 1;
            padding: 10px;
        }

        footer div h3 {
            margin-bottom: 15px;
            font-size: 1.1em;
            color: #4a4a8a;
        }

        footer div p,
        footer div a {
            font-size: 0.9em;
            color: #333;
            text-decoration: none;
            margin: 5px 0;
            display: block;
        }

        footer div a:hover {
            color: #4a4a8a;
        }

        footer .social-icons {
            display: flex;
            gap: 10px;
        }

        footer .social-icons a img {
            width: 30px;
            height: 30px;
        }

        .logout-button {
            position: fixed;
            bottom: 60px;
            right: 30px;
            background-color: #5e3baf;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .logout-button:hover {
            background-color: #7359b0;
            transform: scale(1.1);
        }
    </style>
     <script>
        // Check session status to ensure the user is logged in
        function checkSession() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "/CSE311 Project - Huts for Paws/session_check.php", true); // Check session status
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log("Session refreshed successfully");
                } else {
                    console.error("Failed to refresh session");
                }
            };
            xhr.send();
        }

        // Periodically check session every 5 minutes
        setInterval(checkSession, 300000); // Check every 5 minutes
    </script>
</head>

<body>
    <header>
        <div class="logo">
            <a href="http://localhost/htp2/user_dashboard.php">
                <img src="Images/Logo.png" alt="Huts for Paws Logo">
             </a>
        </div>

        <nav>
            <a href="http://localhost/htp2/animal.php">Adopt</a>
            <a href="http://localhost/htp2/rehome_requests.php">Rehome</a>
            <a href="http://localhost/htp2/dev_info.html">About Us</a>
        </nav>
        <div class="profile">
            <?php if (!empty($admin_data['Profile_Picture'])): ?>
                <img src="<?php echo htmlspecialchars($admin_data['Profile_Picture']); ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="https://via.placeholder.com/100" alt="Profile Picture">
            <?php endif; ?>
            <a href="edit_profile_user.php?user=<?php echo urlencode($admin_name); ?>">
                <?php echo htmlspecialchars($admin_data['Full_Name']); ?>
            </a>
        </div>
    </header>

    <div class="breadcrumb">
        <a href="#">Home</a> &gt; <a href="#">User Dashboard</a>
    </div>

    <main>
        <h1>Welcome, <?php echo htmlspecialchars($admin_data['Full_Name']); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($admin_data['Email']); ?></p>
        <div class="button-container">
            <button>Applications and Requests &gt; </button>
        </div>
    </main>

    <form action="logout.php" method="post">
        <button type="submit" class="logout-button">Logout</button>
    </form>

    <footer>
        <div>
            <h3>How Can We Help?</h3>
            <p><a href="#">Adopt a pet</a></p>
            <p><a href="#">Rehome a pet</a></p>
            <p><a href="#">Adopt FAQ's</a></p>
            <p><a href="#">Rehome FAQ's</a></p>
        </div>
        <div>
            <h3>Contact Us</h3>
            <p>CSE311L</p>
            <p>Summer 2024</p>
            <p>Group 08</p>
        </div>
        <div>
            <h3>Keep In Touch With Us</h3>
            <p>hutsforpaws@gmail.com</p>
            <form>
                <input type="email" placeholder="E-mail Address" style="padding: 5px; border-radius: 5px;">
                <button
                    style="padding: 5px 10px; border: none; background-color: #5e3baf; color: white; border-radius: 5px;">Subscribe</button>
            </form>
            
        </div>
    </footer>
</body>

</html>