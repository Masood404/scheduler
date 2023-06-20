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
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
                <div class="h_chatBlock">
                    <i class="fi fi-rr-messages h_chatIcon"></i>
                    <div class="h_chatTitle">Chat Title</div>
                </div>
            </div>
        </div>
        <div class="chatContainer">
            <div class="chatMessages">
                <div class="messageBlockWrapper">
                    <div class="messageBlock">
                        <div class="roleIconContainer">
                            <img class="roleIcon" src="/scheduler/assets/images/User Icon.png">
                        </div>
                        <div class="cm_contentContainer">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                        ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                        laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                        voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                        non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </div>
                    </div>
                </div>
                <div class="messageBlockWrapper m_chatGpt_wrapper">
                    <div class="messageBlock">
                        <div class="roleIconContainer">
                            <img class="roleIcon" src="/scheduler/assets/images/User Icon.png">
                        </div>
                        <div class="cm_contentContainer">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                        ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                        laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                        voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                        non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </div>
                    </div>
                </div>
                <div class="messageBlockWrapper">
                    <div class="messageBlock">
                        <div class="roleIconContainer">
                            <img class="roleIcon" src="/scheduler/assets/images/User Icon.png">
                        </div>
                        <div class="cm_contentContainer">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                        ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                        laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                        voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                        non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </div>
                    </div>
                </div>
                <div class="messageBlockWrapper m_chatGpt_wrapper">
                    <div class="messageBlock">
                        <div class="roleIconContainer">
                            <img class="roleIcon" src="/scheduler/assets/images/User Icon.png">
                        </div>
                        <div class="cm_contentContainer">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                        ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                        laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                        voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                        non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </div>
                    </div>
                </div>
            </div>
            <div class="chatSendMessageWrapper">
                <div class="chatSendMessageContainer">
                    <textarea class="c_sendMessageElems" id="sendMessageText" placeholder="Send a message" rows="1" cols="70" wrap="hard"></textarea>
                    <button class="c_sendMessageElems" id="sendMessageButton">
                        <i class="fi fi-rs-paper-plane-top c_sendIcon"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
    <?php get_footer();?>
 </body>