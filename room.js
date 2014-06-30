var roomURL = "room.php";
var lastRoomId =  -1;
var onRoomPage = true;
var destinedId = 0;
var roomWidget = '<label for="roomName">Room Name:</label>' +
            '<input type="text" name="roomName" id="roomName" maxlength="15" size="30"/>' +
            '<input type="button" value="Create Room" id="createRoom" />';


window.onload = function(){
    
    retrieveRooms();
};

function fromChatToRoom()
{
    onRoomPage = true;
    $("#widget").html(roomWidget);
    lastRoomId = -1;
    destinedId = 0;
    retrieveRooms();
}

function fillRoomWidget()
{
    $("#widget").html(roomWidget);
    initRoom();
}

function retrieveRooms()
{
    var mode = "retrieveRoom";
    var params = "mode=" + mode + "&roomId=" + lastRoomId;
    $.ajax({
       type: "POST",
       url: roomURL,
       data: params,
       dataType: "json",
       success: function(msg){
           if(msg.error == null){
               lastRoomId = msg.roomId;
               $("#scroll").html($("#scroll").html() + msg.room);
               addListener();
               if(onRoomPage)
                    setTimeout(retrieveRooms,500);
           }else{
               
               if(onRoomPage)
                    setTimeout(retrieveRooms,500);
           }
       },
       error: function(xhr, msg){
          // alert(xhr + " " + msg);
       }
    });
    fillRoomWidget();
    
}

function addListener()
{
    var allRoom = document.getElementsByClassName("room");
    for(var i = 0; i < allRoom.length; i++){
        allRoom[i].addEventListener('click',function(){ 
            var realId = replaceStrId(this.id);
            prepareToChat(realId);
        },false);
    }
}

function initRoom()
{
    $("#createRoom").click(function(){
        createRoom();
    });
    $("#roomName").keyup(function(e){
        if(e.keyCode == 13){    //keyCode == 13 means enter button
            createRoom();
        }
    });
}

function createRoom()
{
    var mode = "createRoom";
    var roomName = $("#roomName").val();
    var params = "mode=" + mode + "&roomName=" + roomName;
    if(trim(roomName) != ""){
        $.ajax({
           type: "POST",
           url: roomURL,
           data: params ,
           dataType:"json",
           success: function(msg){
               if(msg.error == null){
                   if(msg.createdId)
                       prepareToChat(msg.createdId);
                   $("#roomName").val("");
               }else{
                   alert(msg.error);
               }
           },
           error: function(xhr,msg){
               alert(xhr + msg);
           }
        });
    }
    
}


function prepareToChat(wantedId)
{
    //alert(wantedId);
    onRoomPage = false;
    $("#scroll").html("");
    $("#widget").html("");
    destinedId = wantedId;
    setTimeout(redirect ,1000);
}

function redirect()
{
    fromRoomToChat(destinedId); //chat.js
}

function replaceStrId(text)
{
    return text.replace("id","");
}



