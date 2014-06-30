<?php
function connect(){
    $db = new mysqli("localhost","root","","ajaxchatroomori");
    
    if($db->connect_errno > 0){
        echo "Cannot connect to database";
        exit;
    }else{
        return $db;
    }
}

?>
