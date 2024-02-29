/**
 * @typedef {Object} Chat
 * @property {number} id - The ID of the chat.
 * @property {Array<Object>} contents - An array of chat contents.
 * @property {string} title - The title of the chat.
 * @property {string} username - The username associated with the chat.
 */

/**
 * @typedef {Object} Content
 * @property {string} message - The message content.
 * @property {string} response - The response content.
 */

/**
 * Chats Interface module for handling chat-related operations.
 * @returns {Object} - Object containing methods for chat operations.
 */
const ChatsInterface = () => {
    if (!isLogged) {
        throw "User is not authorized";
    }

    const prompt = async (prompt, chatId = null) => {
        try {
            setAuthorizationHeader();
            const response = await $.ajax({
                type: "POST",
                url: `${__project_url__}/api/chats/prompt.php`,
                data: {
                    prompt: prompt,
                    chatId: chatId
                },
            });
            return response.chatId;
        } catch (error) {
            throw error.responseJson || error;
        }
    };

    const get = async (chatId) => {
        setAuthorizationHeader();
        return await $.ajax({
            type: "GET",
            url: `${__project_url__}/api/chats/get-chat.php`,
            data: {
                chatId: chatId
            }
        });
    };

    const getAll = async () => {
        setAuthorizationHeader();
        return await $.ajax({
            type: "GET",
            url: `${__project_url__}/api/chats/get-chat.php`,
        });
    };

    const deleteChat = async (chatIds) => {
        setAuthorizationHeader();
        return await $.ajax({
            type: "DELETE",
            url: `${__project_url__}/api/chats/delete-chat.php?chatId=[${chatIds}]`,
        });
    };

    return {
        /**
         * Sends a prompt to the chat.
         * @param {string} prompt - The prompt message.
         * @param {number} [chatId=null] - The ID of the chat. Defaults to null.
         * @returns {Promise<number>} - Resolves into the newly added content's chat ID.
         */
        prompt,
        /**
        * Retrieves a chat by ID.
        * @param {number} chatId - The ID of the chat.
        * @returns {Promise<Chat>} - Resolves to an object containing chat data.
        */
        get,
        /**
        * Retrieves all chats for the user.
        * @returns {Promise<Array<Chat>>} - Resolves to an array containing all the chats data for the user.
        */
        getAll,
        /**
         * Deletes chats by ID(s).
         * @param {number|Array<number>} chatIds - A single chat ID or an array of chat IDs.
         * @returns {Promise<Array<Chat>>} - Resolves to all the chats on successful chat deletion.
         */
        deleteChat
    };
};

