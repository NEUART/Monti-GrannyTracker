<?php

// checkins.php
// gets username as get variable and displays all checkins on a table
// also displays map of last checkin
require('info.php');

$selected_user = $_GET["user"];
if(isset($_GET["num_view"])){
	$num_view=$_GET["num_view"];
} else{
	$num_view=5; //default value
}
if (isset($_GET["zoom"])){
	$zoom = $_GET["zoom"];
} else {
	$zoom = 15; // default zoom level
}


$link = mysql_connect($sql_server, $sql_username, $sql_password) or die("could not connect to sql server");
$sql = "SELECT * FROM $db.checkins WHERE username = '$selected_user' ORDER BY time DESC";
$query = mysql_query($sql);

$num_checks = mysql_num_rows($query);
$map_gps = mysql_result($query, 0, 'gps');

if ($num_checks){
	$i=0;
	$row = "";
	$master = array();
	mysql_data_seek($query, 0);
	while($entry = mysql_fetch_assoc($query)){
		if ($i < $num_view){
			$master[$i]  = array("gps" => $entry["gps"], "time" => $entry["time"], "id" => $entry["id"], "bpm" => $entry["bpm"]);
		}
		$check_id = $entry["id"];
		$gps = $entry["gps"];
		$bpm = $entry["bpm"];
		$time = $entry["time"];
		$row .= "<tr><td>$check_id</td><td>$gps</td><td>$bpm</td><td>$time</td><td>"
				. "<a href=\"javascript: load_map('$gps');\">"
				. "<img height=40 width=40 src='map_icon.gif' /></a></td></tr>";
		$i++;
	}
	//http://maps.google.com/maps/api/staticmap?markers=size:mid|color:red|label:A|29.631756,-82.373142&sensor=false&size=500x300&zoom=15
	//$master = array_reverse($master);
	$map_img = "http://maps.google.com/maps/api/staticmap?markers=";
	$trail_index=1;
	foreach ($master as $location){
		if ($trail_index==1){
			$map_img.="size:mid|color:red|label:A|" . $location["gps"] . "&markers=size:small|color:blue";
			$trail_index++;
		} elseif($trail_index== min($num_view, $num_checks)){
			// last trail, change color
			$map_img .= "&markers=size:mid|color:green|label:B|" . $location["gps"];
		} else {
			// the middle trails
			$map_img .= "|" . $location["gps"];
			$trail_index++;
		}
	}
	$map_img .= "&path=color:green|weight:3";
	foreach($master as $location){
		$map_img .= "|" . $location["gps"];
	}
	$map_img .= "&sensor=false&size=500x300&zoom=$zoom";
	
	$chart_img =  "http://chart.apis.google.com/chart"
				. "?chxl=0:";
	// add each checkin id
	foreach ($master as $checkin){
		$chart_img .= "|" . $checkin["id"];
	}
	
	$chart_img .= "|2:|BPM|3:|Check-in%20ID"
				. "&chxp=0";
	// add each checkin axis
	$axis_num = min($num_view, $num_checks);
	$axis = range($axis_num-1, 0);
	foreach($axis as $a){
		$chart_img .= "," . $a;
	}
	$chart_img .= "|2,50|3,50"
				. "&chxr=0,0," . ($axis_num-1) . "|1,5,225"
				. "&chxs=0,676767,11.5,0,lt,676767|1,676767,11.5,0,lt,676767"
				. "&chxtc=0,2|1,10"
				. "&chxt=x,y,y,x"
				. "&chs=500x222"
				. "&cht=lxy"
				. "&chds=0,225,0,225";
	// add each bpm
	$chart_img .= "&chd=t:-1|";
	$first_data = true;
	$master = array_reverse($master);
	foreach ($master as $heartbeat){
		if ($first_data){
			$chart_img .= $heartbeat["bpm"];
			$first_data = false;
		} else {
			$chart_img .= "," . $heartbeat["bpm"];
		}
	}
	$chart_img .= "&chdl=BPM"
				. "&chls=1"
				. "&chtt=" . $selected_user . "'s+BPM";
	echo "<img id = \"map\" src=\"$map_img\" /><br />";
	echo "<br/>";
	echo "<img src=\"$chart_img\" /><br />";
	echo "<table align='center'><tr><th>ID</th><th>GPS Coordinates</th><th>BPM</th><th>Check-in Time</th><th>Map</th></tr>";
	echo $row;
	//echo "<pre>";	print_r($master); echo "</pre>";
	
}
/*
for ($i = 0; $i < $num_checks; $i++){
	
	$gps = mysql_result($query, $i, 'gps');
	$bpm = mysql_result($query, $i, 'bpm');
	$time = mysql_result($query, $i, 'time');
	
	$row = "";
	$row .= "<tr><td>$gps</td><td>$bpm</td><td>$time</td><td><img height=40 width=40 src='map_icon.gif' /></td></tr>";
	echo $row;
	
}
*/
echo "</table>";

	

?>
