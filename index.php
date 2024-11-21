<?php
// Load API key from .env file
$env = parse_ini_file('.env');
$api_key = $env['API_KEY'];

// Connect to the database
$MySQL = mysqli_connect("localhost", "root", "", "weather_app") or die('Error connecting to MySQL server.');

// Fetch cities marked for display
$query = "SELECT city_name FROM locations WHERE is_displayed = 1";
$locationsResult = mysqli_query($MySQL, $query);

// Function to fetch weather data for a city
function getWeatherDataForCity($cityName, $apiKey) {
    $url = "http://api.openweathermap.org/data/2.5/weather?q=$cityName&appid=$apiKey&units=metric";
    
    $response = file_get_contents($url);
    return json_decode($response, true); // Return decoded weather data
}

// Fetch weather data for each city
$weatherData = [];
while ($location = mysqli_fetch_assoc($locationsResult)) {
    $cityName = $location['city_name'];
    $weather = getWeatherDataForCity($cityName, $api_key);
    if ($weather && isset($weather['main'])) {
        $weatherData[] = [
            'city' => $cityName,
            'temp' => $weather['main']['temp'],
            'description' => $weather['weather'][0]['description'],
            'icon' => $weather['weather'][0]['icon']
        ];
    }
}

mysqli_close($MySQL);
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

    <!-- Main Content -->
    <div class="container text-center my-5">
        <h1 class="display-4 text-primary">Welcome to WeatherApp!</h1>
        <p class="lead">Get accurate weather updates for today and the next 7 days. <br>Sign in to access personalized features!</p>
        <a href="register.php" class="btn btn-primary btn-lg mt-3">Get Started</a>
    </div>

    <!-- Weather Section -->
    <div class="weather-info">
            <h2 class="text-primary mb-4">Current Weather</h2>
            <div class="row">
                <?php foreach ($weatherData as $weather): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="http://openweathermap.org/img/wn/<?php echo $weather['icon']; ?>@2x.png" class="weather-icon card-img-top" alt="Weather icon">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($weather['city']); ?></h5>
                                <p class="card-text weather-temp">
                                    <?php echo $weather['temp']; ?>°C
                                </p>
                                <p class="weather-description">
                                    <?php echo ucfirst($weather['description']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>



        <!--Weather Section Below Cards -->
        <div class="container text-center my-5">
        <div class="fun-weather-section">
            <h2 class="text-primary mb-4">Weather Zone 🌦</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="fun-card">
                        <h4>☀ Sun's Out!</h4>
                        <p class="fun-text">Time to soak in some rays and enjoy the warmth. You deserve it!</p>
                        <div class="sun-animation"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fun-card">
                        <h4>🌧 Rain, Rain, Go Away</h4>
                        <p class="fun-text">Rain or shine, we're here to make it fun! Grab your umbrella ☔️</p>
                        <div class="rain-animation"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fun-card">
                        <h4>🌩 Thunderstorms Ahead!</h4>
                        <p class="fun-text">Thunderstorms are exciting, but stay safe! ⚡</p>
                        <div class="storm-animation"></div>
                    </div>
                </div>
            </div>
            <div class="weather-stickers">
                <span class="weather-sticker">🌈</span>
                <span class="weather-sticker">💨</span>
                <span class="weather-sticker">🌪️</span>
                <span class="weather-sticker">❄️</span>
            </div>
        </div>
    </div>
</div>

<!-- Background Animation -->
<div class="background-animation">
    <div class="cloud cloud-1"></div>
    <div class="cloud cloud-2"></div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
