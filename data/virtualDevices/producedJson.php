<?php
include 'connectDB.php';
$id = $_GET["id"];
//echo "User: ".$user."<br>";
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
$hour = substr($date,11,8);
//echo "Date: ".$newDate."<br>";
$monthNum = substr($date,3,2);
$year= substr($newDate,0,4);
/*$day = $_GET["day"];
$dayNum=$day;
$month = $_GET["month"];
$monthNum =$month;
$year = 2014;//$_GET["year"];
$hour = $_GET["hour"];
if($month<10)
	$month="0".$month;
	
if($day<10)
	$day="0".$day;

$newDate = $year."-".$month."-".$day;*/


//echo "Date: ".$newDate."<br>";
//echo "Hour: ".$hour."<br>";
$newHour=$hour;
/*if($hour<10)
	$hour="0".$hour;
$newHour=$hour.":00:00";
*/

//$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
//echo "Month: ".$monthName."<br>"; 

if($id{0}!='C')
{
	$queryMaxData = "SELECT MAX(data) FROM solarlog";
	$resMax = mysqli_query($con,$queryMaxData) or die("Bad SQL");
	$rowMax = mysqli_fetch_array($resMax);
	$maxData = $rowMax[0];
	$queryMinData = "SELECT MIN(data) FROM solarlog";
	$resMin = mysqli_query($con,$queryMinData) or die("Bad SQL");
	$rowMin = mysqli_fetch_array($resMin);
	$minData = $rowMin[0];
	//echo $minData;
	
	if($newDate>$maxData)
	{
		$queryData= $maxData;
		//echo $queryData;
	}
	else if($newDate<$minData)
	{
		$queryData=$minData;
		//echo $queryData;
	}
	else
	{
		$queryData= $newDate;
		//echo $queryData;
		
	}

	$power=0;

	$ora=substr($newHour,0,2);
	$minuti=substr($newHour,3,2);
	$secondi=substr($newHour,6,2);
	$mese=substr($queryData,5,2);
	$giorno=substr($queryData,8,2);
	$anno=substr($queryData,0,4);
	$timeStamp= mktime($ora,$minuti,$secondi,$mese,$giorno,$anno);
	$timeStamp1 = $timeStamp-150;
	$timeStamp2 = $timeStamp+150;
	$time1= date("H:i:s",$timeStamp1);
	$time2 = date("H:i:s",$timeStamp2);
	$num=0;
	$lastDateAvailable=0;
	$nearestTime=0;
	while($num==0)
	{
		$query = "SELECT * FROM solarlog WHERE user='$id' and data='$queryData' and time between '$time1' and '$time2'";
		//echo $query;
		$res = mysqli_query($con,$query) or die("Bad SQL");
		$num=mysqli_num_rows($res);
		if($num!=0)
		{
			$row = mysqli_fetch_array($res);
//			echo "LastDateAvailable: ".$row[data];
//			echo "<br>NearestTime: ".$row[time];
//			echo "<br>PowerProduced: ".$row[power]."<br>";
			$lastDateAvailable=$row['data'];
			$nearestTime=$row['time'];
			$power=$row['power'];
		}
		else
		{
			$timeStamp1 = $timeStamp1-300;
			$timeStamp2 = $timeStamp-300;
			$time1= date("H:i:s",$timeStamp1);
			$time2 = date("H:i:s",$timeStamp2);
		}
	}
}
else
{
if($monthNum<10)
	$monthNum=substr($monthNum,1,1);
$year=2013;
$query="SELECT total FROM produced_energy WHERE user_id='$id' and month=$monthNum AND year=$year	";
//echo "<br>".$query;
$res = mysqli_query($con,$query) or die("Bad SQL");

$row = mysqli_fetch_array($res);
$total = $row[0];
//echo "Total Produced: ".$total."<br>";
$monthNum=1;
//echo jdmonthname($monthNum, CAL_MONTH_GREGORIAN_LONG);

$newHour=substr($newHour,0,2).":00:00";

$query="SELECT consumption FROM hour_consumption WHERE hour='$newHour' and month=$monthNum";
//echo ".<br>".$query;
$res = mysqli_query($con,$query) or die("Bad SQL");

$row = mysqli_fetch_array($res);
$consumption = $row[0];
//echo "Consumption: ".$consumption."<br>";


$dayNum=substr($newDate,8,2);



$query="SELECT total_irradiation FROM irradiation WHERE day=$dayNum and month=$monthNum AND year=$year;";
//echo "<br>".$query;
$res = mysqli_query($con,$query) or die("Bad SQL");

$row = mysqli_fetch_array($res);
$total_irradiation = $row[0];
//echo "Total Irradiation: ".$total_irradiation."<br>";
$monthNum=1;
//echo jdmonthname($monthNum, CAL_MONTH_GREGORIAN_LONG);

$value=$total*$consumption*$total_irradiation;
$lastDateAvailable=$newDate;
$nearestTime=$newHour;
$power=$value;


/*echo "LastDateAvailable: ".$newDate;
echo "<br>NearestTime: ".$newHour;
echo "<br>PowerProduced =".$value;*/
}
$jsonString="{\"id\":\"".$id."\",\"date\":\"".$newDate."\",\"hour\":\"".$newHour."\",\"lastDateAvailable\":\"".$lastDateAvailable."\",\"nearestTime\":\"".$nearestTime."\",\"powerOut\":".$power.", \"type\":\"".$typeName."\"}";
echo $jsonString;
?>




