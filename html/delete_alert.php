<?php 
	// delete_alert.php
	require_once('info.php');
	if (isset($_POST["delete_alert_id"])){
		$delete_alert_id = $_POST["delete_alert_id"];
		$sql = "DELETE FROM $db.alerts WHERE id = '$delete_alert_id'";
		$link = mysql_connect($sql_server, $sql_username, $sql_password) or die("Could not connect to database");
		$query = mysql_query($sql);
		if ($query){
			echo "<h3>Alert deleted: ID = $delete_alert_id </h3>";
		} else {
			echo "<h3>Error! " . mysql_error() . "</h3>";
		}
		
	} else {
		echo "<h3>No POST data</h3>";
	}
?>
