<?php
require_once 'chatClass.php';
$curId = 0;
if(isset($_POST['curRoomId']))
    $curId = $_POST['curRoomId'];

$chat = new Chat($curId);

$mode = $_POST['mode'];

if($mode == "Retrieve"){
    $lastId = $_POST['lastMessageId'];
    echo $chat->retrieveMessage($lastId);
}else if($mode == "Send"){
    
    $userName = $_POST['username'];
    $message = $_POST['messages'];
    $date = $_POST['curdate'];
    echo $chat->sendMessage($userName, $message,$date);
}else if ($mode == "curDate"){
    echo Chat::NOW();
}else if($mode == "deleteAll"){
    echo $chat->deleteAllMessage();
}else if($mode == "userAmount"){
    $fileName = $_POST['fileName'];
    $userName = $_POST['userName'];
    echo $chat->getNumOnline($userName, $fileName, 6);
}
//clean output buffer
//if(ob_get_length())
        //ob_clean();

//headers to prevent caching
header('Expires: Wed, 23 Dec 1980 00:30::00 GMT');
header('Last-Modified: '.  gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
//header('Content-Type: text/xml');
?>
