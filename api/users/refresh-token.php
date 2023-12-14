<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    $authorizationResult = authorizeToken();

    if(isset($authorizationResult["error"])){
        sendErrorResponse($authorizationResult["error"], 401);
    }

    $token = $authorizationResult["token"];

    try{
        echo json_encode([
            "authToken" => Users::refreshToken($token)
        ]);
    }
    catch(Exception $e){
        sendErrorResponse($e->getMessage(), 401);
    }
?>