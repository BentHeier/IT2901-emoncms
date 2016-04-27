<?php
include 'connectDB.php';

include_once "device_model.php";
  $device = new Device($con);

if(isset($_GET['json']))
  {
	  $jsonString =  $_GET['json']; 
	  error_log($jsonString);
      //$result = "Sto stampando jsonString: ".$jsonString;  
	   // $jsonString = preg_replace('/[^\w.:\{\},\ ]/','"',$jsonString);
	    $jsonString =  strtr ($jsonString, array ("'" => '"'));
	    $jsonString = json_decode($jsonString);	 

    $cmd=$jsonString->cmd;
     if($cmd == 'types' )
		$result = $device->types();
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
			  $result = $device->addDevice($jsonString->template,$jsonString->name,$jsonString->user);
			 
	elseif($cmd == 'list')   
		   {
			   $result = $device->listDevice($jsonString->user);
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
			     $d=shell_exec("date \"+%Y%m%d %H:%M:%S\"");
		
			   $result = $device->read($jsonString->user,$jsonString->device,$d);
		   }
	elseif($cmd == 'getload')
			  $result = $device->getload($jsonString->template, $jsonString->unit);
	elseif($cmd == 'getprofile')
			  $result = $device->getprofile($jsonString->deviceid, $jsonString->mode);
	elseif($cmd == 'getprediction')
			 $result = $device->getprediction($jsonString->template, $jsonString->unit);
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
	elseif($cmd == 'samples')
			 $result = $device->samples($jsonString->device,$jsonString->date,$jsonString->template);
     elseif($cmd == 'set')
			 $result = $device->set($jsonString->device,$jsonString->parameters);
	 elseif($cmd == 'get')
			 $result = $device->get($jsonString->device);

  else $result = "{'error': 'unsupported command'}";
  
  if(isset($_GET['standard']))
     $result =  strtr ($result, array ("'" => '"'));
     
  echo $result;   
  
}
?>
