<img src="../images/logo.jpg" alt="Monti Logo" width="308" height="200"> 
<h2>Tracking Page</h2>

Select Granny to Track <select name="granny" id = "granny" >

<?php 

require('info.php');

$link = mysql_connect($sql_server, $sql_username, $sql_password) or die('Could not connect to sql server');
$sql = "(SELECT username FROM $db.alerts) UNION (SELECT username FROM $db.checkins) ORDER BY username DESC";
$query = mysql_query($sql);
$num_users = mysql_num_rows($query);
// build array of usernames from alerts db
$usernames =  array();

for ($i = 0; $i < $num_users; $i++){
	$name=mysql_result($query, $i, 'username');
	if (!in_array($name, $usernames)){
		// add to array
		$usernames[] = $name;
		echo "<option value = \"$name\">$name</option>";
	} else{
		// do nothing
	}
}

?>
</select>
Number of checkins to map/chart
<select name="num_view" id="num_view" >
<option>1</option>
<option>3</option>
<option>5</option>
<option>10</option>
<option>15</option>
<option>20</option>
<option>25</option>
<option selected>30</option>
<option>35</option>
<option>40</option>
</select>
Zoom level
<select name="zoom_level" id="zoom_level">
<?php 
	$zoom = range(0,21); // the zoom range for google maps
	foreach($zoom as $zoomlevel){
		if ($zoomlevel == 15){
			echo "<option selected>$zoomlevel</option>";
		} else {
			echo "<option>$zoomlevel</option>";
		}
	}
?>
</select>

<div id="checkin_area">
</div>

