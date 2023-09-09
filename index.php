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
            <div class="create-task-wrapper">
                <button class="create-task">
                    <i class="fi fi-bs-plus create-task-icon"></i>
                    Create Task
                </button>
            </div>
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
                        <li class="calendar-inactive-days">1</li>
                        <li>2</li>
                        <li>3</li>
                        <li>4</li>
                        <li>5</li>
                        <li>6</li>
                        <li>7</li>
                        <li>8</li>
                        <li>9</li>
                        <li>10</li>
                        <li>11</li>
                        <li>12</li>
                        <li>13</li>
                        <li>14</li>
                        <li>15</li>
                        <li>16</li>
                        <li>17</li>
                        <li>18</li>
                        <li>19</li>
                        <li>20</li>
                        <li>21</li>
                        <li>22</li>
                        <li>23</li>
                        <li>24</li>
                        <li>25</li>
                        <li>26</li>
                        <li>27</li>
                        <li>28</li>
                        <li>29</li>
                        <li>30</li>
                        <li id="calendar-current-day">31</li>
                    </ul>
                </div>
            </div>
            <div class="task-history-wrapper">

            </div>
        </aside>
        <div class="hours">
            someText
        </div>
    </main>
    <?php get_footer();?>
 </body>