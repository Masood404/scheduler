<?php 
    require_once __DIR__.DIRECTORY_SEPARATOR."core-setup.php";

    //Treat all the warning as exceptions.
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }
        
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });

    echo "Please provide the following credentials:\n";
    
    echo "Database Host: ";
    $database_host = trim(fgets(STDIN));
    if(empty($database_host)){
        die("Database Host is required. Exiting setup.\n");
    }

    echo "Database Username: ";
    $database_username = trim(fgets(STDIN));
    if(empty($database_username)){
        die("Database Username is required. Exiting setup.\n");
    }
    
    echo "Database Password(Leave empty and enter if there is none): ";
    $database_password = trim(fgets(STDIN));

    echo "Database Name : ";
    $database_name = trim(fgets(STDIN));
    if(empty($database_name)){
        die("Database Name is required. Exiting setup.\n");
    }

    #region validate database credentials.
    try {
        // Create connection
        $conn = new mysqli($database_host, $database_username, $database_password, $database_name);
    
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        } else {
            echo 
            <<<OUTPUT
            
            ####################  
            Database connected successfully
            ####################
            \n
            OUTPUT;
        }
    
        // Close connection
        $conn->close();
    } catch (Exception $e) {
        // Handle the connection error
        die("Failed connecting to the database.\nConnection error: " . $e->getMessage() . "\nExiting setup.\n");
    }
    #endregion
    
    echo "Please provide the following API keys:\n";

    echo "Note, this is gonna use the maximum of 5 tokens for a request.\nOpen AI Api Key: ";
    $open_ai_key = trim(fgets(STDIN));
    if(empty($open_ai_key)){
        die("Open AI Api Key is required. Exiting setup.\n");
    }

    #region validate open ai api key            
    $aiCurl = curl_init();
    $headers = [
        "Authorization: Bearer $open_ai_key",
        "Content-Type: application/json"
    ];
    $requestParams = [
        "model" => "gpt-3.5-turbo",
            "messages" => [ 
                [
                    "role" => "user",
                    "content" => "This is a test."
                ]
            ],
        "max_tokens" => 5
    ];

    $aiCurlOptions = [
        CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestParams),
        CURLOPT_RETURNTRANSFER => true
    ];

    curl_setopt_array($aiCurl, $aiCurlOptions);

    try{
        $response = curl_exec($aiCurl);
        if(!$response){
            throw new Exception(curl_error($aiCurl), curl_errno($aiCurl));
        }
        if(isset(json_decode($response, true)["error"])){
            throw new Exception(json_decode($response, true)["error"]["message"]);
        }
    }
    catch(Exception $e){
        // Close the cURL session.
        curl_close($aiCurl);

        // die with exception.
        die("\nFailed validating Open Ai Api Key, The curl session to send a test prompt responded with: " . $e->getMessage() . " (Error Code: " . $e->getCode() . ")\nExiting setup.\n");
    }
    curl_close($aiCurl);
    echo 
    <<<OUTPUT
 
    ####################  
    Open AI API Key successfully validated
    ####################
    \n
    OUTPUT;
    #endregion

    echo "Weather Api Key: ";
    $weather_key = trim(fgets(STDIN));
    if(empty($weather_key)){
        die("Weather Api Key is required. Exiting setup.\n");
    }

    #region validate weather api key
    $weatherUrl = "http://api.weatherapi.com/v1/current.json?key=$weather_key&q=London";

    $weatherCurl = curl_init($weatherUrl);
    curl_setopt($weatherCurl, CURLOPT_RETURNTRANSFER, true);
    try {
        $response = curl_exec($weatherCurl);
        $httpCode = curl_getinfo($weatherCurl, CURLINFO_HTTP_CODE);

        if($httpCode != 200){
            throw new Exception(json_decode($response, true)["error"]["message"]);
        }

    } 
    catch (Exception $e) {
        curl_close($weatherCurl);
        // die with exception.
        die("\nFailed validating Weather Api Key, The curl session to request a test forecast responded with: ".$e->getMessage()."\n");
    }
    curl_close($weatherCurl);
    echo <<<OUTPUT

    ####################  
    Weather API Key successfully validated
    ####################
    \n
    OUTPUT; 
    #endregion

    echo "Generating VAPID keys...\n";

    try{
        $vapid = require(realpath(__DIR__."/generateVAPID.php"));
        $publicVapid = $vapid["publicKey"];
        $privateVapid = $vapid["privateKey"];
    }
    catch(Exception $e){
        die("\nFailed Generating VAPID keys, Generating Error: ".$e->getMessage()."\n");
    }

    echo 
    <<<OUTPUT
    The default XAMPP path is different for each operating system.
    Here is a list of default XAMPP paths:
        
    * Linux --> /opt/lampp
    * Mac --> /Applications/XAMPP
    * Windows --> C:\\xampp

    XAMPP path: 
    OUTPUT;

    $xampp = realpath(trim(fgets(STDIN)));
    if(empty($xampp)){
        die("Xampp directory path is required. Exiting setup\n");
    }
    if(!file_exists(realpath($xampp))){
        die("Xampp directory does not exist. Exiting setup.\n");
    }

    echo 
    <<<OUTPUT
    To find the server root file look for a directory called htdocs inside your xampp. 
    By default it is:

    * Linux --> /opt/lampp/htdocs
    * Mac --> /Application/XAMPP/htocs
    * Windows --> C:\\xampp\\htdocs

    ***NOTE*** 
    An already existing scheduler-config directory will be deleted!

    Web Root Path: 
    OUTPUT;
    
    $web_root = realpath(trim(fgets(STDIN)));
    if(empty($web_root)){
        die("Web root directory path is required. Exiting setup.\n");
    }
    if(!file_exists(realpath($web_root))){
        die("Web root directory does not exist. Exiting setup.\n");
    }

    echo "Creating the scheduler-config directory at xampp root...\n";

    $scheduler_config_path = $xampp.DIRECTORY_SEPARATOR."scheduler-config";

    //If a directory already exists delete it.
    if(file_exists($scheduler_config_path)){
        deleteDirectory($scheduler_config_path);
    }

    mkdir($scheduler_config_path);

    echo "Generating and putting rsa keys in files...\n";

    try{
        $rsaKeys = require(__DIR__.DIRECTORY_SEPARATOR."generateRSA.php");

        $pubKey = $rsaKeys["publicKey"];
        $privKey = $rsaKeys["privateKey"];

        //Create files
        $pubFile = fopen($scheduler_config_path.DIRECTORY_SEPARATOR."public_key.pem", "w");
        $privFile = fopen($scheduler_config_path.DIRECTORY_SEPARATOR."private_key.pem", "w");

        //Write rsa keys into the files
        fwrite($pubFile, $pubKey);
        fwrite($privFile, $privKey);

        //Close files
        fclose($pubFile);
        fclose($privFile);
    }
    catch(Exception $e){
        deleteDirectory($scheduler_config_path);
        die("\nFailed putting the RSA keys in the config directory, Error: ".$e->getMessage());
    }

    echo "At Xampp root, Creating the config.php file...\n";

    $xampp = str_replace("\\", "\\\\", $xampp);
    $web_root = str_replace("\\", "\\\\", $web_root);

    $configPhp = 
    <<<PHP
    <?php
    /*
        This file contains all the enviormental variables which contains sensitive keys or other data used for the 
        scheduler application, none of these credentials should be shared or somehow revealed to the frontend, all the keys
        such as api keys, vapid keys, ssl keys, etc. are different to everyone who wants to host this app are different.

        without this file, the scheduler app wil not run!

        If you have not configured this file through setup or if you have somehow provided wrong credentials, you will run into
        errors.

        If you wanted to update this file due to some errors or changing credentials, run setup.php through the command line.
        If you run into any problems please feel free to contact me with my details is in the README.

        Thank you.
    */

    \$public_key = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "public_key.pem");
    \$private_key = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."private_key.pem");

    return [
        "XAMPP" => "$xampp",
        "Web_Root" => "$web_root",

        "DB_Host" => "$database_host",
        "DB_User" => "$database_username",
        "DB_Password" => "$database_password",
        "DB_Name" => "$database_name",

        "Open_Ai_Key" => "$open_ai_key",
        "Weather_Key" => "$weather_key",

        "Public_VAPID" => "$publicVapid",
        "Private_VAPID" => "$privateVapid",

        "Public_Key" => \$public_key,
        "Private_Key" => \$private_key
    ];
    ?>
    PHP;

    try{
        $configFile = fopen($scheduler_config_path.DIRECTORY_SEPARATOR."config.php", "w");
        fwrite($configFile, $configPhp);
        fclose($configFile);
        
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR."config-path.txt", $scheduler_config_path);
    }
    catch(Exception $e){
        deleteDirectory($scheduler_config_path);
        die("Failed creating the config.php file. Error: ".$e->getMessage());
    }

    echo "Setting database...\n";

    try {
        require __DIR__.DIRECTORY_SEPARATOR."setup_database.php";
    } 
    catch (Exception $e) {
        die("Failed setting the database, Databse Error: ".$e->getMessage());
    }

    echo "Enabling the cronjob...\n";
    try{
        require __DIR__.DIRECTORY_SEPARATOR."enableCron.php";
    }
    catch (Exception $e){
        die("Failed enabling the cronjob used for scheduling notifications, Error: ".$e->getMessage());
    }

    echo 
    <<<OUTPUT

    ####################  
    Setup complete!
    ####################
    \n
    OUTPUT;
?>