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

    //smtp  发送邮件
    function email_sock($host,$port,$errno,$errstr,$timeout,$auth,$user,$pass,$from)
    {
        $this->host = $host;
        $this->port = $port;
        $this->errno = $errno;
        $this->errstr = $errstr;
        $this->timeout = $timeout;
        $this->auth = $auth;
        $this->user = $user;
        $this->pass = $pass;
        $this->from = $from;
    }

    function send_mail_sock($subject,$message,$to,$from_name,$mailformat=0)//邮件标题,邮件内容,收件地址,邮件格式1=text|0=html,默认为0
    {
        $host = $this->host;
        $port = $this->port;
        $errno = $this->errno;
        $errstr = $this->errstr;
        $timeout = $this->timeout;
        $auth = $this->auth;
        $user = $this->user;
        $pass = $this->pass;
        $from = $this->from;

        /*
        1.创建sock，并打开连接
        2.设置为阻塞模式
        3.测试smtp应答码是否为220，220代表邮件服务就绪
        4.发送用户身份验证，由用户设置
        1=EHLO Host Domain \r\n
        0=HELO Host Domain \r\n
        ?.读取服务器端发送给客户端的返回数据
        smtp.163.com 发送的数据为：
        250-PIPELINING//流水命令，告诉客户端可以一次发送多个命令来提高速度，在这里PHP
        并没有使用，因为PHP单个文件的运行还是单线程的
        250-AUTH LOGIN PLAIN
        250-AUTH=LOGIN PLAIN
        250 8BITMIME//得到这一行也就是smtp服务器发送结束了，等待客户端发送命令
        5.发送AUTH LOGIN命令
        6.发送用户名
        7.发送密码
        ?.身份验证过成功后后，
        8.向服务器添加from
        9.向服务器添加to
        10.发送DATA命令,开始输入email数据,以"."号结束
        11.书写邮件内容
        12.将邮件内容发送到smtp服务器
        13.发送QUIT命令，结束会话
        */
        $fp = fsockopen($host,$port,$errno,$errstr,$timeout);//打开sock的网络连接
        if(!$fp){return '1.没有设置好smtp服务';}

        stream_set_blocking($fp, true);//设置为阻塞模式,此模式读不到数据则会停止在那

        $mail_return=fgets($fp, 512);//读取512字节内容
        if(substr($mail_return, 0, 3) != '220')
        {return $host.'-2.返回应答码为'.$mail_return;}//返回应答码所代表意思请参考'smtp协议.txt'


        fputs($fp, ($auth ? 'EHLO' : 'HELO')." ".$host."\r\n");//服务器标识用户身份 1=身份验证的标识,0=不需要身份验证的标识
        $mail_return = fgets($fp, 512);
        if(substr($mail_return, 0, 3) != 220 && substr($mail_return, 0, 3) != 250)
        {return $host.'-3.返回应答码为'.substr($mail_return, 0, 3);}

        while(true)
        {
            $mail_return = fgets($fp, 512);
            if(substr($mail_return, 3, 1) != '-' || empty($mail_return))
            {break;}
        }


        if($auth)
        {
            fputs($fp, "AUTH LOGIN\r\n");
            $mail_return = fgets($fp, 512);
            if(substr($mail_return, 0, 3) != 334)
            {return $host.'-5.返回应答码为'.substr($mail_return, 0, 3);}

            fputs($fp, base64_encode($user)."\r\n");
            $mail_return = fgets($fp, 512);
            if(substr($mail_return, 0, 3) != 334)
            {return $host.'-6.返回应答码为'.substr($mail_return, 0, 3).'user='.$user;}

            fputs($fp, base64_encode($pass)."\r\n");
            $mail_return=fgets($fp, 512);
            if(substr($mail_return, 0, 3) != 235)
            {return $host.'-7.用户验证失败，应答码为'.substr($mail_return, 0, 3);}
        }

//向服务器添加FROM and TO
//------------------------------------------------------------------------------------------------------------------------
        fputs($fp, "MAIL FROM: ".$from."\r\n");//有两种格式，MAIL FROM:xxx@xx.com和MAIL FROM: <xxx@xx.com>
        $mail_return = fgets($fp, 512);
        if(substr($mail_return, 0, 3) != 250)
        {
            fputs($fp, "MAIL FROM: <".$from.">\r\n");
            $mail_return = fgets($fp, 512);
            if(substr($mail_return, 0, 3) != 250)
            {return $host.'-8.返回应答码为'.$mail_return;}
        }

        foreach(explode(',', $to) as $mailto)
        {
            $mailto = trim($mailto);
            if($mailto)
            {
                fputs($fp, "RCPT TO: ".$mailto."\r\n");
                $mail_return = fgets($fp, 512);
                if(substr($mail_return, 0, 3) != 250)
                {
                    fputs($fp, "RCPT TO: <".$mailto.">\r\n");
                    $mail_return = fgets($fp, 512);
                    if(substr($mail_return, 0, 3) != 250)
                    {return $host.'-9.返回应答码为'.substr($mail_return, 0, 3);}
                }
            }

        }
//------------------------------------------------------------------------------------------------------------------------
        fputs($fp, "DATA\r\n");//开始输入email数据,以"."号结束
        $mail_return = fgets($fp, 512);
        if(substr($mail_return, 0, 3) != 354)
        {return $host.'-10.返回应答码为'.substr($mail_return, 0, 3);}

//邮件内容
//-----------------------------------------------------------
        $subject='=?utf-8?B?' . base64_encode($subject) . '?=';
        $mail_message = "From:".$from_name.'<'.$from.">\r\n";
        $mail_message .= "To:".$to."\r\n";
        $mail_message .= "Subject:".str_replace("\n",' ',$subject)."\r\n";
        if($mailformat==1)
        {$mail_message .= "Content-Type: text/html;\r\n"; }
        else
        {$mail_message .= "Content-Type: text/html;\r\n";}
        $mail_message .= "charset=utf-8\r\n\r\n";
        $mail_message .= $message;
        $mail_message .= "\r\n.\r\n";
//-----------------------------------------------------------

        fputs($fp,$mail_message);
        fputs($fp,"QUIT\r\n");

        return 1;
    }


}

?>