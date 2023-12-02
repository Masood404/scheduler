<?php
    require_once dirname(__DIR__).DIRECTORY_SEPARATOR."DBConn.php";

    /**
     * Represents an interface to manage chats and their contents in the database.
     */
    class ChatsDBManager {
        private DBConn $_DBConn;

        /**
         * The name of the table containing chat records which contains data on: 
         * - Chat Id
         * - Chat Title
         * - Chat Username
         */
        private const CHATS_TABLE = "chats";

        /**
         * The name of the table containing chat's contents records which contains data on:
         * - Record's Content Id
         * - Record's Message
         * - Record's Response
         * - Record's Chats Id
         */
        private const CHATS_CONTENTS_TABLE = "chatscontents";
    
        public function __construct() {
            $this->_DBConn = DBConn::getInstance();
        }

        /**
         * Get all the chat and its content for a username.
         * @param string $username The username to retrieve chats for
         * @param int|null $chatId If provided, filters only a single chat record and its content (optional)
         * @return array An array of chats with associated contents
         */
        public function getChat(string $username, int|null $chatId = null) {
            $_DBConn = $this->_DBConn;
        
            // SQL query to retrieve chats and their corresponding contents
            $query = "
                SELECT c.*, cc.id as contentId, cc.message, cc.response, cc.chatId
                FROM chats c 
                LEFT JOIN chatscontents cc ON c.id = cc.chatId
                WHERE c.username = ?;
            ";
            $param_s = $username;

            //Check either if chatId parameter is provided
            if($chatId != null){
                $query = "
                    SELECT c.*, cc.id as contentId, cc.message, cc.response, cc.chatId
                    FROM chats c 
                    LEFT JOIN chatscontents cc ON c.id = cc.chatId
                    WHERE c.username = ? AND c.id = ?;
                ";
                $param_s = [$username, $chatId];
            }

            // Execute the query and get the result
            try{
                $result = $_DBConn->executeQuery($query, $param_s);
            }
            catch(Exception){   
                throw new Exception("Failed to fetch chat for a username");
            }
        
            $chats = [];
        
            // Loop through the fetched rows
            while ($row = $result->fetch_assoc()) {
                $chatId = $row['id'];
        
                // If the chat does not exist in the $chats array, create it
                if (!isset($chats[$chatId])) {
                    $chats[$chatId] = [
                        'id' => $chatId,
                        'username' => $row['username'],
                        'contents' => [],
                    ];
                }
        
                // If there is chat content associated with the chat, add it to 'contents' array
                if ($row['chatId']) {
                    $chats[$chatId]['contents'][] = [
                        'id' => $row['contentId'], 
                        'message' => $row['message'],
                        'response' => $row['response'],
                        'chatId' => $row['chatId']                
                    ];
                }
            }
            
            if(empty($chats)){
                throw new Exception("The provided chat id does not exist in the database.");
            }

            // Return the array of chats (indexed numerically)
            return array_values($chats);
        }        

        /**
         * Add a new chat record.
         * @param string $title The title of the chat
         * @param string $username The username associated with the chat
         * @return array The details of the added chat
         */
        public function addChat(string $title, string $username): array {
            $_DBConn = $this->_DBConn;

            $_chats = self::CHATS_TABLE;
    
            //Query to insert a chat record.
            $addQuery = "INSERT INTO $_chats (title, username) VALUES (?, ?);";

            //Execute the query.
            $_DBConn->executeQuery($addQuery, [$title, $username]);
        
            //Get the Id of the latest record that got add.
            $lastId = $_DBConn->getConnection()->insert_id;
            
            //Select query to get the latest record
            $selectQuery = "SELECT * FROM $_chats WHERE id = ?;";

            //Execute and check for errors.
            try {
                $result = $_DBConn->executeQuery($selectQuery, $lastId);
            } 
            catch (Exception) {
                //throw $th;
                throw new Exception("Failed to fetch the recently created chat record from the db");
            }
    
            $result = $result->fetch_assoc();
            
            return $result;
        }
    
        /**
         * Add a new content for a chat record.
         * @param int $chatId The ID of the chat to which content is added
         * @param string $message The message content
         * @param string $response The response content
         */
        public function addChatContent(int $chatId, string $message, string $response): void {
            $_DBConn = $this->_DBConn;
    
            $_chatsContents = self::CHATS_CONTENTS_TABLE;

            //Query to insert a new chat content.
            $query = "INSERT INTO $_chatsContents (chatId, message, response) VALUES (?, ?, ?);";
            
            try{
                $_DBConn->executeQuery($query, [ $chatId, $message, $response ]);
            }
            catch(Exception){
                throw new Exception("Failed executing adding a chat content to the db");
            }
        }

         /**
         * Delete the entire chat and its contents from the DB.
         * @param int $chatId The ID of the chat to delete
         */
        public function deleteChat(string $username, int $chatId){
            $_DBConn = $this->_DBConn;

            //Query to delete the entire chat and its contents
            $query = "DELETE chats, chatscontents
            FROM chats
            JOIN chatscontents ON chats.id = chatscontents.chatId
            WHERE chats.username = ? AND chats.id = ?";

            try {
                $_DBConn->executeQuery($query, [$username, $chatId]);
            } 
            catch (Exception) {
                throw new Exception("Failed executing deleting chat from the db");
            }
            

        }
    }
?>