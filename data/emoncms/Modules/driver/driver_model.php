<?php global $path, $session, $user; 

/*
 All Emoncms code is released under the GNU Affero General Public License.
 See COPYRIGHT.txt and LICENSE.txt.

 ---------------------------------------------------------------------
 Emoncms - open source energy visualisation
 Part of the OpenEnergyMonitor project:
 http://openenergymonitor.org
 */

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');


function getArray($string) {
			$json = preg_replace('/[^\w\s-.:,]/','', $string);
			$data = explode(',', $json);
			$return = Array();
			
			foreach($data as $d) $return[] = trim($d);
			
			return array_unique($return);
	  }

class Driver
{
    private $mysqli;
    private $input;
    private $redis;
    private $feed;
    private $process;
    private $devices;
  
    
    
    public function __construct($mysqli,$redis,$input, $feed, $process, $devices)
    {
        $this->mysqli = $mysqli;
        $this->input = $input;
        $this->redis = $redis;
        $this->feed = $feed;
        $this->process = $process;
        $this->devices = $devices;
     
    }
    
     public function updateVHMeter($userid)
     {
		  $nodeid = $this->devices->get_nodeid($userid, "vdriver", "vhmeter");
		  $consumption_id=Null;
		  $pv_id=Null;
		  //echo var_dump($nodeid);
		  if(!$nodeid["success"]) 
			  return "false";
		    
		  $dbinputs = $this->input->get_inputs($userid);
		  $pv_process_list = Null;
		  $grid_process_list = Null;
		  
		  foreach ($dbinputs as $node)
		   {
			  
			  //echo var_dump($node);
		      if(isset($node["powerIn"]))
		      {
				 if($grid_process_list==Null) 
					$grid_process_list="";
				 else
					$grid_process_list=$grid_process_list.",";
		         $grid_process_list = $grid_process_list."11:".$node["powerIn"]["id"];
		      }
		      if(isset($node["powerOut"]))
		      {
			error_log("found pwOut".$node["powerOut"]["id"]);		 
				 if($pv_process_list==Null) 
					$pv_process_list="";
				 else
					$pv_process_list=$pv_process_list.",";
				 $pv_process_list = $pv_process_list."11:".$node["powerOut"]["id"];
		      }
		      if(isset($node["consumption"]))
		       $consumption_id=$node["consumption"]['id'];
		      if(isset($node["pv"]))
			   $pv_id=$node["pv"]['id'];
		  }
				
		  
		   if($grid_process_list!=Null)
		   {
			$feed_kwh = $this->feed->get_id($userid, "consumption_kwh");
			$feed_power = $this->feed->get_id($userid, 'consumption_power');
			$feed_kwhd = $this->feed->get_id($userid, 'consumption_kwhd');
		   
			$grid_process_list = $grid_process_list.",1:".$feed_power.",4:".$feed_kwh.",5:".$feed_kwhd;
			}
			
		   if($pv_process_list!=Null)
		   {
		    $feed_kwh = $this->feed->get_id($userid, "pv_kwh");
		    $feed_power = $this->feed->get_id($userid, 'pv_power');
		    $feed_kwhd = $this->feed->get_id($userid, 'pv_kwhd');
		   
		    $pv_process_list = $pv_process_list.",1:".$feed_power.",4:".$feed_kwh.",5:".$feed_kwhd;
		    }
		    //echo $grid_process_list;
		    if(isset($grid_process_list))
		       $this->input->set_processlist($consumption_id, $grid_process_list);
		    if(isset($pv_process_list))
			  $this->input->set_processlist($pv_id, $pv_process_list);
			  
			 return "ok";
		 
		 }
    
