<?php
    //Dependecies
    require_once realpath(__DIR__."/../includes/view_init.php");

    $css = path_to_url(__CSS__) . "/chat-gpt.css";
    $showdownJs = path_to_url(__NODE_MODULES__) . "/showdown/dist/showdown.js";
    $userJs = path_to_url(__JS__)."/User.js";
    $js = path_to_url(__JS__) . "/chat-gpt.js";

    $headItem = <<<HTML
        <link rel= "stylesheet" href="$css">
        <script src="$showdownJs"></script>
        <script src="$js"></script>

        <title>Chat GPT</title>
    HTML;
    get_header($headItem);
?>
<body>       
    <main>
        <div class="chatHistoryBar">
            <div class="newChatButtonWrapper">
                <button>
                    <i class="fi fi-sr-plus"></i>
                    <span>New Chat</span>
                </button>
            </div>
            <div id="chat_selection_wrapper">
                <label for="chat_selection_box" id="chat_selection">
                    <input type="checkbox" id="chat_selection_box">
                    Select
                </label>
                <i id="chat_selection_delete" class="fi fi-rr-trash"></i>
            </div>
            <div class="chatHistory">
                <!-- Inject through js -->
            </div>
        </div>
        <div class="chatContainer">
            <div class="chatMessages">
                <div class="defaultMessageContainer">
                    <img src="<?php echo path_to_url(__IMAGES__); ?>/transparent gpt icon.png">
                    <h2>Chat Gpt</h2>
                    <p>
                    Chatbot GPT, or Generative Pre-trained Transformer, is a sophisticated AI model developed by OpenAI.
                    It is designed to assist users with various tasks and provide helpful responses in conversational style.
                    GPT utilizes transformers, a type of neural network architecture, to understand and generate human-like
                    text based on the input received. With its vast pre-training on a wide range of data, GPT has the ability
                    to generate coherent and contextually relevant answers to users' queries on any subject.
                    </p>
                </div>
                <!-- Inject through js -->
            </div>
            <div class="chatSendMessageWrapper">
                <div class="chatSendMessageContainer">
                    <textarea class="c_sendMessageElems" id="sendMessageText" placeholder="Send a message" rows="1" cols="70" wrap="hard"></textarea>
                    <button class="c_sendMessageElems" id="sendMessageButton">
                        <i class="fi fi-rs-paper-plane-top c_sendIcon"></i>
                    </button>
                </div>
                <div class="openAiAff">
                    Powered by <a href="https://openai.com/">OpenAI</a>
                </div>
            </div>
        </div>
    </main>
    <?php get_footer();?>
 </body>