<?php
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "scripts" . DIRECTORY_SEPARATOR . "fetchForecast.php";

    header("Access-Control-Allow-Origin: " . path_to_url(__SERVER_ROOT__)); // Specify allowed domain for CORS

    header("Content-Type: application/json");
        
    try {
        $lat = isset($_GET["lat"]) ? $_GET["lat"] : null;
        $lon = isset($_GET["lon"]) ? $_GET["lon"] : null;
    
    
        if ($lat === null || $lon === null) {
            throw new Exception("Latitude or longitude parameter is unset");
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array("error" => $e->getMessage())); // Return standardized error response
        exit;
    }
        
    try {
        $forecastData = fetchForecast($lat, $lon, null, true);
        echo $forecastData; // Return forecast data in JSON format
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("error" => $e->getMessage())); // Return generic error message
        exit;
    }
    
?>