     public function  createVHMeter($userid, $node)
     {
		 
				//$inputs = $this->input->get_inputs($userid);
				$feeds = $this->feed->get_user_feeds($userid);
			
				$dbinputs = $this->input->get_inputs($userid);
				
				
		 	    if (!isset($dbinputs[$node]["consumption"]))
		 	    {			    
					$inputid = $this->input->create_input($userid, $node, "consumption");
					$dbinputs[$node]['consumption'] = array('id'=>$inputid);
				}
                else			  
                    $inputid = $dbinputs[$node]["consumption"]["id"];
               
               
               //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","consumption_power",1,2,json_decode('{"interval":60}'));
                
                if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}
                
                        
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","consumption_kwh",1,2,json_decode('{"interval":10}'));
                 if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				 }
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","consumption_kwhd",2,2,json_decode('{"interval":86400}'));
                if($feedid["success"])
                {
                 $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                 $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}
				
				
				
		
		if (!isset($dbinputs[$node]["pv"]))
			{
				$inputid = $this->input->create_input($userid, $node, "pv");
                $dbinputs[$node]['pv'] = array('id'=>$inputid);
             }
        else
			$inputid = $dbinputs[$node]['pv']['id'];
                
                 //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","pv_power",1,2,json_decode('{"interval":60}'));
                if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}    
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","pv_kwh",1,2,json_decode('{"interval":10}'));
                if($feedid["success"])
                {
                	$this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}    
                 //Kwh to KWhd
                
               
				$feedid=$this->feed->create($userid,"","pv_kwhd",2,2,json_decode('{"interval":86400}'));
				if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}
				
			
			 
		
		  if(!isset($dbinputs[$node]["pv2grid"]))	  
		  {
			  
				$inputid = $this->input->create_input($userid, $node, "pv2grid");
                $dbinputs[$node]['pv2grid'] = array('id'=>$inputid);
			}
          else
				$inputid = $dbinputs[$node]['pv2grid']['id'];
               
                $pvfeed=$this->feed->get_id($userid,"pv_power");
                $cfeed=$this->feed->get_id($userid,"consumption_power");
                
                //Add this pv to pv2grid
                $this->input->add_process($this->process, $userid, $inputid,29,$pvfeed);
			    $this->input->add_process($this->process, $userid, $inputid,30,$cfeed);
			    $this->input->add_process($this->process, $userid, $inputid,24,"");
			    
			    
			      //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","pv2grid_power",1,2,json_decode('{"interval":60}'));
                
                if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
                  }      
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","pv2grid_kwh",1,2,json_decode('{"interval":10}'));
                 if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
                   }     
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","pv2grid_kwhd",2,2,json_decode('{"interval":86400}'));
                if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
			    }
			    
			  
			  
	     if (!isset($dbinputs[$node]["pv2household"]))
		  
		  {
			  
				$inputid = $this->input->create_input($userid, $node, "pv2household");
                $dbinputs[$node]['pv2household'] = array('id'=>$inputid);
          }
                $pvfeed=$this->feed->get_id($userid,"pv_power");
                $cfeed=$this->feed->get_id($userid,"consumption_power");
                
                //Add this pv to pv2grid
                $this->input->add_process($this->process, $userid, $inputid,36,$pvfeed);
			    $this->input->add_process($this->process, $userid, $inputid,36,$cfeed);
			    
			    
			      //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","pv2household_power",1,2,json_decode('{"interval":60}'));
                 if($feedid["success"])
                {
                $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
			   }     
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","pv2household_kwh",1,2,json_decode('{"interval":10}'));
                 if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}       
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","pv2household_kwhd",2,2,json_decode('{"interval":86400}'));
                if($feedid["success"])
                {
					$this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
					$this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
			    }
			    
			  
			 
	       if (!isset($dbinputs[$node]["grid2household"])) 
		  {  
			$inputid = $this->input->create_input($userid, $node, "grid2household");
            $dbinputs[$node]['grid2household'] = array('id'=>$inputid);
               }
          else
			$inputid = $dbinputs[$node]['grid2household']['id'];
			
			
                $pvfeed=$this->feed->get_id($userid,"pv_power");
                $cfeed=$this->feed->get_id($userid,"consumption_power");
               
               
                //Add this pv to pv2grid
                $this->input->add_process($this->process, $userid, $inputid,29,$cfeed);
			    $this->input->add_process($this->process, $userid, $inputid,30,$pvfeed);
			    $this->input->add_process($this->process, $userid, $inputid,24,"");
			    
			     //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","grid2household_power",1,2,json_decode('{"interval":60}'));
                if($feedid["success"])
                {
                $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}       
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","grid2household_kwh",1,2,json_decode('{"interval":10}'));
                if($feedid["success"])
                {
                $this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}      
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","grid2household_kwhd",2,2,json_decode('{"interval":86400}'));
                if($feedid["success"])
                {
                $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: '.$node.'"}');
				}
			    
			    
				//add a cron job to periodically update the smart meter
				$result = $this->mysqli->query("SELECT apikey_write FROM users WHERE id=".$userid);
			    if ($result->num_rows == 1) 
					{
						$row=$result->fetch_object();
						$apikey=$row->apikey_write;
						$cronjob='*/1 * * * * wget -qO- "http://localhost/emoncms/input/post.json?node='.$node.'&json={consumption:0,pv:0,grid2household:0,pv2grid:0,pv2household:-1}&apikey='.$apikey.'"';
						$input = shell_exec('crontab -l');
						file_put_contents('/tmp/crontab.txt',$input);
						file_put_contents('/tmp/crontab.txt', $cronjob.PHP_EOL,FILE_APPEND);
						exec('crontab /tmp/crontab.txt');
					}
					
			  
		 
		 }
    
    

    
    
    public function connectVMeter($post, $userid)
    {
		//$template = $_GET["template"];
		$template = $post["template"];
		$deviceName = $post["deviceName"];
		$result =Array();
		$result["success"]= false;
		
		if($template==""||$template==null)
				{
				 $result["message"] = "Missing template!";
				  return  $result;
			    }
		else
		{
				if($deviceName==""||$deviceName==null)
					{
						$result["message"] = "Missing template!";
						return  $result;
						}
				else
				{
					$template = str_replace("'", '"', $template);
					
					$jsonString = json_decode($template,true);
					$templateType = "".$jsonString["type"];
					$templateId = $jsonString["id"];
					$templateName = $jsonString["name"];
				
					if($templateType=="VH Meter")
						{
							
							$emontemplates=$this->devices->list_templates($userid);
							foreach ($emontemplates as $template)
								if(/*$template->productName===$templateName and*/ $template->productType===$templateType)
								{
								$emoncmsTemplateID=$template->id;	
								 break;
									}
							
							if(!isset($emoncmsTemplateID))
							{
								$output = $this->devices->add_template($userid, $templateName, $templateType, "none","look at virtual devices", getArray("{consumption,pv,pv2grid,grid2household}"), getArray("{ }"));
								$emoncmsTemplateID=$output["templateid"];							
								}
							
							$output = $this->devices->register_node($userid, "vdriver" , "vhmeter", getArray("{consumption,pv,pv2grid,grid2household}"));
						    //echo var_dump($output);
						    
						    if(!$output["success"])
						       {
								  $result ["success"]=false;
								  $result ["message"]=$output; 
								  return $result;
								   }
						    $nodeid=$output["nodeid"];
						    
						    
						    
							$output = $this->devices->add_device($userid, $deviceName, $emoncmsTemplateID, getArray("{$nodeid}"));
							
							
							
							$success = $output["success"];
							$result =Array();
							if($success){
								$this->createVHMeter($userid, $nodeid);
								$result ["success"]=true;
								$result ["message"]="virtual device added";
								$result ["driver"]="none";
								$result ["deviceid"]=$output["deviceid"];
							}
							else
							{
								$result ["success"]=false;
								$result ["message"]=$output;
								}
							return $result;
							
							
							}
					
					
					{
						$url = "http://localhost/virtualDevices/device.php?json={'cmd':'add','template':'".$templateId."','name':'".$deviceName."','user':'". $userid."'}";
					   // echo $url;
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						//curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
						$output = curl_exec($ch);
						$info = curl_getinfo($ch);
						curl_close($ch);
						
						$jsonString = json_decode($output);
						$deviceId=$jsonString->id;
						
						
					}
					//RECUPERO ADDRESS, EXECUTIONTYPE E MODE
					$url = "http://localhost/virtualDevices/device.php?json={'cmd':'deviceinfo','user':'". $userid."','device':'".$deviceId."'}";
					//echo "<br>".$url;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					//curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
					$output = curl_exec($ch);
					//echo "<br>".$output;
					$info = curl_getinfo($ch);
					curl_close($ch);
					error_log("output".$output);
					$jsonString = json_decode($output);
					$address = $jsonString->addr."-".$deviceId;
					$executionType = $jsonString->executionType;
					$modeVector = $jsonString->modes;
		
					$mode="";
					if($modeVector!="" and $modeVector!=null)
					{
						//echo "<br>".$address."<br>".$executionType."<br>";
						foreach($modeVector as $modeTemp)
						{
							$modeName=$modeTemp->name;
							$modeId = $modeTemp->id;
							//echo $modeName." ".$modeId."<br>";
							$mode = $mode."$modeId:$modeName,";
						}
						$mode = substr($mode,0,strlen($mode)-1);
					}
					
					
					//RECUPERARE I TYPE
					
					$url = "http://localhost/virtualDevices/device.php?json={'cmd':'read','user':'".$userid."','device':'".$deviceId."'}";
					#echo "<br>".$url;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					//curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
					$output = curl_exec($ch);
					//echo "<br>".$output;
					$info = curl_getinfo($ch);
					curl_close($ch);
					$jsonObj = json_decode($output,true);
					$numNodi =0;
					$nodeTypes ="";
					//echo var_dump($jsonObj);
					//echo array_key_exists("powerOut", $jsonObj);
					if($templateType=="Solar Panel")
					   $templateType="Virtual PV";
 					if(array_key_exists("powerin", $jsonObj))
						{
							//$templateType=$templateType."c";
							$nodeTypes=$nodeTypes."powerIn,controller,"; //SOSTITUIRE ENERGY
							$numNodi++;
						}
					if(array_key_exists("powerOut", $jsonObj))
						{
							//$templateType=$templateType."p";
							$nodeTypes=$nodeTypes."powerOut,"; //SOSTITUIRE ENERGY
							$numNodi++;
						}
					if(array_key_exists("temperature", $jsonObj))
						{
							$nodeTypes=$nodeTypes."temperature,";
							$numNodi++;
						}
						
					if(array_key_exists("controller", $jsonObj))
						{
							$nodeTypes=$nodeTypes."controller,";
							$numNodi++;
						}
					
					if($numNodi==0) {
						
						$result = Array();
						$result["result"]="failure";
						$result["message"]="device inputs not supported";
						return $result;
						}
					
					$nodeTypes = substr($nodeTypes,0,strlen($nodeTypes)-1);
					//echo "SONIO QUI ".$numNodi;
					
					//echo $nodeTypes;
					//AGGIUNGI EMONCMS TEMPLATE
					
					$emontemplates=$this->devices->list_templates($userid);
					foreach ($emontemplates as $template)
						if(/*$template->productName===$templateName and */$template->productType===$templateType)
								{
								$emoncmsTemplateID=$template->id;	
								$templateSuccess = 1;
								break;
									}
							
					if(!isset($emoncmsTemplateID))
							{
								$output = $this->devices->add_template( $userid, $templateName, $templateType, $executionType, "missing",getArray("{".$nodeTypes."}"), getArray("{".$mode."}"));
								$emoncmsTemplateID = $output["templateid"];	
								$templateSuccess = $output["success"];						
								}
				
					 
					//DA USARE PER ASSOCIARE NODO E TEMPLATE
					
					
					if($templateSuccess)
					{
						//CREARE NODE
						$output=$this->add($userid,1,"Virtual Driver");
						$emoncmsDriverId = $output["driverid"];
						
						//dico al driver che è gestito dal devicemodule
						 $this->mysqli->query("INSERT INTO  user_driver_par (driverid, name,value) VALUES ($emoncmsDriverId, 'managed','yes')");
						
						
						$output = $this->devices->register_node($userid, "vdriver","vdriver:".$emoncmsDriverId, getArray("{".$nodeTypes."}"));
						$nodeSuccess = $output["success"];
						if(!$nodeSuccess) 
							return $output;
						else if($nodeSuccess)
						{
						$nodeID = $output["nodeid"]; //DA USARE PER ASSOCIARE NODO E TEMPLATE
							
						$deviceID=0;
						$polling=1;
						$urlV="http://localhost/virtualDevices";
						$user=1; //PRENDERE ID UTENTE
						$nodeDriver = $nodeID;
						$virtualDeviceId = $deviceId;
						$this->set_parameters($emoncmsDriverId, '{"emondevice":'.$deviceID.',"devicename":"'.$deviceName.'","polling":3,"user":'.$userid.',"url":"'.$urlV.'","node":'.$nodeDriver.',"device":'.$virtualDeviceId.'}');							
						$this->updateVHMeter($userid, $nodeID, $templateType);
						$this->startstop($emoncmsDriverId,null);
						
						$output["templateID"]=$emoncmsTemplateID;
						
						return $output;
						}
					}
				}
			}
		
		}
    
    
   
    
     public function createDevice($post, $userid)
     {
		 
			//$template = $_GET["template"];
			$template = $post["template"];
			$deviceName = $post["deviceName"];
			if($template==""||$template==null)
				echo "Return to the previous page and select a template!";
			else
			{
				if($deviceName==""||$deviceName==null)
					echo "Return to the previous page and insert a device name";
				else
				{
					$deviceName = str_replace(" ", "_",$deviceName);
					$template = str_replace("'", '"', $template);
					
					$jsonString = json_decode($template,true);
					$templateType = "".$jsonString["type"];
					$templateId = $jsonString["id"];
					$templateName = $jsonString["name"];
				
					if($templateType=="VH Meter")
						{
							
						$emontemplates=$this->devices->list_templates($userid);
						
						foreach ($emontemplates as $template)				
							if(/*$template->productName===$templateName and*/ $template->productType===$templateType)
								{
								$emoncmsTemplateID=$template->id;	
								break;	
									}
							
							
							
						if(!isset($emoncmsTemplateID))
							{
								
							
								
								
								/***Aggiungo il template***/
								
								$output = $this->devices->add_template( $userid, $templateName, $templateType, "none","description missing", getArray("{consumption,pv,pv2grid,grid2household}"), getArray("{ }"));
								$emoncmsTemplateID = $output["templateid"];							
								}
							
							
							
							
							
							
							$output = $this->devices->register_node($userid, "vdriver" , "vhmeter", getArray("{consumption,pv,pv2grid,grid2household}"));
						    //echo var_dump($output);
						    
						    if(!$output["success"])
						       {
								  $result ["success"]=false;
								  $result ["message"]=$output; 
								  return $result;
								   }
						    $nodeid=$output["nodeid"];
							$output = $this->devices->add_device($userid, $deviceName, $emoncmsTemplateID, getArray("{$nodeid}"));
							
							
							
							$success = $output["success"];
							$result =Array();
							if($success){
								$this->createVHMeter($userid, $nodeid);
								$result ["success"]=true;
								$result ["message"]="virtual device added";
								$result ["driver"]="none";
								$result ["deviceid"]=$output["deviceid"];
							}
							else
							{
								$result ["success"]=false;
								$result ["message"]=$output;
								}
							return $result;
							
							
							}
					
					//CREO il virtual device
					{
						$url = "http://localhost/virtualDevices/device.php?json={'cmd':'add','template':'".$templateId."','name':'".$deviceName."','user':'". $userid."'}";
					
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						//curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
						$output = curl_exec($ch);
						$info = curl_getinfo($ch);
						curl_close($ch);
						
						$jsonString = json_decode($output);
						$deviceId=$jsonString->id;
						
						
					}
					//RECUPERO ADDRESS, EXECUTIONTYPE E MODE
					$url = "http://localhost/virtualDevices/device.php?json={'cmd':'deviceinfo','user':'". $userid."','device':'".$deviceId."'}";
					//echo "<br>".$url;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					//curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
					$output = curl_exec($ch);
					//echo "<br>".$output;
					$info = curl_getinfo($ch);
					curl_close($ch);
					
					$jsonString = json_decode($output);
					$address = $jsonString->addr."-".$deviceId;
					$executionType = $jsonString->executionType;
					$modeVector = $jsonString->modes;
					$mode="";
					
					if($modeVector!="" and $modeVector!=null)
					{
						//echo "<br>".$address."<br>".$executionType."<br>";
						foreach($modeVector as $modeTemp)
						{
							$modeName=$modeTemp->name;
							$modeId = $modeTemp->id;
							//echo $modeName." ".$modeId."<br>";
							$mode = $mode."$modeId:$modeName,";
						}
						$mode = substr($mode,0,strlen($mode)-1);
						
					}
					
					
					//RECUPERARE I TYPE
					
					$url = "http://localhost/virtualDevices/device.php?json={'cmd':'read','user':'".$userid."','device':'".$deviceId."'}";
					#echo "<br>".$url;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					//curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
					$output = curl_exec($ch);
					//echo "<br>".$output;
					$info = curl_getinfo($ch);
					curl_close($ch);
					$jsonObj = json_decode($output,true);
					$numNodi =0;
					$nodeTypes ="";
					//echo var_dump($jsonObj);
					//echo array_key_exists("powerOut", $jsonObj);
					if($templateType=="Solar Panel")
					   $templateType="Virtual PV";
 					if(array_key_exists("powerin", $jsonObj))
						{
							//$templateType=$templateType."c";
							$nodeTypes=$nodeTypes."powerIn, controller,"; //SOSTITUIRE ENERGY
							$numNodi++;
						}
					if(array_key_exists("powerOut", $jsonObj))
						{
							//$templateType=$templateType."p";
							$nodeTypes=$nodeTypes."powerOut,"; //SOSTITUIRE ENERGY
							$numNodi++;
						}
					if(array_key_exists("temperature", $jsonObj))
						{
							$nodeTypes=$nodeTypes."temperature,";
							$numNodi++;
						}
						
					if(array_key_exists("controller", $jsonObj))
						{
							$nodeTypes=$nodeTypes."controller,";
							$numNodi++;
						}
					
					if($numNodi==0) {
						
						$result = Array();
						$result["result"]="failure";
						$result["message"]="device inputs not supported";
						return $result;
						}
					
					
					$nodeTypes = substr($nodeTypes,0,strlen($nodeTypes)-1);
					//echo "SONIO QUI ".$numNodi;
					
					//echo $nodeTypes;
					//AGGIUNGI EMONCMS TEMPLATE
				
					
					
					
				
					$emontemplates=$this->devices->list_templates($userid);
					
					foreach ($emontemplates as $template)
						if(/*$template->productName===$templateName and */$template->productType===$templateType)
								{
								$emoncmsTemplateID=$template->id;	
								$templateSuccess = 1;
								break;
									}
							
						if(!isset($emoncmsTemplateID))
							{
								
								$output = $this->devices->add_template( $userid, $templateName, $templateType, $executionType, "look at virtual devices",getArray("{".$nodeTypes."}"), getArray("{".$mode."}"));
								$emoncmsTemplateID = $output["templateid"];
								//DA USARE PER ASSOCIARE NODO E TEMPLATE
								$templateSuccess = $output["success"];							
								}
				
					
					
					
					if($templateSuccess)
					{
						//CREARE NODE
						$output=$this->add($userid,1,"Virtual Driver");
						$emoncmsDriverId = $output["driverid"];
						
						//dico al driver che è gestito dal devicemodule
						 $this->mysqli->query("INSERT INTO  user_driver_par (driverid, name,value) VALUES ($emoncmsDriverId, 'managed','yes')");
						
						
						$output = $this->devices->register_node($userid, "vdriver" , "vdriver:".$emoncmsDriverId, getArray("{".$nodeTypes."}"));
						$nodeSuccess = $output["success"];
						if(!$nodeSuccess) 
							return $output;
						else if($nodeSuccess)
						{
							$nodeID = $output["nodeid"]; //DA USARE PER ASSOCIARE NODO E TEMPLATE
							$nodes="";
							for($i=0;$i<$numNodi;$i++)
							{
								$nodes = $nodes.$nodeID.",";
							}
							$nodes = substr($nodes,0,strlen($nodes)-1);
							// "OK";
							
							
							$output = $this->devices->add_device($userid, $deviceName, $emoncmsTemplateID, getArray("{".$nodes."}"));
							
							$success = $output["success"];
							if($success)
							{
								
								$deviceID=$output["deviceid"];
								$polling=1;
								$urlV="http://localhost/virtualDevices";
								$user=1; //PRENDERE ID UTENTE
								$nodeDriver = $nodeID;
								$virtualDeviceId = $deviceId;
								$output=$this->set_parameters($emoncmsDriverId, '{"emondevice":'.$deviceID.',"devicename":"'.$deviceName.'","polling":3,"user":'.$userid.',"url":"'.$urlV.'","node":'.$nodeDriver.',"device":'.$virtualDeviceId.'}');							
								
								
								 $this->updateVHMeter($userid, $nodeID, $templateType);
								
								
								if($output=="ok"){
								 $result =Array();
								 $result ["success"]=true;
								 $result ["message"]="virtual device added";
								 $result ["driver"]=$emoncmsDriverId;
								 $result ["deviceid"]=$deviceID;
								 return $result;
							  }
							}
						}
					}
				}
			}
			
		}			
		 
		 
    
