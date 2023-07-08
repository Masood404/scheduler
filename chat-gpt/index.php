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
                    <h2>Chat Gpt Api</h2>
                    <p>
                        The GPT-3.5 API implemented in Scheduler is a language model developed by OpenAI. It allows users to interact with
                        the scheduling application in a conversational manner. By understanding and generating human-like text, Scheduler's
                        GPT-3.5 API enables users to schedule appointments, manage events, set reminders, and coordinate meetings using plain
                        English input. The API's advanced natural language processing capabilities enhance the user experience, providing a
                        more intuitive and user-friendly interface. Users can communicate with Chat Gpt as if they were talking to a human
                        assistant, simplifying their scheduling tasks and saving time and effort.
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