<?php
    require_once __DIR__ . DIRECTORY_SEPARATOR . "DBConn.php";
    require_once __VENDOR__ . DIR_S . "autoload.php";

use Firebase\JWT\Key;
use Minishlink\WebPush\Subscription;
use Firebase\JWT\JWT;

    class Users{
        private static string $_privateKey = MY_CONFIG["Private_Key"];
        private static string $_publicKey = MY_CONFIG["Public_Key"];
        private static string $_jwtAlgo = "RS256";

        private function __construct(){
            //Uninstantiable 
        }
        /**
         * @param string|array|null $subscription this string should be json formatted
         */
        public static function createUser(string $username, string $password, ?string $email = null, string|array|Subscription|null $subscription = null){
            global $_DBConn;

            //Hash the password and rencrypt the email.
            $hashPass = password_hash($password, PASSWORD_DEFAULT);
            $encEmail = $email != null ? self::encrypt($email) : null;

            //Handle subscription types by converting any other type to json formatted string so it can be stored in the db.
            if($subscription instanceof Subscription || is_array($subscription)){
                $json_encoded = json_encode($subscription, JSON_PRETTY_PRINT);
                if($json_encoded === false){
                    die("Users: failed encoding the subscription to json");
                }
                else{
                    $subscription = $json_encoded;
                }
            }
            elseif(is_string($subscription)){
                //Check if the string is in json format
                $decoded = json_decode($subscription);
                if($decoded == null || $subscription !== "null"){
                    die("Users: subscription is not formatted in json");
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
            global $_DBConn; 

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
            global $_DBConn;

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
                $expiration = time() + 30 * 30; //After 30 minutes
            }

            $tokenPayload = [
                "usr" => $username,
                "exp" => $expiration
            ];

            $token = JWT::encode($tokenPayload, self::$_privateKey, self::$_jwtAlgo);

            return $token;

        }
        /**
         * @return null|string
         */
        public static function getHashPass(string $username) : string|null{
            global $_DBConn;

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
        public static function encrypt(string $data){
            $pubKey = openssl_pkey_get_public(self::$_publicKey);

            openssl_public_encrypt($data, $enc_data, self::$_publicKey);
            $enc_data = base64_encode($enc_data);
            return $enc_data;
        }
        public static function decrypt(string $enc_data){
            $enc_data = base64_decode($enc_data);
            $privKey = openssl_pkey_get_private(self::$_privateKey);

            openssl_private_decrypt($enc_data, $dec_data, $privKey);

            return $dec_data;
        }
    }
?>