<?php
require_once 'constant.php';

class Chat{
    private $db;
    private $roomId;
    
    
    function __construct($room_id = 0)
    {
        $this->db = new mysqli("localhost","root","","ajaxchatroomori");
        $this->roomId = $room_id;
    }
    
    function __destruct()
    {
        $this->db->close();        
    }
    
    public function deleteAllMessage()
    {
        $query = "DELETE FROM chat WHERE room_id = ".$this->roomId;
        $stml = $this->db->prepare($query);
        if($stml != FALSE){
            $work = $stml->execute();
            if($work)
                return $this->successJson();
            else
                return $this->failedJson();
            $stml->close();
        }else{
            return $this->failedJson();
        }
    }
    
    public function sendMessage($userName, $message, $date = "")
    {
        $query = "INSERT INTO chat(user_name,posted_on,message, room_id) VALUES(?,?,?,?)";
        $stml = $this->db->prepare($query);
        if($stml != FALSE){
            if($date == "")
                $vars = $this->NOW();
            else
                $vars = $date;
            $var = strval($vars);
            $stml->bind_param('sssi',$userName, $var, $message,$this->roomId);
            $work = $stml->execute();
            if($work){
                return $this->successJson();
            }else{
                return $this->failedJson();
            }
            $stml->close();
        }else{
            return $this->failedJson();
        }
    }
    public function retrieveMessage($id = 0)
    {
        $query = "";
        $date = $this->NOW();
        $strDate = strval($date);
        $strQDate = "\"".$strDate."\"";
        //if user had the id, we do the normal way
        if($id > 0){    
            $query = "SELECT chat_id, user_name, posted_on, message FROM chat WHERE chat_id > ".$id . " AND ".
                " room_id = " . $this->roomId;
                    
        }//usually happens when user first time enter the application or refresh.
        //going to choose 30 previous messages
        else{
            $query = "SELECT chat_id, user_name, posted_on, message FROM chat WHERE posted_on < ".$strQDate
                ." AND room_id = ". $this->roomId. " ORDER BY posted_on DESC LIMIT 30";
        }
        
        $stml = $this->db->prepare($query);
        if($stml != FALSE){
            $tempId = 0;
            $cache = array();
            $abc = true;
            $result = array();
            $result['data'] = "";
            $stml->bind_result($cht_id, $username,$posted_on,$message);
            $work = $stml->execute();
            $stml->store_result();
            if($work){
                if($stml->num_rows() == 0)
                    return $this->failedJson ();
                while($stml->fetch() && $stml->num_rows() > 0){
                    if($id > 0){
                        $str = "<div class=\"item\">[ ".$posted_on." ] ". $username ." said: ".$message. "</div><br/>";
                        $result['id'] = $cht_id;
                        $result['data'] = $result['data'] . $str;
                    }else{
                        if($abc)
                            $tempId = $cht_id;  //only once since in query we use DESC, so the first will be the last in js
                        $abc = false;
                        $str = "<div class=\"item\">[ ".$posted_on." ] ". $username ." said: ".$message. "</div><br/>";
                        array_push($cache, $str);
                    }
                }
                if($abc == false){
                    while($cache != null)//pop will delete one element at the end and return it.
                        $result['data'] = $result['data'] . array_pop($cache);
                    
                    $result['id'] = $tempId;
                }
                return json_encode($result);
            }else{
                return $this->failedJson("Execute stml failed");
            }
            
        }else{
            return $this->failedJson("Prepare statement failed" . $query);
        }
    }
    public function getNumOnline($userName, $fileName, $seconds)
    {
        $answer = array();
        $answer['listOfName'] = "<div class=\"listOfUser\">". $userName . " (You)"."</div><br />";
        //HTTP_USER_AGENT will gather user's personal information, ex: 
        //Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) alt :  get_browser() func
        $ip = getenv("REMOTE_ADDR"). getenv("HTTP_USER_AGENT");
        $out = "";
        $online = 1;
        
        if(file_exists($fileName)){
            //rtrim = Strip whitespace (or other characters) from the end of a string
            $users = explode("\n", rtrim(file_get_contents($fileName)));
            foreach($users as $user){
                list($username, $usertime, $userip, $roomid) = explode('|', $user);
                if(time() - $usertime < $seconds && $userip != $ip 
                        && $roomid == $this->roomId){
                    $out .= $username. '|' .$usertime . '|'. $userip. '|' . $roomid ."\n";
                    $answer['listOfName'] .= "<div class=\"listOfUser\">". $username ."</div><br />";
                    ++$online;
                }
            }
        }
        $answer['num'] = "<div class=\"listOfUser\"> User Online : ".$online. "</div><br/>";
        $out .= $userName . '|' . time() . '|' . $ip . '|' . $this->roomId ."\n";
        file_put_contents($fileName, $out);
        return json_encode($answer);
    }
    
    static public function NOW()
    {
        date_default_timezone_set("UTC"); 
        return date("Y-m-d H:i:s");
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
}


?>
