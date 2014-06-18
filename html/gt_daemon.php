<?php

require("info.php");

function send_email_alerts($email_list, $username, $bpm, $threshold, $gps){
	// function takes a list of emails, the bpm, the threshold, and the gps
	// and sends an email alert with a formletter to each email
	// return value is total number of emails sent




	require_once('class.phpmailer.php');
	require_once('info.php');
	global $mail_username, $mail_password, $mail_from;

	$exploded_email_list = explode(",", $email_list);
	//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
	foreach($exploded_email_list as $email_to){

		$mail             = new PHPMailer();


		$body = "ALERT! A user's heart rate has dropped below the set allowed limit!\nUser $username's heartrate is $bpm, below their alert cutoff of $threshold bpm.\n
			Their coordinates are at $gps. Please send help now!\n\nGrannyTrack Daemon";
			
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->Host       = "mail.gmail.com"; // SMTP server
		$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
		$mail->Username   = $mail_username;  	// GMAIL username
		$mail->Password   = $mail_password;            // GMAIL password
		
		
		$test_from = $mail_username . "@gmail.com";
		//echo "from: $test_from ";
		$mail->SetFrom($test_from, 'GrannyTracker');

		$mail->AddReplyTo($test_from,"GrannyTracker Team");

		$mail->Subject    = "GRANNY ALERT! $username's bpm has dropped below allowed limit!";

		$mail->AltBody    = $body;

		$mail->MsgHTML($body);
		
		$address = $email_to;
		$mail->AddAddress($address, "Cousin IT");
		//echo "Email sent to $email_to ";

		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
		if(!$mail->Send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			echo "Message sent!";
		}
	}
}
// mail email checking loop
while(true){	
	// connect to imap server
	$mbox = imap_open($mail_server.$gmail_box, $mail_username,$mail_password) or die('Cannot connect to Gmail: ' . imap_last_error());
	//if ($mbox){echo "success ";}
	
	// get all grannytracker emails
	$emails = imap_search($mbox,'SUBJECT "Info From Team NEUARTmaxx"', SE_UID);
	//print_r($emails);
	if ($emails){
		
		// if there are emails with subj grannytrack
		//echo " emails! ";
	
		// start tabs
		$num_processed = 0;
		$num_skipped = 0;
		$num_spam = 0;
		$num_alerts = 0;
		$total_proc = 0;
		$num_emails = count($emails);
		foreach($emails as $email_number){
		
			//echo " $email_number ";
			$total_proc++;
			$struct = imap_fetchstructure($mbox, $email_number, FT_UID);
			$code = $struct->encoding; // determine the message encoding
			$body = imap_fetchbody($mbox, $email_number, 1, FT_UID); //fetch the body
			
			if ($code==4){
				$body = imap_qprint($body);
			}
				
			$body = strtolower($body);
			$checkin = parse_ini_string($body);
			//echo  " $checkin ";
			//echo "<p>$body</p>";
			
			if (empty($checkin)){
				//echo "UID: " . $email_number . " - spam email detected<br />";
				//print_r($checkin);
				$num_spam++;
			} else {
				// process the email
				print_r($checkin);
				// check to see if email checkin has already been entered into database
				// every checkin email UID is stored, so search db for that UID
				$link = mysql_connect($sql_server, $sql_username, $sql_password);
				// UID is $email_number, so use that to search DB
				$sql = "SELECT * FROM $db.checkins WHERE UID = '$email_number'";
				$query = mysql_query($sql);
				if (!mysql_num_rows($query)){
				// no results found for that UID, so insert entry into database
					//////////////////////////////////////////////////////////////////////////
					// NOTE FOR SCOTT:
					//
					// the below line pulls the username from the email body
					// as username = "grannysmith"
					// $username = $checkin["username"];
					//
					// the below line is for the prototype testing, so the username will always be test_user
					//$username = "test_user";
					$username = $checkin["username"];
					$gps = $checkin["gps"];
					$bpm = $checkin["bpm"];
					$sql = "INSERT INTO $db.checkins (username, gps, bpm, time, UID) VALUES ('$username', '$gps', '$bpm', NOW(), '$email_number')";
					$num_processed++;
					$add_checkin_result = mysql_query($sql) or die("could not add entry" . mysql_error());
					
					// now search for alerts for this username and check each one to see if the bpm is below threshold
					$sql = "SELECT * from $db.alerts WHERE username = '$username'";
					$alert_query = mysql_query($sql);
					$num_alerts = mysql_num_rows($alert_query);
					//echo " Num alerts: $num_alerts ";
					while ($alert = mysql_fetch_assoc($alert_query)){
					
						$bpm_threshold = $alert['bpm_thresh'];
						$email_list = $alert['emails'];
						$bpm_range = $alert['bpm_range'];
						echo "BPM Thresh: $bpm_threshold BPM: $bpm\n";
						
						switch ($bpm_range) {
							case 0:
								if ($bpm < $bpm_threshold){
									// if bpm is lower than threshold
									echo "Alert! email sent\n";
									send_email_alerts($email_list, $username, $bpm, $bpm_threshold, $gps);
									$num_alerts++;
								} else {echo "bpm OK\n"; }
								break;
							case 1:
								if ($bpm > $bpm_threshold){
									// if bpm is lower than threshold
									echo "Alert! email sent\n";
									send_email_alerts($email_list, $username, $bpm, $bpm_threshold, $gps);
									$num_alerts++;
								} else {echo "bpm OK\n"; }
								break;
							case 2: 
								if ($bpm == $bpm_threshold){
									// if bpm is lower than threshold
									echo "Alert! email sent\n";
									send_email_alerts($email_list, $username, $bpm, $bpm_threshold, $gps);
									$num_alerts++;
								} else {echo "bpm OK\n"; }
								break;
							case 3:
								if ($bpm <= $bpm_threshold){
									// if bpm is lower than threshold
									echo "Alert! email sent\n";
									send_email_alerts($email_list, $username, $bpm, $bpm_threshold, $gps);
									$num_alerts++;
								} else {echo "bpm OK\n"; }
								break;
							case 4:
								if ($bpm >= $bpm_threshold){
									// if bpm is lower than threshold
									echo "Alert! email sent\n";
									send_email_alerts($email_list, $username, $bpm, $bpm_threshold, $gps);
									$num_alerts++;
								} else {echo "bpm OK\n"; }
								break;
						}
					}
				} else {
					//echo "UID: $email_number already found. Bypassing...<br/>";
					$num_skipped++;
				}
			}
		}
		
	}
	$timestamp = date("d/m/y : H:i:s", time()) ;
	$log = "\nTotal: $total_proc New: $num_processed Skip: $num_skipped Alerts: $num_alerts Spam: $num_spam $timestamp";
	$handle = fopen("log.txt", "a");
	if ($handle){
		if (fwrite($handle, $log)){
			echo "log written";
		}
		
	}
	echo $log;
	fclose($handle);
sleep(5);
}		
?>
