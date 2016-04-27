<?php
include 'connectDB.php';
$id = $_GET["id"];
echo "ID=".$id;
$templateId = $_GET["templateId"];
$query="SELECT type FROM templates WHERE id=\"".$templateId."\"";
$res = mysqli_query($con,$query) or die("Bad SQL");
$row = mysqli_fetch_array($res);
$typeId = $row[0];
$query="SELECT type FROM device_type WHERE id=\"".$typeId."\"";
$res = mysqli_query($con,$query) or die("Bad SQL");
$row = mysqli_fetch_array($res);
$typeName = $row[0];
$date1 = $_GET["date1"];
$newDate1 = substr($date1,6,4)."-".substr($date1,3,2)."-".substr($date1,0,2);
$time1 = substr($date1,11,8);
$hour1 = substr($date1,11,8);
$day1 = substr($date1,0,2);
$weekDay1 = date('l', strtotime($newDate1));
$monthNum1 = substr($date1,3,2);
$monthName1 = date("F", mktime(0, 0, 0, $monthNum1, 10));
$year1= substr($newDate1,0,4);


$newHour1=$hour1;


$date2 = $_GET["date2"];
$newDate2 = substr($date2,6,4)."-".substr($date2,3,2)."-".substr($date2,0,2);
$hour2 = substr($date2,11,8);
$monthNum2 = substr($date2,3,2);
$weekDay2 = date('l', strtotime($newDate2));
$monthName2 = date("F", mktime(0, 0, 0, $monthNum2, 10));
$year2= substr($newDate2,0,4);
$newHour2=$hour2;
$day2 = substr($date2,0,2);

$ora1=substr($hour1,0,2);
$minuti1=substr($hour1,3,2);
$secondi1=substr($hour1,6,2);
$mese1=$monthNum1;
$giorno1=$day1;
$anno1=$year1;

$ora2=substr($hour2,0,2);
$minuti2=substr($hour2,3,2);
$secondi2=substr($hour2,6,2);
$mese2=$monthNum2;
$giorno2=$day2;
$anno2=$year2;

$timeStamp1= mktime($ora1,$minuti1,$secondi1,$mese1,$giorno1,$anno1);
$timeStamp2= mktime($ora2,$minuti2,$secondi2,$mese2,$giorno2,$anno2);
$timeStampActual=$timeStamp1;

$actualDate = date("Y-m-d H:i:s",$timeStampActual);
//echo "ACTUAL DATE ".$actualDate."<br>";


echo "{\"results\":[";


 	
        



if($id{0}!='C')
{
$first=true;      
	while($timeStampActual<=$timeStamp2)
	{
		$newDate1 = substr($actualDate,0,10);
		$time = substr($actualDate,11,8);
		$hour = substr($actualDate,11,8);
		$weekday = date('l', strtotime($newDate1));
		$monthNum = substr($actualDate,5,2);
		//echo $monthNum;
		$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
		$year= substr($newDate1,0,4);
		if($weekday=="Sunday"||$weekday=="Saturday")
		{	
			$date = "2014-07-13";
		}
		else
		{
			$date = "2014-07-11";
		}
		
	$time = date("H:i:s",$timeStampActual);
	$query="SELECT * FROM solarlogmod WHERE user=\"".$id."\" and data=\"".$date."\" and time = \"".$time."\"";
	echo "la query è ".$query."<br>";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$power=$row['power'];
	echo "La power è".$power;
	if(!$power) $power=0;
	//echo "First = ".$first."<br>";
	//echo "La potenza è ".$power."<br>";
	if(!$first) $jsonString=$jsonString.", ";
	$jsonString = "{";
	$jsonString = $jsonString."\"lastDateAvailable\":\"".$newDate1."\",\"nearestTime\":\"".$hour."\",";
	$jsonString = $jsonString." \"powerIn\": ".$power."}";
	$timeStampActual+=300;
	if($timeStampActual<=$timeStamp2)
		echo $jsonString.",";
		else
		echo $jsonString;
	$first=false;
	
	$actualDate = date("Y-m-d H:i:s",$timeStampActual);
	}
	echo "]}";
}
else
{
	$weekDay=$weekDay1;
	$timeStampActual=$timeStamp1;
	$actualDate = date("Y-m-d H:i:s",$timeStampActual);
	$hour=$hour1;
	$monthNum = substr($actualDate,5,2);
	$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
	$newDate = substr($actualDate,0,10);
	while($timeStampActual<=$timeStamp2)
	{
		$query="SELECT zone FROM band WHERE day_of_week='$weekDay' and '$hour' between start_time AND end_time;";
		$res = mysqli_query($con,$query) or die("Bad SQL");
		$row = mysqli_fetch_array($res);
		$zone = $row[0];

		$query="SELECT power FROM trial_consume,trial_identity WHERE user_code= '$id' and zone='$zone' and month='$monthName' and trial_consume.user_id=trial_identity.user_id";
		$res = mysqli_query($con,$query) or die("Bad SQL");
		$row = mysqli_fetch_array($res);
		$power = $row[0];
		$weekday = date('l', strtotime($actualDate));
		$jsonString = "{";
		$jsonString = $jsonString."\"lastDateAvailable\":\"".$newDate."\",\"nearestTime\":\"".$hour."\",";
		$jsonString = $jsonString."\"powerIn\": ".$power."}";
		
		$timeStampActual+=300;
		if($timeStampActual<=$timeStamp2)
		echo $jsonString.",";
		else
		echo $jsonString;
		$actualDate = date("Y-m-d H:i:s",$timeStampActual);
		$weekday = date('l', strtotime($actualDate));
		$time = substr($actualDate,11,8);
		$hour = substr($actualDate,11,8);
		$monthNum = substr($actualDate,5,2);
		$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
		$newDate = substr($actualDate,0,10);
	}
	echo "]}";
}

?>