/*
    // USES: redis input & user
    public function create_driver($userid, $nodeid, $name)
    {
        $userid = (int) $userid;
        $nodeid = (int) $nodeid;

        if ($nodeid<32)
        {

          $name = preg_replace('/[^\w\s-.]/','',$name);
          $this->mysqli->query("INSERT INTO input (userid,name,nodeid) VALUES ('$userid','$name','$nodeid')");
          
          $id = $this->mysqli->insert_id;
          
          $this->redis->sAdd("user:inputs:$userid", $id);
	        $this->redis->hMSet("input:$id",array('id'=>$id,'nodeid'=>$nodeid,'name'=>$name,'description'=>"", 'processList'=>"")); 
	        
	      }
	      
	      return $id;     
    }
    */
    
    

    
      public function startstop($driverid, $emondevice)
      {
		  
		$address="";
		if($emondevice!=null)
		  {
			  $temp = $this->get_driverid($emondevice);
			  
			  if($temp["success"])
				{	 $driverid=	$temp["driverid"];
			         //get address from emondevice
			         //$device_address= $this->devices->
				}
			  error_log("mooo".$driverid." ".$emondevice);
			}
		  
		$status=$this->get_status($driverid);
		$rows=$this->get_parameters($driverid);
		$parameters = array(); 
		/* 
	    $paramsids = $this->redis->sMembers("user:drivers:params:$driverid");
        foreach ($paramsids as $id)
        {
		  
          $p = $this->redis->hGet("driver:params:$id",'name');
          $parameters[$p]=$this->redis->hGet("driver:params:$id",'value');
          
				   
        
	     }
	     
		  */
		 foreach ($rows as $par)
          {
			  $p=$par['name'];
			  $parameters[$p]=$par['value'];
		  }
		  $parameters["deviceaddress"]=$address;
		  /*ob_start();
		  var_dump($parameters);
		  $contents = ob_get_contents();
		  ob_end_clean();
		  error_log("debug".$contents);*/
		  
		  
		  
		 $userid=$this->redis->hGet("driver:$driverid",'userid');
		
		  //error_log("SELECT apikey_write FROM users WHERE id=".$userid);
		  
		  $result = $this->mysqli->query("SELECT apikey_write FROM users WHERE id=".$userid);
		  error_log("SELECT apikey_write FROM users WHERE id=".$userid);
          if ($result->num_rows == 1) 
              {$row=$result->fetch_object();
				  $apikey=$row->apikey_write;
			  }
			  
	    $result=$this->mysqli->query("select type from  user_drivers where id=$driverid");
	     if ($result->num_rows == 1) 
             {$row=$result->fetch_object();
				
	            include "Modules/driver/driver$row->type/startstop.php";
			 }
		 else $result="error";
		 return $result;
	  }
    
    
    public function exists($id)
    {
      $id = (int) $id;
      $result = $this->mysqli->query("SELECT id FROM driver WHERE `id` = '$id'");
      if ($result->num_rows > 0) return true; else return false;
    }
    
    
