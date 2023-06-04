<head>
    <link rel="stylesheet" href="<?php echo get_css_direcrory_url() . '/style.css'; ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . '/includes/packages/node_modules/@flaticon/flaticon-uicons/css/all/all.css'; ?>">

    <script src="<?php echo get_js_directory_url() . '/jquery.min.js' ?>"></script>
    <script src="<?php echo get_js_directory_url() . '/main.js' ?>"></script>
</head>
<header>
    <div class="brandContainer">
        <h1>Scheduler</h1>
    </div>
    <div class="weatherBlock">
    <ul class="weatherContainer">
        <li class="todayWeather">
            <i class="fi fi-rr-cloud-sun"></i>
            <h4>Monday</h4>
            <p><b>Min:</b> 20C&deg;<br><b><b>Current:</b> 22C&deg;<br>Max:</b> 25C&deg;</p>
        </li>
        <li class="tommorowWeather">
            <i class="fi fi-sr-cloud-showers"></i>  
            <h4>Tuesday</h4>
            <p><b>Min:</b> 20C&deg;<br><b>Max:</b> 25C&deg;</p>
        </li>
        <li class="afterTommorowWeather">
            <i class="fi fi-sr-sun"></i>
            <h4>Wednesday</h4>
            <p><b>Min:</b> 20C&deg;<br><b>Max:</b> 25C&deg;</p>
        </li>
    </ul>
    </div>
    <div class="menuContainer">
        <nav>
        <ul>
            <li><a href="/scheduler/">Home</a></li>
            <li><a href="#">Chat GPT</a></li>
            <li><a href="#">Weather</a></li>
        </ul>
        </nav>
        <div class="menuButtonContainer">
            <i class="fi fi-br-menu-burger navButton"></i>
        </div>
    </div>
</header>