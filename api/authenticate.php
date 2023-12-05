<?php
    /*
        TEST FILE
    */

    require_once realpath(__DIR__."/../includes/api_init.php");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, X-Requested-With");

    if(!isset($_POST["username"]) && !isset($_POST["password"])){
        sendErrorResponse("Invalid credentials", 401);
    }

    $username = $_POST["username"];
    $password = $_POST["password"];

    echo json_encode(["token" => Users::Authenticate($username, $password)]);

?>