/*
    // USES: redis input
    public function set_timevalue($id, $time, $value)
    {
        $id = (int) $id;
        $time = (int) $time;
        $value = (float) $value;

        // $time = date("Y-n-j H:i:s", $time);
        // $this->mysqli->query("UPDATE input SET time='$time', value = '$value' WHERE id = '$id'");
        
        $this->redis->hMset("input:lastvalue:$id", array('value' => $value, 'time' => $time));
    }

*/
    // used in conjunction with controller before calling another method
    public function belongs_to_user( $userid, $driverid)
    {
        $userid = (int) $userid;
        $driverid = (int) $driverid;

        $result = $this->mysqli->query("SELECT id FROM user_drivers WHERE userid = '$userid' AND id = '$driverid'");
        if ($result->fetch_array()) return true; else return false;
    }
/*
    // USES: redis input
    private function set_processlist($id, $processlist)
    {
      // CHECK REDIS
      $this->redis->hset("input:$id",'processList',$processlist);
      $this->mysqli->query("UPDATE input SET processList = '$processlist' WHERE id='$id'");
      
    }


    // USES: redis input
  */
  
  
   public function set_fields($id,$fields)
    {
		$id = intval($id);
        //error_log($fields);
        $fields = json_decode(stripslashes($fields));
        
		 $array = array();
        // Repeat this line changing the field name to add fields that can be updated:
        if (isset($fields->description)) $array[] = "`description` = '".preg_replace('/[^\w\s-]/','',$fields->description)."'";
        if (isset($fields->name)) $array[] = "`name` = '".preg_replace('/[^\w\s-.]/','',$fields->name)."'";
        if (isset($fields->status)) $array[] = "`status` = '".$fields->status."'";
        // Convert to a comma seperated string for the mysql query
        
        
        
        $fieldstr = implode(",",$array);
        
        $this->mysqli->query("UPDATE user_drivers SET ".$fieldstr." WHERE `id` = '$id'");
        // CHECK REDIS?
        // UPDATE REDIS
        if (isset($fields->name)) $this->redis->hset("driver:$id",'name',$fields->name);
        if (isset($fields->description)) $this->redis->hset("driver:$id",'description',$fields->description);        
        if (isset($fields->status)) $this->redis->hset("driver:$id",'status',$fields->status);        
        
        
        
         if ($this->mysqli->affected_rows>0){
            return array('success'=>true, 'message'=>'Field updated');
        } else {
            return array('success'=>false, 'message'=>'Field could not be updated');
        }
	}
  
  
   public function set_parameters($id, $fields)
   {
	   //error_log($fields);
	   $fields=strtr ($fields, array ("'" => '"'));
	   $fields = json_decode(stripslashes($fields),true);
	   
	      foreach ($fields as $name => $value)
                {
					
					 $this->mysqli->query("UPDATE user_driver_par SET value='".$value."' WHERE `driverid` = '$id' and name='".$name."'");
					 $result = $this->mysqli->query("select id from user_driver_par  WHERE `driverid` = '$id' and name='".$name."'");
					 if($row = $result->fetch_object())
						$this->load_param_to_redis($row->id);
					
					//recuperare id prima di chiamare redis
					 //$this->load_param_to_redis($id);
					/*
					$this->redis->hMSet("driver:params:$id",array(
					'id'=>$id,
					'name'=>$name,
					'value'=>$value
	      ));*/
			     }
			     
			     return "ok";
			     
		
	   }
  
  
    public function set_parameter($id,$fields)
    {
        $id = intval($id);
        
        $fields = json_decode(stripslashes($fields));
        
         //error_log("parameter:".$fields->value);
             
         if (isset($fields->value))     
            $value=preg_replace('/[^\w\s-]/','',$fields->value);
              //error_log("parameter:".$value);
         $this->mysqli->query("UPDATE user_driver_par SET value='".$fields->value."' WHERE `id` = '$id'");
        
		
		
       // if (isset($fields->value)) $this->redis->hset("driver:$id",'$id',$fields->value);
/*
        // Repeat this line changing the field name to add fields that can be updated:
        if (isset($fields->description)) $array[] = "`description` = '".preg_replace('/[^\w\s-]/','',$fields->description)."'";
        if (isset($fields->name)) $array[] = "`name` = '".preg_replace('/[^\w\s-.]/','',$fields->name)."'";
        if (isset($fields->status)) $array[] = "`status` = '".$fields->status."'";
        // Convert to a comma seperated string for the mysql query
        
        
        
        $fieldstr = implode(",",$array);
        * 
        $this->mysqli->query("UPDATE user_drivers SET ".$fieldstr." WHERE `id` = '$id'");
        
        // CHECK REDIS?
        // UPDATE REDIS
        if (isset($fields->name)) $this->redis->hset("driver:$id",'name',$fields->name);
        if (isset($fields->description)) $this->redis->hset("driver:$id",'description',$fields->description);        
        if (isset($fields->status)) $this->redis->hset("driver:$id",'status',$fields->status);        
        */
        
        
         if ($this->mysqli->affected_rows>0){
			 $this->load_param_to_redis($id);
			
            return array('success'=>true, 'message'=>'Field updated');
        } else {
            return array('success'=>false, 'message'=>'Field could not be updated');
        }
    }
    
    public function add($userid, $drivertype, $description)
    {
		
		 $this->mysqli->query("INSERT INTO user_drivers (type, status, userid) VALUES ($drivertype, 0, $userid)");
		 $driverid= $this->mysqli->insert_id;
		 $qresult=$this->mysqli->query("select name from driver_parameters where id_driver=$drivertype");
		 
		 while($row=$qresult->fetch_object())
		 {
			 $this->mysqli->query("INSERT INTO  user_driver_par (driverid, name,value) VALUES ($driverid, '$row->name','')");
	
			}
			 
		$this->load_to_redis($userid);
		//$this->load_params_to_redis($driverid);
		$result = Array();
		$result["driverid"]=$driverid;
		return $result;
		
		}
		
		
		
		
	public function setupFeeds($driverid, $userid, $nodeid, $type)
	{
		
		//create virtual meter on node 0
		 
		
		
		
		$data = array();
		if($type=="consumer" or $type=="prosumer") $data["in"]=0;		
		if($type=="producer" or $type=="prosumer") $data["out"]=0;
		
		$dbinputs = $this->input->get_inputs($userid);
		
		
		
	    if (!isset($dbinputs[0]["consumption"]))
			{
				$inputid = $this->input->create_input($userid, 0, "consumption");
                $dbinputs[0]['consumption'] = true;
                $dbinputs[0]['consumption'] = array('id'=>$inputid);
                
                //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","consumption_power",1,2,json_decode('{"interval":60}'));
                $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","consumption_kwh",1,2,json_decode('{"interval":10}'));
                $this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","consumption_kwhd",2,2,json_decode('{"interval":86400}'));
                $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
				
				
				
				}
		
		if (!isset($dbinputs[0]["pv"]))
			{
				$inputid = $this->input->create_input($userid, 0, "pv");
                $dbinputs[0]['pv'] = true;
                $dbinputs[0]['pv'] = array('id'=>$inputid);
                
                 //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","pv_power",1,2,json_decode('{"interval":60}'));
                $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","pv_kwh",1,2,json_decode('{"interval":10}'));
                $this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","pv_kwhd",2,2,json_decode('{"interval":86400}'));
                $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
		
		
		
			
			
		}
		
		 
		
		  if (!isset($dbinputs[0]["pv2grid"]))
		  
		  {
			  
				$inputid = $this->input->create_input($userid, 0, "pv2grid");
                $dbinputs[0]['pv2grid'] = true;
                $dbinputs[0]['pv2grid'] = array('id'=>$inputid);
               
                $pvfeed=$this->feed->get_id($userid,"pv_power");
                $cfeed=$this->feed->get_id($userid,"consumption_power");
                
                //Add this pv to pv2grid
                $this->input->add_process($this->process, $userid, $inputid,29,$pvfeed);
			    $this->input->add_process($this->process, $userid, $inputid,30,$cfeed);
			    $this->input->add_process($this->process, $userid, $inputid,24,"");
			    
			    
			      //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","pv2grid_power",1,2,json_decode('{"interval":60}'));
                $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","pv2grid_kwh",1,2,json_decode('{"interval":10}'));
                $this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","pv2grid_kwhd",2,2,json_decode('{"interval":86400}'));
                $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
			    
			    
			  }
			  
	     if (!isset($dbinputs[0]["pv2household"]))
		  
		  {
			  
				$inputid = $this->input->create_input($userid, 0, "pv2household");
                $dbinputs[0]['pv2household'] = true;
                $dbinputs[0]['pv2household'] = array('id'=>$inputid);
               
                $pvfeed=$this->feed->get_id($userid,"pv_power");
                $cfeed=$this->feed->get_id($userid,"consumption_power");
                
                //Add this pv to pv2grid
                $this->input->add_process($this->process, $userid, $inputid,36,$pvfeed);
			    $this->input->add_process($this->process, $userid, $inputid,36,$cfeed);
			    
			    
			      //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","pv2household_power",1,2,json_decode('{"interval":60}'));
                $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","pv2household_kwh",1,2,json_decode('{"interval":10}'));
                $this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","pv2household_kwhd",2,2,json_decode('{"interval":86400}'));
                $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
			    
			    
			  }
			 
	       if (!isset($dbinputs[0]["grid2household"]))
		  
		  {
			  
				$inputid = $this->input->create_input($userid, 0, "grid2household");
                $dbinputs[0]['grid2household'] = true;
                $dbinputs[0]['grid2household'] = array('id'=>$inputid);
               
                $pvfeed=$this->feed->get_id($userid,"pv_power");
                $cfeed=$this->feed->get_id($userid,"consumption_power");
               
               
                //Add this pv to pv2grid
                $this->input->add_process($this->process, $userid, $inputid,29,$cfeed);
			    $this->input->add_process($this->process, $userid, $inputid,30,$pvfeed);
			    $this->input->add_process($this->process, $userid, $inputid,24,"");
			    
			     //Log power to feed: Realtime(1), phptimeseries(2)
                $feedid=$this->feed->create($userid,"","grid2household_power",1,2,json_decode('{"interval":60}'));
                $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                //Compute energy Kw-> Kwh
                $feedid=$this->feed->create($userid,"","grid2household_kwh",1,2,json_decode('{"interval":10}'));
                $this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
                        
                 //Kwh to KWhd
                $feedid=$this->feed->create($userid,"","grid2household_kwhd",2,2,json_decode('{"interval":86400}'));
                $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node: 0"}');
			    
			    
			    
				//add a cron job to periodically update the smart meter
				$result = $this->mysqli->query("SELECT apikey_write FROM users WHERE id=".$userid);
			    if ($result->num_rows == 1) 
					{
						$row=$result->fetch_object();
						$apikey=$row->apikey_write;
						$cronjob='*/1 * * * * wget -qO- "http://localhost/emoncms/input/post.json?node=0&json={consumption:0,pv:0,grid2household:0,pv2grid:0,pv2household:-1}&apikey='.$apikey.'"';
						$input = shell_exec('crontab -l');
						file_put_contents('/tmp/crontab.txt',$input);
						file_put_contents('/tmp/crontab.txt', $cronjob.PHP_EOL,FILE_APPEND);
						exec('crontab /tmp/crontab.txt');
					}
			  }
	
		/*
		ob_start();
		var_dump($dbinputs[0]["pv2grid"]);
		$contents = ob_get_contents();
		ob_end_clean();
		error_log($contents);
		*/
	
		
        foreach ($data as $name => $value)
                { 
                    if (!isset($dbinputs[$nodeid]["power".$name])) {
						
						
						if($name=="out") 	$node0 = $dbinputs[0]["pv"];
						else $node0 = $dbinputs[0]["consumption"];
						
                        $inputid = $this->input->create_input($userid, $nodeid, "power".$name);
                        $dbinputs[$nodeid][$name] = true;
                        $dbinputs[$nodeid][$name] = array('id'=>$inputid);
                        
                        //Add this input to the smart meter on node 0
                        $this->input->add_process($this->process, $userid, $node0['id'],11,$inputid);
                        $process_list = $this->input->get_processlist($node0['id']);
						$array = explode(",", $process_list);
                        $this->input->move_process($node0['id'],count($array),-1);
                        $this->input->move_process($node0['id'],(int)(count($array)-1),-1);
                        $this->input->move_process($node0['id'],(int)(count($array)-2),-1);
                        
                        //Log power to feed: Realtime(1), phptimeseries(2)
                        $feedid=$this->feed->create($userid,"","power".$name."_".$nodeid,1,2,json_decode('{"interval":60}'));
                        $this->input->add_process($this->process, $userid, $inputid,1,$feedid["feedid"]);
                        $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node:'.$nodeid.'"}');
                        
                        //Compute energy Kw-> Kwh
                        $feedid=$this->feed->create($userid,"","energy".$name."_".$nodeid,1,2,json_decode('{"interval":10}'));
                        $this->input->add_process($this->process, $userid, $inputid,4,$feedid["feedid"]);
                        $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node:'.$nodeid.'"}');
                        
                        //Kwh to KWhd
                        $feedid=$this->feed->create($userid,"","denergy".$name."_".$nodeid,2,2,json_decode('{"interval":86400}'));
                        $this->input->add_process($this->process, $userid, $inputid,5,$feedid["feedid"]);
                        $this->feed->set_feed_fields($feedid["feedid"], '{"tag":"Node:'.$nodeid.'"}');
                        
                        
                        
                    } 
                }
                
                
		}
		
