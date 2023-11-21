<?php
    /**
     * @param string $url
     * @param string $text
     * @param string $title
     */
    function navElem($url, $text, $title = null){
        preg_match("/<title>(.*)<\/title>/siU", get_head_html_item(), $title_matches);

        if($title === null){
            $title = $text;
        }

        if(isset($title_matches[1]) && $title == $title_matches[1]){
            echo <<<EOD
                <li class = "currentPageNavElem"><a href="$url">$text</a></li>
            EOD;
        }
        else{
            echo <<<EOD
                <li><a href="$url">$text</a></li>
            EOD;
        }
    }
?>
<head>
    <?php 
    echo get_default_head_items(); 
    echo get_head_html_item();
    ?>
</head>
<header>
    <div class="brandContainer">
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
            <?php
            navElem(__PROJECT_URL__, 'Home');
            navElem(__PROJECT_URL__ . "/chat-gpt", 'Chat GPT');
            navElem(__PROJECT_URL__ . "/weather", 'Weather'); 
            ?>
        </ul>
        </nav>
        <div class="menuButtonContainer">
            <i class="fi fi-br-menu-burger navButton"></i>
        </div>
    </div>
</header>