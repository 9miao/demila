<?php
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------

header("Content-type: text/html; charset=utf-8");
class email {
	
	public $contentType = 'text/plain';
	public $charSet = 'UTF-8';
	public $fromEmail = 'no-reply@domain.com';
	public $subject = '';
	public $message = '';	
	
	public $sendTo = '';
	private $sendCc = '';
	private $sendBcc = '';	
	
	public function to($email) {
		if(is_array($email)) {
			foreach($email as $e) {
				if($this->sendTo != '') {
					$this->sendTo .= ', ';
				}
				$this->sendTo .= $e;
			}
		}
		else {
			if($this->sendTo != '') {
				$this->sendTo .= ', ';
			}
			$this->sendTo .= $email;
		}
		
		return true;
	}
	
	public function cc($email) {
		if(is_array($email)) {
			foreach($email as $e) {
				if($this->sendCc != '') {
					$this->sendCc .= ', ';
				}
				$this->sendCc .= $e;
			}
		}
		else {
			if($this->sendCc != '') {
				$this->sendCc .= ', ';
			}
			$this->sendCc .= $email;
		}
		
		return true;
	}

	public function bcc($email) {
		if(is_array($email)) {
			foreach($email as $e) {
				if($this->sendBcc != '') {
					$this->sendBcc .= ', ';
				}
				$this->sendBcc .= $e;
			}
		}
		else {
			if($this->sendBcc != '') {
				$this->sendBcc .= ', ';
			}
			$this->sendBcc .= $email;
		}
		
		return true;
	}
	
	public function send() {
		
		if($this->sendTo == '' && $this->sendCc == '' && $this->sendBcc == '') {
			return 'Please set a recipient.';
		}
		
		if($this->subject == '') {
			return 'Please set a subject.';
		}else{
			$this->subject = "=?UTF-8?B?".base64_encode($this->subject)."?=";
		}
		
		if($this->message == '') {
			return 'Please set a message.';
		}
		
		$headers  = 'MIME-Version: 1.0'.PHP_EOL;
		$headers .= 'Content-type: '.$this->contentType.'; charset='.$this->charSet.PHP_EOL;
		$headers .="Content-Transfer-Encoding: 8bit".PHP_EOL;

		$headers .= 'From: '.$this->fromEmail.PHP_EOL;
		if($this->sendCc != '') {
			$headers .= 'Cc: '.$this->sendCc.PHP_EOL;
		}
		if($this->sendBcc != '') {
			$headers .= 'Bcc: '.$this->sendBcc.PHP_EOL;
		}

		return mail($this->sendTo, $this->subject, $this->message, $headers);
		
	}
}

?>