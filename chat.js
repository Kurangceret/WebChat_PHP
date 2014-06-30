var chatURL = "chats.php";  //url of the back logic
//first time entering the application it will be set to -1, so all previous message
//can be get
var lastMessageId = -1; 
var tempCache = "";//contains <span> to put the error message
var checkCache = [];    //used to cache the previous 2 message, to prevent duplicate
checkCache[0] = "";
checkCache[1] = "";
var curDate = "";   //current date, will be refreshed every 0.5s
var count = 0;  //var used in conjunction with check cache
var onChatPage = false;


var chatWidget = '<input type="text" id="userName" name="username" maxlength="20" size="20" />\n' +
                '<input type="text" id="messageBox" name="messages" maxlength="2000" size="50" />\n' +
                '<input type="button" id="sendButton" value="Send"  />\n' +
                '<input type="button" id="deleteAll" value="Delete All" />\n' +
                '<input type="hidden" id="roomId" value= 0 />\n' +
                '<input type="button" id="leaveRoom" value="Leave this Room " /> \n';



function fromRoomToChat(roomId)
{
    
   // alert("swag");
    $("#widget").html(chatWidget);
    
    onChatPage = true;
    //alert(chatWidget);
    $("#roomId").val(roomId);
    initChat();
}

function prepareToRoom()
{
    onChatPage = false;
    $("#scroll").html("");
    $("#widget").html("");
    setTimeout(fromChatToRoom,1000);
}

function initChat()
{
    $("#sendButton").click(function (){
        sendMessage();
    });
    $("#userName").blur(function(){
       generateRandom(); 
    });
    $("#messageBox").keyup(function(e){
        if(e.keyCode == 13){    //keyCode == 13 means enter button
            sendMessage();
        }
    });
    $("#leaveRoom").click(function(){
       prepareToRoom(); 
    });
    $("#deleteAll").click(function(){
       $.ajax({
          type:"POST",
          url:chatURL,
          data:"mode=" + "deleteAll",
          dataType:"json",
          success:function(msg){
              if(msg.success != null){
                  $("#scroll").html("");
              }else{
                  
              }
          },
          error:function(xhr, msg){
      
          }
       });
    });
    setInterval(refreshDate,500);
    generateRandom();
    retrieveMessage();
    checkUserAmount();
}

function checkUserAmount()
{
    var userName = $("#userName").val();
    var mode = "userAmount";
    var curRoomId = $("#roomId").val();
    var fileName = "room" + curRoomId +".txt";
    var params = "mode=" + mode + "&fileName=" + fileName
    + "&userName=" + userName;
    $.ajax({
       type: "POST",
       url: chatURL,
       data: params,
       dataType: "json",
       success: function(msg){
          $("#userOnline").html(msg.num + msg.listOfName);
          if(onChatPage)
              setTimeout(checkUserAmount,500);
           
       },
       error: function(xhr,msg){
            alert(xhr + " " + msg);
       }
    });
}

function refreshDate()
{
    $.post(chatURL, {mode: "curDate"}, function (msg){
        curDate = msg.toString();
        $("#dateLoop").html(curDate + " GMT(+0) " );
    });
}

function generateRandom()
{
    var oUser = document.getElementById("userName");
    if(oUser.value == "")
        oUser.value = "Guest" + Math.floor(Math.random() * 1000);
}
function sendMessage()
{
    var modes = "Send";
    var message = $("#messageBox").val();
    var userName = $("#userName").val();
    var curRoomId = $("#roomId").val();
    $("#messageBox").val("");
    var params = "mode=" + modes + "&username=" + userName + "&messages=" + message +"&curdate=" + curDate
                + "&curRoomId=" + curRoomId;
    if(trim(userName) != "" && trim(message) != ""){
        var tempDate = curDate.replace(/\s+/, "");
        var errorId = "mg" + tempDate;
        tempCache = "<div class=\"item\">[ " + curDate + " ] " + userName +" said: " + message + "\
        <span class=\"error\" id=\"" + errorId + "\"></span></div><br/>";
        
        var dump = "<div class=\"item\">[ " + curDate + " ] " + userName +" said: " + message + "</div><br/>";
        $("#scroll").html($("#scroll").html() + tempCache);
        
        if(count % 2 == 0)
            count = 0;
        checkCache[count] = dump;
        count++;
        var objDiv = document.getElementById("scroll");
        objDiv.scrollTop = objDiv.scrollHeight;
       /* $.post(chatURL,{mode: modes,username:userName,messages:message,curdate: curDate},function(data){
           
        });*/
        $.ajax({
            type: "POST",
            url: chatURL,
            data: params,
            dataType: "json",
            success: function(msg){
                if(msg.error != null){
                    $("#" + errorId).html("(This message had failed to sent, please sent again later");
                }
            }
        });
    }
}


function retrieveMessage()
{
    var modes = "Retrieve";
    var curRoomId = $("#roomId").val();
    var params = "mode=" + modes + "&lastMessageId=" + lastMessageId + "&curRoomId=" + curRoomId;
    $.ajax({
        type: "POST",
        url: chatURL,
        data: params,
        dataType: "json",
        success: function(msg){
            if(msg.error == null){
               // alert(msg.id);
                
                lastMessageId = msg.id;
                if(msg.data != checkCache[0] && msg.data != checkCache[1]){
                    $("#scroll").html( $("#scroll").html() + msg.data);
                    /*If I scroll down 5px in this window, 
                     * the window's scrollTop value is 5. If I scroll right 10px in a 
                     * scrollable div, the div's scrollLeft value is 10.
                     * scrollHeight is the total height, including content scrolled out of view.*/                
                    var objDiv = document.getElementById("scroll");
                    objDiv.scrollTop = objDiv.scrollHeight;
                    
                }
                if(onChatPage)
                    setTimeout(retrieveMessage,500);
                
            }else{
                if(onChatPage)
                    setTimeout(retrieveMessage,500);
                //alert(msg.error);
            }
        },
        error: function(xhr, msg){
            //alert(xhr + " " + msg);
        }
    });
    
}

function trim(s)
{
    return s.replace(/(^\s+)|(\s+$)/g,"");
}

