<?php
require_once realpath(__DIR__."/../../includes/api_init.php");
require_once __SCRIPTS__.DIR_S."Task.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

$authorizationResult = authorizeToken();
// Check for authorization error.
if(isset($authorizationResult["error"])){
    sendErrorResponse($authorizationResult["error"], 401);
}
// Check request method.
if($_SERVER['REQUEST_METHOD'] !== 'PUT'){
    sendErrorResponse('Bad Request Method: Only PUT requests are allowed', 405);
}

$_PUT = file_get_contents('php://input');
$_PUT = json_decode($_PUT, true);

// Extract the username 
$username = $authorizationResult["usr"];

if(!isset($_PUT["tasks"])){
    sendErrorResponse("Required field tasks is missing in the request:");
}

$tasks = $_PUT['tasks'];
if(!$tasks || $tasks == null){
    sendErrorResponse("Field task for tasks is expected formatted as a JSON array");
}

try {
    foreach($tasks as $task){
        // Get the task instance.
        $taskInstance = Task::getTaskById($username, $task['id']);

        // Update the completd status of the task instance.
        $taskInstance->complete($task['completed']);

        // Empty json
        echo json_encode([]);
    }
} 
catch (Exception $th) {
    sendErrorResponse($e->getMessage(), 500);
}