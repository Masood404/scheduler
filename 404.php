<?php
    require_once realpath(__DIR__."/includes/constants.php");

    http_response_code(404);

    $styleCss = path_to_url(__CSS__)."/style.css";
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo $styleCss; ?>">
        <style>
            main{
                display: flex;
                flex-flow: column;
                align-items: center;
                height: 100vh;
                justify-content: center;
            }
            h1{
                font-size: 8em;
                margin: 0.2em 0;
            }
            h2{
                margin: 0;
            }
            h3{
                margin-block-end: 2em;
            }
            a{
                border: none;
                background-color: var(--default-button-color);
                color: white;
                padding: 1em 3em;

                transition: box-shadow 0.2s ease;
            }
            a:hover{
                color: white;
                box-shadow: 0 0  10px 1px var(--default-shadow-color);
            }
        </style>
        <title>404 Not found!</title>
    </head>
    <body>
        <main>
            <h1>404</h1>
            <h2>Oops!</h2>
            <h3>The resource you are looking for does not exist</h3>
            <a href="<?php echo __PROJECT_URL__; ?>">Go back to Home</a>
        </main>
    </body>
</html>