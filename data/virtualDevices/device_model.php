<?php
class Device
{
    private $mysql;

    public function __construct($con)
    {
      $this->mysql=$con;
    }
    
    public function  typesold()
    {
		$query="SELECT id,type, behaviour from device_type";
	    $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		 
        $result="{ \"types\":[";
 	$first=true;      
        while($row = mysqli_fetch_array($res)){
	     if(!$first)  $result=$result.", ";	
             $result=$result." { \"id\": ".$row[0].", \"name\": \"".$row[1]."\", \"type\": $row[2]}";
	$first=false;	
		}
        $result=$result."]}";
        return $result;
	}
	
	
    public function getprofile($deviceid, $mode){
		
		$query="SELECT template_alt from device_list, modes where device_id=".$deviceid." and device_list.template=modes.template and modes.name ='".$mode."'";
		//echo $query;
	
		$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		$obj=mysqli_fetch_object($res);
		if($obj){
			$templateid= $obj->template_alt;
			return $this->getload($templateid, 'e');
			}
		else 
			return "{\"response\": \"getProfile\", \"result\": \"error\", \"msg\": \"no matching profile\"}] }";
		
		
	}
	
	
	
	public function getload($idtemplate, $unit){
		$query="SELECT samples.number, samples.value from samples where template=$idtemplate";
		//echo $query;
		$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		$result="";
		$energy = 0;
		$x = 0;
		$y = 0;
		while($row = mysqli_fetch_array($res))	
		if ($unit=='e')
		{
		
		$energy = $energy + ((($row[1] + $y)/2 * ($row[0] - $x))/60);
		$x = $row[0];
		$temp = $x*60;
		$y = $row[1];
		$result=$result."".$temp." ".$energy."\n";
		}
		else
			{$result=$result."".$row[0].",".$row[1]."\n";}
		return $result;
	
	}




 public function getprediction($idtemplate, $unit){
		$query="SELECT type, datatype, name FROM templates WHERE id=\"".$idtemplate."\"";
		$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		$row = mysqli_fetch_array($res);
		$typeId = $row[0];
		$dataType = $row[1];
		$name = $row[2];
		$today=getdate();
	 	$date="2013-".$today[mon]."-".$today[mday];

		$query="SELECT time, power from solarlogmod where user='".$name."' and data='".$date."' order by time" ;
                //echo $query;
                $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
                $result="";
                $energy = 0;
                $x = 0;
                $y = 0;
                while($row = mysqli_fetch_array($res))

		{
		        $elements = explode(':',$row[0]);
                        $secs = 3600*$elements[0]+60*$elements[1]+$elements[2];
	
                if ($unit=='e')
                {
		
                $energy = $energy + (($row[1] + $y)/2 * (($secs - $x)/3600));
                $x = $secs;
                $y = $row[1];
                $result=$result."".$secs." ".$energy."\n";
                }
                else
			{

			    $elements = explode(':',$row[0]);
			    $secs = 3600*$elements[0]+60*$elements[1]+$elements[2];

				$result=$result."".$secs.",".$row[1]."\n";}
			}
                return $result;

        }
	
		
	public function  templates()
    {	
		$query="select templates.id, device_type.type,templates.name from templates, device_type where device_type.id=templates.type;";
		$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
        $result="{ \"templates\":[";
              $first=true;
		while($row = mysqli_fetch_array($res))	
                   {
		if(!$first) $result=$result.", ";	
			$result=$result." { \"id\": ".$row[0].", \"type\": \"".$row[1]."\", \"name\": \"".$row[2]."\"}";
			$first=false;	
			}
	return $result."]}";
     }
     
