<?php 
    /*
        This file creates an sql connection to the constant variable $_DBConn.
    */

    require_once __DIR__ . DIRECTORY_SEPARATOR . "constants.php";
    $_DBConn = new mysqli(MY_CONFIG["DB_Host"], MY_CONFIG["DB_User"], MY_CONFIG["DB_Password"], MY_CONFIG["DB_Name"]);

    if($_DBConn->connect_error){
        die("_DBConn Connect error: " . $_DBConn->connect_error);
    }
?>