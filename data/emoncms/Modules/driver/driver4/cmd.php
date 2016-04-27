<?php
 
 
	$c= "-";
	$inthat =$cmd['address'];
	
	// before -
	$wsncontroller =  substr($inthat, 0, strpos($inthat, $c));
	
	// after -
	if (!is_bool(strpos($inthat, $c)))
      $wsnaddress =  substr($inthat, strpos($inthat,$c)+strlen($c));
	
	

    $paramsids = $this->redis->sMembers("user:drivers:params:$wsncontroller");
    
    $parameters = Array();
    foreach ($paramsids as $id)
        {
		  
          $p = $this->redis->hGet("driver:params:$id",'name');
          $p=trim($p);
          $parameters[$p]=$this->redis->hGet("driver:params:$id",'value');
         }
   
  
    $url=$parameters["url"]."?json={'address':".$wsnaddress.",'cmd':'switch', 'parameters':{'status':".$cmd['status']."}}";
    //echo "$url";
    
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  //curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
	$output = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
 
?>
