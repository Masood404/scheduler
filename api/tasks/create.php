<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");
    require_once __SCRIPTS__.DIR_S."Task.php";

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    $authorizationResult = authorizeToken();
    // Check for authorization errors
    if(isset($authorizationResult["error"])){
        sendErrorResponse($authorizationResult["error"], 401);
    }

    // Successful authorization
    // Extract the username
    $username = $authorizationResult["usr"];

    if(!isset($_POST["task"])){
        print_r($_POST);
        sendErrorResponse("Required field task is missing in the request");
    }

    $task = $_POST["task"];

    if(gettype($task) != "array"){
        sendErrorResponse("The field task is expected to be of type json, ".gettype($task)." given.");
    }
    
    // Check for expected keys.
    if(!array_key_exists("title", $task) || !array_key_exists("startTime", $task) || !array_key_exists("endTime", $task)){
        sendErrorResponse("Unexpected task format");
    }

    try{
        // Construct a new Task object.
        $taskObj = new Task($username, null, $task["title"], $task["startTime"], $task["endTime"]);
        // Add the Task object's record to the database.
        $taskObj->add();

        // Echo the newly created task object in JSON.
        echo json_encode($taskObj->getAssoc());
    }
    catch (Exception $e){
        sendErrorResponse($e->getMessage());
    }
