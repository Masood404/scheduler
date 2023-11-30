<?php
    require_once dirname(__DIR__).DIRECTORY_SEPARATOR."constants.php";

    /**
     * Get a forecast data from the real api endpoint which is from weatherapi.com
     * 
     * @param float $lat Lattitude
     * @param float $lat Longitude
     * @param int $days The amount of days to get data on (optional)
     */
    function fetchForecast(float $lat, float $lon, ?int $days = null, bool $json = false){
        $apiKey = MY_CONFIG["Weather_Key"];

        $days = $days == null ? 3 : $days;

        //Validate coordinates
        if(
            $lat < -90 || $lat > 90 ||
            $lon < -180 || $lon > 180
        ){
            throw new Exception("Forecast: Invalid lattitude or longitude.");
        }

        $_realEndpoint = "http://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$lat,$lon&days=$days";

        $response = file_get_contents($_realEndpoint);

        if(!$response){
            throw new Exception("Forecast: Error Retrieving forecast data from an external api.");
        }
        $response = json_decode($response);
        $response = $json ? json_encode($response) : $response;

        return $response;
    }   

?>