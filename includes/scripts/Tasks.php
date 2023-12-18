<?php
    /*
        This file contains utility functions for tasks records in the database.
    */

    require_once realpath(__DIR__."/../DBConn.php");

    /**
     * Represents a database interface for Tasks.
     */
    class Tasks{
        private function __construct(){
            //Uninstantiable
        }

        /**
         * @param string $username - The username associated with the task.
         * @param int $id - The id of the requested instance, null value will return all the instances.
         */
        public static function getTask(string $username, null|int $id = null) : array|Exception{
            $_DBConn = DBConn::getInstance();

            // Query to run, it also checks if id is null.
            $query = is_null($id) ?
            <<<SQL
            SELECT * FROM tasks
            WHERE username = ?;
            SQL : 
            <<<SQL
            SELECT * FROM tasks
            WHERE username = ? AND id = ?;
            SQL;    

            try{
                $result = $_DBConn->executeQuery($query, is_null($id) ? $username: [$username, $id]);
            }
            catch(Exception $e){
                throw new Exception("Failed Executing query for getTask, Error: ".$e->getMessage());
            }

            return $result->fetch_all(MYSQLI_ASSOC);
        } 
        /**
         * @param string $username - The username to associate with the task.
         * @param DateTime|int $startTime - The task's start time. If its in int format it should be a unix timestamp in seconds.
         * @param DateTime|int $endTime - The task's end time. If its in int format it should be a unix timestamp in secons.
         * @param string $title - The title of the task.
         * 
         * @return array|Exception - The newly created task in associative array format.
         */
        public static function createTask(string $username, DateTime|int $startTime, DateTime|int $endTime, string $title){
            $_DBConn = DBConn::getInstance();

            $startTime = $startTime instanceof DateTime ? $startTime->getTimestamp() : $startTime;
            $endTime = $endTime instanceof DateTime ? $endTime->getTimestamp() : $endTime;

            $query = 
            <<<SQL
            INSERT INTO tasks (username, startTime, endTime, title)
            VALUES (?, ?, ?, ?);
            SQL;  

            try{
                // Run the create/insert query
                $_DBConn->executeQuery($query, [$username, $startTime, $endTime, $title]);
            }
            catch(Exception $e){
                throw new Exception("Failed executing query for creating a task, Error: ".$e->getMessage());
            }

            // Get the latest record's id
            $lastId = $_DBConn->getConnection()->insert_id;

            // Retrive the newly created task record from the database.
            $task = self::getTask($username, $lastId);

            return $task;

        }  
        /**
         * @param int|DateTime $startTime Task's start time.
         * @param int|DateTime $endTime Task's end time.
         */
        public static function hasDateOverlap(string $username, int|DateTime $startTime, int|DateTime $endTime){
            $startTime = $startTime instanceof DateTime ? $startTime->getTimestamp() : $startTime;
            $endTime = $startTime instanceof DateTime ? $startTime->getTimestamp() : $endTime;

            $tasks = self::getTask($username);
    
            foreach($tasks as $task){
                if(
                    //Overlapping Conditions
                    ($startTime >= $task["startTime"] && $startTime <= $task["endTime"]) ||
                    ($endTime >= $task["startTime"] && $endTime <= $task["endTime"]) ||
                    ($startTime < $task["startTime"] && $endTime > $task["endTime"])
                ){
                    return true;
                }
            }
            return false;
        }
        /**
         * The nearest next task from the current time
         */
        public static function getNextTask(){
            $_DBConn = DBConn::getInstance()->getConnection();
    
            $currentTime = new DateTime("now");
            $currentTimeStamp = $currentTime->getTimestamp();
    
            $getQ = "SELECT * FROM tasks
            WHERE startTime > $currentTimeStamp AND completed = 0
            ORDER BY ABS(startTime - $currentTimeStamp)
            LIMIT 1;";
    
            // This method does not utilize user input thus no need to bind parameters before executing the query.
            $nextTask = $_DBConn->query($getQ)->fetch_assoc();
    
            return $nextTask;
    
        }
        /**
         * Deletes a task record from the database
         * @param string $username The username associated with the task.
         * @param int[] $taskIds The task id(s) in array.
         */
        public static function deleteTask(string $username, array $taskIds){
            $_DBConn = DBConn::getInstance();
            
            // Start of the sql query.
            $query = 
            "
            DELETE FROM tasks
            WHERE username = ? AND id IN (
            ";

            // Construct the query to have place holders for each elements of the array.
            // Have the loop end one index before the last element.
            for($i = 0; $i < count($taskIds) - 1; $i++){
                $taskId = $taskIds[$i];

                if(!is_int($taskId)){
                    throw new Exception('Argument $taskIds elements is expected to be of type int to deleteTasks, '.gettype($taskId).' given');
                }
                $query .= "?, ";
            }

            // Add the last placeholder.
            $query .= "?);";

            try {
                $_DBConn->executeQuery($query, [$username, ...$taskIds]);
            } 
            catch (mysqli_sql_exception $e){
                throw new Exception("A database error occured at deleting task(s), Databse error: ".$e->getMessage());
            }
            catch (Exception $e) {
                throw new Exception("Failed deleting task(s), Error: ".$e->getMessage());
            }
        }
    }
?>