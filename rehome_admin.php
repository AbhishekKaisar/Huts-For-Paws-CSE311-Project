<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "Huts_for_Paws_2"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Accept button click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept'])) {
    $rehome_id = $_POST['rehome_id'];
    
    // Insert record into Animal table
    $sql = "
        INSERT INTO Animal (rehome_id, type, name, gender, age, breed, vaccinated, potty_trained, image)
        SELECT rehome_id, type, name, gender, age, breed, vaccinated, potty_trained, image
        FROM Rehome
        WHERE rehome_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rehome_id);
    if ($stmt->execute()) {
        echo "<script>alert('Record added to Animal table successfully!');</script>";
    } else {
        echo "<script>alert('Error adding record: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Fetch data with a JOIN query
$sql = "
    SELECT 
        users.Full_Name AS owner_name,
        users.Phone AS owner_cell,
        users.Address AS owner_address,
        Rehome.rehome_id,
        Rehome.type,
        Rehome.name,
        Rehome.gender,
        Rehome.age,
        Rehome.breed,
        Rehome.vaccinated,
        Rehome.potty_trained,
        Rehome.reason_behind_rehome
    FROM Rehome
    JOIN users ON Rehome.UserID = users.UserID
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rehome Requests</title>
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            border: 1px solid #e0e0e0;
        }
        .card h2 {
            margin: 0 0 20px;
        }
        .card div {
            margin-bottom: 10px;
        }
        .card .info {
            display: flex;
            justify-content: space-between;
        }
        .card .info div {
            width: 48%;
        }
        .card form {
            display: inline-block;
        }
        .card button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .accept {
            background-color: #4CAF50;
            color: white;
        }
        .decline {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="Images/Logo.png" alt="Huts for Paws Logo">
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
    <h1>Admin Dashboard - Rehome Requests</h1>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="card">
                <h2>Rehome Request</h2>
                <div class="info">
                    <div>
                        <h3>Owner Info</h3>
                        <p><strong>Name:</strong> <?= $row['owner_name']; ?></p>
                        <p><strong>Cell:</strong> <?= $row['owner_cell']; ?></p>
                        <p><strong>Address:</strong> <?= $row['owner_address']; ?></p>
                    </div>
                    <div>
                        <h3>Animal Info</h3>
                        <p><strong>ID:</strong> <?= $row['rehome_id']; ?></p>
                        <p><strong>Type:</strong> <?= ucfirst($row['type']); ?></p>
                        <p><strong>Name:</strong> <?= $row['name']; ?></p>
                        <p><strong>Gender:</strong> <?= ucfirst($row['gender']); ?></p>
                        <p><strong>Age:</strong> <?= $row['age']; ?> year(s)</p>
                        <p><strong>Breed:</strong> <?= $row['breed']; ?></p>
                        <p><strong>Potty Trained:</strong> <?= ucfirst($row['potty_trained']); ?></p>
                        <p><strong>Vaccinated:</strong> <?= ucfirst($row['vaccinated']); ?></p>
                        <p><strong>Reason:</strong> <?= $row['reason_behind_rehome']; ?></p>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="rehome_id" value="<?= $row['rehome_id']; ?>">
                    <button type="submit" name="accept" class="accept">Accept</button>
                </form>
                <form method="POST">
                    <!-- Decline functionality can be added here -->
                    <button type="submit" name="decline" class="decline">Decline</button>
                </form>
            </div>
            <?php
        }
    } else {
        echo "<p>No rehome requests found.</p>";
    }
    $conn->close();
    ?>
</body>
</html>
