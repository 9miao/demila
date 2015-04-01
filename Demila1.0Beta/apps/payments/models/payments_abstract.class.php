<?php

abstract class payments_abstract {

	protected $meta = array();
	
	private $key = 'payment-key-hiiden';
	
	public function __construct($meta = array()) {
		$this->meta = $meta;
	}

	/*   */
	
	abstract function generateForm($order_data = array());
	
	public function getMeta($key) {
		return isset($this->meta[$key]) ? $this->meta[$key] : '';
	}
	
	public function getUserData($key) {
		return isset($_SESSION['user'][$key]) ? $_SESSION['user'][$key] : '';
	}
	
	public function encrypt($value) {
		if (!$this->key) { 
			return $value;
		}
		
		$output = '';
		
		for ($i = 0; $i < strlen($value); $i++) {
			$char = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			
			$output .= $char;
		} 
		
        return base64_encode($output); 
	}
	
	public function decrypt($value) {
		if (!$this->key) { 
			return $value;
		}
		
		$output = '';
		
		$value = base64_decode($value);
		
		for ($i = 0; $i < strlen($value); $i++) {
			$char = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			
			$output .= $char;
		}
		
		return $output;
	}
	
}

?>