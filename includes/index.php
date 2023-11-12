<?php 
    $config = require("c:/xampp/.config/config.php");
    //scheduler project directory
    $schRoot = $_SERVER['DOCUMENT_ROOT'] . '/scheduler';

    $baseUrl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $baseUrl .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] . '/scheduler' : "";

    //database connection

    $hostname = "localhost";
    $database = "scheduler";
    $username = "root";
    $password = "";

    $conn = new mysqli($hostname, $username, $password, $database);

    #region paths get and print functions
    function get_templates_directory(){
        global $schRoot;

        if(file_exists($schRoot . '/templates')){
            return $schRoot . '/templates';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_templates_directory_url(){
        global $schRoot;
        global $baseUrl;

        if(file_exists($schRoot . '/templates')){
            return $baseUrl . '/templates';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_assets_directory(){
        global $schRoot;

        if(file_exists($schRoot . '/assets')){
            return $schRoot . '/assets';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_assets_directory_url(){
        global $schRoot;
        global $baseUrl;

        if(file_exists($schRoot . '/assets')){
            return $baseUrl . '/assets';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_css_direcrory(){
        if(file_exists(get_assets_directory() . '/css')){
            return get_assets_directory() . '/css';
        }
        else{
            throw_css_dir_error();
        }
    }
    function get_css_direcrory_url(){
        if(file_exists(get_css_direcrory())){
            return get_assets_directory_url() . '/css';
        }
        else{
            throw_css_dir_error();
        }
    }
    function get_js_directory(){
        if(file_exists(get_assets_directory() . '/js')){
            return get_assets_directory() . '/js';
        }
        else {
            throw_js_dir_error();
        }
    }
    function get_js_directory_url(){
        if(file_exists(get_js_directory())){
            return get_assets_directory_url() . '/js';
        }
        else{
            throw_js_dir_error();
        }
    }
    function get_images_directory(){
        if(file_exists(get_assets_directory() . '/images')){
            return get_assets_directory() . '/images';
        }
        else{
            throw_images_dir_error();
        }
    }
    function get_images_directory_url(){
        if(file_exists(get_images_directory())){
            return get_assets_directory_url() . '/images';
        }
        else{
            throw_images_dir_error();
        }
    }
    
    function get_header_path(){
        if(file_exists(get_templates_directory() . '/header.php')){
            return get_templates_directory() . '/header.php';
        }
        else{
            throw_header_file_error();
        }
    }
    function get_header_path_url(){
        if(file_exists(get_header_path())){
            return get_templates_directory_url() . '/header.php';
        }
        else{
            throw_header_file_error();
        }
    }
    $head_html_item;
    function get_header($headHtmlItem = ''){
        global $head_html_item;
        $head_html_item = $headHtmlItem;
        if(file_exists(get_header_path())){
            include get_header_path();
            return get_header_path();
        }
        else{
            throw_header_file_error();
        }
    }
    function get_footer_path(){
        if(file_exists(get_templates_directory() . '/footer.php')){
            return get_templates_directory() . '/footer.php';
        }  
        else{
            throw_footer_file_error();
        }
    }
    function get_footer_path_url(){
        if(file_exists(get_footer_path())){
            return get_templates_directory_url() . '/footer.php';
        }
        else{
            throw_footer_file_error();
        }
    }
    function get_footer(){
        if(file_exists(get_footer_path())){
            include get_footer_path();
            return get_footer_path();
        }
        else{
            throw_footer_file_error();
        }
    }

    function get_current_url(){
        $currentPagURL = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $currentPagURL .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return $currentPagURL;
    }

    #endregion

    #region custom exceptions
    
    function throw_template_dir_error(){
        throw new Exception('template directory does not exist:');
    }
    function throw_header_file_error(){
        throw new Exception('header.php does not exist in the template directory or the template directory does not exist:');
    }
    function throw_footer_file_error(){
        throw new Exception('footer.php does not exist in the template directory or the template directory does not exist:');
    }
    function throw_assets_dir_error(){
        throw new Exception('assets directory does not exist:');
    }
    function throw_css_dir_error(){
        throw new Exception('css directory does not exist:');
    }
    function throw_js_dir_error(){
        throw new Exception('js directory does not exist:');   
    }
    function throw_images_dir_error(){
        throw new Exception('js directory does not exist:');  
    }
    #endregion

    function get_default_head_items(){
        $styleCss = get_css_direcrory_url() . '/style.css';
        $jqueryJs = get_js_directory_url() . '/jquery.min.js';
        $mainJs = get_js_directory_url() . '/main.js';

        $html = <<<EOD
            <link rel="stylesheet" href="$styleCss">
            <link rel="stylesheet" href="/scheduler/includes/packages/node_modules/@flaticon/flaticon-uicons/css/all/all.css">
    
            <script src="$jqueryJs"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.4.0/showdown.min.js"></script>
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