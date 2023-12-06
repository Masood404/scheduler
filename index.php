<?php
    /*
        Front controller.
    */

    require_once realpath(__DIR__."/includes/constants.php");

    $requestURI = $_SERVER["REQUEST_URI"];
    $baseUri = path_to_uri(__PROJECT__);

    $pages = scandir(__PROJECT__.DIR_S."view");

    // Remove '.' (current directory) and '..' (parent directory) from $pages
    $pages = array_diff($pages, array('.', '..'));

    $pageFound = false;

    foreach($pages as $page){
        //remove the ".php" from file names.
        $pageName = str_replace(".php", "", $page);
        // Optional trailing slash and case-insensitive
        $pagePattern = "/$pageName\/?/i"; 
        
        if(preg_match($pagePattern, $requestURI)){
            require_once __VIEW__.DIR_S.$page;
            $pageFound = true;
            break;
        }
    }
    if (!$pageFound && $requestURI == $baseUri."/") {
        require_once __VIEW__.DIR_S."home.php";
    } 
    elseif($requestURI == path_to_uri(__CSS__)."/style.css"){
        require_once __CSS__.DIR_S."style.php";
    }
    elseif(!$pageFound) {
        require_once "404.php";
    }

?>