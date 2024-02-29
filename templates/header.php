<head>
    <?php 
    echo get_default_head_items(); 
    echo get_head_html_item();
    ?>
</head>
<header>
    <div class="brandContainer">
        <img src="<?php echo path_to_url(__IMAGES__).'/IT-Scheduler_Logo.png'; ?>" alt="">
        <h1>Scheduler</h1>
    </div>
    <div class="weatherBlock">
    <ul class="weatherContainer">
        <li class="todayWeather">
            <!--Add Data with js-->
        </li>
        <li class="tommorowWeather">
            <!--Add Data with js-->
        </li>
        <li class="afterTommorowWeather">
            <!--Add Data with js-->
        </li>
    </ul>
    </div>
    <div class="menuContainer">
        <nav>
        <ul>
            <!-- Render with js -->
        </ul>
        </nav>
        <div class="menuButtonContainer">
            <i class="fi fi-br-menu-burger navButton"></i>
        </div>
    </div>
</header>