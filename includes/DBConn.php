<?php 
    require_once __DIR__ . DIRECTORY_SEPARATOR . "constants.php";
    
    /**
     * Represents a singleton which is used for database connection.
     */
    class DBConn {
        private static $instance = null;
        private $connection;
    
        private function __construct() {
            $this->connection = new mysqli(
                MY_CONFIG["DB_Host"],
                MY_CONFIG["DB_User"],
                MY_CONFIG["DB_Password"],
                MY_CONFIG["DB_Name"]
            );
    
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
        }
    
        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    
        public function getConnection() {
            return $this->connection;
        }
    
        // You can add other database-related methods here
    }

?>