<?php
    /*
        This file contains utility functions for tasks records in the database.
    */

    require_once realpath(__DIR__."/../DBConn.php");

    /**
     * Represents a scheduled Task and also represents a database interface for Tasks.
     */
    class Task{
        private string $username;
        private ?int $id;
        private string $title;
        private int $startTime;   
        private int $endTime;
        private bool $completed;

        /**
         * Create a task object.
         * This does not schedule's the task.
         * 
         * @param string $username The username associated with the task.
         * @param ?int $id The task's id. Id is nullable so when the add record gets triggered, it will be assigned then.
         * @param string $title The task's title.
         * @param int|DateTime $startTime The task's start time in Unix timestamp with the unit of minutes or a DateTime object.
         * @param int|DateTime $endTime The task's end time in Unix timestamp with the unit of minutes or a DateTime object.
         */
        public function __construct(string $username, int $id = null, string $title, int|DateTime $startTime, int|DateTime $endTime, bool $completed = false) {
            $this->username = $username;
            $this->id = $id;
            $this->title = $title;
            $this->startTime = $startTime;
            $this->endTime = $endTime;
            $this->completed = $completed;
        }
        /**
         * Add the task object's record to the database.
         * 
         * @return void Will return void on successful insert.
         * @throws Exception Throws an exception if the task object have a date period overlap with other task in the user's database, Can also throw other unexcpected ones.
         */
        public function add(){
            if($this->hasDateOverlap()){
                throw new Exception("The Task object contains a date period overlap with other tasks in the user's database.");
            }
            
            $DBConn = DBConn::getInstance();

            // Default query containing object's id in the query.
            $query = 
            <<<SQL
            INSERT INTO tasks (username, id, title, startTime, endTime, completed)
            VALUES (?, ?, ?, ?, ?, ?);
            SQL;

            // Check if id is null, if its null it will be assigned to the id provided by mysql.
            if(is_null($this->id)){   
                $query = <<<SQL
                INSERT INTO tasks (username, title, startTime, endTime, completed)
                VALUES (?, ?, ?, ?, ?);
                SQL;

                // Execute insert query without id. The id will be provided by the mysqli connection's insert_id property.
                $DBConn->executeQuery($query, [$this->username, $this->title, $this->startTime, $this->endTime, $this->completed]);

                // Get the latest record's insert id and .
                $this->id = $DBConn->getConnection()->insert_id;

                // Terminate the rest of the function.
                return;
            }

            // Include the object's id in the insert query if its not null.
            $DBConn->executeQuery($query, [$this->username, $this->id, $this->title, $this->startTime, $this->endTime, $this->completed]);
        }   
        /**
         * Get the current task object in associative array.
         */   
        public function getAssoc(){
            return [
                "username" => $this->username,
                "id" => $this->id,
                "title" => $this->title,
                "startTime" => $this->startTime,
                "endTime" => $this->endTime,
                "completed" => $this->completed
            ];
        }  
        /**
         * Represents a method to check if the task's time is not overlapping with other tasks in the database.
         */
        public function hasDateOverlap() : bool{
            $startTime = $this->startTime;
            $endTime = $this->endTime;

            $tasks = self::getAll($this->username);
    
            foreach($tasks as $task){
                if(
                    //Overlapping Conditions
                    ($startTime >= $task->startTime && $startTime <= $task->endTime) ||
                    ($endTime >= $task->startTime && $endTime <= $task->endTime) ||
                    ($startTime < $task->startTime && $endTime > $task->endTime)
                ){
                    return true;
                }
            }
            return false;
        }
        /**
         * Get the element by the username and id.
         * @param string $username
         * @param int $id
         * 
         * @return Task|NoTaskFoundException Returns the Task object on success, throws NoTaskFound exception on no object being found.
         */
        public static function getTaskById(string $username, int $id) : Task|NoTaskFoundException{
            $DBConn = DBConn::getInstance();

            // Query to run, it also checks if id is null.
            $query =  
            <<<SQL
            SELECT * FROM tasks
            WHERE username = ? AND id = ?;
            SQL;    

            try{
                $result = $DBConn->executeQuery($query, [$username, $id]);
                $result = $result->fetch_assoc();
            }
            catch(Exception $e){
                throw new Exception("Failed Executing query for getTask, Error: ".$e->getMessage());
            }

            if(empty($result)){
                throw new NoTaskFoundException();
            }

            return new self($result["username"], $result["id"], $result["title"], $result["startTime"], $result["endTime"], $result["completed"]);
        }

        /**
         * @param bool $assoc Specify wether the returned task's be in associative array.
         * @return Task[]|array All the task objects from the database.
         */
        public static function getAll(string $username, bool $assoc = false) : array{
            $DBConn = DBConn::getInstance();

            $query = 
            <<<SQL
            SELECT * FROM tasks 
            WHERE username = ?;
            SQL;

            try{
                $result = $DBConn->executeQuery($query, $username);
                $result = $result->fetch_all(MYSQLI_ASSOC);
            }
            catch(Exception $e){
                throw new Exception("Failed executing query for getAll, Error: ".$e->getMessage());
            }

            $tasks = [];

            if($assoc){
                $tasks = $result;
            }
            else{
                // Iterate through result to convert type to Task.
                foreach($result as $task){
                    array_push($tasks, new self($task["username"], $task["id"], $task["title"], $task["startTime"], $task["endTime"], $task["completed"]));
                }
            }

            return $tasks;

        }

    }
    class NoTaskFoundException extends Exception{
        public function __construct(string|null $message = null, int $code = 0, Throwable|null $previous = null) {
            $message = $message == null ? "No task found by id from the database" : $message;

            parent::__construct($message, $code, $previous);
        }
    };

?>