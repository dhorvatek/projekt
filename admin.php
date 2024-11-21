<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: weatherpage.php');
    exit();
}

// Connect to the database
$MySQL = mysqli_connect("localhost", "root", "", "weather_app") or die('Error connecting to MySQL server.');

// Fetch available cities
$locationsQuery = "SELECT * FROM locations";
$locationsResult = mysqli_query($MySQL, $locationsQuery);

// Handle form submission for selecting cities
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['locations'])) {
        // Update the database with the selected cities
        $selectedLocations = $_POST['locations'];
        foreach ($selectedLocations as $cityId) {
            $updateQuery = "UPDATE locations SET is_displayed = 1 WHERE id = $cityId";
            mysqli_query($MySQL, $updateQuery);
        }
    } else {
        // Deselect all locations
        $updateQuery = "UPDATE locations SET is_displayed = 0";
        mysqli_query($MySQL, $updateQuery);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="weatherpage.php">Weather App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="weatherpage.php">Weather</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin.php">Admin</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary mx-2" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Dashboard Content -->
    <div class="container mt-4">
        <h1 class="text-center">Admin Dashboard</h1>
        <div class="row mt-4">
            <!-- Side Navigation -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="#users" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                        Users
                    </a>
                    <a href="#locations" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                        Manage Locations
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="col-md-9">
                <!-- Users Section -->
                <div id="users" class="collapse show">
                    <div class="card mb-3">
                        <div class="card-header">
                            User Management
                        </div>
                        <div class="card-body">
                            <h4>User List</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($MySQL, "SELECT id, username, email, role FROM users");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>{$row['username']}</td>
                                            <td>{$row['email']}</td>
                                            <td>{$row['role']}</td>
                                            <td>
                                                <a href='edit_user.php?id={$row['id']}' class='btn btn-primary btn-sm'>Edit</a>
                                                <a href='delete_user.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                    mysqli_close($MySQL);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Locations Section (Future Idea) -->
                <div id="locations" class="collapse show">
                    <div class="card mb-3">
                        <div class="card-header">
                            Manage Locations
                        </div>
                        <div class="card-body">
                            <form method="POST" action="admin.php">
                                <h4>Select Cities to Display on Homepage</h4>
                                <div class="form-check">
                                    <?php while ($location = mysqli_fetch_assoc($locationsResult)): ?>
                                        <input class="form-check-input" type="checkbox" name="locations[]" value="<?php echo $location['id']; ?>" 
                                            <?php echo $location['is_displayed'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">
                                            <?php echo htmlspecialchars($location['city_name']); ?>
                                        </label><br>
                                    <?php endwhile; ?>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                            </form>
                        </div>

               
                        
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
