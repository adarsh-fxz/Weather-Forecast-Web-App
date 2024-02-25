<?php

// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'weather_db';

$conn = mysqli_connect($servername, $username, $password, $database);
if ($conn) {
    // echo "Sql connected";
} else {
    echo "Sql not connected" . mysqli_connect_error();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['q'])) {
        $city = $_GET['q'];
    } else {
        $city = "Cebu City";
    }
}

$selectAllData = "SELECT * FROM weatherData WHERE cityName = '$city' ORDER BY id DESC LIMIT 7";
$result = mysqli_query($conn, $selectAllData);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    $json_data = json_encode($rows); // convert php array to json format
    echo $json_data;

} else {
    $errorResponse = ['error' => true, 'message' => 'No data found'];
    $json_data = json_encode($errorResponse);
    echo $json_data;
}

mysqli_close($conn);