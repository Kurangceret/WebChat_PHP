<?php
require_once 'roomClass.php';

$room = new Room();

$mode = $_POST['mode'];


if($mode == "createRoom"){
    $roomName = $_POST['roomName'];
    echo $room->createRoom($roomName);
}else if($mode == "retrieveRoom"){
    $lastRoomId = $_POST['roomId'];
    echo $room->retrieveRoom($lastRoomId);
}


?>