/*
    // USES: redis input
    public function add_process($process_class,$userid, $inputid, $processid, $arg, $newfeedname,$newfeedinterval)
    {
        $userid = (int) $userid;
        $inputid = (int) $inputid;	
        $processid = (int) $processid;			                              // get process type (ProcessArg::)
        $arg = (float) $arg;                                              // This is: actual value (i.e x0.01), inputid or feedid
        $newfeedname = preg_replace('/[^\w\s-.]/','',$newfeedname);	      // filter out all except for alphanumeric white space and dash
        $newfeedinterval = (int) $newfeedinterval;

        $process = $process_class->get_process($processid);
        $processtype = $process[1];                                       // Array position 1 is the processtype: VALUE, INPUT, FEED
        $datatype = $process[4];                                          // Array position 4 is the datatype

        switch ($processtype) {
            case ProcessArg::VALUE:                                           // If arg type value
                $arg = floatval($arg);
                $id = $arg;
                if ($arg == '') return array('success'=>false, 'message'=>'Argument must be a valid number greater or less than 0.');
                break;
            case ProcessArg::INPUTID:                 // If arg type input
                if (!$this->exists($arg)) return array('success'=>false, 'message'=>'Input does not exist!');

                break;
            case ProcessArg::FEEDID:                  // If arg type feed
                $name = ''; if ($arg!=-1) $name = $this->feed->get_field($arg,'name');  // First check if feed exists of given feed id and user.
                $id = $this->feed->get_id($userid,$name);
                if (($name == '') || ($id == '')) {
                    $result = $this->feed->create($userid,$newfeedname, $datatype, $newfeedinterval);
                    if ($result['success']==true) $arg = $result['feedid']; else return $result;
                }
                break;
            case ProcessArg::NONE:                                           // If arg type none
                $arg = 0;
                $id = $arg;
                break;
        }

        $list = $this->get_processlist($inputid);
        if ($list) $list .= ',';
        $list .= $processid . ':' . $arg;
        $this->set_processlist($inputid, $list);

        return array('success'=>true, 'message'=>'Process added');
    }
*/
    /******
    * delete input process by index
    ******/
    // USES: redis input
    /*
    public function delete_process($inputid, $index)
    {
        $inputid = (int) $inputid;
        $index = (int) $index;

        $success = false;
        $index--; // Array is 0-based. Index from process page is 1-based.

        // Load process list
        $array = explode(",", $this->get_processlist($inputid));

        // Delete process
        if (count($array)>$index && $array[$index]) {unset($array[$index]); $success = true;}

        // Save new process list
        $this->set_processlist($inputid, implode(",", $array));

        return $success;
    }

    /******
    * move_input_process - move process up/down list of processes by $moveby (eg. -1, +1)
    ******/
    // USES: redis input
    /*
    public function move_process($id, $index, $moveby)
    {
        $id = (int) $id;
        $index = (int) $index;
        $moveby = (int) $moveby;

        if (($moveby > 1) || ($moveby < -1)) return false;  // Only support +/-1 (logic is easier)

        $process_list = $this->get_processlist($id);
        $array = explode(",", $process_list);
        $index = $index - 1; // Array is 0-based. Index from process page is 1-based.
        
        $newindex = $index + $moveby; // Calc new index in array
        // Check if $newindex is greater than size of list
        if ($newindex > (count($array)-1)) $newindex = (count($array)-1);
        // Check if $newindex is less than 0
        if ($newindex < 0) $newindex = 0;
        
        $replace = $array[$newindex]; // Save entry that will be replaced
        $array[$newindex] = $array[$index];
        $array[$index] = $replace;

        // Save new process list
        $this->set_processlist($id, implode(",", $array));
        return true;
    }

    // USES: redis input
    public function reset_process($id)
    {
       $id = (int) $id;
       $this->set_processlist($id, "");
    }

    // USES: redis input & user
    public function get_inputs($userid)
    {
        $userid = (int) $userid;
        if (!$this->redis->exists("user:inputs:$userid")) $this->load_to_redis($userid);
       
        $dbinputs = array(); 
        $inputids = $this->redis->sMembers("user:inputs:$userid");        
        
        foreach ($inputids as $id)
        {
          $row = $this->redis->hGetAll("input:$id");
          if ($row['nodeid']==null) $row['nodeid'] = 0;
          if (!isset($dbinputs[$row['nodeid']])) $dbinputs[$row['nodeid']] = array();
          $dbinputs[$row['nodeid']][$row['name']] = array('id'=>$row['id'], 'processList'=>$row['processList']);
        }
        
        return $dbinputs;
    }

    //-----------------------------------------------------------------------------------------------
    // This public function gets a users input list, its used to create the input/list page
    //-----------------------------------------------------------------------------------------------
    // USES: redis input & user & lastvalue
     
    */
    public function getlist($userid)
    {     
	
		
        $userid = (int) $userid;
       
        //$qresult = $this->mysqli->query("select driver.id, name, status, description from user_drivers, driver where userid= '$userid' and user_drivers.type=driver.id;");
        $userid = (int) $userid;
        /*if (!$this->redis->exists("user:drivers:$userid"))*/ 
        $this->load_to_redis($userid);
        
        
        
        $drivers = array();
        $driverids = $this->redis->sMembers("user:drivers:$userid");
        
        foreach ($driverids as $id)
        {
          $row = $this->redis->hGetAll("driver:$id");
          /*
          $lastvalue = $this->redis->hmget("input:lastvalue:$id",array('time','value'));
          $row['time'] = $lastvalue['time'];
          $row['value'] = $lastvalue['value'];*/
          $drivers[] = $row;
        }
        return $drivers;
    }
    
   
    
    public function get_driverid($deviceid)
    {
		
		$result = $this->mysqli->query("select driverid from user_driver_par where name='emondevice' and value=".$deviceid);
        
        $id = Array();
        if($row = $result->fetch_object())
          {
			   $id['driverid'] = $row->driverid;
			   $id['success'] = true;
		   }
        else 
           {
			   $id['message'] = "device not found";
			   $id['success'] = false;
			   }
			   
       return $id;
		
		}
    
    public function get_parameter($driverid, $par)
    {
        // LOAD REDIS
        $driverid = (int) $driverid;
        $parameters = array();
		$parameters = $this->get_parameters($driverid);
		
		 $count = count($parameters);
		 //error_log("count ".$count);
		 /*
		 for ($i = 0; $i < $count; $i++) {
		    error_log("res ".$parameters($i));
			// if($parameters($i)->name == $par)
			//  return $parameters($i)->value;
			    
			 
		  }*/
		  return "";
    }
    
    public function get_status($driverid)
    {
        // LOAD REDIS
        $driverid = (int) $driverid;
        //if (!$this->redis->exists("driver:$id")) $this->load_input_to_redis($id);
        return $this->redis->hget("driver:$driverid",'status');
    }
    
