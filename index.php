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
        <section>
            <div>
                <h3>Create User</h3>
                <label for="username">Username</label>
                <br>
                <input type="text" id="username">
                <br>
                <label for="passowrd">Passowrd</label>
                <br>
                <input type="password" id="password">
                <br>
                <label for="email">Email</label>
                <br>
                <input type="email" id="email">
                <br>
                <input type="checkbox" id="subscribe">
                <label for="subscribe">subscribe</label>
                <br>
                <input type="button" value="Submit" id="submit">
            </div>
            <div>
                <h3>Login User</h3>
                <label for="log-user">Username or Email</label>
                <br>
                <input type="text" id="log-user">
                <br>
                <label for="log-password">Password</label>
                <br>
                <input type="password" id="log-password">
                <br>
                <br>
                <input type="button" value="Log In" id="log-in">
            </div>
            <br>
        </section>
    </main>
    <?php get_footer();?>
 </body>