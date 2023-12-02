<?php
    require_once __DIR__ . DIRECTORY_SEPARATOR . "DBConn.php";
    require_once __VENDOR__ . DIR_S . "autoload.php";

    use Firebase\JWT\BeforeValidException;
    use Firebase\JWT\ExpiredException;
    use Firebase\JWT\Key;
    use Minishlink\WebPush\Subscription;
    use Firebase\JWT\JWT;
    use Firebase\JWT\SignatureInvalidException;
    use ParagonIE\ConstantTime\Base64;

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
                die("_DBConn Preperation failed: " . $_DBConn->error);
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
                "subscription" => isset($enc_user["subscription"]) ? self::decrypt($enc_user["subscription"]) : null
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
                $expiration = time() + 30 * 60; //After 30 minutes
            }

            $tokenPayload = [
                "usr" => $username,
                "exp" => $expiration
            ];

            $token = JWT::encode($tokenPayload, self::$_privateKey, "RS256");

            return $token;

        }
        /**
         * Authorizes a user based on a JSON Web Token.
         *
         * @param string $jwt The JSON Web Token
         *
         * @return string|array The token's username or an empty string or on failure,
         *                     an array will be returned containing the information the error.
         */
        public static function Authorize(string $jwt): string|array {
            try {
                // Decoding the JWT token using the provided public key and algorithm RS256
                $decodedToken = JWT::decode($jwt, new Key(self::$_publicKey, "RS256"));

                // Extracting the username from the decoded token
                $username = $decodedToken->usr;

                return $username;
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
    }

?>