<?php
// view_alerts.php
// view a table of alerts and allow to edit
require('info.php');

$link = mysql_connect($sql_server, $sql_username, $sql_password) or die("could not connect to sql server");
$sql = "SELECT * FROM $db.alerts";
$query = mysql_query($sql);
$num_rows = mysql_num_rows($query);
echo "<table><tr><th>User</th><th>Name</th><th>BPM Threshold</th><th>BPM Range</th><th>Recipients</th></tr>";
for ($i=0; $i < $num_rows; $i++){
	$alert_id = mysql_result($query, $i, 'id');
	$user = mysql_result($query, $i, 'username');
	$name = mysql_result($query, $i, 'name');
	$bmp_thresh = mysql_result($query, $i, 'bpm_thresh');
	$bmp_range = mysql_result($query, $i, 'bpm_range');
	$emails = mysql_result($query, $i, 'emails');
	
	switch ($bmp_range) {
		case 0:
			$bmp_range_text="Less Than";
			break;
		case 1:
			$bmp_range_text="Greater Than";
			break;
		case 2:
			$bmp_range_text="Equal To";
			break;
		case 3:
			$bmp_range_text="Less Than or Equal To";
			break;
		case 4:
			$bmp_range_text="Greater Than or Equal To";
			break;
		
	}
	
	$row = "<tr><td>$user</td><td>$name</td><td>$bmp_thresh</td><td>$bmp_range_text</td><td><textarea readonly rows=3 cols=30>$emails</textarea></td><td><input type = \"button\" class = \"edit_btn\" value = \"Edit\" id = \"edit_alert_" . $alert_id . "\"> | <input type=\"button\" value=\"Delete\" class = \"del_btn\" id = \"del_alert_" . $alert_id . "\"/></tr>";
	echo $row;
}
echo "</table>";
?>