     public function  template($type)
    {	
		$query="select templates.id, device_type.type,templates.name from templates, device_type where device_type.id=templates.type and device_type.id=".$type.";";
		//echo $query;
		$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
        	$result="{ \"templates\":[";
	 	$first=true;	
		while($row = mysqli_fetch_array($res))	{
			if(!$first) $result=$result.", ";
			 $result=$result." { \"id\": ".$row[0].", \"type\": \"".$row[1]."\", \"name\": \"".$row[2]."\"}";
			$first=false;
			}
		return $result."]}";
     }
     
     
      public function  template_info($id)
	  {
		  $query="select parameter, value from template_info where template_id=".$id.";";
		//echo $query;
		$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
        $result="{ \"templateinfo\":[";
	$first=true;
        while($row = mysqli_fetch_array($res))	{
			if(!$first) $result=$result.", ";
			 $result=$result." { \"parameter\": \"".$row[0]."\", \"value\": \"".$row[1]."\"}";
			 $first=false;
			}
			
			
		  $result=$result."], \"modes\": [";

          $query1="select name, template_alt from modes where template=".$id;
          $res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
          $first=true;
          while($row = mysqli_fetch_array($res))        {

                        if(!$first) $result=$result.", ";

                         $result=$result." { \"name\": \"".$row[0]."\", \"id\": \"".$row[1]."\"}";
                         $first=false;

                        }
			
		return $result."]}";
      }
		  
public function  addDevice($template,$name,$user)
	  {
		//INSERT DEVICE
        $query="INSERT INTO device_list (name,template, user) values('".$name."',".$template.",'".$user."');";
       // echo $query;
        $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
        $id_device= $this->mysql->insert_id;
        //echo $id_device."...";

		//Gets type and parameters 
		$query1="SELECT  device_parameter.parameter from device_parameter, templates where templates.id=".$template." and device_parameter.device_type=templates.type;";
		//echo $query1;
		$res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
		
		//INITIALIZE PARAMETERS
        while($row = mysqli_fetch_array($res))	
			{
				$parameters="INSERT INTO device_parameter_value (id_device, parameter,value) VALUES(".$id_device.",'".$row[0]."','');";
				mysqli_query($this->mysql,$parameters) or die("Bad SQL");
		     }
		 	 
		return "{\"id\": \"".$id_device."\"}";
	  }
	  
	  
public function	  listDevice( $user)
	  {
		   $query="select device_list.device_id, device_list.name, device_list.template, device_type.type, device_type.behaviour, classes.name from device_type,  templates, device_list, classes  where device_list.user='".$user."' and device_list.template=templates.id and templates.type=device_type.id and superclassid=classes.id;";
           //echo $query1;
		   $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		
		   $result="{ \"devicelist\":[";
		   $first=true;      
		   while($row = mysqli_fetch_array($res))	{
				   if(!$first)  $result=$result.", ";	
						$result=$result." { \"id\": ".$row[0].", \"name\": \"".$row[1]."\", \"template\": \"".$row[2]."\", \"type\":".$row[4].", \"class\": \"".$row[5]."\"}";
					$first=false;
		}
		   return $result."]}";
	  }
	  
	  
	 public function removeDevice( $user, $device)
	  {
		   $query="delete  from device_list where user='".$user."' and device_id=".$device.";";
           //echo $query1;
		   $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		   $query="delete  from device_parameter_value where id_device=".$device.";";
           //echo $query1;
		   $result = mysqli_query($this->mysql,$query) or die("Bad SQL");
		  
		   return "{\"removed\": true}";
	  }
	  
	  
	  public function removeAllDevice( $user)
	  {
		   $query="select device_id from device_list where user='".$user."';";
		   $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		    while($row = mysqli_fetch_array($res))	
			{
			  $query="delete  from device_parameter_value where id_device=".$row[0].";";
              //echo $query1;
		      $result = mysqli_query($this->mysql,$query) or die("Bad SQL");
			}
		   $query="delete  from device_list where user='".$user."';";
           
		   $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		  
		   
		   return "{\"removed\": true}";
	  } 
	  
	  
	  
