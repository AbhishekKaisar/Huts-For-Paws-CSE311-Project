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
    $animal_id = $_POST['animal_id'];

    // Delete record from Animal table
    $sql = "DELETE FROM Animal WHERE animal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $animal_id);
    if ($stmt->execute()) {
        echo "<script>alert('Animal adoption accepted and record removed from Animal table!');</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Fetch adoption requests with JOIN query
$sql = "
    SELECT 
        Adoption.adoption_id,
        Adoption.timestamp,
        Adoption.num_of_children,
        Adoption.num_of_adults,
        Adoption.animal_proof,
        Adoption.other_pets,
        Adoption.other_pets_spayed,
        users.Full_Name AS adopter_name,
        users.Phone AS adopter_phone,
        users.Address AS adopter_address,
        Animal.animal_id,
        Animal.type,
        Animal.name,
        Animal.gender,
        Animal.age,
        Animal.breed,
        Animal.vaccinated,
        Animal.potty_trained
    FROM Adoption
    JOIN users ON Adoption.UserID = users.UserID
    JOIN Animal ON Adoption.animal_id = Animal.animal_id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Requests</title>
    <style>
        
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
    <h1>Admin Dashboard - Adoption Requests</h1>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="card">
                <h2>Adoption Request</h2>
                <div class="info">
                    <div>
                        <h3>Adopter Info</h3>
                        <p><strong>Name:</strong> <?= $row['adopter_name']; ?></p>
                        <p><strong>Phone:</strong> <?= $row['adopter_phone']; ?></p>
                        <p><strong>Address:</strong> <?= $row['adopter_address']; ?></p>
                    </div>
                    <div>
                        <h3>Animal Info</h3>
                        <p><strong>ID:</strong> <?= $row['animal_id']; ?></p>
                        <p><strong>Type:</strong> <?= ucfirst($row['type']); ?></p>
                        <p><strong>Name:</strong> <?= $row['name']; ?></p>
                        <p><strong>Gender:</strong> <?= ucfirst($row['gender']); ?></p>
                        <p><strong>Age:</strong> <?= $row['age']; ?> year(s)</p>
                        <p><strong>Breed:</strong> <?= $row['breed']; ?></p>
                        <p><strong>Potty Trained:</strong> <?= ucfirst($row['potty_trained']); ?></p>
                        <p><strong>Vaccinated:</strong> <?= ucfirst($row['vaccinated']); ?></p>
                    </div>
                </div>
                <h3>Adoption Details</h3>
                <p><strong>Number of Children:</strong> <?= $row['num_of_children']; ?></p>
                <p><strong>Number of Adults:</strong> <?= $row['num_of_adults']; ?></p>
                <p><strong>Animal Proof:</strong> <?= ucfirst($row['animal_proof']); ?></p>
                <p><strong>Other Pets:</strong> <?= ucfirst($row['other_pets']); ?></p>
                <p><strong>Other Pets Spayed:</strong> <?= ucfirst($row['other_pets_spayed']); ?></p>
                <form method="POST">
                    <input type="hidden" name="animal_id" value="<?= $row['animal_id']; ?>">
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
        echo "<p>No adoption requests found.</p>";
    }
    $conn->close();
    ?>
</body>
</html>
