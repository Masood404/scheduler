<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");
    require_once __SCRIPTS__.DIR_S."Task.php";

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    
    $authorizationResult = authorizeToken();
    // Check for authorization error.
    if(isset($authorizationResult["error"])){
        sendErrorResponse($authorizationResult["error"], 401);
    }
    // Check request method.
    if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
        sendErrorResponse('Bad Request Method: Only DELETE requests are allowed', 405);
    }

    $_DELETE = file_get_contents('php://input');
    $_DELETE = json_decode($_DELETE, true);

    // Extract the username 
    $username = $authorizationResult["usr"];

    if(!isset($_DELETE["ids"])){
        sendErrorResponse("Required field ids for tasks is missing in the request:");
    }

    $taskIds = $_DELETE['ids'];
    if(!$taskIds || $taskIds == null){
        sendErrorResponse("Field ids for tasks is expected formatted as a JSON array");
    }

    try{
        Task::deleteByIds($username, $taskIds);

        echo json_encode(Task::getAll($username, true));
    }
    catch(Exception $e){
        sendErrorResponse($e->getMessage(), 500);
    }

?>