	  public function samples($device, $date, $template)
      {
		  
		  $query1="SELECT  parameter,value from device_parameter_value where id_device=".$device.";";
		  
		  $res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
		  $parameters =array();
		   while($row = mysqli_fetch_array($res))	
			{
				$parameters[$row[0]]=$row[1];
		     }
		  $started=$parameters['startedon'];
		  if($started=="") return '{"powerin": 0}';
		  else{
		   if($date==null) 
		     $sample=shell_exec("date +%s")-shell_exec("date -d '".$started."' +%s");
		   else
		      $sample=shell_exec("date -d '".$date."' +%s")-shell_exec("date -d '".$started."' +%s");
		    
		    
		  //echo $sample;    
		  //if($parameters['sampleperiod']=="60sec")
		     $sample=$sample/60;
		     $sample = round($sample);
		  //   echo $sample;
		  $query2="SELECT value from samples where template=".$template." and number=".$sample; 
		  $res = mysqli_query($this->mysql,$query2) or die("Bad SQL");
		  $row = mysqli_fetch_array($res);
		  if($row[0]==null)
			  return "{\"powerin\": \"0\"}";
		  else
		    return "{\"powerin\": \"".$row[0]."\"}";
	      }
		  
		  }
	  
	  	  
	  
	   
	  
	  
	    public function read($user, $device, $d)
	    {
		   
		   	$query="select device_list.template,templates.type, templates.datatype from device_list, templates where device_list.template=templates.id and 	user='".$user."' and device_id=".$device.";";  
			$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
			$row = mysqli_fetch_array($res);
			//echo $row['template'];
			$query="SELECT name FROM templates WHERE id=\"".$row['template']."\"";
			$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
			$row2 = mysqli_fetch_array($res);
			
		   error_log(strtolower($row[2]));
		 if(strtolower($row[2])!="samples")
		 {
		   switch($row[1]){
		   case 3:
		        return $this->produced($row2[0], $d, $row['template']);
				break;
			default;
				 return $this->consumed($row2[0], $d,$row['template']);
				 break;
		   }
		  }
		else
		{
			
			
		 $template =  $row['template'];
			
		 $query1="SELECT  parameter,value from device_parameter_value where id_device=".$device.";";
		  
		  $res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
		  $parameters =array();
		   while($row = mysqli_fetch_array($res))	
			{
				$parameters[$row[0]]=$row[1];
		     }
		     
		     
		  if (array_key_exists("startedon",$parameters) )
			$started=$parameters['startedon'];
		  else
		    $started="";
		    
		  error_log("started:".$started);
		  if($started=="") return '{"powerin": 0}';
		  else{
		   if($d==null) 
		     $sample=shell_exec("date +%s")-shell_exec("date -d '".$started."' +%s");
		   else
		      $sample=shell_exec("date -d '".$d."' +%s")-shell_exec("date -d '".$started."' +%s");
		    
		   
		  error_log("template:".$template.",sample:".$sample);    
		  //if($parameters['sampleperiod']=="60sec")
		     $sample=$sample/60;
		     $sample = round($sample);
		  //   echo $sample;
		  $query2="SELECT value from samples where template=".$template." and number=".$sample; 
		  
		  $res = mysqli_query($this->mysql,$query2) or die("Bad SQL");
		  $row = mysqli_fetch_array($res);
		  
		  
		  
		  if(!$row)
			  return "{\"powerin\": \"0\"}";
		  else
		    return "{\"powerin\": \"".$row[0]."\"}";
	      }
		  
			
		 }  		   
	  
	  }
	  
	  public function readInterval($user, $device, $date1,$date2)
	{
		
		
		$query="select device_list.template,templates.type, templates.datatype from device_list, templates where device_list.template=templates.id and 	user='".$user."' and device_id=".$device.";";  
		//echo $query;
			$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
			$row = mysqli_fetch_array($res);
		
			$query="SELECT name FROM templates WHERE id=\"".$row['template']."\"";
			//echo $query;
			$res = mysqli_query($this->mysql,$query) or die("Bad SQL");
			$row2 = mysqli_fetch_array($res);
		   //echo "L'id è".$row2[0];
		   //$date1 = substr($date1,6,2)."%2F".substr($date1,4,2)."%2F".substr($date1,0,4)."+".substr($date1,9,8);
		   //$date2 = substr($date2,6,2)."%2F".substr($date2,4,2)."%2F".substr($date2,0,4)."+".substr($date2,9,8);
		  switch($row[1]){
		   case 3:
				echo $this->consumedInterval($row2[0],$date1,$row['template'],$date2);
				break;
			default;
				echo $this->producedInterval($row2[0],$date1,$row['template'],$date2);
				break;
			}

	}
	  
	  
	  public function get($device)
	  {
		   $query="SELECT parameter,value from device_parameter_value  where id_device=".$device.";";
		   $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
		   $result ="[";
		   
		   while($row = $res->fetch_object())
		     $result=$result.'{"'.$row->parameter.'":"'.$row->value.'"},';
		   $result=substr($result,0,-1);
		   return $result."]";
		   
	  }
	  
