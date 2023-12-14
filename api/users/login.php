<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, X-Requested-With");

    if(!isset($_POST["loginData"])){
        sendErrorResponse("required field loginData not provided");
    }
    $loginData = $_POST["loginData"];

    try {
        $loginData = json_decode(Users::decrypt($loginData), true);
        
        //30 days or the default token expiration time.
        $authTokenExp = $loginData["remember"] == true ? time() + 60 * 60 * 24 * 30 : null;

        $authToken = Users::Authenticate($loginData["username"], $loginData["password"], $authTokenExp);

        echo json_encode([
            "authToken" => $authToken
        ]);
    } 
    catch (Exception $e) {
        sendErrorResponse($e->getMessage());
    }
?>