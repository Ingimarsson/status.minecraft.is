<?php
class minecraft_server {
 
    private $address;
    private $port;
 
    public function __construct($address, $port = 25565){
        $this->address = $address;
        $this->port = $port;
    }
 
    public function get_ping_info(&$info){
        $socket = @fsockopen($this->address, $this->port, $errno, $errstr, 5.0);
   
        if ($socket === false){
            return false;
        }
   
        fwrite($socket, "\xfe\x01");
   
        $data = fread($socket, 256);
   
        if (substr($data, 0, 1) != "\xff"){
            return false;
        }
   
        if (substr($data, 3, 5) == "\x00\xa7\x00\x31\x00"){
            $data = explode("\x00", mb_convert_encoding(substr($data, 15), 'UTF-8', 'UCS-2'));
        }else{
            $data = explode('§', mb_convert_encoding(substr($data, 3), 'UTF-8', 'UCS-2'));
        }
   
        if (count($data) == 3){
            $info = array(
                'version'        => '1.3.2',
                'motd'            => $data[0],
                'players'        => intval($data[1]),
                'max_players'    => intval($data[2]),
            );
        }else{
            $info = array(
                'version'        => $data[0],
                'motd'            => $data[1],
                'players'        => intval($data[2]),
                'max_players'    => intval($data[3]),
            );
        }
   
        return true;
    }
 
}
