<?php
    require_once __DIR__ . DIRECTORY_SEPARATOR . "DBConn.php";
    class Users{
        private static string $privateKey = MY_CONFIG["Private_Key"];
        private static string $publicKey = MY_CONFIG["Public_Key"];

        private function __construct(){
            //Uninstantiable 
        }
        public static function createUser(string $username, string $password, ?string $email = null, ?string $subscription = null){
            global $_DBConn;

            //Re-encrypt user password and email to add to the database.
            $encPass = self::encrypt($password);
            $encEmail = $email != null ? self::encrypt($email) : null;

            $query = "INSERT INTO users (username, encPass, encEmail, subscription)
            VALUES (?, ?, ?, ?);";

            $stmt = $_DBConn->prepare($query);
            if(!$stmt){
                die("_DBConn Preperation failed: " . $_DBConn->error);
            }

            $stmt->bind_param("ssss", $username, $encPass, $encEmail, $subscription);
            $stmt->execute();
        }
        public static function getUser(string $username){
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
                "email" => self::decrypt($enc_user["encEmail"]),
                "subscription" => $enc_user["subscription"]
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