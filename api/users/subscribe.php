<?php
require_once realpath(__DIR__."/../../includes/api_init.php");
require_once __SCRIPTS__.DIR_S."Users.php";

header("Access-Control-Allow-Origin: *");
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

try{
    if(!isset($_POST["aesKey"]) || !isset($_POST["subscription"])){
        throw new Exception("The Encrypted AES key and the encrypted subscription key is unset in the required request field");
    }

    //Decrypt the aes key using the RSA key then decrypt the subscription using the aquired aesKey.
    $aesKey = Users::decrypt($_POST["aesKey"]);
    $sub = Users::decryptWithAes($_POST["subscription"], $aesKey);

    Users::subscribe($username, $sub);
}
catch(Exception $e){
    sendErrorResponse($e->getMessage());
}
