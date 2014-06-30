<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Original Room Chat</title>
        <script src="chat.js"></script>
        <script src="room.js"></script>
        <script src='jquery-2.0.3.js'></script>
        <link rel="stylesheet" href="chat.css"/>
    </head>
    <body><center> <?php  date_default_timezone_set("UTC"); 
    echo "<div id=\"dateLoop\">".date("Y-m-d H:i:s"); echo " GMT(+0) </div>" ?>
        <noscript>
            Your browser does not support JavaScript!
        </noscript>
        <table id="content">
            <tr>
                <td>
                    <div id ="scroll">
                        <!--<div class="room">test</div>
                        <div class="room">anothertest</div>-->
                    </div>
                </td>
                <td>
                    <div id= "userOnline">

                    </div>
                </td>
            </tr>
        </table>
        <div id="widget">
            <!--<label for="roomName">Room Name:</label>
            <input type="text" name="roomName" id="roomName" maxlength="15" size="30"/>
            <input type="button" value="Create Room" id="createRoom" />-->
        </div>
    </center>
    </body>
</html>
<!--<input type="text" id="userName" name="username" maxlength="20" size="20" />
            <input type="text" id="messageBox" name="messages" maxlength="2000" size="50" />
            <input type="button" id="sendButton" value="Send"  />
            <input type="button" id="deleteAll" value="Delete All" />
            <input type="hidden" id="roomId" value= 0 />-->