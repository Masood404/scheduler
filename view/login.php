<?php
    require_once realpath(__DIR__."/../includes/index.php");
    $userJs = path_to_url(__JS__)."/User.js";

    $html = <<<HTML
        <title>Login</title>

        <script src="$userJs"></script>

        <style>
            main{
                display: flex;
                height: 75vh;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            #login-form{
                display: flex;
                flex-flow: column;
                align-items: center;
                padding: 1em;
                background-color: var(--primary-color);
                border-radius: 10px;
                box-shadow: 0 0 20px 1px var(--default-shadow-color);
                width: 350px;
            }
            #login-form > input{
                display: block;
                width: 100%;
                margin-block: 0.4em;
                font-size: 20px;
                border-radius: 5px;
                border: none;
                outline: none;
                padding: 0.25em;
            }
            #login-check-boxes{
                margin-block-start: 1em;
                margin-block-end: 1em;
                accent-color: var(--accent-color);
            }
            #login-button{
                font-size: large;
                background-color: var(--default-button-color);
                color: var(--default-text-color);
                border: none;
                padding: 0.5em 1em;
                border-radius: 5px;
                cursor: pointer;
                transition: box-shadow 0.2s ease;
            }
            #login-button:hover{
                box-shadow: 0 0 10px 1px var(--default-shadow-color);
            }
            #login-register{
                margin-block-start: 1em;
                margin-block-end: 1em;
            }
            #logged-in-message > h2{
                margin-block: 2em;
                text-align: center;
            }
            #back-to-home{
                display: block;
                border: none;
                background-color: var(--default-button-color);
                color: white;
                padding: 1em 3em;

                transition: box-shadow 0.2s ease;
            }
            #back-to-home:hover{
                color: white;
                box-shadow: 0 0  10px 1px var(--default-shadow-color);
            }
        </style>
    HTML;

    get_header($html);
?>
<body>
    <script>
        $(document).ready(() => {
            const $main = $("main");

            let form = /* html */ `
            <form id="login-form">
                <h1 id="login-header">Login</h1>
                <input type="text" id="login-username" placeholder="Username">
                <input type="password" id="login-password" placeholder="Password">
                <div id="login-check-boxes">
                    <input type="checkbox" id="login-remember">
                    <label for="login-remember">Remember me</label>
                </div>
                <button id="login-button">
                    Login
                </button>
                <div id="login-register">Don't have an account? <a href="<?php echo __PROJECT_URL__."/register"; ?>">Register Here</a></div>
            </form>
            `
            if(isLogged){
                form = /* html */ `
                <div id="logged-in-message">
                    <h2>Already Logged In</h2>
                    <a id="back-to-home" href="<?php echo __PROJECT_URL__ ?>">Go back to Home</a>
                </div>
                `;
            }

            $main.html(form);

            $main.on("click", "#login-button", (e) => {
                e.preventDefault();

                const validizeInput = (jqElement, valid = false) => {
                    if(valid){
                        jqElement.css({
                            "border": ""
                        });
                    }
                    else{
                        jqElement.css({
                            "border": "2px solid red"
                        });
                    }

                    return valid;
                }

                const $username = $("#login-username");
                const $password = $("#login-password");
                const remember = $("#login-remember").is(":checked");
                
                if(
                    validizeInput($username, User.validateUsername($username.val())) &&
                    validizeInput($password, User.validatePassword($password.val()))
                ){
                    User.login($username.val(), $password.val(), remember).catch((error) => {
                        alert(error);
                    })
                }
            });
            
        });
    </script>
    <main>
        <!-- Render through js -->
    </main>
</body>