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
        <aside>
            <div class="calendar-wrapper">
                <div class="calendar">
                    <div class="calendar-top">
                        <i class="fi fi-rr-angle-small-left calendar-month-changer" id="month-prev"></i>
                        <div id="calendar-month">
                            August 2023
                        </div>
                        <i class="fi fi-rr-angle-small-right calendar-month-changer" id="month-next"></i>
                    </div>
                    <ul class="calendar-week-days">
                        <li>Sun</li>
                        <li>Mon</li>
                        <li>Tue</li>
                        <li>Wed</li>
                        <li>Thu</li>
                        <li>Fri</li>
                        <li>Sat</li>
                    </ul>
                    <ul class="calendar-days">
                        <!--Render calendar-->
                    </ul>
                </div>
            </div>
            <div class="create-task-wrapper">
                <button id="create-task">
                    <i class="fi fi-bs-plus create-task-icon"></i>
                    Create Task
                </button>
                <label for="task-title">Task Title</label><br>
                <input type="text" id="task-title">
                <div class="task-dates">
                    <div class="selected-date-wrapper">
                        <label for="selected-date-input">Start Date</label><br>
                        <input type="text" id="selected-date-input">
                    </div>
                    <div class="next-selected-date-wrapper">
                        <label for="next-date-input">End Date</label><br>
                        <input type="text" id="next-date-input">
                    </div>
                </div>
            </div>
            <!-- <div class="task-history-wrapper">

            </div> -->
        </aside>
        <div class="hours">
            <!--Render hours -->
        </div>
    </main>
    <?php get_footer();?>
 </body>