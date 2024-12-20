<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        form {
            background-color: white;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
        }

        input[type="number"],
        select,
        button {
            width: 100%;
            padding: 8px;
            margin: 10px 0 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        select {
            font-size: 1em;
        }
    </style>
</head>
<body>
    <h1>Adoption Request Form</h1>

    <?php
    // Database configuration
    $servername = "localhost";
    $username = "root"; // Update with your database username
    $password = ""; // Update with your database password
    $dbname = "Huts_for_Paws_2";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (
            isset($_POST['user_id']) &&
            isset($_POST['animal_id']) &&
            isset($_POST['num_of_children']) &&
            isset($_POST['num_of_adults']) &&
            isset($_POST['animal_proof']) &&
            isset($_POST['other_pets']) &&
            isset($_POST['other_pets_spayed'])
        ) {
            $user_id = $_POST['user_id'];
            $animal_id = $_POST['animal_id'];
            $num_of_children = $_POST['num_of_children'];
            $num_of_adults = $_POST['num_of_adults'];
            $animal_proof = $_POST['animal_proof'];
            $other_pets = $_POST['other_pets'];
            $other_pets_spayed = $_POST['other_pets_spayed'];

            // Insert query
            $insquery = "INSERT INTO adoption (UserID, animal_id, num_of_children, num_of_adults, animal_proof, other_pets, other_pets_spayed) 
                         VALUES ('$user_id', '$animal_id', '$num_of_children', '$num_of_adults', '$animal_proof', '$other_pets', '$other_pets_spayed')";
            $result = $conn->query($insquery);

            // If successful, redirect to prevent resubmission on refresh
            if ($result) {
                echo "<p style='color:green;'>Adoption record added successfully!</p>";
                // Redirect to the same page to prevent resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
            }
        }
    }
    ?>

    <form action="" method="POST">
        <label for="user_id">User ID:</label>
        <input type="number" id="user_id" name="user_id" required><br><br>

        <label for="animal_id">Animal ID:</label>
        <input type="number" id="animal_id" name="animal_id" required><br><br>

        <label for="num_of_children">Number of Children:</label>
        <input type="number" id="num_of_children" name="num_of_children" required><br><br>

        <label for="num_of_adults">Number of Adults:</label>
        <input type="number" id="num_of_adults" name="num_of_adults" required><br><br>

        <label for="animal_proof">Animal Proof:</label>
        <select id="animal_proof" name="animal_proof" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select><br><br>

        <label for="other_pets">Other Pets:</label>
        <select id="other_pets" name="other_pets" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select><br><br>

        <label for="other_pets_spayed">Other Pets Spayed:</label>
        <select id="other_pets_spayed" name="other_pets_spayed" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
