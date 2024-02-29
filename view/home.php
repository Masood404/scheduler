<?php 
    require_once realpath(__DIR__."/../includes/view_init.php");

    $homeCss = path_to_url(__CSS__)."/home.css";
    $homeJs = path_to_url(__JS__)."/home.js";

    $head = <<<HTML
        <link rel="stylesheet" href="$homeCss">
        <script src="$homeJs"></script>
        <title>Home</title>
    HTML;

    get_header($head);
?>
<body>
<main>      
    <aside>
        <div id="calendar">
            <!--Render Calendar via js-->
        </div>
    </aside>
    <section>
        <div id="hours-top">
            <div id="current-dayYdate">
                <div id="current-day">
                    <!--Render via js-->
                </div>
                <div id="current-date">
                <!--Render via js-->
                </div>
            </div>
            <div id="current-month">
                <!--Render via js-->
            </div>
            <form id="task-inputs">
                <div id="task-title-container">
                    <label for="task-title">Task Title</label>
                    <input type="text" id="task-title">
                </div>
                <fieldset id="start-time-container">
                    <legend>Start Time</legend>
                    <label for="start-hour">hour</label>
                    <input type="number" id="start-hour">
                    <label for="start-min">min</label>
                    <input type="number" id="start-min">
                </fieldset>
                <fieldset id="end-time-container">
                    <legend>End Time</legend>
                    <label for="end-hour">hour</label>
                    <input type="number" id="end-hour">
                    <label for="end-min">min</label>
                    <input type="number" id="end-min">
                </fieldset>
            </form>
            <div id="task-inputs-toggles">
                <i id="new-task" title="Create new task" class="fi fi-sr-add"></i>
                <i id="cancel-task" style="display: none;" title="Cancel new task" class="fi fi-sr-circle-xmark"></i>
            </div>
        </div> 
        <div id="hours">
            <!--Render Hours via js-->
        </div>
    </section>
</body>