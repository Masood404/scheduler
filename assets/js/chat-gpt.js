let apiResponse = []

$.ajax({
    type: "GET",
    url: "/scheduler/includes/tempDB.json",
    async : false,
    success: function (response) {
        apiResponse = response;
    },
    error: function(){
        console.log("could not retrive gpt instances from db");
    }
});  

let gptInstances = [];
let gptCurrentInstance = {};
let gptLatestId = () => gptInstances.length;
let gptCurrentId = gptLatestId();
let loadgptInstances = () => {
    let chatHistoryH = $(".chatHistory");

    chatHistoryH.html("");

    for(let i = 0; i < gptInstances.length; i++){
        chatHistoryH.prepend(gptInstances[i].blockH);
    }
}

for(let i = 0; i < apiResponse.length; i++){
    gptInstances[i] = Gpt_Convo(apiResponse[i]);
}

let defaultMessageTemplate = "";

let isNewChatAllowed = true;

$(document).ready(function () {
    defaultMessageTemplate = $(".chatMessages").html()
    loadgptInstances();

    let sendTextE = $("#sendMessageText");
    let sendButtonE = $("#sendMessageButton");
    
    sendTextE.on("input", function () {
        if(parseInt(sendTextE[0].scrollHeight / parseFloat(getComputedStyle(sendTextE[0]).lineHeight)) > 10){
            sendTextE.addClass("scrollable");
        }
        else{
            sendTextE.removeClass("scrollable")
            sendTextE.attr("rows", 1);
            sendTextE.attr("rows", parseInt(sendTextE[0].scrollHeight / parseFloat(getComputedStyle(sendTextE[0]).lineHeight)));
        }
    });
    sendTextE.bind('keypress',function(e){
        if(e.which === 13 && !e.shiftKey){
            e.preventDefault();
            SendMessage(sendTextE.val());
            sendTextE.val("")
        }
    })
    sendButtonE.click(function(){
        SendMessage(sendTextE.val());
        sendTextE.val("")
    })
    
    let newChatButton = $(".newChatButtonWrapper button");

    newChatButton.on("click", function(){
        if(isNewChatAllowed){
            Gpt_Convo();
        }
        if(gptCurrentInstance.contents.length > 0){
            isNewChatAllowed = true;
        }
        else{
            isNewChatAllowed = false;
        }
    });
    $(".chatHistory").on("click", ".h_chatBlock",function () {
        SwitchGptTo($(this).attr("data-gptId"));
    });
    
    console.log(gptInstances);
});
function SendMessage(message){
    if(gptInstances.length < 1){
        Gpt_Convo();
    }
    if(message.length > 5){
        gptCurrentInstance.newContent(message);
        SwitchGptTo(gptCurrentId);
    }
    else{
        alert("Invalid Input")
    }
}
function SwitchGptTo(id){
    gptCurrentInstance = gptInstances[id];
    gptCurrentId = id;
    $(".chatMessages").html("");
    $(".chatMessages").html(gptCurrentInstance.getContentsH());  

    const responseCodingE = $(".m_chatGpt_wrapper .cm_contentContainer pre code");
    
    responseCodingE.each(function(i, obj){
        const classes =  $(obj).attr("class");
        let langName = ""; 

        for(let i = 0; i < classes.length; i++){
            if(classes[i] == " "){
                break;
            }
            else{
                langName += classes[i];
            }
        }

        const codeTopH = /*html*/`
            <div class="codeTop">
                <div class="m_codeLangName">${langName}</div>
                <div class="m_codeCopyContainer">
                    <i class="fi fi-rs-clipboard m_copyCodeIcon"></i>
                    <span>Copy code</span>
                </div>
            </div>
        `;

        $(obj).closest("pre").prepend(codeTopH);
    });
    responseCodingE.closest("pre").find(".codeTop .m_codeCopyContainer").click(function(){
        const currentE = this;
        const copyText = $(currentE).closest(".codeTop").closest("pre").find("code").html();
        const eDefaultText = $(currentE).find("span").html();

        navigator.clipboard.writeText(copyText);

        $(currentE).find(".m_copyCodeIcon").removeClass("fi-rs-clipboard");
        $(currentE).find(".m_copyCodeIcon").addClass("fi-bs-check");

        $(currentE).find("span").html("Copied");

        setTimeout(function(){
            $(currentE).find(".m_copyCodeIcon").removeClass("fi-bs-check");
            $(currentE).find(".m_copyCodeIcon").addClass("fi-rs-clipboard");

            $(currentE).find("span").html(eDefaultText);
        }, 1500)
    });
}
function Gpt_Convo(jsonFrom = {
    title : "New Chat",
    contents : [],
    id : gptLatestId(),
}){
    let obj = {
        title : jsonFrom.title || "New Chat",
        contents : jsonFrom.contents || [],
        blockH : null,
        id : jsonFrom.id || gptLatestId(),
        newContent : () => {},
        setTitle : () => {},
        getContentsH : getContentsH
    };

    obj.newContent = (message) => {
        let gptContent = {
            response : null,
            message : message
        };

        $.ajax({
            type: "POST",
            url: "/scheduler/includes/chatGptApi.php",
            data:
            { 
                gptInstance : 
                {
                    message : message,
                    id : obj.id,
                    title : obj.title
                }
            },
            async : false,
            success: function (response) {
                gptContent.response = JSON.parse(response).response;
            },
            error : function (){
                console.log("could not retrive data for gpt response")
            }
        });
        obj.contents.push(gptContent);

        return gptContent;
    }
    obj.setTitle = (titleName) => {
        obj.title = titleName;
        LoadBlock();
    }
    obj.blockH = LoadBlock();

    gptInstances.push(obj);
    gptCurrentInstance = obj;

    loadgptInstances();
    SwitchGptTo(obj.id);

    return obj;

    function LoadBlock(){
        let blockH = /*html*/`
            <div class="h_chatBlock" data-gptId = "${obj.id}">
                <i class="fi fi-rr-messages h_chatIcon"></i>
                <div class="h_chatTitle">${obj.title}</div>
            </div>
        `;  

        obj["blockH"] = blockH;

        return blockH;
    }
    function getContentsH(){
        let messageH = "";
        if(obj.contents.length > 0){
            for(let i = 0; i < obj.contents.length; i++){
                messageH += GenerateHtml(obj.contents[i].message);
                messageH += GenerateHtml(obj.contents[i].response, true);
            }
        }
        else{
            messageH = defaultMessageTemplate;
        }
        return messageH;

        function GenerateHtml(content, isChatContent = false){
            let html;
            let updatedContent = "";
            let mdConverter =  new showdown.Converter();

            updatedContent = mdConverter.makeHtml(content);

            if(!isChatContent){
                html = /*html*/`
                <div class="messageBlockWrapper">
                    <div class="messageBlock">
                        <div class="roleIconContainer">
                            <img class="roleIcon" src="/scheduler/assets/images/User Icon.png">
                        </div>
                        <div class="cm_contentContainer">
                            ${content}
                        </div>
                    </div>
                </div>
            `;
            }
            else{
                html = /*html*/`
                <div class="messageBlockWrapper m_chatGpt_wrapper">
                    <div class="messageBlock">
                        <div class="roleIconContainer">
                            <img class="roleIcon" src="/scheduler/assets/images/Gpt Icon.png">
                        </div>
                        <div class="cm_contentContainer">
                            ${updatedContent}
                        </div>
                    </div>
                </div>
            `;
            }
            return html;
        }
    }
}