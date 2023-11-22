<?php 
    require_once __DIR__ . DIRECTORY_SEPARATOR . "constants.php";

    $head_html_item;
    function get_header($headHtmlItem = ''){
        global $head_html_item;
        $head_html_item = $headHtmlItem;
        $_header_path = __TEMPLATES__ . DIR_S . "header.php";

        if(file_exists($_header_path)){
            include $_header_path;
            return $_header_path;
        }
        else{
            die("Header file does not exist at: $_header_path");
        }
    }
    function get_footer(){
        $_footer_path = __TEMPLATES__ . DIR_S . "footer.php";

        if(file_exists($_footer_path)){
            include $_footer_path;
            return $_footer_path;
        }
        else{
            die("Footer file does not exist at: $_footer_path");
        }
    }

    function get_current_url(){
        $currentPagURL = HTTP_WRAPPER;
        $currentPagURL .= HTTP_HOST . $_SERVER['REQUEST_URI'];

        return $currentPagURL;
    }

    function get_default_head_items(){

        $styleCss = path_to_url(__CSS__) . '/style.css';
        $flatIcon = path_to_url(__NODE_MODULES__) . "/@flaticon/flaticon-uicons/css/all/all.css";

        $jqueryJs = path_to_url(__JS__) . '/jquery.min.js';
        $mainJs = path_to_url(__JS__) . '/main.js';
        $showdown = path_to_url(__NODE_MODULES__) . "/showdown/dist/showdown.min.js";

        $html = <<<EOD
            <link rel="stylesheet" href="$styleCss">
            <link rel="stylesheet" href="$flatIcon">
    
            <script src="$jqueryJs"></script>
            <script src="$showdown"></script>
            <script src="$mainJs"></script>
        EOD;

        return $html;
    }
    function get_head_html_item(){
        global $head_html_item;
        return $head_html_item;
    }
    
?>