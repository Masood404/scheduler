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

        /**
         * This method, executeQuery, takes a SQL query and an optional array of parameters to bind to the query. 
         * It prepares the statement, binds the parameters if provided, executes the query, 
         * fetches the results, and returns the fetched data.
         * You can then use this method to execute SQL queries in a more abstracted way.
         * 
         * __Example:__
         * <code>
         * <?php
         * $instance = DBConn::getInstance();
         *
         * $query = "SELECT * FROM chatscontents
         * WHERE id = ? OR id = ?; ";
         *
         * //Result is all the records with id being either 1 or 2 in this query example.
         * $result = $instance->executeQuery($query, [1, 2]); 
         * </code>
         * 
         * @param string $query The query, as a string. It must consist of a single SQL statement. 
         * The SQL statement may contain zero or more parameter markers represented by question mark (?) 
         * characters at the appropriate positions. Note : The markers are legal only in certain places in SQL statements. 
         * For example, they are permitted in the VALUES() list of an INSERT statement (to specify column values for a row), 
         * or in a comparison with a column in a WHERE clause to specify a comparison value. However, 
         * they are not permitted for identifiers (such as table or column names).
         * 
         * @param array|int|string $params An optional list array with as many elements as there are bound parameters in the SQL statement being executed. 
         * Only object or array values are treated as string that is converted to json format.
         */
        public function executeQuery(string $query, array|int|string $params = []) : mysqli_result|bool {
            $stmt = $this->connection->prepare($query);
    
            if (!$stmt) {
                throw new Exception("Failed preparing query");
            }

            if(!is_array($params)){
                $params = [$params];
            }
    
            if (!empty($params)) {
                $types = '';
                $bindParams = [];
    
                /**
                 * Generates the appropriate types string for binding those parameters in a prepared statement.
                 */
                foreach ($params as $value) {
                    switch (gettype($value)) {
                        case 'boolean':
                        case 'integer':
                            $types .= 'i';
                            break;
                        case 'string':
                            $types .= 's';
                            break;
                        case 'array':
                        case 'object':
                            $value = json_encode($value);
                            $types .= 's';
                            break;
                        default:
                            throw new Exception("Could not determine value types");
                    }
    
                    $bindParams[] = $value;
                }
    
                // Bind parameters with determined types
                if (!empty($types)) {
                    $stmt->bind_param($types, ...$bindParams);
                }
            }
    
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute query");
            }
    
            $result = $stmt->get_result();
    
            return $result;
        }
    }
?>