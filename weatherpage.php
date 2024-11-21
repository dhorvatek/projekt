<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Handle logout request
if (isset($_POST['logout'])) {
    // Unset session variables
    session_unset();
    
    // Destroy the session
    session_destroy();
    
    // Expire the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    header('Location: index.php');
    exit();
}

// Load API key from .env file
$env = parse_ini_file('.env');
$api_key = $env['API_KEY'];

$city = 'Zabok';

// check for user input: city
if (isset($_POST['city'])) {
    $city = $_POST['city'];
}

// Fetch weather data from OpenWeatherMap API
$weather_url = "http://api.openweathermap.org/data/2.5/forecast?q={$city}&units=metric&appid={$api_key}";

// API request
$response = file_get_contents($weather_url);
$data = json_decode($response, true);

if ($data['cod'] != '200') {
    $error_message = "Error: " . $data['message'];
    $weather_data = null;
} else {
    $weather_data = $data;
    $weather_condition = $weather_data['list'][0]['weather'][0]['main'];
    $weather_icon = $weather_data['list'][0]['weather'][0]['icon'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
</head>
<body class="weather-<?php echo strtolower($weather_condition); ?>">

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
                        <a class="nav-link text-white" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="favourites.php">Favourites</a>
                    </li>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="admin.php">Admin</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <!-- Logout Button -->
                        <form method="POST" action="weatherpage.php">
                            <button type="submit" name="logout" class="btn logout">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
    <h1 class="my-4">Weather Forecast</h1>

    <!-- City Input Form -->
    <form method="POST" action="weatherpage.php" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="city" placeholder="Enter city" value="<?php echo htmlspecialchars($city); ?>">
            <button type="submit" class="btn btn-primary">Get Weather</button>
        </div>
    </form>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif ($weather_data): ?>
        <div class="weather-info">
            <h3>Weather in <?php echo htmlspecialchars($city); ?></h3>
            <img src="http://openweathermap.org/img/wn/<?php echo $weather_icon; ?>.png" alt="Weather Icon" class="weather-icon">
            <p class="weather-description"><?php echo $weather_data['list'][0]['weather'][0]['description']; ?></p>
            <p class="weather-temp">Temperature: <?php echo $weather_data['list'][0]['main']['temp']; ?>°C</p>

            <h4>Next 7 Days</h4>
            <div class="row">
                <?php foreach ($weather_data['list'] as $day): ?>
                    <?php if (strtotime($day['dt_txt']) > time()) { ?>
                        <div class="col-md-2 mb-3">
                            <div class="card">
                                <div class="card-body d-flex align-items-center">
                                    <img src="http://openweathermap.org/img/wn/<?php echo $day['weather'][0]['icon']; ?>.png" alt="Weather Icon" class="weather-icon">
                                    <div>
                                        <h5 class="card-title"><?php echo date('l, d M', strtotime($day['dt_txt'])); ?></h5>
                                        <p>Temp: <?php echo $day['main']['temp']; ?>°C</p>
                                        <p><?php echo $day['weather'][0]['description']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
