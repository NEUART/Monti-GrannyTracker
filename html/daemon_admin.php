<html>
<head>
<title>Daemon admin</title>

</head>
<body>
<?php
require('info.php');

// function to see if pid is running
function pid_is_running($pid) {
	// create our system command
	$cmd = "ps $pid";

	// run the system command and assign output to a variable ($output)
	exec($cmd, $output);
	
	// check the number of lines that were returned
	if(count($output) >= 2){

		// the process is still alive
		return true;
	}

	// the process is dead
	return false;
}


// check if process is running

$link = mysql_connect($sql_server, $sql_username, $sql_password) or die("could not connect to database: " . mysql_error());
				
$sql = "SELECT * FROM $db.pids ORDER BY time DESC";
$query = mysql_query($sql) or die("could not query database: "  . mysql_error());



$num_rows = mysql_num_rows($query);

if ($num_rows){
	// get last pid and see if running
	$test_pid = mysql_result($query, 0, "pid");
	echo "LAST PID: " . $test_pid;
	if (!pid_is_running($test_pid)){
		// if process is not running
		$new_pid = exec ("/usr/bin/php gt_daemon.php >/dev/null & echo $!");
		echo "<h2>Daemon started: PID: $new_pid </h2>";
		$sql = "INSERT INTO $db.pids (pid, time) VALUES ('$new_pid', NOW() )";
		mysql_query($sql) or die("could not add PID to database" .   mysql_error());
		echo "<br />Added PID to database";
	} else {
		echo "<h2>Daemon already running, PID = " . $test_pid . " , nothing to do...but <a href = \"kill.php?pid=$test_pid\" >KILL!!!</a></h2>";
	}
} 

?>
</body>
</html>