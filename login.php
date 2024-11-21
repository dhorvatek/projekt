<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="index.php">☀ WeatherApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login.php">Sign In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary mx-2" href="register.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="my-4">Sign In</h1>
        <p>Login to access personalized weather updates and features!</p>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign In</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <?php
session_start();
if (isset($_SESSION['user_id'])) {
    // If the user is already logged in
    header('Location: weatherpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $MySQL = mysqli_connect("localhost", "root", "", "weather_app") or die('Error connecting to MySQL server.');

    $username = mysqli_real_escape_string($MySQL, $_POST['username']);
    $password = mysqli_real_escape_string($MySQL, $_POST['password']);

    // Query
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($MySQL, $query);
    $user = mysqli_fetch_assoc($result);

    // vallidation
    if ($user && password_verify($password, $user['password'])) {
        // Start the session for given data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // redirect if true
        header('Location: weatherpage.php');
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }

    mysqli_close($MySQL);
}
?>

</body>
</html>