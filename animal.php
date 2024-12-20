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

// Capture filter values from GET request
$type = isset($_GET['type']) ? $_GET['type'] : '';
$breed = isset($_GET['breed']) ? $_GET['breed'] : '';
$vaccinated = isset($_GET['vaccinated']) ? $_GET['vaccinated'] : '';
$potty_trained = isset($_GET['potty_trained']) ? $_GET['potty_trained'] : '';

// Base SQL query
$sql = "SELECT animal_id, type, name, gender, age, breed, vaccinated, potty_trained, image FROM Animal WHERE 1=1";

// Add filters to the query
if (!empty($type)) {
    $sql .= " AND type = '" . $conn->real_escape_string($type) . "'";
}
if (!empty($breed)) {
    $sql .= " AND breed = '" . $conn->real_escape_string($breed) . "'";
}
if (!empty($vaccinated)) {
    $sql .= " AND vaccinated = '" . $conn->real_escape_string($vaccinated) . "'";
}
if (!empty($potty_trained)) {
    $sql .= " AND potty_trained = '" . $conn->real_escape_string($potty_trained) . "'";
}

// Execute the query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt a Pet</title>
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

        /* Main Layout */
        main {
            display: flex;
            padding: 1rem;
        }

        /* Sidebar Filters */
        .filters {
            width: 250px;
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 8px;
        }

        .filters h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #4a4a8a;
        }

        .filters button, .filters select {
            display: block;
            width: 100%;
            margin: 0.5rem 0;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            cursor: pointer;
        }

        .filter-category button {
            width: 48%;
            margin-right: 2%;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .show-all {
            display: block;
            margin: 1rem 0;
            padding: 0.5rem;
            
           
            text-align: center;
            font-weight: bold; 
            
            background-color: #4a4a8a;
            color: black;
            border-radius: 8px;
            cursor: pointer;/* Optional for better visibility */
        }

        .show-all:hover {
            background-color: #3a3170;
        }

        /* Pet Cards */
        .pet-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            flex: 1;
            margin-left: 1rem;
        }

        .pet-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            height: 350px;
        }

        .pet-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .pet-info {
            padding: 1rem;
        }

        .pet-info h3 {
            font-size: 1.2rem;
            color: #4a4a8a;
        }

        .pet-info p {
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .pet-info button {
            padding: 0.5rem 1rem;
            border: none;
            background-color: #4a4a8a;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
        }

        .pet-info button:hover {
            background-color: #3a3170;
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

    <!-- Main Content -->
    <main>
        <!-- Sidebar Filters -->
        <aside class="filters">
            <h2>Filters</h2>
            <form method="GET" id="filterForm">
                <div class="filter-category">
                    <button type="button" onclick="setFilter('type', 'cat')">Cat</button>
                    <button type="button" onclick="setFilter('type', 'dog')">Dog</button>
                </div>
                <div class="breed-filter">
                    <label>Breed</label>
                    <select name="breed" onchange="submitFilter()">
                        <option value="">All</option>
                        <option value="Deshi">Deshi</option>
                        <option value="Bideshi">Bideshi</option>
                    </select>
                </div>
                <div class="vaccinated-filter">
                    <label>Vaccinated</label>
                    <select name="vaccinated" onchange="submitFilter()">
                        <option value="">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="potty-filter">
                    <label>Potty Trained</label>
                    <select name="potty_trained" onchange="submitFilter()">
                        <option value="">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <input type="hidden" name="type" id="filterType">
            </form>
            <button class="show-all" onclick="window.location.href='animal.php'">Show All</button>
        </aside>

        <!-- Pet Cards -->
        <section class="pet-cards">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imageData = base64_encode($row['image']);
                    $imageSrc = "data:image/jpeg;base64," . $imageData;
                    ?>
                    <div class="pet-card">
                        <img src="<?= $imageSrc; ?>" alt="Pet image">
                        <div class="pet-info">
                            <h3><?= htmlspecialchars($row['name']); ?></h3>
                            <p>Breed: <?= htmlspecialchars($row['breed']); ?></p>
                            <p>Age: <?= htmlspecialchars($row['age']); ?> years</p>
                            <button onclick="window.location.href='pet_info.php?animal_id=<?= $row['animal_id']; ?>'">More Info</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No animals available for adoption.</p>";
            }
            $conn->close();
            ?>
        </section>
    </main>

    <script>
        function setFilter(name, value) {
            document.getElementById("filterType").value = value;
            document.getElementById("filterForm").submit();
        }

        function submitFilter() {
            document.getElementById("filterForm").submit();
        }
    </script>
</body>
</html>
