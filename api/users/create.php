<?php
    require_once realpath(__DIR__."/../../includes/api_init.php");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, X-Requested-With");

    if($_SERVER["REQUEST_METHOD"] !== "POST"){
        sendErrorResponse("Method not supported", 405);
    }
    
    if(isset($_POST["createData"])){
        //For user creation.
        //Extract user data.
        $userData = json_decode(Users::decrypt($_POST["createData"]), true);
        //Extract email (nullable)
        $email = $userData["email"] != null ? $userData["email"] : null; 

        //Decryption error.
        if(!$userData){
            sendErrorResponse("Unable to decrypt user data for user creation");
        }

        //Both Aes key and subscription would either be set or null at the same time (nullable).
        $aesKey = null;
        $subscription = null;
        
        try{
            if(isset($_POST["aesKey"], $_POST["subscription"])){
                //Decrypt the aes key using the RSA key then decrypt the subscription using the aquired aesKey.
                $aesKey = Users::decrypt($_POST["aesKey"]);
                $sub = Users::decryptWithAes($_POST["subscription"], $aesKey);
            }
            else{
                throw new Exception("Failed decrypting subscription");
            }
    
            //Create the user
            Users::createUser($userData["username"], $userData["password"], $email, $sub);

            //Respond with an authorization token.
            echo json_encode([
                "authToken" => Users::Authenticate($userData["username"], $userData["password"])
            ]);
        }
        catch(Exception $e){
            sendErrorResponse($e->getMessage());
        }

    }
    else{
        sendErrorResponse("Required fields not provided");
    }
?>