<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");
    require_once __SCRIPTS__.DIR_S."ChatsDBManager.php";
    require_once __SCRIPTS__.DIR_S."OpenAIChatHandler.php";

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    // Authorize the token
    $authorizationResult = authorizeToken();

    // Check for authorization errors
    if(isset($authorizationResult["error"])){
        sendErrorResponse($authorizationResult["error"], 401);
    }

    // Successful authorization
    // Extract the username
    $username = $authorizationResult;

    // Implement the chats interface that connects to the database.
    $ChatsDB = new ChatsDBManager();

    // Initializing variables
    $chatId = null;
    $prevMessages = null;

    // Check if 'prompt' field is present in the request
    if(!isset($_POST["prompt"])){
        sendErrorResponse("The required field prompt is missing in the request.", 400);
    }

    // Check if 'chatId' field is provided
    if(isset($_POST["chatId"])){
        // If field provided, retrieve and validate the chatId
        $chatId = $_POST["chatId"];

        try{
            // Retrieve the chat based on username and chatId
            $chat = $ChatsDB->getChat($username, $chatId);
        }
        catch(Exception $e){
            sendErrorResponse($e->getMessage(), 400);
        }

        // Fetch contents of the chat
        $chatsContents = $chat["contents"];

        // Prepare previous messages
        if(count($chatsContents) > 0){
            $prevMessages = [];
            foreach($chatsContents as $content){
                array_push($prevMessages,[
                    "role" => "assistant",
                    "content" => $content["response"]
                ]);
                array_push($prevMessages, [
                    "role" => "user",
                    "content" => $content["message"]
                ]);
            }
        }
    }

    // Extract the prompt from the request
    $prompt = $_POST["prompt"];

    try{
        // Initialize response variable
        $response = "";

        // Generate response based on previous messages and prompt
        if($prevMessages != null && $chatId != null){
            $response = OpenAIChatHandler::messagePrompt($prompt, $prevMessages);
        }
        else{
            // If no previous messages or chatId, generate a title and response
            $title = OpenAIChatHandler::GenerateTitle($prompt);
            $response = OpenAIChatHandler::messagePrompt($prompt);

            // Create a new chat and retrieve its ID
            $chatId = $ChatsDB->addChat($title, $username)["id"];
        }
        
        // Add content to the chat
        $ChatsDB->addChatContent($chatId, $prompt, $response);

        // Send the response as JSON
        echo json_encode([
            "response" => $response
        ]);
    }
    catch(Exception $e){
        // Catch and handle exceptions
        sendErrorResponse($e->getMessage(), 500);
    }
?>