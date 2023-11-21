<?php 
    require_once __DIR__ . DIRECTORY_SEPARATOR . "constants.php";

    //database connection

    $hostname = "localhost";
    $database = "scheduler";
    $username = "root";
    $password = "";

    $conn = new mysqli($hostname, $username, $password, $database);

    $head_html_item;
    function get_header($headHtmlItem = ''){
        global $head_html_item;
        $head_html_item = $headHtmlItem;
        $_header_path = __TEMPLATES__ . DIR_S . "header.php";

        if(file_exists($_header_path)){
            include $_header_path;
            return $_header_path;
        }
        else{
            die("Header file does not exist at: $_header_path");
        }
    }
    function get_footer(){
        $_footer_path = __TEMPLATES__ . DIR_S . "footer.php";

        if(file_exists($_footer_path)){
            include $_footer_path;
            return $_footer_path;
        }
        else{
            die("Footer file does not exist at: $_footer_path");
        }
    }

    function get_current_url(){
        $currentPagURL = HTTP_WRAPPER;
        $currentPagURL .= HTTP_HOST . $_SERVER['REQUEST_URI'];

        return $currentPagURL;
    }

    function get_default_head_items(){

        $styleCss = path_to_url(__CSS__) . '/style.css';
        $flatIcon = path_to_url(__NODE_MODULES__) . "/@flaticon/flaticon-uicons/css/all/all.css";

        $jqueryJs = path_to_url(__JS__) . '/jquery.min.js';
        $mainJs = path_to_url(__JS__) . '/main.js';
        $showdown = path_to_url(__NODE_MODULES__) . "/showdown/dist/showdown.min.js";

        $html = <<<EOD
            <link rel="stylesheet" href="$styleCss">
            <link rel="stylesheet" href="$flatIcon">
    
            <script src="$jqueryJs"></script>
            <script src="$showdown"></script>
            <script src="$mainJs"></script>
        EOD;

        return $html;
    }
    function get_head_html_item(){
        global $head_html_item;
        return $head_html_item;
    }

    /**
     * @param int $id - The id of the requested instance, negative or no value will return all the instances.
     */
    function getTask($id = null){
        global $conn;
        if($id < 0 || $id == null){
            //Get all task instances Query
            $getQ = "SELECT * FROM tasks;";
            
            $tasks = $conn->query($getQ)->fetch_all(MYSQLI_ASSOC);
            return $tasks;
        }
        else{
            //Get task instance Query 
            $getQ = "SELECT * FROM tasks
            WHERE id = $id;";

            $taskArr = $conn->query($getQ)->fetch_assoc(); //Store the query result in an assoc arrayS
            return $taskArr;
        }
    }

    /**
     * @param array $taskToCheck - The task to check if it overlaps any other task instance.
     */
    function hasDateOverlap(array $taskToCheck){
        $tasks = getTask();

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
        global $conn;

        $currentTime = new DateTime("now");
        $currentTimeStamp = $currentTime->getTimestamp();

        $getQ = "SELECT * FROM tasks
        WHERE startTime > $currentTimeStamp AND completed = 0
        ORDER BY ABS(startTime - $currentTimeStamp)
        LIMIT 1;";

        $nextTask = $conn->query($getQ)->fetch_assoc();

        return $nextTask;

    }
    /**
     * Delete a task record from the database
     * @param int $taskId
     */
    function deleteTask($taskId){
        global $conn;
    
        //Task id does not exist or out of bounds
        if($taskId > count(getTask()) || $taskId < 0){
            return false;
        }
        else{
            $deleteQ = "DELETE FROM tasks
            WHERE id = $taskId;";

            $conn->query($deleteQ);
            return true;
        }
    }

    
?>