<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Huts_for_Paws_2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get animal_id from URL
$animal_id = isset($_GET['animal_id']) ? intval($_GET['animal_id']) : 0;

if ($animal_id > 0) {
    // Fetch animal details
    $sql = "SELECT * FROM Animal WHERE animal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $animal_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $animal = $result->fetch_assoc();
    } else {
        die("Animal not found.");
    }
} else {
    die("Invalid request.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Details</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .content{
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f4f9;
            padding: 15px 30px;
        }

        header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #5e3baf;
        }

        header nav a {
            margin: 0 1rem;
            color: #4a4a8a;
            text-decoration: none;
            font-weight: bold;
        }

        header nav a:hover {
            color: #5e3baf;
        }

        /* Main Layout */
        main {
            display: flex;
            padding: 1rem;
        }

        .pet-overview {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .pet-image-section {
            flex: 1;
            min-width: 350px;
        }

        .pet-image-section img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .pet-gallery {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .pet-gallery img {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .pet-gallery img:hover {
            transform: scale(1.1);
        }

        .pet-details {
            align-self: center;
            flex: 2;
        }

        .pet-details h2 {
            font-size: 28px;
            color: #444;
        }

        .pet-info {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

        .info-item i {
            color: #5cb85c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #5cb85c;
            color: #fff;
        }

        /* Adopt Now Button */
        .adopt-now {
            margin-top: 20px;
            text-align: center;
        }

        .adopt-now button {
            background-color: #9990DA;
            color: white;
            font-size: 18px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .adopt-now button:hover {
            background-color: #675BC8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header>
        <div class="logo"><img src="Images/Logo.png"></div> 
        <nav>
            <a href="Pages/for_adoption.html">Adopt</a>
            <a href="#rehome">Rehome</a>
            <a href="#care">Care Guide</a>
            <a href="#about">About Us</a>
        </nav>
        </header>
        <div class="content">
            <!-- Pet Overview Section -->
            <div class="pet-overview">
                <!-- Pet Image Section -->
                <div class="pet-image-section">
                    <?php
                    $imageData = base64_encode($animal['image']);
                    $imageSrc = "data:image/jpeg;base64," . $imageData;
                    ?>
                    <img src="<?= $imageSrc; ?>" alt="Pet Image">
                </div>

                <div class="pet-details">
                    <h2>Pet Name: <?= htmlspecialchars($animal['name']); ?></h2>
                    <table>
                        <tr>
                            <td><strong>Animal ID:</strong></td>
                            <td><?= htmlspecialchars($animal['animal_id']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?= htmlspecialchars($animal['type']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Age:</strong></td>
                            <td><?= htmlspecialchars($animal['age']); ?> year</td>
                        </tr>
                        <tr>
                            <td><strong>Gender:</strong></td>
                            <td><?= htmlspecialchars($animal['gender']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Breed:</strong></td>
                            <td><?= htmlspecialchars($animal['breed']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Vaccinated:</strong></td>
                            <td><?= htmlspecialchars($animal['vaccinated']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Potty Trained:</strong></td>
                            <td><?= htmlspecialchars($animal['potty_trained']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="adopt-now">
    <a href="http://localhost/htp2/adoption_requests.php">
        <button>Adopt Now</button>
    </a>
</div>

        </div>
    </div>
</body>
</html>