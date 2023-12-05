<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");
    require_once __SCRIPTS__.DIR_S."ChatsDBManager.php";

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    //Authorize the token
    $authorizationResult = authorizeToken();

    // Check for authorization errors
    if(isset($authorizationResult["error"])){
        sendErrorResponse($authorizationResult["error"], 401);
    }

    // Successful authorization
    // Extract the username
    $username = $authorizationResult["usr"];

    // Implement the chats interface that connects to the database.
    $ChatsDB = new ChatsDBManager();

    // Check if 'chatId' field is present in the request
    if(!isset($_GET["chatId"])){
        //Extract the chatId from the request
        sendErrorResponse("The required field chatId is missing in the request.");
    }

    // Extract the chatId from the request
    $chatId = $_GET["chatId"];

    $chatId = json_decode($chatId, true);

    try{
        if(is_array($chatId)){
            //Delete multiple chats
            $ChatsDB->deleteChats($username, $chatId);
        }
        else{
            //Delete a single chat
            $ChatsDB->deleteChat($username, $chatId);
        }

        //Get all the chats for the user.
        echo json_encode($ChatsDB->getChat($username));
    }
    catch(Exception $e){
        if($e->getMessage() == "No matching resource found in the database for the provided parameters."){
            //Echo an empty array.
            echo json_encode([]);
        }
        else{
            sendErrorResponse($e->getMessage(), 400);
        }
    }

?>