<?php
    //Dependecies
    require_once $_SERVER['DOCUMENT_ROOT'] . '/scheduler/includes/index.php';
?>
<html>
<?php 
$headItem = <<<EOD
    <link rel="stylesheet" href="/scheduler/assets/css/weather.css">
    <script src="/scheduler/assets/js/weather.js"></script>
EOD;
get_header($headItem);
?>
<body>
    <main>
        <section class="weatherSelectorWrapper">
        <div class="weatherSelector">
            <h2>Forecast</h2>
            <div class="weatherLatAndLon">
                <div class="w_locationInputWrapper">
                    <label for="lattitude">Lattitude</label>
                    <input type="text" id="lattitude">
                </div>
                <div class="currentLocationWrapper">
                    <button id="currentLocationButton">
                        Get Location
                    </button>
                </div>
                <div class="w_locationInputWrapper">
                    <label for="lattitude">Longitude</label>
                    <input type="text" id="longitude">
                </div>
            </div>
            <div class="w_forecastInputWrapper">
                <button id="w_decForLimit">
                    <i class="fi fi-br-angle-square-left w_buttonIcon"></i>
                </button>
                <span id="w_forecastLimit">
                    
                </span>
                <button id="w_incForLimit">
                    <i class="fi fi-br-angle-square-right w_buttonIcon"></i>
                </button>
            </div>
            <div class="w_forecastUpdateWrapper">
                <button id="w_forecastUpdate">
                    Update
                </button>
            </div>
        </div>
        </section>
        <section class="weatherWidgets">
            <!-- add weather widgets through js-->
        </section>
    </main>
    <?php get_footer();?>
 </body>
 </html>