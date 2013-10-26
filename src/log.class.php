<?php

class log {
    private $log;
    public function __construct($file){
        $this->log = fopen($file, "a", true);
    }

    public function write($entry){
        $format = sprintf("%s %s \n", date("Y-m-d H:i:s", mktime()), $entry);
        fwrite($this->log, $format);
    }

    public function __destruct(){
        fclose($this->log);
    }

}
