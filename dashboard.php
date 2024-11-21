<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newUsername'])) {
    $newUsername = trim($_POST['newUsername']);

    if (!empty($newUsername)) {
        // Establish database connection
        $MySQL = mysqli_connect("localhost", "root", "", "weather_app") 
            or die('Error connecting to MySQL server.');

        // Get user ID from session
        $user_id = $_SESSION['user_id']; // Ensure this is set when the user logs in

        // Update query
        $updateQuery = "UPDATE users SET username = '$newUsername' WHERE id = $user_id";

        if (mysqli_query($MySQL, $updateQuery)) {
            $message = "Username updated successfully!";
            $_SESSION['username'] = $newUsername; // Update session variable
        } else {
            $message = "Error updating username. Please try again.";
        }

        // Close the database connection
        mysqli_close($MySQL);
    } else {
        $message = "Please enter a valid username.";
    }
}

// To display the message later in the HTML
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="weatherpage.php">Weather App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="weatherpage.php">Weather</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
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

    <!-- Dashboard Content -->
    <div class="container mt-4">
        <h1 class="text-center">User Dashboard</h1>
        <?php if (!empty($message)) : ?>
            <div class="alert alert-info text-center"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <div class="row mt-4">
            <!-- Navigation -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                        Notifications
                    </a>
                    <a href="#favorites" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                        Favorites
                    </a>
                    <a href="#username" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                        Change Username
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="col-md-9">
                <!-- Notifications -->
                <div id="notifications" class="collapse show">
                    <div class="card mb-3">
                        <div class="card-header">
                            Notifications
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                Weather warning: Snow expected in your area today.
                                <input type="checkbox" class="form-check-input ms-2" checked> Mute Notifications
                            </div>
                            <div class="alert alert-warning" role="alert">
                                Site update: Maintenance scheduled for 2 AM tomorrow.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Favorites -->
                <div id="favorites" class="collapse">
                    <div class="card mb-3">
                        <div class="card-header">
                            Your Favorite Locations
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Zabok
                                    <span class="badge bg-primary">5°C</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Krapina
                                    <span class="badge bg-primary">4°C</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Change Username -->
                <div id="username" class="collapse">
                    <div class="card mb-3">
                        <div class="card-header">
                            Change Username
                        </div>
                        <div class="card-body">
                            <form method="POST" action="dashboard.php">
                                <div class="mb-3">
                                    <label for="newUsername" class="form-label">New Username</label>
                                    <input type="text" class="form-control" id="newUsername" name="newUsername" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Change Username</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
