<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");
    require_once __SCRIPTS__.DIR_S."ChatsDBManager.php";

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET");
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

    //Initialize the variable chatId as nullable
    $chatId = null;

    // Check if 'chatId' field is present in the request
    if(isset($_GET["chatId"])){
        //Extract the chatId from the request
        $chatId = $_GET["chatId"];
    }

    try{
        //Check if chatId is set then return a single chat else return all the available chat for the user.
        $chat_s = $chatId != null ? $ChatsDB->getChat($username, $chatId)[0] : $ChatsDB->getChat($username);

        echo json_encode($chat_s);    
    }   
    catch(Exception $e){
        sendErrorResponse($e->getMessage(), 400);
    }

?>