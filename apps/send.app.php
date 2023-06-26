<?php

$errs = 0;

function replace_quotes_callback($matches) {
	if (count($matches) == 3) {
		return '«»';
	} elseif (!empty($matches[1])) {
		return str_replace('"', '«', $matches[1]);
	} else { 
		return str_replace('"', '»', $matches[4]);
	}
}

function replace_quotes($txt) {
	$txt = htmlspecialchars_decode($txt, ENT_QUOTES);
	$txt = str_replace(array('«', '»'), '"', $txt);

	return preg_replace_callback('/(([\"]{2,})|(?![^\W])(\"))|([^\s][\"]+(?![\w]))/u', 'replace_quotes_callback', $txt);
}

function antisql($txt) {
	$txt = trim($txt);
	$txt = replace_quotes($txt);

	return $txt;
}

$name = antisql($_POST['name']);
$email = antisql($_POST['email']);
$message = antisql($_POST['message']);
$updates = antisql($_POST['updates']);
$selected = antisql($_POST['selected']);

$process = 0;
$block_line = '';
$errs = 0;

$err_name = '';
$err_email = '';
$err_message = '';


if (!isset($name) OR $name == '') {
	$err_name = 'Enter your name';

	$errs = 1;
} else {
	if (!isset($email) OR $email == '') {
		$err_email = 'Enter your email';

		$errs = 1;
	} else if (!preg_match("/^((([0-9A-Za-z]{1}[-0-9A-z\.]{0,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{0,}[0-9А-Яа-я]{1}))@([-0-9A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u", $email)) {
		$err_email = 'Enter correct email';

		$errs = 1;
	} else {
		if (!isset($message) OR $message == '') {
			$err_message = 'Write your message';

			$errs = 1;
		}
	}
}

$updates_line;
$selected_line;

if ($updates != 'false') {
	$updates_line = '<tr style="border-bottom: 1px solid #D8D8D8;"><td style="padding: 10px 15px">Updates:</td><td style="padding: 10px 15px">' . $updates . '</td></tr>';
}

if ($selected != 'false') {
	$selected_line = '<tr style="border-bottom: 1px solid #D8D8D8;"><td style="padding: 10px 15px">Interested:</td><td style="padding: 10px 15px">' . $selected . '</td></tr>';
}


if ($errs == 0) {
	require_once('../classes/KnotMail.php');

	$mail = new KnotMail();
	$mail->to_mail = 'knot.test.mail@gmail.com';
	$mail->subject_mail = 'Spect agency | New order';
	$mail->message_mail = '<div style="background: #EFEFEB; padding-top: 30px; padding-bottom: 30px; font-size: 16px;">
								<table width="600px" style="margin: 0 auto; background: #FFFFFF; border: 1px solid #D8D8D8; border-collapse: collapse;">
									<thead>
										<tr>
											<th style="width: 32%;"></th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr style="border-bottom: 1px solid #D8D8D8;"><td style="padding: 10px 15px">Name:</td><td style="padding: 10px 15px">' . $name . '</td></tr>
										<tr style="border-bottom: 1px solid #D8D8D8;"><td style="padding: 10px 15px">Email:</td><td style="padding: 10px 15px">' . $email . '</td></tr>
										<tr style="border-bottom: 1px solid #D8D8D8;"><td style="padding: 10px 15px">Message:</td><td style="padding: 10px 15px">' . $message . '</td></tr>
										' . $updates_line .'
										' . $selected_line .'
									</tbody>
								</table>
							</div>';

	$mail->send_mail();

	$mail->to_mail = 'romakhokhlov1@gmail.com';

	$mail->send_mail();

	$mail->to_mail = 'info@amaiproteins.com';

	$mail->send_mail();

	$process = 1;
}

$result = array(
	'process' => $process,
	'errs' => array(
		'name' => $err_name,
		'email' => $err_email,
		'message' => $err_message,
		'updates' => $updates,
		'selected' => $selected,
	)
);

echo json_encode($result);

?>