/*
    // USES: redis input
    public function get_name($id)
    {
        // LOAD REDIS
        $id = (int) $id;
        if (!$this->redis->exists("input:$id")) $this->load_input_to_redis($id);
        return $this->redis->hget("input:$id",'name');
    }

    // USES: redis input
    public function get_processlist($id)
    {
        // LOAD REDIS
        $id = (int) $id;
        if (!$this->redis->exists("input:$id")) $this->load_input_to_redis($id);
        return $this->redis->hget("input:$id",'processList');
    }
    
    public function get_last_value($id)
    {
      $id = (int) $id;
      return $this->redis->hget("input:lastvalue:$id",'value');
    }
    

    //-----------------------------------------------------------------------------------------------
    // Gets the inputs process list and converts id's into descriptive text
    //-----------------------------------------------------------------------------------------------
    // USES: redis input
    public function get_processlist_desc($process_class,$id)
    {
        $id = (int) $id;
        $process_list = $this->get_processlist($id);
        // Get the input's process list

        $list = array();
        if ($process_list)
        {
            $array = explode(",", $process_list);
            // input process list is comma seperated
            foreach ($array as $row)// For all input processes
            {
                $row = explode(":", $row);
                // Divide into process id and arg
                $processid = $row[0];
                $arg = $row[1];
                // Named variables
                $process = $process_class->get_process($processid);
                // gets process details of id given

                $processDescription = $process[0];
                // gets process description
                if ($process[1] == ProcessArg::INPUTID)
                  $arg = $this->get_name($arg);
                // if input: get input name
                elseif ($process[1] == ProcessArg::FEEDID)
                  $arg = $this->feed->get_field($arg,'name');
                // if feed: get feed name

                $list[] = array(
                  $processDescription,
                  $arg
                );
                // Populate list array
            }
        }
        return $list;
    }

    // USES: redis input & user*/
    public function delete($userid, $driverid)
    {
        $userid = (int) $userid;
        $driverid = (int) $driverid;
        // Inputs are deleted permanentely straight away rather than a soft delete
        // as in feeds - as no actual feed data will be lost
        //error_log("userid".$userid."  driverid".$driverid);
        
        $qresult = $this->mysqli->query("select value FROM user_driver_par WHERE  driverid = $driverid and name='node'");
        if($row=$qresult->fetch_object())    
			$this->devices->unregister_node($userid, $row->value);
			
        
        $this->mysqli->query("DELETE FROM user_driver_par WHERE  driverid = $driverid");
        $this->mysqli->query("DELETE FROM user_drivers WHERE userid = $userid AND id = $driverid");
        $this->redis->del("driver:$driverid");
        $this->redis->srem("user:drivers:$userid",$driverid);
        

        return "ok";
    }
    /*
    public function clean($userid)
    {
      $result = "";
      $qresult = $this->mysqli->query("SELECT * FROM input WHERE `userid` = '$userid'");
      while ($row = $qresult->fetch_array())
      {
        $inputid = $row['id'];
        if ($row['processList']==NULL || $row['processList']=='')
        {
          $result = $this->mysqli->query("DELETE FROM input WHERE userid = '$userid' AND id = '$inputid'");
          $this->redis->del("input:$inputid");
          $this->redis->srem("user:inputs:$userid",$inputid);

          $result .= "Deleted input: $inputid <br>";
        }
      }
      return $result;
    }
    
    // Redis cache loaders

    private function load_input_to_redis($inputid)
    {
      $result = $this->mysqli->query("SELECT id,nodeid,name,description,processList FROM input WHERE `id` = '$inputid'");
      $row = $result->fetch_object();
      
      $this->redis->sAdd("user:inputs:$userid", $row->id);
      $this->redis->hMSet("input:$row->id",array(
        'id'=>$row->id,
        'nodeid'=>$row->nodeid,
        'name'=>$row->name,
        'description'=>$row->description,
        'processList'=>$row->processList
      ));
      
    }
    
*/    
   
   public function get_parameters($driverid)
   {
	    $driverid = (int) $driverid;
      
        if (!$this->redis->exists("user:drivers:params:$driverid")) 
             $this->load_params_to_redis($driverid);
        
        $params = array();      
        $paramsids = $this->redis->sMembers("user:drivers:params:$driverid");
       
        foreach ($paramsids as $id)
        {
		  
          $row = $this->redis->hGetAll("driver:params:$id");
          $params[] = $row;
        }
        
        return $params;
	   
	   }



    private function load_params_to_redis($driverid)
    {
		$result = $this->mysqli->query("SELECT id,name,value FROM user_driver_par WHERE `driverid` = '$driverid'");
        
        while($row = $result->fetch_object()){
       
        $this->redis->sAdd("user:drivers:params:$driverid", $row->id);
	    $this->redis->hMSet("driver:params:$row->id",array(
	        'id'=>$row->id,
	        'name'=>$row->name,
	        'value'=>$row->value
	      ));
	      
	     }
	   
	}
	
	private function load_param_to_redis($paramid)
    {
	  if(isset($this->redis)) 
		{
		 $result=$this->mysqli->query("SELECT * FROM user_driver_par  WHERE `id` = '$paramid'");
			 
			 
	      
        if($row = $result->fetch_object())
			$this->redis->hMSet("driver:params:$row->id",array(
				'id'=>$row->id,
				'name'=>$row->name,
				'value'=>$row->value
			));
	      
	         }else
         error_log("driver:load_to_redis: REDIS is not SET");  
	   
	}
		


    private function load_to_redis($userid)
    {
		if(isset($this->redis)) 
		{
			$result = $this->mysqli->query("select user_drivers.id, type, name, status, description from user_drivers, driver where userid= $userid and user_drivers.type=driver.id;");
      
              
			while($row = $result->fetch_object()){
			error_log($row->id." ".$row->type." ".$row->name.'description'.$row->description.'status'.$row->status.'userid'.$userid);
			$this->redis->sAdd("user:drivers:$userid", $row->id);    
			$this->redis->hMSet("driver:$row->id",array(
				'id'=>$row->id,
				'type'=>$row->type,
				'name'=>$row->name,
				'description'=>$row->description,
				'status'=>$row->status,
				'userid'=>$userid
	        
			));
		}
		
      }else
         error_log("driver:load_to_redis: REDIS is not SET");
    }
  
  
  
