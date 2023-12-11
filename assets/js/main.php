<?php
    require_once realpath(__DIR__."/../../includes/constants.php");

    header("Content-Type: application/javascript");

    $projectUrl = __PROJECT_URL__;
    
    $staticJs = file_get_contents(__JS__.DIR_S."static".DIR_S."main.js");

    $dynamicJs = <<<JS
    const __project_url__ = "$projectUrl";
    const navElems  = {
        "Home": "$projectUrl",
        "Weather": "$projectUrl/weather",
        "Chat GPT": "$projectUrl/chat-gpt",
        "Login": "$projectUrl/login",
        "Register": "$projectUrl/register"
    }
    
    $staticJs
    JS;

    echo $dynamicJs;
?>