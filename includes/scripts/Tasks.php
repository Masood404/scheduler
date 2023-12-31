<?php
    /*
        This file contains utility functions for tasks records in the database.
    */

    require_once __DIR__ . DIRECTORY_SEPARATOR . "DBConn.php";

    /**
     * Represents a database interface for Tasks.
     */
    class Tasks{
        private function __construct(){
            //Uninstantiable
        }

        /**
         * @param int $id - The id of the requested instance, negative or no value will return all the instances.
         */
        public static function getTask($id = null){
            $_DBConn = DBConn::getInstance()->getConnection();
            if($id < 0 || $id == null){
                //Get all task instances Query
                $getQ = "SELECT * FROM tasks;";
                
                $tasks = $_DBConn->query($getQ)->fetch_all(MYSQLI_ASSOC);
                return $tasks;
            }
            else{
                //Get task instance Query 
                $getQ = "SELECT * FROM tasks
                WHERE id = $id;";
    
                $taskArr = $_DBConn->query($getQ)->fetch_assoc(); //Store the query result in an assoc arrayS
                return $taskArr;
            }
        }
    
        /**
         * @param array $taskToCheck - The task to check if it overlaps any other task instance.
         */
        function hasDateOverlap(array $taskToCheck){
            $tasks = self::getTask();
    
            foreach($tasks as $task){
                if(
                    //Overlapping Conditions
                    ($taskToCheck["startTime"] >= $task["startTime"] && $taskToCheck["startTime"] <= $task["endTime"]) ||
                    ($taskToCheck["endTime"] >= $task["startTime"] && $taskToCheck["endTime"] <= $task["endTime"]) ||
                    ($taskToCheck["startTime"] < $task["startTime"] && $taskToCheck["endTime"] > $task["endTime"])
                ){
                    return true;
                }
            }
            return false;
        }
        /**
         * The nearest next task from the current time
         */
        function getNextTask(){
            $_DBConn = DBConn::getInstance()->getConnection();
    
            $currentTime = new DateTime("now");
            $currentTimeStamp = $currentTime->getTimestamp();
    
            $getQ = "SELECT * FROM tasks
            WHERE startTime > $currentTimeStamp AND completed = 0
            ORDER BY ABS(startTime - $currentTimeStamp)
            LIMIT 1;";
    
            $nextTask = $_DBConn->query($getQ)->fetch_assoc();
    
            return $nextTask;
    
        }
        /**
         * Delete a task record from the database
         * @param int $taskId
         */
        function deleteTask($taskId){
            $_DBConn = DBConn::getInstance()->getConnection();
        
            //Task id does not exist or out of bounds
            if($taskId > count(self::getTask()) || $taskId < 0){
                return false;
            }
            else{
                $deleteQ = "DELETE FROM tasks
                WHERE id = $taskId;";
    
                $_DBConn->query($deleteQ);
                return true;
            }
        }
    }

?>