	  public function set($device, $parameters)
	  {
		  for($i=0;$i<count($parameters);$i++)
		     {
				 
				 $query="SELECT count(*) as count from device_parameter_value  where parameter='".$parameters[$i]->name."' and id_device=".$device.";";
			     
			     $res = mysqli_query($this->mysql,$query) or die("Bad SQL");
				 $row = $res->fetch_object();
				 
				 
				 if($row->count==0)
				 
				   $query="INSERT INTO  device_parameter_value VALUES(NULL, $device,'".$parameters[$i]->name."','".$parameters[$i]->value."');";
			     else
					$query="UPDATE device_parameter_value set value='".$parameters[$i]->value."' where parameter='".$parameters[$i]->name."' and id_device=".$device.";";
			     
					mysqli_query($this->mysql,$query) or die("Bad SQL");
			     
			 }
			 return "ok";
		  
		  }
	public function deviceBehaviour($deviceid, $user)
	{
		$query1="SELECT  behaviour from device_type, templates, device_list where templates.type=device_type.id and templates.id=device_list.template and  device_list.device_id=".$deviceid." and device_list.user='".$user."';";
                  $res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
		  $row = mysqli_fetch_array($res);
		  return "{\"behaviour\": \"".$row[0]."\"}";

	}	  
	public function deviceInfo($id, $user)
	{
	  $result = "{\"id\": ".$id.", \"addr\": \"FFAAFFAAFF\", \"type\":";
          $query1="SELECT  behaviour, device_type.type, device_list.name, device_list.template from device_type, templates, device_list where templates.type=device_type.id and templates.id=device_list.template and  device_list.device_id=".$id." and device_list.user='".$user."';";
	  
		  
          $res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
          $row = mysqli_fetch_array($res);
          $result=$result."\"".$row[1]."\", \"name\": \"".$row[2]."\",";
	  $template=$row[3];
          $query1="select value from template_info where parameter=\"executionType\" and template_id=$template";
          $res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
          $row = mysqli_fetch_array($res);
	  
	  $result = $result." \"executionType\": \"".$row[0]."\", \"modes\": [";

          $query1="select name, template_alt from modes where template=".$template;
          $res = mysqli_query($this->mysql,$query1) or die("Bad SQL");
          $first=true;
          while($row = mysqli_fetch_array($res))        {

                        if(!$first) $result=$result.", ";

                         $result=$result." { \"name\": \"".$row[0]."\", \"id\": \"".$row[1]."\"}";
                         $first=false;

                        }

          $result = $result."]}";
	  return $result;
           }


 public function  types()
    {
                $query="SELECT device_type.id,type, behaviour, classes.name from device_type, classes where superclassid=classes.id";
            $res = mysqli_query($this->mysql,$query) or die("Bad SQL");

        $result="{ \"types\":[";
        $first=true;
        while($row = mysqli_fetch_array($res)){
             if(!$first)  $result=$result.", ";
             $result=$result." { \"id\": ".$row[0].", \"name\": \"".$row[1]."\", \"type\": $row[2], \"category\": \"$row[3]\"}";
        $first=false;
                }
        $result=$result."]}";
        return $result;
        }

