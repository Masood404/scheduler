<?php
    /*
        This file is used to configure api endpoints.
    */

    require_once __DIR__.DIRECTORY_SEPARATOR."Users.php";

    /**
     * Authorizes through HTTP Authorization header.
     */
    function authorizeToken(){
        // Retrieve headers
        $headers = getallheaders();

        // Check for the Authorization header
        if (isset($headers["Authorization"])) {
            $authorizationHeader = $headers["Authorization"];
            if (strpos($authorizationHeader, 'Bearer') === 0) {
                $token = trim(substr($authorizationHeader, 7)); // Extract token after 'Bearer '
                return Users::Authorize($token);
            } else {
                return ["error" => "Bearer Token Unset"];
            }
        } else {
            return ["error" => "Authorization unset"];
        }
    }
    function sendErrorResponse(string $errorMessage, int $httpCode = 400){
        http_response_code($httpCode);
        echo json_encode([
            "error" => $errorMessage
        ]);
        exit();
    }
?>