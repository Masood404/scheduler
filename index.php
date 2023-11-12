<?php
    //Dependecies
    require_once __DIR__ . '/includes/index.php';
?>
<?php
    $homeCss = get_css_direcrory_url() . "/home.css";
    $homeJs = get_js_directory_url() . "/home.js";
    $headElems = <<<EOD
        <link rel="stylesheet" href="$homeCss">
        <script src="$homeJs"></script>
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

    </main>
    <?php get_footer();?>
 </body>