<?php
    /*
        This file is used to configure api endpoints.
    */

    require_once realpath(__DIR__."/scripts/Users.php");

    /**
     * Authorizes through HTTP Authorization header.
     * @return array The header authorization's token payload.
     */
    function authorizeToken(){
        // Retrieve headers
        $headers = getallheaders();

        // Check for the Authorization header
        if (isset($headers["Authorization"])) {
            $authorizationHeader = $headers["Authorization"];
            if (strpos($authorizationHeader, 'Bearer') === 0) {
                $token = trim(substr($authorizationHeader, 7)); // Extract token after 'Bearer '
                $tokenPayload = Users::Authorize($token);
                $tokenPayload["token"] = $token;
                return $tokenPayload;
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