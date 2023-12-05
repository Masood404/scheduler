<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    if(isset($_GET["key"])){
        $requestedKey = $_GET["key"];
        
        if($requestedKey == "vapid"){
            echo json_encode([
                "vapidKey" => MY_CONFIG["Public_VAPID"]
            ]);
        }
        elseif($requestedKey == "rsa"){
            echo json_encode([
                "rsaKey" => MY_CONFIG["Public_Key"]
            ]);
        }
        else{
            sendErrorResponse("Invalid query parameter key");
        }
    }
    else{
        sendErrorResponse("Invalid request");
    }
?>