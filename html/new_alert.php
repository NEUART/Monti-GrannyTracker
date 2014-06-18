<?php

// new_alert.php
// create a new alert

require('info.php');

if (isset($_POST['username'])){
// create new entry
	
	$link = mysql_connect($sql_server, $sql_username, $sql_password) or die("could not connect to sql server");	
	
	// read values
	$username = $_POST["username"];
	$name = $_POST["name"];
	$bpm_thresh = $_POST["bpm_thresh"];
	$emails = $_POST["emails"];
	$bpm_range = $_POST["bpm_range"];
	
	
	$sql = "INSERT INTO $db.alerts (username, name, bpm_thresh, bpm_range, emails) VALUES ('$username', '$name', '$bpm_thresh', '$bpm_range', '$emails')";
	$query = mysql_query($sql);
	if ($query){
		echo "Alert added!";
	}
	else {
		echo "Uh oh! Error! " . mysql_error();
	}
		

} else {
// display entry form
?>

<form  id="new_entry">
<table>
<tr><td>Username</td><td><input type="text" name="username" id="username" /></td></tr>
<tr><td>Alert name</td><td><input type="text" name="name" id="name" /></td></tr>
<tr><td>BPM Threshold for alert</td><td><input type="text" name="bpm_thresh" id="bpm_thresh" /></td></tr>
<tr><td>Range for Alert</td><td><select name="bpm_range" id="bpm_range"><option value="0">Less Than</option><option value="1">Greater Than</option><option value="2">Equal To</option><option value="3">Less That or Equal Too</option><option value="4">Greater Than or Equal To</option></select></td></tr>
<tr><td>Emails to alert (separate with comma)</td><td><textarea rows=3 cols=30 name="emails" id="emails" ></textarea></td></tr>
<input type="hidden" name="new_entry" id="new_entry" value="new_entry"/>
<tr><td><input type="button" id = "new_alert_btn" value="Create Alert" /></td></tr>
</table>
</form>

<?php
}
?>

