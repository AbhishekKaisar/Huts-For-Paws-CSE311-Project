<?php
session_start();
//$user_name = $_SESSION["user_name"];

$user_name = $_GET['user'] ?? $_SESSION['user_name'] ?? null;

// Validate that the username exists
if (!$user_name) {
    die("Error: User not specified or session expired.");
}

$user_name = $_GET['user'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Huts_for_Paws_2";
";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email_query = "SELECT Email FROM users WHERE User_Name = ?";
$email_stmt = $conn->prepare($email_query);
$email_stmt->bind_param("s", $user_name);
$email_stmt->execute();
$email_result = $email_stmt->get_result();
$email_data = $email_result->fetch_assoc();
$email_stmt->close();

if (!$email_data) {
    die("Error: User not found or no email associated with this account.");
}

$email = $email_data['Email'];

// Default values for form
$new_email = '';
$new_phone = '';
$new_briefing = '';
$profile_picture = null;

// Get current profile data
$sql = "
    SELECT User_Name, Email, Phone, Address, Full_Name, Personal_Briefing 
    FROM users 
    WHERE User_Name = ? AND Email = ?
";

$stmt = $conn->prepare($sql);

// Debugging: Check if the preparation failed
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);  // Output error message for debugging
}

$stmt->bind_param("ss", $user_name, $email);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

if (!$profile) {
    die("Error: Unable to fetch profile data.");
}

// Check if the profile data exists
if ($profile === null) {
    $profile = [
        'Email' => '',
        'Phone' => '',
        'Personal_Briefing' => '',
        'Profile_Picture' => null,
    ];
}

// Set default profile picture if none exists
$profile_picture = $profile['Profile_Picture'] ?? null;
if ($profile_picture === null) {
    // Use a placeholder image if no profile picture exists
    $profile_picture = "https://via.placeholder.com/100";
}

// If form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_briefing = $_POST['personal_briefing'] ?? '';
    $remove_picture = isset($_POST['remove_picture']) ? 1 : 0;
    $remove_briefing = isset($_POST['remove_briefing']) ? 1 : 0;

    $profile_picture = null;

    // Handle profile picture upload (if provided)
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    } elseif ($remove_picture) {
        $profile_picture = null; // Remove the picture if checkbox is checked
    }

    // Update profile in the database
    $sql_update = "
        UPDATE users
        SET Email = ?, Phone = ?, Personal_Briefing = ?, Profile_Picture = ? 
        WHERE User_Name = ? AND Email = ?
    ";

    $stmt_update = $conn->prepare($sql_update);

    // Debugging: Check if the preparation failed
    if ($stmt_update === false) {
        die('MySQL prepare error: ' . $conn->error);  // Output error message for debugging
    }

    $null = null;

    try {
        $conn->begin_transaction();
        $stmt_update->bind_param(
            "ssssss",
            $new_email,
            $new_phone,
            $new_briefing,
            $profile_picture,
            $user_name,
            $email
        );

        if ($stmt_update->execute()) {
            $conn->commit();
            echo "Profile updated successfully!";
            header("Location: edit_profile_user.php");
            exit();
        } else {
            $conn->rollback();
            die("Error updating profile: " . $stmt_update->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        die("Transaction failed: " . $e->getMessage());
    }

    $stmt_update->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        /* Add your styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .main-container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input,
        textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 12px;
            background-color: #6a5acd;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #5a4cc9;
        }

        .profile-picture-section img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
    </style>
    <script>
        // Function to keep the session active
        function refreshSession() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/htp2/session_refresh.php", true); // Endpoint to refresh session
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log("Session refreshed successfully.");
                } else {
                    console.error("Session refresh failed.");
                }
            };
            xhr.send();
        }

        // Periodically refresh session every 4 minutes
        setInterval(refreshSession, 240000); // 240,000 ms = 4 minutes
    </script>
</head>

<body>
    <div class="main-container">
        <h2>Edit Profile</h2>
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <!-- Profile Picture -->
            <div class="profile-picture-section">
                <label for="profile_picture">Change Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture">
                <?php
                // Encode the image in base64 format if it exists in the database
                if (!empty($profile['Profile_Picture'])) {
                    // Convert BLOB data to a Base64-encoded string
                    $imageData = base64_encode($profile['Profile_Picture']);
                    $mimeType = 'image/jpeg'; // Default MIME type; adjust if necessary
                    $imageSrc = "data:$mimeType;base64,$imageData";
                } else {
                    // Fallback to placeholder if no image exists
                    $imageSrc = "https://via.placeholder.com/100";
                }                
                ?>
                <img src="<?php echo $imageSrc; ?>" alt="Current Profile Picture">
                <br><br>
                <label>Remove Profile Picture: </label>
                <input type="checkbox" name="remove_picture" value="1">
            </div>

            <!-- Email -->
            <div class="email-section">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($profile['Email']); ?>"
                    required>
            </div>

            <!-- Personal Briefing -->
            <div class="briefing-section">
                <label for="personal_briefing">Personal Briefing:</label>
                <textarea name="personal_briefing" id="personal_briefing"
                    rows="4"><?php echo htmlspecialchars($profile['Personal_Briefing']); ?></textarea>
                <br><br>
                <label>Remove Personal Briefing: </label>
                <input type="checkbox" name="remove_briefing" value="1">
            </div>

            <!-- Phone Number -->
            <div class="phone-section">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($profile['Phone']); ?>"
                    required>
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>
    <script>
        // Check if session is active and redirect if not
        function checkSession() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "/htp2/session_check.php", true);
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText !== "logged_in") {
                    window.location.href = "/htp2/login.php";
                }
            };
            xhr.send();
        }

        // Call checkSession every 5 minutes to ensure user is logged in
        setInterval(checkSession, 300000); // 300,000 ms = 5 minutes
    </script>
</body>

</html>