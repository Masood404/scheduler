<?php
    //Dependecies
    require_once __DIR__ . DIRECTORY_SEPARATOR  ."includes"  . DIRECTORY_SEPARATOR .'index.php';
?>
<?php
    $homeCss = path_to_url(__CSS__) . "/home.css";
    $cryptoJs = path_to_url(__NODE_MODULES__) . "/crypto-js/crypto-js.js";
    $jsencrypt = path_to_url(__NODE_MODULES__) . "/jsencrypt/bin/jsencrypt.min.js";
    $usersJs = path_to_url(__JS__) . "/User.js";
    $homeJs = path_to_url(__JS__) . "/home.js";
    $testJs = path_to_url(__JS__) . "/test.js";

    $headElems = <<<EOD
        <link rel="stylesheet" href="$homeCss">
        <script src="$jsencrypt"></script>
        <script src="$cryptoJs"></script>
        <script src="$usersJs"></script>
        <script src="$homeJs"></script>

        <title>Home</title>
    EOD;    
    get_header($headElems);
 ?>
<body>
    <main>      
        
    </main>
    <?php get_footer();?>
 </body>