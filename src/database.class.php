<?php

class database extends PDO {
    public function __construct($dsn, $user = null, $pass = null, $array = null) {
        parent::__construct($dsn, $user, $pass, $array);
        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
    }

    public function execute($query, $params = array()) {
        $st = $this->prepare($query);
        return $st->execute($params);
    }

    public function fetchall($query, $params = array()) {
        $st = $this->prepare($query);
        $st->execute($params);
        return $st->fetchAll(self::FETCH_ASSOC);
    }

    public function fetch($query, $params = array()) {
        $st = $this->prepare($query);
        $st->execute($params);
        return $st->fetch(self::FETCH_ASSOC);
    }
}
