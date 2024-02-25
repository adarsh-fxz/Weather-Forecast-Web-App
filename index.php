<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast Web App</title>
    <link rel="shortcut icon" href="icon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/b6d8553dda.js" crossorigin="anonymous"></script>
</head>

<body>

    <form method="GET" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <div class="container">
            <div class="left">
                <div class="search">
                    <input name="q" type="text" id="cityInput" placeholder="Search for city or place...">
                    <button id="btn" type="submit">Search</button>
                </div>

                <div id="weatherInfo">
                    <!-- Weather information will be displayed here -->
                </div>
            </div>

            <div class="vl"></div>

            <div class="right">
                <h1></h1>
                <div class="weekContainer">
                    <!-- Past weather information will be displayed here -->
                </div>
            </div>
        </div>
    </form>

    <footer>
        <p>Copyright &copy; Adarsh Gupta</p>
    </footer>


    <?php
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'weather_db';

    $conn = mysqli_connect($servername, $username, $password, $database);
    if ($conn) {
        // echo "Sql connected" . "<br>";
    } else {
        echo "Sql not connected" . mysqli_connect_error();
    }

    // $createDatabase = "CREATE DATABASE weather_db";
    // if (mysqli_query($conn, $createDatabase)) {
    //     echo "Database created";
    // } else {
    //     echo "Failed to create database " . mysqli_error($conn);
    // }

    // $createTable = "CREATE TABLE weatherData(id int auto_increment PRIMARY KEY, locTime varchar(255) NOT NULL, dayOfWeek varchar(255) NOT NULL, monthDate varchar(255) NOT NULL, cityName varchar(255) NOT NULL, country varchar(255) NOT NULL, weatherIcon varchar(255) NOT NULL, weatherDescription varchar(255) NOT NULL, temperature decimal(5,2) NOT NULL, feelsLikeTemp decimal(5,2) NOT NULL, pressure decimal(8,2) NOT NULL, windSpeed decimal(6,2) NOT NULL, humidity int NOT NULL)";
    // if (mysqli_query($conn, $createTable)) {
    //     echo "Table created";
    // } else {
    //     echo "Failed to create table " . mysqli_error($conn);
    // }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['q'])) {
            $city = $_GET['q'];
        } else {
            $city = "Cebu City";
        }
    }

    $apiKey = 'blablablah';

    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . $city . "&appid=" . $apiKey . "&units=metric";
    $response = file_get_contents($url);
    $data = json_decode($response, true); // converts json data to php objects

    // Calculate accurate local time
    $timezoneOffset = $data['timezone'];
    $currentTimeUTC = new DateTime("now", new DateTimeZone("UTC"));
    $currentTimeUTC->modify("+$timezoneOffset seconds"); // Adjust for the timezone offset

    $locTime = $currentTimeUTC->format('H:i'); // formatted time
    $dayOfWeek = $currentTimeUTC->format('l'); 
    $monthDate = $currentTimeUTC->format('M j, Y');
    $cityName = $data['name'];
    $country = $data['sys']['country'];
    $weatherIcon = $data['weather'][0]['icon'];
    $weatherDescription = $data['weather'][0]['description'];
    $temperature = $data['main']['temp'];
    $feelsLikeTemp = $data['main']['feels_like'];
    $pressure = $data['main']['pressure'];
    $windSpeed = $data['wind']['speed'];
    $humidity = $data['main']['humidity'];

    $existingData = "SELECT * FROM weatherData WHERE cityName='$cityName' AND monthDate='$monthDate'";
    $result = mysqli_query($conn, $existingData);

    if (mysqli_num_rows($result) === 0) {
        $insertData = "INSERT INTO weatherData(locTime, dayOfWeek, monthDate, cityName, country, weatherIcon, weatherDescription, temperature, feelsLikeTemp, pressure, windSpeed, humidity) VALUES('$locTime', '$dayOfWeek','$monthDate', '$cityName', '$country', '$weatherIcon', '$weatherDescription', $temperature, $feelsLikeTemp, $pressure, $windSpeed, $humidity)";

        if (mysqli_query($conn, $insertData)) {
            // echo "Data inserted";
        } else {
            echo "Failed to insert data " . mysqli_error($conn);
        }
    } else {
        $updateData = "UPDATE weatherData SET locTime='$locTime', dayOfWeek='$dayOfWeek', monthDate='$monthDate', cityName='$cityName', country='$country', weatherIcon='$weatherIcon', weatherDescription='$weatherDescription', temperature=$temperature, feelsLikeTemp=$feelsLikeTemp, pressure=$pressure, windSpeed=$windSpeed, humidity=$humidity WHERE cityName='$cityName' AND monthDate='$monthDate'";

        if (mysqli_query($conn, $updateData)) {
            // echo "Data updated";
        } else {
            echo "Failed to update data " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
    ?>

    <script src="script.js"></script>

</body>

</html>