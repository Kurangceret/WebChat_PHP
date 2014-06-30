<?php
require_once 'chatClass.php';


class Room{
    private $db;
    
    function __construct()
    {
        $this->db = new mysqli("localhost","root","","ajaxchatroomori");
    }
    
    function __destruct()
    {
        $this->db->close();
    }
    
    function createRoom($roomName)
    {
        $query = "INSERT INTO room(room_name,created_on) VALUES(?,?)";
        $stml = $this->db->prepare($query);
        if($stml == false)
            return $this->failedJson();
        
        $stringDate = strval(Chat::NOW());
        $stringQDate = "\"".$stringDate."\"";
        $stml->bind_param('ss', $roomName, $stringDate);
        $work = $stml->execute();
        
        if($work){
            $answer = array();
            $answer['createdId'] = 0;
            $query = "SELECT room_id FROM room WHERE created_on = $stringQDate";
            $stmd = $this->db->prepare($query);
            if($stmd == false)
                return $this->failedJson("SELECT stmd failed");
            $stmd->bind_result($desiredId);
            $workd = $stmd->execute();
            if($workd == false)
                return $this->failedJson("workdate failed");
            
            $stmd->store_result();
            if($stmd->num_rows() <= 0)
                return $this->failedJson("num_rows select room_id failed");
            
            while($stmd->fetch())
                $answer['createdId'] = $desiredId;
            
            return json_encode($answer);
        }
        else 
            return $this->failedJson();
                
    }
    
    function retrieveRoom($id = 0)
    {
        $query = "";
        $strQDate = strval(Chat::NOW());
        $strQDate = "\"".$strQDate."\"";
        if($id > 0){
            $query = "SELECT room_id, room_name, created_on FROM room WHERE room_id > ".$id;
        }else{
            $query = "SELECT room_id, room_name, created_on FROM room WHERE created_on < ".$strQDate
                ." ORDER BY created_on ASC";
        }
        $stml = $this->db->prepare($query);
        if($stml == false)
            return $this->failedJson("stml failed" . $query);
        
        $answer = array();
        $answer['room'] = "";
        $stml->bind_result($room_id, $room_name, $created_on);
        $work = $stml->execute();
        if($work == false)
            return $this->failedJson("retrieveRoom work failed");
        
        $stml->store_result();
        if($stml->num_rows() <= 0)
            return $this->failedJson("num_rows");
        while($stml->fetch()){
            $answer['roomId'] = $room_id;
            $answer['room'] = $answer ['room'] . "<div id=\"id".$room_id."\" class=\"room\">".$room_name."</div>";
        }
        return json_encode($answer);
    }
    
    
    private function successJson()
    {
        $t = array();
        $t['success'] = "Succesfully";
        $real = json_encode($t);
        return $real;
    }
    private function failedJson($msg = "error")
    {
        $t = array();
        $t['error'] = $msg;
        $real = json_encode($t);
        return $real; 
     }
    
    
    
    
};

?>