$(async () => {
    const defaultMessageContainer = $(".chatMessages").html();

    if (!isLogged) {
        const unloggedHtml = /* html */ `
            <div style="text-align: center; margin-top: 1em;">
                <div style="margin-top: 1em;">User is not logged in!</div>
                <a href="${__project_url__}/login" style="display: block; margin-top: 0.5em;">Login Here</a>
            </div>
        `;
        //Render the message when the user is not logged in.
        $(".chatHistoryBar").html(unloggedHtml);
        //Render the default message
        $(".chatMessages").html(defaultMessageContainer);
        //Remove the chatSendMessage Input with its entire wrapper.
        $(".chatSendMessageWrapper").remove();

        //Break code execution.
        return;
    }

    /**
     * An Instance of ChatsInterface.
     */
    const ChatsI = ChatsInterface();
    /**
     * A global variable used to cache chats.
     */
    let chatsInstances = [];
    /**
     * Currently seleted chat's id.
     */
    let currentChatId = null;

    await renderChats();

    let isChecked = false;
    const $newChatButton = $(".newChatButtonWrapper button");
    const $chatSelection = $("#chat_selection");
    const $chatSelectionBox = $("#chat_selection_box");

    $newChatButton.click(() => {
        //Render the default chat messages.
        renderChatContents(null);
    })

    $chatSelection.click(() => {
        isChecked = $chatSelectionBox.is(":checked");
        if (!isChecked) {
            $(".h_chatBlock").removeClass("h_isSelected");
        }
    });

    $(".chatHistory").on("click", ".h_chatBlock", function () {
        if (isChecked) {
            $(this).toggleClass("h_isSelected");
        } else {
            renderChatContents($(this).attr("data-chat-id"));
        }
    });

    $("#chat_selection_delete").click(() => {
        const chatIds = $(".h_isSelected").map((index, element) => $(element).attr("data-chat-id")).get();
        ChatsI.deleteChat(chatIds)
            .then(renderChats)
            .catch(console.error);
    });

    /**
     * Handler for sending messages.
     */
    const sendMessageHandler = async (e) => {
        const message = $("#sendMessageText").val();
        renderLoader(message);
        ChatsI.prompt(message, currentChatId)
            .then(async (chatId) => {
                await renderChats();
                unrenderLoader();
                renderChatContents(chatId);
            })
            .catch((error) => {
                alert(error);
                unrenderLoader();
            });
    };
    // Click event for the send message button
    $("#sendMessageButton").click(sendMessageHandler);

    // Keypress event for sending messages with Enter key
    $("#sendMessageText").bind("keypress", (e) => {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessageHandler(e);
            $("#sendMessageText").val("")
        }
    })

    // Attach input event listener to the textarea element with id "sendMessageText"
    $("#sendMessageText").on("input", function () {
        // Calculate the number of lines in the textarea, is a function return due to DOM update.
        const numLines = () => parseInt($(this)[0].scrollHeight / parseFloat(getComputedStyle($(this)[0]).lineHeight));


        // Check if there are more than 10 lines
        if (numLines() > 10) {
            // Add the "scrollable" class to the textarea
            $(this).addClass("scrollable");
            $(this).attr("rows", 10);
        }
        else {
            // Remove the "scrollable" class from the textarea
            $(this).removeClass("scrollable")
            // Adjust the number of rows in the textarea to fit the content
            $(this).attr("rows", 1);
            $(this).attr("rows", numLines());
        }
    });


    // Click event for copying code to clipboard
    $(".chatMessages").on("click", ".m_codeCopyContainer", function () {
        const copyText = $(this).closest(".codeTop").closest("pre").find("code").text();
        navigator.clipboard.writeText(copyText);

        // Animate the clipboard by changing the clipboard icon to a tick
        const $icon = $(this).find(".m_copyCodeIcon");
        $icon.removeClass("fi-rs-clipboard").addClass("fi-bs-check");

        setTimeout(() => {
            // Change back to the clipboard icon after 1 second
            $icon.removeClass("fi-bs-check").addClass("fi-rs-clipboard");
        }, 1000);
    });

    /**
     * Renders chat blocks and default messages.
     * @param {Array<Chat>} [chats=null] - Optional parameter containing chat data. If not provided, fetches chats from the ChatsInterface.
     * @returns {Promise}
     */
    async function renderChats(chats = null) {
        try {
            // Fetch chats if not provided
            if (!chats) {
                chats = await ChatsI.getAll();
            }

            // Validate the structure of chats
            if (!Array.isArray(chats)) {
                console.log(chats);
                throw new Error("Argument 'chats' should be an array.");
            }

            const expectedKeys = ["id", "contents", "title", "username"];

            // Check for expected keys in each chat object
            for (const chat of chats) {
                if (!expectedKeys.every(key => key in chat)) {
                    throw new Error("Unexpected argument 'chats'. Some elements are missing.");
                }
            }

            // Cache the fetched chats
            chatsInstances = chats;

            let chatsHtml = "";

            // Generate HTML for each chat block
            for (const chat of chats) {
                chatsHtml += /* html */ `
                    <div class="h_chatBlock" data-chat-id="${chat.id}">
                        <i class="fi fi-rr-messages h_chatIcon"></i>
                        <div class="h_chatTitle">${chat.title}</div>
                    </div>
                `;
            }

            // Render the chat blocks
            $(".chatHistory").html(chatsHtml);

            $(".chatMessages").html(defaultMessageContainer);
        } catch (error) {
            console.error(error);
        }
    }

    /**
     * Renders chat contents based on chat ID.
     * @param {number|null} chatId - The ID of the chat. On null will render the default message.
     * @returns {void}
     */
    function renderChatContents(chatId = null) {
        currentChatId = chatId;

        if (currentChatId == null) {
            $(".chatMessages").html(defaultMessageContainer);
            //Break the execution of the rest of the code.
            return;
        }

        const chat = selectChatById(currentChatId);

        let contentsHtml = "";

        // Generate HTML for each message block in the chat
        for (const content of chat.contents) {
            //Convert the line breaks intp the br tag.
            const updatedMessage = content.message.replace(/(\r\n|\r|\n)/g, "<br>");

            contentsHtml += /* html */ `
            <div class="messageBlockWrapper">
                <div class="messageBlock">
                    <div class="roleIconContainer">
                        <img class="roleIcon" src="/scheduler/assets/images/User Icon.png">
                    </div>
                    <div class="cm_contentContainer" style="white-space:pre-wrap;">${updatedMessage}</div>
                </div>
            </div>
        `;

            // Process markdown content and generate HTML
            const mdConverter = new showdown.Converter();
            const updatedResponse = mdConverter.makeHtml(content.response);

            const $cm_contentContainer = $(/* html */`
                <div class="cm_contentContainer">${updatedResponse}</div>
            `);

            // Update code blocks with language and copy functionality
            $cm_contentContainer.find("pre code").each(function () {
                const classes = $(this).attr("class");
                let langName = classes.split(" ")[0];
                const codeTopH = /* html */ `
                <div class="codeTop">
                    <div class="m_codeLangName">${langName}</div>
                    <div class="m_codeCopyContainer">
                        <i class="fi fi-rs-clipboard m_copyCodeIcon"></i>
                        <span>Copy code</span>
                    </div>
                </div>
            `;
                $(this).closest("pre").prepend(codeTopH);
            });

            contentsHtml += /* html */ `
            <div class="messageBlockWrapper m_chatGpt_wrapper">
                <div class="messageBlock">
                    <div class="roleIconContainer">
                        <img class="roleIcon" src="/scheduler/assets/images/Gpt Icon.png">
                    </div>
                    ${$cm_contentContainer[0].outerHTML}
                </div>
            </div>
        `;
        }

        // Render the chat messages
        $(".chatMessages").html(contentsHtml);
    }

    /**
     * Selects a chat by ID from cached chat instances.
     * @param {number} chatId - The ID of the chat.
     * @returns {Chat|null} - The chat object if found, otherwise null.
     */
    function selectChatById(chatId) {
        const chat = chatsInstances.find(chat => chat.id == chatId);
        return chat || null;
    }

    /**
     * Renders a loader in the chatMessages element.
     * @returns {void}
     */
    function renderLoader(message) {
        //Add the temporary user's message and a loader till prompt gets a response.
        const loaderHtmlTemplate = /* html */ `
        <div class="messageBlockWrapper">
            <div class="messageBlock">
                <div class="roleIconContainer">
                    <img class="roleIcon" src="/scheduler/assets/images/User Icon.png">
                </div>
                <div class="cm_contentContainer">${message}</div>
            </div>
        </div>
        <div class="messageBlockWrapper m_chatGpt_wrapper">
            <div class="messageBlock">
                <div class="roleIconContainer">
                    <img class="roleIcon" src="/scheduler/assets/images/Gpt Icon.png">
                </div>
                <div class="cm_contentContainer"><div class="response-loader"><div></div><div></div><div></div></div></div>
            </div>
        </div>
        `
        $(".defaultMessageContainer").remove();
        $(".chatMessages").append(loaderHtmlTemplate);
    }

    /**
     * Removes the loader from the chatMessages element.
     * @returns {void}
     */
    function unrenderLoader() {
        $("#loader").remove();
    }
});