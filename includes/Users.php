<?php
    require_once __DIR__ . DIRECTORY_SEPARATOR . "DBConn.php";
    require_once __VENDOR__ . DIR_S . "autoload.php";

    use Minishlink\WebPush\Subscription;

    class Users{
        private static string $privateKey = MY_CONFIG["Private_Key"];
        private static string $publicKey = MY_CONFIG["Public_Key"];

        private function __construct(){
            //Uninstantiable 
        }
        /**
         * @param string|array|null $subscription this string should be json formatted
         */
        public static function createUser(string $username, string $password, ?string $email = null, string|array|Subscription|null $subscription = null){
            global $_DBConn;

            //Re-encrypt user password and email to add to the database.
            $encPass = self::encrypt($password);
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

            $query = "INSERT INTO users (username, encPass, encEmail, subscription)
            VALUES (?, ?, ?, ?);";

            $stmt = $_DBConn->prepare($query);
            if(!$stmt){
                die("_DBConn Preperation failed: " . $_DBConn->error);
            }

            $stmt->bind_param("ssss", $username, $encPass, $encEmail, $subscription);
            $stmt->execute();
        }
        /**
         * Get the user data 
         * @return array|null
         */
        public static function getUser(string $username) : array|null{
            global $_DBConn; 

            $query = "SELECT * FROM users WHERE username = ?;";

            $stmt = $_DBConn->prepare($query);
            if(!$stmt){
                die("_DBConn Preperation failed: " . $_DBConn->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            $enc_user = $stmt->get_result()->fetch_assoc();
            $user = [
                "username" => $enc_user["username"],
                "password" => self::decrypt($enc_user["encPass"]),
                "email" => isset($enc_user["encEmail"]) ? self::decrypt($enc_user["encEmail"]) : null,
                "subscription" => isset($enc_user["subscription"]) ? self::decrypt($enc_user["subscription"]) : null
            ];

            return $user;
        }
        private static function encrypt(string $data){
            $pubKey = openssl_pkey_get_public(self::$publicKey);

            openssl_public_encrypt($data, $enc_data, self::$publicKey);
            $enc_data = base64_encode($enc_data);
            return $enc_data;
        }
        public static function decrypt(string $enc_data){
            $enc_data = base64_decode($enc_data);
            $privKey = openssl_pkey_get_private(self::$privateKey);

            openssl_private_decrypt($enc_data, $dec_data, $privKey);

            return $dec_data;
        }
    }
   
?>