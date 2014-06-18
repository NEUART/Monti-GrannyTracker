<?php 
$pid = $_GET["pid"];
$cmd = "kill -9 " . $pid;
exec($cmd, $output);
print_r($output);
?>