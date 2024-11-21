<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App - Register</title>
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
        <h1 class="my-4">Create Your Account</h1>
        <p>Register here to access personalized weather updates and more!</p>
        
        <!-- Registration Form -->
        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    // Database connection
    $MySQL = mysqli_connect("localhost", "root", "", "weather_app") or die('Error connecting to MySQL server.');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($MySQL, $_POST['email']);
        $username = mysqli_real_escape_string($MySQL, $_POST['username']);
        $password = mysqli_real_escape_string($MySQL, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($MySQL, $_POST['confirm_password']);

        // Validation
        $errors = [];
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        // Checking for duplicants in db
        $check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
        $result = mysqli_query($MySQL, $check_query);
        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Email or Username already exists.";
        }

        // Fill db
        if (empty($errors)) {
            // Hash 
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_query = "INSERT INTO users (username, email, password) 
                             VALUES ('$username', '$email', '$hashed_password')";

            if (mysqli_query($MySQL, $insert_query)) {
                echo "<div class='alert alert-success mt-3'>You are successfully registered! You can now <a href='login.php'>login</a>.</div>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($MySQL) . "</div>";
            }
        } else {
            // err
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger mt-3'>$error</div>";
            }
        }

        // Close connection
        mysqli_close($MySQL);
    }
    ?>
</body>
</html>
