<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: weatherpage.php');
    exit();
}

$MySQL = mysqli_connect("localhost", "root", "", "weather_app") or die('Error connecting to MySQL server.');

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch user data from the database
    $result = mysqli_query($MySQL, "SELECT id, username, email, role FROM users WHERE id = '$userId'");
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        die("User not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newUsername = mysqli_real_escape_string($MySQL, $_POST['username']);
    $newRole = mysqli_real_escape_string($MySQL, $_POST['role']);

    // Update user data in the database
    $updateQuery = "UPDATE users SET username = '$newUsername', role = '$newRole' WHERE id = '$userId'";
    if (mysqli_query($MySQL, $updateQuery)) {
        header("Location: admin.php"); // Redirect back to the admin page after successful update
        exit();
    } else {
        $message = "Error updating user.";
    }
}

mysqli_close($MySQL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary mx-2" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center">Edit User</h1>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <?php if (isset($message)) { echo "<div class='alert alert-danger mt-3'>$message</div>"; } ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
