<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");
    require_once __SCRIPTS__.DIR_S."Task.php";

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    $authorizationResult = authorizeToken();
    // Check for authorization errors.
    if(isset($authorizationResult["error"])){
        sendErrorResponse($authorizationResult["error"], 401);
    }

    // Successful authorization
    // Extract the username
    $username = $authorizationResult["usr"];

    // Initialize the task id as nullable.
    $taskId = null;

    if(isset($_GET["id"])){
        // Extract the task id from the request.
        $taskId = $_GET["id"];
    }

    try{
        if($taskId == null){
            // Echo all the task records from the database.
            echo json_encode(Task::getAll($username, true));
        }
        else{
            // Echo the requested task record from the database.
            echo json_encode(Task::getTaskById($username, $taskId)->getAssoc());
        }
    }
    catch(NoTaskFoundException $e){
        // Error with a 400 response code (bad request)
        sendErrorResponse($e->getMessage());
    }
    catch(Exception $e){
        // Error with a 500 response code (Unkown error or internal server error)
        sendErrorResponse($e->getMessage(), 500);
    }
?>