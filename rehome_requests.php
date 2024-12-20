<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "Huts_for_Paws_2"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Rehome (UserID, type, name, gender, age, breed, vaccinated, potty_trained, image, reason_behind_rehome) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssisssss", $userID, $type, $name, $gender, $age, $breed, $vaccinated, $potty_trained, $image, $reason);

    // Set parameters and execute
    $userID = $_POST['UserID'];
    $type = $_POST['type'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $breed = $_POST['breed'];
    $vaccinated = $_POST['vaccinated'];
    $potty_trained = $_POST['potty_trained'];
    $reason = $_POST['reason_behind_rehome'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    } else {
        $image = null;
    }

    if ($stmt->execute()) {
        echo "Record successfully added!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rehome Request</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Body Styling */
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        /* Header Section */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ffffff;
            color: #2E256F;
        }

        header .logo img {
            height: 50px;
        }

        header nav a {
            color: #2E256F;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        /* Form Section */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2E256F;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        form input, 
        form select, 
        form textarea, 
        form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        form input:focus, 
        form select:focus, 
        form textarea:focus {
            outline: none;
            border-color: #0077cc;
            box-shadow: 0 0 5px rgba(0, 119, 204, 0.5);
        }

        form button {
            background-color: #0077cc;
            color: #fff;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #005fa3;
        }

        /* Footer Links */
        footer {
            margin-top: 20px;
            text-align: center;
            color: #555;
        }
    </style>
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
    </header>
    <h2>Rehome Request Form</h2>
    <form action="rehome_requests.php" method="POST" enctype="multipart/form-data">
        <label for="UserID">User ID:</label>
        <input type="number" id="UserID" name="UserID" required><br><br>

        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="cat">Cat</option>
            <option value="dog">Dog</option>
        </select><br><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" maxlength="20" required><br><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select><br><br>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required><br><br>

        <label for="breed">Breed:</label>
        <select id="breed" name="breed" required>
            <option value="Deshi">Deshi</option>
            <option value="Bideshi">Bideshi</option>
        </select><br><br>

        <label for="vaccinated">Vaccinated:</label>
        <select id="vaccinated" name="vaccinated" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select><br><br>

        <label for="potty_trained">Potty Trained:</label>
        <select id="potty_trained" name="potty_trained" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select><br><br>

        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <label for="reason_behind_rehome">Reason Behind Rehome:</label>
        <textarea id="reason_behind_rehome" name="reason_behind_rehome" maxlength="200" required></textarea><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
