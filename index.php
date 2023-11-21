<?php
    //Dependecies
    require_once __DIR__ . DIRECTORY_SEPARATOR  ."includes"  . DIRECTORY_SEPARATOR .'index.php';
?>
<?php
    $homeCss = path_to_url(__CSS__) . "/home.css";
    $jsencrypt = path_to_url(__NODE_MODULES__) . "/jsencrypt/bin/jsencrypt.min.js";
    $homeJs = path_to_url(__JS__) . "/home.js";
    $headElems = <<<EOD
        <link rel="stylesheet" href="$homeCss">
        <script src="$jsencrypt"></script>
        <script src="$homeJs"></script>

        <title>Home</title>
    EOD;    
    get_header($headElems);
 ?>
<body>
    <main>      
        <label>Start Date</label>
        <br>
        <input type="number" id="start-hour">
        <input type="number" id="start-minute">
        <br>
        <label>End Date</label><br>
        <input type="number" id="end-hour">
        <input type="number" id="end-minute">
        <br>
        <br>
        <label for="cron-expression">Cron Expression</label>
        <br>
        <input type="text" id="cron-expression">
        <br>
        <br>
        <input type="button" id="create-task" value="Create Task">
        <input type="button" id="fetch-tasks" value="Fetch Tasks">
        <br>
        <br>
        <label for="task-id">Task Id</label>
        <br>
        <input type="number" id="task-id">
        <br>
        <br>
        <input type="button" id="complete-task" value="Complete">
        <input type="button" id="delete-task" value="Delete">
        <input type="button" id="subscribe-endpoint" value="subscribe">
        <br>
        <br>
        <label for="username">Username</label>
        <br>
        <input type="text" id="username">
        <br>
        <label for="passowrd">Passowrd</label>
        <br>
        <input type="password" id="password">
        <br>
        <input type="button" value="Submit" id="submit">
        <br>
    </main>
    <?php get_footer();?>
 </body>