  private function produced($id, $date, $templateId)
  {
	$con=$this->mysql;  
	$query="SELECT type,datatype FROM templates WHERE id=\"".$templateId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeId = $row[0];
	$dataType=$row[1];
	$query="SELECT type FROM device_type WHERE id=\"".$typeId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeName = $row[0];
//$newDate = substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2);
    
	$newDate = "2013"."-".substr($date,4,2)."-".substr($date,6,2);
	$hour = substr($date,9,8);
	
	$monthNum = substr($date,4,2);
	$year= substr($newDate,0,4);
	$newHour=$hour;


if($dataType!="hist")
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
	$anno = 2013;
	$timeStamp= mktime($ora,$minuti,$secondi,$mese,$giorno,$anno);
	$timeStamp1 = $timeStamp-150;
	$timeStamp2 = $timeStamp+150;
	$time1= date("H:i:s",$timeStamp1);
	//echo $time1;
	$time2 = date("H:i:s",$timeStamp2);
	//echo "<br>".$time2;
	$num=0;
	$lastDateAvailable=0;
	$nearestTime=0;
	/*while($num==0)
	{*/
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
			/*while($row = mysqli_fetch_array($res));
			 echo $row['power'];
			 * */
		}
		else
		{
			/*$timeStamp1 = $timeStamp1-300;
			$timeStamp2 = $timeStamp-300;
			$time1= date("H:i:s",$timeStamp1);
			$time2 = date("H:i:s",$timeStamp2);*/
			$power=0;
		}
	//}
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
	return $jsonString;
	  
	  }
				 
 private function consumed($id, $date, $templateId)
  {
	$con=$this->mysql;
	$query="SELECT type,datatype FROM templates WHERE id=\"".$templateId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeId = $row[0];
	$dataType=$row[1];
	$query="SELECT type FROM device_type WHERE id=\"".$typeId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeName = $row[0];
	$newDate = substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
	$time = substr($date,9,6)."00";
/*$minute = substr($time,4,1);
if($minute<3)
{
	$minute=0;
	$time=substr($date,11,4).$minute.":00";
}
else if ($minute>=3&&$minute<=7)
{
	$minute=5;
	$time=substr($date,11,4).$minute.":00";
}
else if ($minute>7)
{
	$minute=0;
	$dMinute = substr($time,3,1);
	$dMinute++;
	$time=substr($date,11,3).$dMinute.$minute.":00";
}*/
	$hour = substr($date,9,6)."00";
	$weekday = date('l', strtotime($newDate));
	$monthNum = substr($date,3,2);
	$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
	$query="SELECT zone FROM band WHERE day_of_week='$weekday' and '$hour' between start_time AND end_time;";
 	$res = mysqli_query($con,$query) or die("Bad SQL");

	$row = mysqli_fetch_array($res);
	$zone = $row[0];
	if($dataType!="hist")
	{
	//echo $row['name'];
	//echo $id;
	
	error_log("datatype:".$dataType);
	if($dataType=="NULL")
	{
		if($weekday=="Sunday"||$weekday=="Saturday")
			$date = "2014-07-13";
		else
			$date = "2014-07-11";
	}
	else if ($dataType=="periodic")
	{
		$date = "2014-07-11";
		$minute = substr($time,4,1);
		if($minute<=5)
		{
			$minute=0;
			$time=substr($time,0,4).$minute.":00";
		}
		else if ($minute>=6)
		{
			$minute=0;
			$dMinute = substr($time,3,1);
			$dhour=substr($time,0,2);
			$dMinute++;
			if($dMinute==6)
			{	$dMinute=0;
				$dhour=substr($time,0,2);
				$dhour++;
			}
			$time=$dhour.":".$dMinute.$minute.":00";
			
		}		
	}
	else if ($dataType=="virgin")
	{
		$date = "2014-07-11";
	}
	else if ($dataType=="zero")
	{
		$date = "2014-07-11";
	}
	$query="SELECT * FROM solarlogmod WHERE user=\"".$id."\" and data=\"".$date."\" and time = \"".$time."\"";
	#echo $query;
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$power=$row['power'];
	//echo $power;
	//$zone="Undefined";
	if($power==null)
		$power=0;
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
	$jsonString = $jsonString."\"lastDateAvailable\":\"".$newDate."\",\"nearestTime\":\"".$time."\", \"zone\":";
	$jsonString = $jsonString." \"".$zone."\", \"powerin\": ".$power.", \"type\":\"".$typeName."\"}";
	
	return $jsonString;
}

private function producedInterval($id, $date1, $templateId, $date2){
	
	$con=$this->mysql;
	$query="SELECT type FROM templates WHERE id=\"".$templateId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeId = $row[0];
	$query="SELECT type FROM device_type WHERE id=\"".$typeId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeName = $row[0];

	$newDate1 = substr($date1,0,4)."-".substr($date1,4,2)."-".substr($date1,6,2);
	$hour1 = substr($date1,9,8);
	$monthNum1 = substr($date1,4,2);
	$year1= substr($newDate1,0,4);

	$newHour1=$hour1;


	$newDate2 = substr($date2,0,4)."-".substr($date2,5,2)."-".substr($date2,6,2);
	$hour2 = substr($date2,9,8);
	$monthNum2 = substr($date2,4,2);
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
	$jsonString="{\"results\":[ ";
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
	$jsonString=$jsonString."]}";
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

$jsonString="{\"results\":[ ";
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
					$jsonString=$jsonString.",";
				}
			}
		}
	 }
	}
	$jsonString=substr($jsonString,0,strlen($jsonString)-1);
	return $jsonString."]}";

	}			
 
}

