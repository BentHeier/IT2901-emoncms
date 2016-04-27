<html>
<body>
<?php
include 'connectDB.php';
$jsonp=$_POST['json'];

$deviceType = $_POST['deviceType'];
$deviceId =  $_POST['deviceId'];
$query="insert into device_list (device_id,device_type,user) values ('".$deviceId."','".$deviceType."',1)";
//echo $query;
$time = time();
$res = mysqli_query($con,$query) or die("Bad SQL0");
if($deviceType=="bulb")
{
	$query="insert into device_parameter_value (incremental,id_device,parameter,value) values ('null','".$deviceId."','state',0)";
	$res = mysqli_query($con,$query) or die("Bad SQL1");
	$query="insert into device_parameter_value (incremental,id_device,parameter,value) values ('null','".$deviceId."','power_in',0)";
	mysqli_query($con,$query) or die("Bad SQL");
	//echo "<br>".$query;
}
else if($deviceType=="battery")
{
	$query="insert into device_parameter_value values ('null','".$deviceId."','power_in',0)";
	mysqli_query($con,$query) or die("Bad SQL2");
	//echo "<br>".$query;
	$query="insert into device_parameter_value values ('null','".$deviceId."','power_out',0)";
	mysqli_query($con,$query) or die("Bad SQL");
	//echo "<br>".$query;
	$query="insert into device_parameter_value values ('null','".$deviceId."','lastTime',".$time.")";
	mysqli_query($con,$query) or die("Bad SQL");
	//echo "<br>".$query;
	$query="insert into device_parameter_value values ('null','".$deviceId."','energy_max',0)";
	//echo "<br>".$query;
	mysqli_query($con,$query) or die("Bad SQL");
	$query="insert into device_parameter_value values ('null','".$deviceId."','max_power_in',0)";
	//echo "<br>".$query;
	mysqli_query($con,$query) or die("Bad SQL");
	$query="insert into device_parameter_value values ('null','".$deviceId."','max_power_out',0)";
	//echo "<br>".$query;
	mysqli_query($con,$query) or die("Bad SQL");
	$query="insert into device_parameter_value values ('null','".$deviceId."','energyLevel',0)";
	//echo "<br>".$query;
	mysqli_query($con,$query) or die("Bad SQL");
	
}


?>

OK
</body>
</html>
