<?php 
    require_once realpath(__DIR__."/../../includes/constants.php");

    header("Content-Type: application/javascript");

    $projectUrl = __PROJECT_URL__;

    //Get crypto dependecies.
    $CryptoJS = file_get_contents(__NODE_MODULES__.DIR_S."crypto-js".DIR_S."crypto-js.js");

    $JSEncrypt = file_get_contents(__NODE_MODULES__.DIR_S."jsencrypt".DIR_S."bin".DIR_S."jsencrypt.js");

    //Get the User.js static file
    $UserJs = file_get_contents(__JS__.DIR_S."static".DIR_S."User.js");

    $dynamicJs = <<<JS
    //#region crypto modules
    
    //#region CryptoJS
    $CryptoJS

    //#endregion
    //#region JSEncrypt
    $JSEncrypt

    //#endregion

    //#endregion

    $UserJs
    JS;

    echo $dynamicJs;
?>