<?php

class log {
    private $log;
    public static $instance;
    
    public function __construct($file){
        $this->log = fopen($file, "a");
        self::$instance = $this;
    }
    
    static public function getInstance() {
        if (self::$instance == null) {
            return new log();
        }
        return self::$instance;
    }

    public function write($entry){
        $format = sprintf("%s %s \n", date("Y-m-d H:i:s", mktime()), $entry);
        fwrite($this->log, $format);
    }

    public function __destruct(){
        fclose($this->log);
    }

}
