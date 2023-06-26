<?php

class KnotMail {

	var $to_mail;
	var $subject_mail;
	var $message_mail;

	function send_mail() {
		$headers_mail  = "Content-type: text/html; charset=utf-8 \r\n";
		$headers_mail .= "From: sweelin™ <knot@knott.fun>\r\n";

		mail($this->to_mail, $this->subject_mail, $this->message_mail, $headers_mail);
	}
}

?>