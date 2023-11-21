<?php 
    require_once __DIR__ . DIRECTORY_SEPARATOR ."index.php";
    header('Content-Type: application/json; charset=utf-8');

    $apiKey = MY_CONFIG["Weather_Key"];

    if($_SERVER['REQUEST_METHOD'] === 'GET'){

        if(isset($_GET['lattitude']) && isset($_GET['longitude'])){
            $lattitude = $_GET['lattitude'];
            $longitude = $_GET['longitude'];
            $url = "http://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$lattitude,$longitude&days=3";
            $response = file_get_contents($url);

            if(!isset($response["error"])){
                echo $response;
            }
            else{
                header("HTTP/1.0 403 Forbidden");
            }
        }
        else{
            header("HTTP/1.0 403 Forbidden");
        }
    }
?>