<?php
    require_once realpath(__DIR__."/../DBConn.php");
    require_once __VENDOR__ . DIR_S . "autoload.php";

    use Firebase\JWT\BeforeValidException;
    use Firebase\JWT\ExpiredException;
    use Firebase\JWT\Key;
    use Minishlink\WebPush\Subscription;
    use Firebase\JWT\JWT;
    use Firebase\JWT\SignatureInvalidException;
    use ParagonIE\ConstantTime\Base64;

    /**
     * An interface used for users managment.
     */
    class Users{
        private static string $_privateKey = MY_CONFIG["Private_Key"];
        private static string $_publicKey = MY_CONFIG["Public_Key"];

        private function __construct(){
            //Uninstantiable 
        }
        /**
         * @param string|array|null $subscription If its a string it should be in json format.
         */
        public static function createUser(string $username, string $password, ?string $email = null, string|array|Subscription|null $subscription = null){
            $_DBConn = DBConn::getInstance()->getConnection();

            //Hash the password and rencrypt the email.
            $hashPass = password_hash($password, PASSWORD_DEFAULT);
            $encEmail = $email != null ? self::encrypt($email) : null;

            //Handle subscription types by converting any other type to json formatted string so it can be stored in the db.
            if($subscription instanceof Subscription || is_array($subscription)){
                $json_encoded = json_encode($subscription, JSON_PRETTY_PRINT);
                if($json_encoded === false){
                    throw new Exception("Users: failed encoding the subscription to json");
                }
                else{
                    $subscription = $json_encoded;
                }
            }
            elseif(is_string($subscription)){
                //Check if the string is in json format
                $decoded = json_decode($subscription);
                if($decoded == null || $subscription === "null"){
                    throw new Exception("Users: the subscription string should be formatted in json");
                }
            }

            $query = "INSERT INTO users (username, hashPass, encEmail, subscription)
            VALUES (?, ?, ?, ?);";

            $stmt = $_DBConn->prepare($query);
            if(!$stmt){
                throw new Exception("_DBConn Preperation failed: " . $_DBConn->error);
            }

            $stmt->bind_param("ssss", $username, $hashPass, $encEmail, $subscription);
            $stmt->execute();
        }
        /**
         * Get the user data such as username, user email, user subscription.
         * @return array|null
         */
        public static function getUser(string $username) : array|null{
            $_DBConn = DBConn::getInstance()->getConnection(); 

            $query = "SELECT * FROM users WHERE username = ?;";

            $stmt = $_DBConn->prepare($query);
            if(!$stmt){
                throw new Exception("Preperation failed: " . $_DBConn->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();
            if($result->num_rows === 0){
                //username not found
                return null;
            }
            
            $enc_user = $result->fetch_assoc();
            $user = [
                "username" => $enc_user["username"],
                "email" => isset($enc_user["encEmail"]) ? self::decrypt($enc_user["encEmail"]) : null,
                "subscription" => $enc_user["subscription"]
            ];

            return $user;
        }
        /**
         * Checks if the user exists
         */
        public static function exists(string $username) : bool{
            $_DBConn = DBConn::getInstance()->getConnection();

            $query = "SELECT CASE 
            WHEN EXISTS (SELECT 1 FROM users WHERE username = ?) 
            THEN 1 
            ELSE 0 
            END AS username_exists;";

            $stmt = $_DBConn->prepare($query);

            if(!$stmt){
                throw new Exception("_DBConn Preperation failed: " . $_DBConn->error);
            }
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $exists = $stmt->get_result()->fetch_assoc()["username_exists"];

            return $exists;
        }
        /**
         * Get a user's subscription from the database converted to the Subscription object.
         * @throws SubscriptionNotFoundException Will throw this Exception if no subscription were found in the databse.
         */
        public static function getSubscription(string $username) : Minishlink\WebPush\Subscription{
            $_DBConn = DBConn::getInstance();

            // Query to retrive the user's subscription.
            $query =
            <<<SQL
            SELECT subscription FROM users
            WHERE username = ?;
            SQL;

            // Execute and fetch subscrption.
            $subscription = $_DBConn->executeQuery($query, $username)->fetch_assoc()["subscription"];
            $subscription = json_decode($subscription, true);

            // Check if the returned subscription is null, means the user have not subscribed.
            if($subscription == null){
                // Achknowledge and terminate.
                throw new SubscriptionNotFoundException();
            }

            // Construct the subscription object.
            $subscriptionObj = Subscription::create($subscription);

            return $subscriptionObj;
        }
        /**
         * @return string An Authorization token
         */
        public static function Authenticate(string $username, string $password, null|int|DateTime $expiration = null) : string{
            if(!Users::exists($username)){
                throw new Exception("Authentication failed: Invalid username");
            }

            $hashPass = Users::getHashPass($username);

            if(!password_verify($password, $hashPass)){
                throw new Exception("Authentication failed: Invalid password");
            }
            if($expiration instanceof DateTime){
                $expiration = $expiration->getTimestamp();
            }
            elseif(is_null($expiration)){
                $expiration = time() + 60 * 60 * 24; //After 24 hours
            }

            //Generate a new jwt.
            $tokenPayload = [
                "usr" => $username,
                "exp" => $expiration,
                "salt" => self::generateRandomString()
            ];

            $token = JWT::encode($tokenPayload, self::$_privateKey, "RS256");

            return $token;

        }
        /**
         * Authorizes a user based on a JSON Web Token.
         *
         * @param string $jwt The JSON Web Token
         *
         * @return string|array The token's payload or an empty string or on failure,
         *                     an array will be returned containing the information the error.
         */
        public static function Authorize(string $jwt): array {
            try {
                // Decoding the JWT token using the provided public key and algorithm RS256
                $decodedToken = JWT::decode($jwt, new Key(self::$_publicKey, "RS256"));

                //Check if the user exists and the token is not blacklisted.
                $valid = self::exists($decodedToken->usr) && !self::isBlackListedToken($jwt);
                if(!$valid){
                    throw new ExpiredException("The provided token is blacklisted or the token's user does not exist");
                }

                //Convert decoded token to an associative array.
                $tokenArr = [
                    "usr" => $decodedToken->usr,
                    "exp" => $decodedToken->exp,
                    "salt" => $decodedToken->salt
                ];

                return $tokenArr;
            }
            catch (ExpiredException $e){
                return [
                    "error" => "Expired",
                    "errorMessage" => $e->getMessage()
                ];
            } 
            catch (BeforeValidException $e) {
                return [
                    "error" => "BeforeValid",
                    "errorMessage" => $e->getMessage()
                ];
            } 
            catch (SignatureInvalidException $e) {
                return [
                    "error" => "InvalidSignature",
                    "errorMessage" => $e->getMessage()
                ];
            } 
            catch (UnexpectedValueException $e) {
                return [
                    "error" => "InvalidToken",
                    "errorMessage" => $e->getMessage()
                ];
            }
            catch (Exception $e) {
                // Consider handling specific exceptions thrown during JWT decoding
                // Log or handle the exception as required
                // Returning an empty string in case of any exception for simplicity
                error_log('Authorization failed: ' . $e->getMessage());
                return [
                    "error" => "UnIdentifiedError",
                    "errorMessage" => $e->getMessage()
                ];
            }
        }
        /**
         * Refreshes a JWT token by re-authorizing and generating a new token based on the provided token.
         * 
         * @param string $jwt The JWT token to be refreshed.
         * @return string The newly generated JWT token.
         * @throws Exception If there's an error in authorization or during the token generation process.
         */
        public static function refreshToken(string $jwt){
            // Authorize token 
            $authResult = self::Authorize($jwt);

            // Check if there's an error in the authorization result
            if(isset($authResult["error"])){
                // Throw an exception with the specific error message if authorization fails
                throw new Exception($authResult["error"]);
            }

            //Black list the previous token
            self::blackListToken($jwt);

            //Change the token's salt which is its unique identifier.
            $authResult["salt"] = self::generateRandomString();

            // Generate a new token based on the authorized result and private key using RS256 algorithm
            return JWT::encode($authResult, self::$_privateKey, "RS256");
        }
 
        /**
         * Blacklists a JWT token by adding it to the token_black_list table in the database.
         * 
         * @param string $jwt The JWT token to be blacklisted.
         * @throws Exception If the provided token is already invalidated or encounters an error during the process.
         */
        public static function blackListToken(string $jwt){
            // Authorize token
            $authResult = self::Authorize($jwt);

            // Check if there's an error in the authorization result
            if(isset($authResult["error"])){
                // Throw an exception if the token is already invalidated
                throw new Exception("The provided token for the method Users::blackListToken is already invalidated");
            }

            // Get a database connection instance
            $_DBConn = DBConn::getInstance();  

            // Extract the expiration time from the token payload
            $exp = $authResult["exp"];

            // Prepare the SQL query to insert the token into the blacklist table
            $query = "INSERT INTO token_black_list (token, expiration)
                    VALUES (?, ?);";

            // Execute the query with token and expiration values
            $_DBConn->executeQuery($query, [$jwt, $exp]);
        }
        /**
         * Checks if a token is from the 'token_black_list' table in the database.
         * @param string $token The JWT token to check for.
         * @throws Exception If an error occurs during the database query execution.
         */
        public static function isBlackListedToken(string $token): bool|Exception{
            // Get a database connection interface
            $_DBConn = DBConn::getInstance();

            // The SQL query 
            $query = "SELECT CASE 
            WHEN EXISTS (SELECT 1 FROM token_black_list WHERE token = ?) 
            THEN 1 
            ELSE 0 
            END AS token_exists;";

            try {
                //Execute query and get result
                $result = $_DBConn->executeQuery($query, $token);

                return $result->fetch_assoc()["token_exists"];

            } catch (mysqli_sql_exception $e) {
                // Throw an exception if there's an error during the database query execution
                throw new Exception("Error run: " . $e->getMessage());
            }
        }
        

        /**
         * @return null|string The user's hashed password which is stored in the database.
         */
        public static function getHashPass(string $username) : string|null{
            $_DBConn = DBConn::getInstance()->getConnection();

            $query = "SELECT hashPass FROM users WHERE username = ?";
            $stmt = $_DBConn->prepare($query);

            if(!$stmt){
                throw new Exception("Preperation failed: " . $_DBConn->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();
            if($result->num_rows === 0){
                //username does not exist 
                return null;
            }
            $hasPass = $result->fetch_assoc()["hashPass"];

            return $hasPass;
            
        }
        /**
         * Encrypts with RSA
         */
        public static function encrypt(string $data){
            $pubKey = openssl_pkey_get_public(self::$_publicKey);

            openssl_public_encrypt($data, $enc_data, self::$_publicKey);
            $enc_data = base64_encode($enc_data);
            return $enc_data;
        }
        /**
         * Decrypts an RSA encryption
         */
        public static function decrypt(string $enc_data){
            $enc_data = base64_decode($enc_data);
            $privKey = openssl_pkey_get_private(self::$_privateKey);

            openssl_private_decrypt($enc_data, $dec_data, $privKey);

            return $dec_data;
        }
        /**
         * Decrypts an AES encryption.
         * @param string|Base64 $encrypted_data The encrypted data should be parsed into a base 64 string.
         * @param string|Base64 $aes_key The aes key should be parsed into a base 64 string.
         */
        public static function decryptWithAes(string|Base64 $encrypted_data, string|Base64 $aes_key): string|false{
            $aes_key = base64_decode($aes_key);
            $encrypted_data = base64_decode($encrypted_data);

            $decrypted = openssl_decrypt($encrypted_data, "aes-256-ecb", $aes_key, OPENSSL_RAW_DATA);

            return $decrypted;
        }
        /**
         * Function to generate a random string
         */
        public static function generateRandomString($length = 10) {
            // Define characters that will be used in generating the random string
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            
            // Initialize an empty string to store the generated random string
            $randomString = '';
            
            // Loop through the specified length to create the random string
            for ($i = 0; $i < $length; $i++) {
                // Append a randomly selected character from the character set to the random string
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            // Return the generated random string
            return $randomString;
        }

    }

    class SubscriptionNotFoundException extends Exception{
        public function __construct(string|null $message = null, int $code = 0, Throwable|null $previous = null) {
            $message = $message == null ? "No subscription found for the user in the database" : $message;

            parent::__construct($message, $code, $previous);
        }
    }

?>