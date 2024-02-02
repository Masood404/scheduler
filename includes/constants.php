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
     * The path to the configuratin dirctory which contains config files such as enviormental variables or system paths.
     * Should be set to the path of scheduler-config.php/config.php
     */
    define("__CONFIG__", file_get_contents(realpath(__DIR__.DIR_S."setup".DIR_S."config-path.txt")));

    /**
     * @var mixed[] This contains all the project's enviormental variables.
     */
    define("MY_CONFIG", require(__CONFIG__ . DIR_S . "config.php"));

    /**
     * The xampp and the server root paths vary based on the operating systems and certain other conditions.
     * For further problems you can always get the contanct informations on the README file or https://github.com/Masood404/scheduler.
     * 
     * ***Note***
     * Do not change this directly from here, should be changed from the config file.
     * 
     * The default xampp path due to different operating system, for example:
     ** Linux --> /opt/lampp
     ** Mac --> /Applications/XAMPP
     ** Windows --> C:\\xampp
     */
    define("__XAMPP__", MY_CONFIG["XAMPP"]);

    /**
    * The server root directory
    *
    * ***Note***
    * Do not change this directly from here, should be changed from the config file.
    *
    * By default it is:
    ** Linux --> /opt/lampp/htdocs
    ** Mac --> /Application/XAMPP/htocs
    ** Windows --> C:\\xampp\htdocs
    */
    define("__SERVER_ROOT__", MY_CONFIG["Web_Root"]);
    
    /**
     * You can change this directory to the path towards this project if the project folder is not in a subfolder of the server root,
     * only have the server root for this constant
     */
    define("__PROJECT__", realpath(__DIR__."/.."));   
    
    define("__ASSETS__", __PROJECT__  . DIR_S . "assets");
    define("__CSS__", __ASSETS__ . DIR_S . "css");
    define("__JS__", __ASSETS__ . DIR_S . "js");
    define("__IMAGES__"  , __ASSETS__ . DIR_S . "images");

    define("__VIEW__", __PROJECT__ . DIR_S . "view");

    define("__TEMPLATES__", __PROJECT__ . DIR_S . "templates");

    define("__INCLUDES__", __PROJECT__ . DIR_S . "includes");
    define("__PACKAGES__", __INCLUDES__ . DIR_S . "packages");
    define("__NODE_MODULES__", __PACKAGES__ . DIR_S . "node_modules");
    define("__VENDOR__", __PACKAGES__ . DIR_S . "vendor");
    define("__SCRIPTS__", __INCLUDES__ . DIR_S . "scripts");
 
    #endregion

    #region url paths

    /**
     * The public root name of the server in the url or also known as host name, if your are in local development its probably 'localhost'
     */
    define("HTTP_HOST", $_SERVER["SERVER_NAME"]);

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
     * NOT THE SAME AS "path_to_uri()".
     * 
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

    /**
     * NOT THE SAME AS "path_to_url()".
     * 
     * Converts system paths to a url, for example:
     * c:/xampp/htdocs/scheduler/assets --> /scheduler/assets
     * 
     * This functiion also requires constants of:
     * * \_\_SREVER_ROOT\_\_
     * * DIR_S
     * make sure to have these constant set vailidly.
     * 
     * @param string path the path you want to change to uri
     *  @return string str_replace(DIR_S, "/", str_replace(__SERVER_ROOT__, "", $path));
     */
    function path_to_uri($path){
        return str_replace(DIR_S, "/", str_replace(__SERVER_ROOT__, "", $path));
    }

?>