public function execCmd($nodeid,$driverid,$cmd){
	
	//get driver id
	
	//get parameters
	$this->get_parameters($driverid);
	$parameters= array();

	$paramsids = $this->redis->sMembers("user:drivers:params:$driverid");
        foreach ($paramsids as $id)
        {
		  
          $p = $this->redis->hGet("driver:params:$id",'name');
          $p=trim($p);
          $parameters[$p]=$this->redis->hGet("driver:params:$id",'value');
         }
	include "Modules/driver/driver$driverid/cmd.php";
	return $result;
	}
  
public function reserve($user,$driverid){
	
		$result = $this->mysqli->query("select distinct node from node_driver  where user_id= $user order by node;");
		$i=0;
		
		while($row = $result->fetch_object() and $i<30){
			
		   if($row->node!=$i)
		    {
			   $this->mysqli->query("insert into node_driver  VALUES($i, $driverid,$user );");
		       return $i;
		    }
		    else $i++;
		}
		
		if($i>=30)
		 return -1;
		else
		 {  $this->mysqli->query("insert into node_driver  VALUES($i, $driverid,$user );");
	        return  $i;
	     }
	     
	
	   
	}
	
public function release($user,$driverid, $nodeid){
	
return $this->mysqli->query("delete from node_driver where user_id=$user and node=$nodeid and driver_id=$driverid;");
	   
	}	
	
	
	public function connect($user,$driverid, $nodeid){
	
		return $this->mysqli->query("insert into node_driver  VALUES($nodeid, $driverid,$user );");
	   
	}	
		
public function  driversNodes($user)
{
	$result = $this->mysqli->query("select node, driver_id as driver from node_driver where user_id=$user order by node;");
	$nodes= "{ nodes: ";
	$first=0;
    while($row = $result->fetch_object()){
		
		if($first>0) $nodes=$nodes+", "; 
		  else $first=1;
	    $nodes=$nodes." { id: ".$row->node.", driver: ".$row->driver."}";
	
	}
     $nodes=$nodes." }";
     return $nodes;
  
 }
}
