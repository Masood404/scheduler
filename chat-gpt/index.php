<?php
    //Dependecies
    require_once $_SERVER['DOCUMENT_ROOT'] . '/scheduler/includes/index.php';
?>
<?php 
$headItem = <<<EOD
    <link rel="stylesheet" href="/scheduler/assets/css/chat-gpt.css">
    <script src="/scheduler/assets/js/chat-gpt.js"></script>
EOD;
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
            <div class="chatHistory">
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
            </div>
        </div>
        <div class="chatContainer">
            <div class="chatMessages">
                <div class="defaultMessageContainer">
                    <img src="/scheduler/assets/images/transparent gpt icon.png">
                    <h2>Chat Gpt</h2>
                    <p>
                        Chatbot GPT, or Generative Pre-trained Transformer, is a sophisticated AI model developed by OpenAI.
                        It is designed to assist users with various tasks and provide helpful responses in conversational style.
                        GPT utilizes transformers, a type of neural network architecture, to understand and generate human-like
                        text based on the input received. With its vast pre-training on a wide range of data, GPT has the ability
                        to generate coherent and contextually relevant answers to users' queries on any subject.
                    </p>
                </div>
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