private function consumedInterval($id, $date1, $templateId, $date2){
	
	$con=$this->mysql;
	$query="SELECT type FROM templates WHERE id=\"".$templateId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeId = $row[0];
	$query="SELECT type FROM device_type WHERE id=\"".$typeId."\"";
	$res = mysqli_query($con,$query) or die("Bad SQL");
	$row = mysqli_fetch_array($res);
	$typeName = $row[0];
	$newDate1 = substr($date1,0,4)."-".substr($date1,4,2)."-".substr($date1,6,2);
	$time1 = substr($date1,9,8);
	$hour1 = substr($date1,9,8);
	$day1 = substr($date1,6,2);
	$weekDay1 = date('l', strtotime($newDate1));
	$monthNum1 = substr($date1,4,2);
	$monthName1 = date("F", mktime(0, 0, 0, $monthNum1, 10));
	$year1= substr($newDate1,0,4);


	$newHour1=$hour1;

	$newDate2 = substr($date2,0,4)."-".substr($date2,4,2)."-".substr($date2,6,2);
	$hour2 = substr($date2,9,8);
	$monthNum2 = substr($date2,4,2);
	$weekDay2 = date('l', strtotime($newDate2));
	$monthName2 = date("F", mktime(0, 0, 0, $monthNum2, 10));
	$year2= substr($newDate2,0,4);
	$newHour2=$hour2;
	$day2 = substr($date2,6,2);

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
    //echo $ora2." ".$minuti2." ".$secondi2." ".$mese2." ".$giorno2." ".$anno2;
	
	$timeStamp2= mktime($ora2,$minuti2,$secondi2,$mese2,$giorno2,$anno2);
	$timeStampActual=$timeStamp1;

	$actualDate = date("Y-m-d H:i:s",$timeStampActual);
	//echo "ACTUAL DATE ".$actualDate."<br>";


	$jsonString="{\"results\":[";


 	
        



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
		$res = mysqli_query($con,$query) or die("Bad SQL");
		$row = mysqli_fetch_array($res);
		$power=$row['power'];
		if(!$power) $power=0;
		//echo "First = ".$first."<br>";
		//echo "La potenza è ".$power."<br>";
		if(!$first) $jsonString=$jsonString.", ";
		$jsonString = $jsonString."{";
		$jsonString = $jsonString."\"lastDateAvailable\":\"".$newDate1."\",\"nearestTime\":\"".$hour."\",";
		$jsonString = $jsonString." \"powerin\": ".$power."}";
		$timeStampActual+=300;
		
		$first=false;
		
		$actualDate = date("Y-m-d H:i:s",$timeStampActual);
		}
		$jsonString=$jsonString."]}";
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
			$jsonString = $jsonString."{";
			$jsonString = $jsonString."\"lastDateAvailable\":\"".$newDate."\",\"nearestTime\":\"".$hour."\",";
			$jsonString = $jsonString."\"powerin\": ".$power."}";
			
			$timeStampActual+=300;
			if($timeStampActual<=$timeStamp2)
				$jsonString=$jsonString.",";
			
			$actualDate = date("Y-m-d H:i:s",$timeStampActual);
			$weekday = date('l', strtotime($actualDate));
			$time = substr($actualDate,11,8);
			$hour = substr($actualDate,11,8);
			$monthNum = substr($actualDate,5,2);
			$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
			$newDate = substr($actualDate,0,10);
		}
		$jsonString=$jsonString."]}";
		
	}
		return $jsonString;
	}

}
