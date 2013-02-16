<?php

class database extends PDO {
	public function __construct($dsn, $user = null, $pass = null, $array = null) {
		parent::__construct($dsn, $user, $pass, $array);
		$this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
	}

	public function execute($query) {
		$st = $this->prepare($query);
		return $st->execute();
	}

	public function get($query) {
		$st = $this->prepare($query);
		$st->execute();
		return $st->fetchAll(self::FETCH_ASSOC);
	}
}
?>
