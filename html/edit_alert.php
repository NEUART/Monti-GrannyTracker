<?php 

// edit_alert.php
// allows to edit one alert
require('info.php');

if (isset($_GET["alert_id"]) and !isset($_POST["save_data"])){
	// if we are loading the alert to edit
	// then edit it
	$alert_id = $_GET["alert_id"];


	$sql = "SELECT * FROM $db.alerts WHERE id = '$alert_id'";

	$link = mysql_connect($sql_server, $sql_username, $sql_password);

	$query = mysql_query($sql);

	if (!$query){
		die ("invalid alert ID");
	}

	if (mysql_num_rows($query) != 1){
		die ("Query error!");
	}

	$username = mysql_result($query, 0, 'username');
	$name = mysql_result($query, 0, 'name');
	$bpm_thresh = mysql_result($query, 0, 'bpm_thresh');
	$bpm_range = mysql_result($query, 0, 'bpm_range');
	$emails = mysql_result($query, 0, 'emails');
?>

<form id="edit_alert">
<table>
<tr><td>Internal ID</td><td><input name = "alert_id" id = "alert_id" type="text" value=<?php echo $alert_id; ?> readonly disabled /></td></tr>
<tr><td>Username</td><td><input name = "username" id = "username" type="text" value="<?php echo $username; ?>" readonly /></td></tr>
<tr><td>Alert Name</td><td><input name = "name" id = "name" type = "text" value = "<?php echo $name; ?>" /></td></tr>
<tr><td>BPM Threshold</td><td><input name = "bpm_thresh" id = "bpm_thresh" type = "text" value = "<?php echo $bpm_thresh; ?>" /></td></tr>
<tr><td>BPM Range</td><td><select name="bpm_range" id="bpm_range"><option value="0" <?PHP if ($bpm_range == 0) {echo "selected";} ?>>Less Than</option><option value="1" <?PHP if ($bpm_range == 1) {echo "selected";} ?>>Greater Than</option><option value="2" <?PHP if ($bpm_range == 2) {echo "selected";} ?>>Equal To</option><option value="3" <?PHP if ($bpm_range == 3) {echo "selected";} ?>>Less That or Equal Too</option><option value="4" <?PHP if ($bpm_range == 4) {echo "selected";} ?>>Greater Than or Equal To</option></select></td></tr>
<tr><td>Emails to Alert:</td><td><textarea name="emails" id = "emails" cols=30 rows=3><?php echo $emails; ?></textarea></td></tr>
<input type="hidden" name="save_data" id = "save_data" value="true" /><br />
<tr><td><input type="button" value="Save" id="save_btn"></td></tr>
</table>
</form>


<?php
} elseif (isset($_POST['save_data'])){
// else save the data
	$alert_id = $_POST["alert_id"];
	$username = $_POST["username"];
	$name = $_POST["name"];
	$bpm_thresh = $_POST["bpm_thresh"];
	$bpm_range = $_POST["bpm_range"];
	$emails = $_POST["emails"];
	$link = mysql_connect($sql_server, $sql_username, $sql_password) or die("could not connect to mysql");
	$sql = "UPDATE $db.alerts SET username = '$username', name = '$name', bpm_thresh = '$bpm_thresh', bpm_range = '$bpm_range', emails = '$emails' WHERE id = $alert_id";
	$query = mysql_query($sql);
	if ($query){
		echo "Alert updated!";
	}
	//UPDATE `grannytrack`.`alerts` SET `name`='my test' WHERE `id`='2';
}
?>