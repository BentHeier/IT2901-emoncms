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
$date1 = $_GET["date1"];

$newDate1 = substr($date1,6,4)."-".substr($date1,3,2)."-".substr($date1,0,2);
$hour1 = substr($date1,11,8);
$monthNum1 = substr($date1,3,2);
$year1= substr($newDate1,0,4);

$newHour1=$hour1;


$date2 = $_GET["date2"];
$newDate2 = substr($date2,6,4)."-".substr($date2,3,2)."-".substr($date2,0,2);
$hour2 = substr($date2,11,8);
$monthNum2 = substr($date2,3,2);
$year2= substr($newDate2,0,4);
$newHour2=$hour2;

if($id{0}!='C')
{
	$queryMaxData = "SELECT MAX(data) FROM solarlogmod where user = ".$id;
	$resMax = mysqli_query($con,$queryMaxData) or die("Bad SQL");
	$rowMax = mysqli_fetch_array($resMax);
	$maxData = $rowMax[0];
	$queryMinData = "SELECT MIN(data) FROM solarlogmod where user =".$id;
	$resMin = mysqli_query($con,$queryMinData) or die("Bad SQL");
	$rowMin = mysqli_fetch_array($resMin);
	$minData = $rowMin[0];
	//echo $minData;
	$queryData1= $newDate1;
	$queryData2= $newDate2;
	/*echo "QueryData1 ".$queryData1."<br>";
	echo "QueryData2 ".$queryData2."<br>";
	echo "MaxData1 ".$maxData."<br>";
	echo "Min1 ".$minData."<br>";*/
	if($newDate1>$maxData || $newDate1<$minData)
	{
		$queryData1= $minData;
		//echo $queryData;
	}
	if($newDate2<$minData||$newDate2>$maxData)
	{
		$queryData2=$maxData;
		//echo $queryData;
	}
	

	$power=0;

	$ora1=substr($newHour1,0,2);
	$minuti1=substr($newHour1,3,2);
	$secondi1=substr($newHour1,6,2);
	$mese1=substr($queryData1,5,2);
	$giorno1=substr($queryData1,8,2);
	$anno1=substr($queryData1,0,4);
	$timeStamp= mktime($ora1,$minuti1,$secondi1,$mese1,$giorno1,$anno1);
	$timeStamp01 = $timeStamp-150;
	$timeStamp02 = $timeStamp+150;
	$time11= date("H:i:s",$timeStamp01);
	$time12 = date("H:i:s",$timeStamp02);
	
	$ora2=substr($newHour2,0,2);
	$minuti2=substr($newHour2,3,2);
	$secondi2=substr($newHour2,6,2);
	$mese2=substr($queryData2,5,2);
	$giorno2=substr($queryData2,8,2);
	$anno2=substr($queryData2,0,4);
	$timeStamp2= mktime($ora2,$minuti2,$secondi2,$mese2,$giorno2,$anno2);
	$timeStamp21 = $timeStamp2-150;
	$timeStamp22 = $timeStamp2+150;
	$time21= date("H:i:s",$timeStamp21);
	$time22 = date("H:i:s",$timeStamp22);
	
	
	
	$num=0;
	$lastDateAvailable1=0;
	$nearestTime1=0;
	$jsonString="";
	echo "{\"results\":[ ";
	while($num==0)
	{
		//$query = "SELECT * FROM solarlogmod WHERE user='$id' and data='$queryData1' and time between '$time11' and '$time12'";
		//echo $query;
		$query = "SELECT * FROM solarlogmod WHERE user =\"".$id."\" and data between \"".$queryData1."\" and \"".$queryData2."\"";
		//echo $query;
		$res = mysqli_query($con,$query) or die("Bad SQL");
		$num=mysqli_num_rows($res);
		//echo "NUM = ".$num."<br>";
		
		if($num!=0)
		{
			while($row = mysqli_fetch_array($res))
			{
				$lastDateAvailable=$row['data'];
				$nearestTime=$row['time'];
				$dbTimeStamp = mktime(substr($nearestTime,0,2),substr($nearestTime,3,2),substr($nearestTime,6,2),substr($lastDateAvailable,5,2),substr($lastDateAvailable,8,2),substr($lastDateAvailable,0,4));
				if(($lastDateAvailable==$queryData1&&$dbTimeStamp<$timeStamp)||($lastDateAvailable==$queryData2&&$dbTimeStamp>$timeStamp2))
					continue;
				$power=$row['power'];
				$jsonString=$jsonString."{\"lastDateAvailable\":\"".$lastDateAvailable."\",\"nearestTime\":\"".$nearestTime."\",\"powerOut\":".$power."}";
				$jsonString=$jsonString.",";
		
				
			}
			//echo $index;
		}
		else
		{
			$timeStamp01 = $timeStamp01-300;
			$timeStamp02 = $timeStamp-300;
			$time11= date("H:i:s",$timeStamp01);
			$time12 = date("H:i:s",$timeStamp02);
			
			/*$timeStamp01 = $timeStamp01-300;
			$timeStamp02 = $timeStamp-300;
			$time11= date("H:i:s",$timeStamp01);
			$time12 = date("H:i:s",$timeStamp02);*/
		}
	}
	$jsonString=substr($jsonString,0,strlen($jsonString)-1);
	echo $jsonString."]}";
}
else
{
if($monthNum1<10)
	$monthNum1=substr($monthNum1,1,1);
if($monthNum2<10)
	$monthNum2=substr($monthNum2,1,1);
	
$year1=2013;
$year2=2013;

$queryTotal="SELECT total, month FROM produced_energy WHERE user_id='$id' and month between $monthNum1 AND $monthNum2";
$resTotal = mysqli_query($con,$queryTotal) or die("Bad SQL");

$dayNum1=substr($newDate1,8,2);
$dayNum2=substr($newDate2,8,2);

$queryIrradiation="SELECT total_irradiation, day, month FROM irradiation;";// WHERE day=$dayNum and month=$monthNum1 AND year=$year1;";
$resIrradiation = mysqli_query($con,$queryIrradiation) or die("Bad SQL");

$newHour1=substr($newHour1,0,2).":00:00";
$newHour2=substr($newHour2,0,2).":00:00";

$queryConsumption="SELECT consumption, hour, month FROM hour_consumption;";// WHERE hour='$newHour1' and month=$monthNum1";
$resConsumption = mysqli_query($con,$queryConsumption) or die("Bad SQL");

echo "{\"results\":[ ";
$jsonString="";
while($totals=mysqli_fetch_array($resTotal))
{
	//echo"Sono qui1<br>";
	$total=$totals[0];
	//echo $total."<br>";
	mysqli_data_seek($resIrradiation,0);
	while($irradiations=mysqli_fetch_array($resIrradiation))
	{
		
		
		if($irradiations['month']==$totals['month'] && ($irradiations['day']>=$dayNum1 &&$irradiations['day']<=$dayNum2))
		{
			//echo $irradiations['month']." ".$totals['month']."<br>";
			//echo"Sono qui2<br>";
			$total_irradiation=$irradiations[0];
			//echo$total_irradiation."<br>";
			//echo "<br>".$irradiations['day']."<br>";
			mysqli_data_seek($resConsumption,0);
			while($consumptions=mysqli_fetch_array($resConsumption))
			{
				$consumption = $consumptions[0];
				//echo $consumption."<br>";
				//echo $consumptions['hour']." ".$newHour1." ".$newHour2."<br>";
				if($consumptions['month']==$totals['month'])//&&($consumptions['hour']>=$newHour1 &&$consumptions['hour']<=$newHour2))
				{
					//echo"Sono qui3<br>";
					if(($irradiations['day']==$dayNum1 && $consumptions['hour']<=$newHour1)||($irradiations['day']==$dayNum2 && $consumptions['hour']>=$newHour2))
					continue;
					$value=$total*$consumption*$total_irradiation;
					$lastDateAvailable=$newDate1;
					$nearestTime=$newHour1;
					$power=$value;
					$jsonString=$jsonString."{\"lastDateAvailable\":\"".$lastDateAvailable."\",\"nearestTime\":\"".$nearestTime."\",\"powerOut\":".$power."}";
					echo $jsonString.",";
				}
			}
		}
	}
}
	$jsonString=substr($jsonString,0,strlen($jsonString)-1);
	echo $jsonString."]}";






/*$value=$total*$consumption*$total_irradiation;
$lastDateAvailable=$newDate1;
$nearestTime=$newHour1;
$power=$value;
$jsonString="{\"id\":\"".$id."\",\"date\":\"".$newDate1."\",\"hour\":\"".$newHour1."\",\"lastDateAvailable\":\"".$lastDateAvailable."\",\"nearestTime\":\"".$nearestTime."\",\"powerOut\":".$power.", \"type\":\"".$typeName."\"}";
echo $jsonString;
*/
}

?>

