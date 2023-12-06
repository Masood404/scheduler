<?php
    require_once realpath(__DIR__."/../../includes/constants.php");

    header("Content-Type: application/js");

    $dynamicJs = "const __project_url__ = \"".__PROJECT_URL__."\"\n";
    $staticJs = file_get_contents(__JS__.DIR_S."static".DIR_S."main.js");

    echo $dynamicJs.$staticJs;
?>