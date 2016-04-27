<?php
include 'connectDB.php';
$id = $_GET["id"];
$templateId = $_GET["templateId"];
$query="SELECT type FROM templates WHERE id=\"".$templateId."\"";
$res = mysqli_query($con,$query) or die("Bad SQL");
$row = mysqli_fetch_array($res);
$typeId = $row[0];
$query="SELECT type FROM device_type WHERE id=\"".$typeId."\"";
$res = mysqli_query($con,$query) or die("Bad SQL");
$row = mysqli_fetch_array($res);
$typeName = $row[0];
$date = $_GET["date"];
$newDate = substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2);
$time = substr($date,11,8);
$hour = substr($date,11,8);
$weekday = date('l', strtotime($newDate));
$monthNum = substr($date,3,2);
$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
$query="SELECT zone FROM band WHERE day_of_week='$weekday' and '$hour' between start_time AND end_time;";

$res = mysqli_query($con,$query) or die("Bad SQL");

$row = mysqli_fetch_array($res);
$zone = $row[0];
if($id{0}!='C')
{
	//echo $row['name'];
	//echo $id;
	
	if($weekday=="Sunday"||$weekday=="Saturday")
	{
		$date = "2014-07-13";
	}
	else
	{
		$date = "2014-07-11";
	}
	$query="SELECT * FROM solarlogmod WHERE user=\"".$id."\" and data=\"".$date."\" and time = \"".$time."\"";
	//echo $query;
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$power=$row['power'];
	//echo $power;
	//$zone="Undefined";
}
else
{
$monthNum=1;

$query="SELECT power FROM trial_consume,trial_identity WHERE user_code= '$id' and zone='$zone' and month='$monthName' and trial_consume.user_id=trial_identity.user_id";

$res = mysqli_query($con,$query) or die("Bad SQL");

$row = mysqli_fetch_array($res);
$power = $row[0];

 
$weekday = date('l', strtotime($date));

}


$jsonString = "{\"id\": \"".$id."\",\"date\": \"".$newDate."\", \"hour\": \"".$hour."\",";
$jsonString = $jsonString."\"lastDateAvailable\":\"".$newDate."\",\"nearestTime\":\"".$hour."\", \"zone\":";
$jsonString = $jsonString." \"".$zone."\", \"powerIn\": ".$power.", \"type\":\"".$typeName."\"}";
echo $jsonString;
?>
