<?php 
    require_once realpath(__DIR__."/../../includes/constants.php");

    header("Content-type: text/css; chatset: UTF-8");

    $dynamicStyle = "@import url('".path_to_url(__NODE_MODULES__)."/@flaticon/flaticon-uicons/css/all/all.css');\n";
    $staticStyle = file_get_contents(realpath(__DIR__."/static/style.css"));

    echo $dynamicStyle.$staticStyle;
