<?php
    /*
        This file contains all the constants which contains information on certain directory paths, urls and references to certain
        sensitive keys or information like apikeys, ssl keys, vapid keys, database credentials, etc.
    */

    /**
     * Shorthand for the constant DIRECTORY_SEPARATOR,
     */
    define("DIR_S", DIRECTORY_SEPARATOR);

    #region system paths

    /* 
    The xampp and the server root paths vary based on the operating systems and certain other conditions.
    For further problems you can always get the contanct informations on the README file or https://github.com/Masood404/scheduler. 
    */

    /**
     * The getenv('XAMPP_PATH') checks for the path to xampp but for any other reason:
     * Change the default xampp path due to different operating system, for example:
     ** Linux --> /opt/lampp/htdocs
     ** Mac --> /Applications/XAMPP
     ** Windows --> C:\\xampp
     */
    define("__XAMPP__", getenv("XAMPP_PATH") ?: /* change this if any error occurs ----> */ "C:\\xampp");

    /**
     * The path to the configuratin dirctory which contains config files such as enviormental variables.
     */
    define("__CONFIG__", __XAMPP__ . DIR_S . ".config");

    /**
    * To find the server root file look for a directory called htdocs inside your xampp or web server. 
    */
    define("__SERVER_ROOT__", __XAMPP__ . DIR_S . "htdocs");
    
    /**
     * You can change this directory to the path towards this project if the project folder is not in a subfolder of the server root,
     * only have the server root for this constant
     */
    define("__PROJECT__", __SERVER_ROOT__ . DIR_S . "scheduler");   
    
    define("__ASSETS__", __PROJECT__  . DIR_S . "assets");
    define("__CSS__", __ASSETS__ . DIR_S . "css");
    define("__JS__", __ASSETS__ . DIR_S . "js");
    define("__IMAGES__"  , __ASSETS__ . DIR_S . "images");

    define("__TEMPLATES__", __PROJECT__ . DIR_S . "templates");

    define("__INCLUDES__", __PROJECT__ . DIR_S . "includes");
    define("__PACKAGES__", __INCLUDES__ . DIR_S . "packages");
    define("__NODE_MODULES__", __PACKAGES__ . DIR_S . "node_modules");
    define("__VENDOR__", __PACKAGES__ . DIR_S . "vendor");
 
    #endregion

    /**
     * @var mixed[] This contains all the project's enviormental variables.
     */
    define("MY_CONFIG", require(__CONFIG__ . DIR_S . "config.php"));

    #region url paths

    /**
     * The public root name of the server in the url or also known as host name, if your are in local development its probably 'localhost'
     */
    define("HTTP_HOST", "localhost");

    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $protocol = $isSecure ? "https://" : "http://";

    /**
     * The http wrapper based on a secured request or a non-secured request.
     */
    define("HTTP_WRAPPER", $protocol);

    /**
     * The project url could be the root or http host or in sub folder, 
     * The following statments to declare this constant automatically checks if the project is in a subfolder
     */
    define("__PROJECT_URL__", path_to_url(__PROJECT__));

    #endregion

    /**
     * Converts system paths to a url, for example:
     * c:/xampp/htdocs/scheduler/assets --> http://localhost/scheduler/assets
     * 
     * This functiion also requires constants of:
     * * HTTP_WRAPPER 
     * * HTTP_HOST
     * * \_\_SREVER_ROOT\_\_
     * * DIR_S
     * make sure to have these constant set vailidly.
     * 
     * @param string path the path you want to change to url
     * @return string HTTP_WRAPPER . HTTP_HOST . str_replace(DIR_S, "/",str_replace(__SERVER_ROOT__, "", $path));
     */
    function path_to_url($path){
        return HTTP_WRAPPER . HTTP_HOST . str_replace(DIR_S, "/",str_replace(__SERVER_ROOT__, "", $path));
    }
?>