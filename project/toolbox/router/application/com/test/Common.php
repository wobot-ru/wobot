<?php
class Common {
	private $_message;

	public function __construct($message) {
		$this->_message = $message;
	}

	public function __toString() {
		return $this->_message;
	}
}
?>