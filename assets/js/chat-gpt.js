$(document).ready(function () {
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
            SendMessage();
        }
    })
    sendButtonE.click(function(){
        SendMessage();
    })
});
function SendMessage(){
    
}