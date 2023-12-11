<?php
    require_once realpath(__DIR__."/../includes/index.php");

    $userJs = path_to_url(__JS__)."/User.js";

    $html = <<<HTML
        <title>Register</title>

        <script src="$userJs"></script>

        <style>
            main{
                display: flex;
                height: 75vh;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            #register-form{
                display: flex;
                flex-flow: column;
                align-items: center;
                padding: 1em;
                background-color: var(--primary-color);
                border-radius: 10px;
                box-shadow: 0 0 20px 1px var(--default-shadow-color);
                width: 350px;
            }
            #register-form > input{
                display: block;
                width: 100%;
                margin-block: 0.4em;
                font-size: 20px;
                border-radius: 5px;
                border: none;
                outline: none;
                padding: 0.25em;
            }
            #register-check-boxes{
                margin-block-start: 1em;
                margin-block-end: 1em;
                accent-color: var(--accent-color);
            }
            #register-button{
                font-size: large;
                background-color: var(--default-button-color);
                color: var(--default-text-color);
                border: none;
                padding: 0.5em 1em;
                border-radius: 5px;
                cursor: pointer;
                transition: box-shadow 0.2s ease;
            }
            #register-button:hover{
                box-shadow: 0 0 10px 1px var(--default-shadow-color);
            }
            #register-login{
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
                <form id="register-form">
                    <h1 id="register-header">Register</h1>
                    <input type="text" id="register-username" placeholder="Username">
                    <input type="email" id="register-email" placeholder="Email (optional)">
                    <input type="password" id="register-password" placeholder="Password">
                    <input type="password" id="register-confirm-password" placeholder="Confirm Password">
                    <div id="register-check-boxes">
                        <input type="checkbox" id="register-subscription">
                        <label for="register-subscription">Subscribe</label>
                        <input type="checkbox" id="register-remember">
                        <label for="register-remember">Remember me</label>
                    </div>
                    <button id="register-button">
                        Register
                    </button>
                    <div id="register-login">Already have an account? <a href="<?php echo __PROJECT_URL__."/login"; ?>">Login Here</a></div>
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

            $main.on("click", "#register-button", async (e) => {
                e.preventDefault();

                /**
                 * Helper function to display input error.
                 * @returns The valid parameter.
                 */
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

                //Get the input elements.
                const $username = $("#register-username");
                const $password = $("#register-password");
                const $passwordConfirm = $("#register-confirm-password");
                const $email = $("#register-email");

                //Check if the remember input is checked
                const remember = $("#register-remember").is(":checked");
                //Check if the subscribe input is checked
                const subscrbe = $("#register-subscription").is(":checked");

                //Check if the email is empty then null, if its not empty then the input's value.
                const email = $email.val() == "" ? null : $email.val();

                //If any input invalidation happens the validizeInput() will display it.
                if(
                    validizeInput($username, User.validateUsername($username.val())) &&
                    validizeInput($password, User.validatePassword($password.val())) &&
                    validizeInput($passwordConfirm, $passwordConfirm.val() == $password.val()) && 
                    validizeInput($email, email == null || User.validateEmail($email.val())) 
                ){
                    try {
                        //Check if the subscription input is checked then initialize subscription
                        const subscription = subscrbe ? await User.notifManager.requestSubscription() : null;
    
                        //Create the user
                        await User.create($username.val(), $password.val(), remember, email, subscription);

                    } catch (error) {
                        alert(error);
                    }
                }
                
                
            });
            
        });
    </script>
    <main>

    </main>
</body>