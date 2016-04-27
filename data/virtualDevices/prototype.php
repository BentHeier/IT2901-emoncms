<?php
include 'connectDB.php';

include_once "device_model.php";
  $device = new Device($con);

if(isset($_GET['json']))
  {
	  $jsonString =  $_GET['json']; 
      //$result = "Sto stampando jsonString: ".$jsonString;  
	    $jsonString = preg_replace('/[^\w.:\{\},\ ]/','"',$jsonString);
	    
	    $jsonString = json_decode($jsonString);	 
	
    $cmd=$jsonString->cmd;
     if($cmd == 'types' )
		$result = $device->typesm();
     elseif($cmd == 'templates')
      {  
		  if(isset($jsonString->type))
		     { $type=$jsonString->type;
				 $result = $device->template($type);
			 }
		  else	 
		    $result = $device->templates();
		  
      }elseif($cmd == 'templateinfo')
      {
		  if(isset($jsonString->id))
		  {
			  $template_id=$jsonString->id;
			  $result = $device->template_info($template_id);
			  }
		  }
       elseif($cmd == 'add')
	{
	  $result = $device->addDevice($jsonString->template,$jsonString->name,$jsonString->user);
	  $jsonObj = json_decode($result);
	  /*$result = "{\"id\": ".$jsonObj->id.", \"addr\": \"FFAAFFAAFF\", \"type\":";
	  $query1="SELECT  behaviour, device_type.type, device_list.name, device_list.template from device_type, templates, device_list where templates.type=device_type.id and templates.id=device_list.template and  device_list.device_id=".$jsonObj->id." and device_list.user='".$jsonString->user."';";

          $res = mysqli_query($con,$query1) or die("Bad SQL");
	  $row = mysqli_fetch_array($res);
	  $result=$result."\"".$row[1]."\", \"name\": \"".$row[2]."\", \"mode\": [";
	  $query1="select name, template_alt from modes where template=".$row[3];
   	  $res = mysqli_query($con,$query1) or die("Bad SQL");
	  $first=true;
	  while($row = mysqli_fetch_array($res))	{

			if(!$first) $result=$result.", ";

			 $result=$result." { \"name\": \"".$row[0]."\", \"id\": \"".$row[1]."\"}";
			 $first=false;

			}
	  
	$result = $result."]}"; 	*/
	$result = $device->deviceInfo($jsonObj->id, $jsonString->user);
	}			 
	elseif($cmd == 'list')   
		   {
			   $result = $device->listDevice($jsonString->user);
			   $jsonObject = json_decode($result);
			   $list=$jsonObject->devicelist;
			   $result = "{\"devicelist\": [";
			   $first = true;
			   foreach($list as $item)
				{
				  if(!$first) $result = $result.", ";
				  else $first=false;
				  $result=$result."".$device->deviceInfo($item->id, $jsonString->user);
				  
				}
			   $result = $result."]}";
			    
		   }
    elseif($cmd == 'remove')   
		   {
			   $result = $device->removeDevice($jsonString->user, $jsonString->device);
		   }
	elseif($cmd == 'removeall')   
		   {
			   $result = $device->removeAllDevice($jsonString->user);
		   }
    elseif($cmd == 'read')   
		   {
			   if(isset($jsonString->date))
			     $d=$jsonString->date;
			    else 
			     $d=null;
			   $result = $device->read($jsonString->user,$jsonString->device,$d);
		   }
	elseif($cmd == 'getload')
			  $result = $device->getload($jsonString->template, $jsonString->unit);
	elseif($cmd == 'readinterval')
	{
		$result = $device->readInterval($jsonString->user,$jsonString->device,$jsonString->date1,$jsonString->date2);
	}
	elseif($cmd == 'info')
        {
                $result = $device->deviceBehaviour($jsonString->device,$jsonString->user);
        }
	elseif($cmd == 'deviceinfo')
        {
                $result = $device->deviceInfo($jsonString->device,$jsonString->user);
        }



  else $result = "{'error': 'unsupported command'}";
  
  if(isset($_GET['standard']))
     $result =  strtr ($result, array ("'" => '"'));
     
  echo $result;   
  
}
?>
