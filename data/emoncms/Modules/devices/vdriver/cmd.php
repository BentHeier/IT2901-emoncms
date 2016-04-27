<?php
 
 
	$c= ":";
	$inthat =$cmd['address'];
	$status =$cmd['status'];
	  
	
	// before -
	$drivertype =  substr($inthat, 0, strpos($inthat, $c));
	
	// after -
	if (!is_bool(strpos($inthat, $c)))
    {
	    $driverinstance =  substr($inthat, strpos($inthat,$c)+strlen($c));
	
	

		$paramsids = $this->redis->sMembers("user:drivers:params:$driverinstance");
    
		$parameters = Array();
		foreach ($paramsids as $id)
         {
		  
			$p = $this->redis->hGet("driver:params:$id",'name');
            $p=trim($p);
           $parameters[$p]=$this->redis->hGet("driver:params:$id",'value');
         }
   
  
		  $deviceid=$parameters['device'];
		 
		  
		  
		  //inverte lo stato
		  $now= shell_exec('date "+%Y-%m-%d %H:%M:%S"');
		  
		  $now=substr($now, 0, -1);
		  $now= strtr ($now, array (" " => '%20'));
		  if($status==1)
		    $parname="startedon";
		  else
		    $parname="stoppedon";
		  
		  $url = 'http://localhost/virtualDevices/device.php?json={"cmd":"set","device":"'.$deviceid.'","parameters":[{"name":"status","value":'.$status.'},{"name":"'.$parname.'","value":"'.$now.'"}]}';
		  error_log("url:".$url);
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  //curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
		  $output = curl_exec($ch);
		  $info = curl_getinfo($ch);
		  curl_close($ch);
		}
?>
