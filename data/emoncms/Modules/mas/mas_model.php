<?php
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



class MASInterface
{
    private $mysqli;
    private $redis;
    private $log;
    private $loadsdir="/home/www-data/Dropbox/cossmicprototype/neighbour";
    private $devices;
    
    public function __construct($mysqli,$redis, $feed)
    {
        $this->mysqli = $mysqli;
        $this->redis = $redis;
        $this->log = new EmonLogger(__FILE__);
        $this->feed = $feed;
    }
    
    public function set_devices($devices)
    {
		$this->devices=$devices;
		} 
     
    
    public function profiles($userid){
		$profiles = Array();
		$dir = "/var/www/emoncms/profiles";
		$dh  = opendir($dir);
		
		
     
        $ids = Array();
        while (false !== ($filename = readdir($dh))) 
		{
			if(!is_dir($dir."/".$filename))
			{			
			  
				$profile= Array();
				$profile["id"]=$filename;
				$profile["user"]=substr($filename,1, strpos($filename,"d")-strpos($filename,"u")-1);
				$profile["deviceid"]=substr($filename,strpos($filename,"d")+1,strpos($filename,"m")-strpos($filename,"d")-1);
				$profile["modeid"]=substr($filename,strpos($filename,"m")+1,strpos($filename,".")-strpos($filename,"m")-1);
				$profile["filename"]= $filename;
				$profile["source"]= "file";
				$profile["available"]= 1;
				$profiles[]=$profile;
				$ids[]=$filename;
		    }
			  		  
		  }
        $profileids = $this->redis->keys("profiles:*");
        foreach ($profileids as $id)
        {
			
			 $profile= Array();
			 $temp = $this->redis->hGetAll($id);
		  if(!in_array($temp["filename"],$ids)){		
			 $profile["id"]=$temp["filename"];		 	 
			 $profile["user"]=$temp["user"];
			 $profile["deviceid"]=$temp["device"];
			 $profile["modeid"]=$temp["mode"];
			 $profile["filename"]= $temp["filename"];	
			 $profile["source"]= "redis";
			 $profile["available"]= $temp["available"];
			 $profiles[]=$profile;	
		   }
		}
		
		
		
		
		return $profiles;		
		}
		
	public function getTSProfile($filename){
		$ts = Array();
		$axis1 = Array();
		$axis2 = Array();
		$myfile = fopen("/var/www/emoncms/profiles/".$filename, "r") or die("Unable to open file!");
		$duration=fgets($myfile);
		$poly=Array();
		$poly[0]=fgets($myfile);
		$poly[1]=fgets($myfile);
		$poly[2]=fgets($myfile);
		$poly[3]=fgets($myfile);	
		fclose($myfile);
	
		$step=$duration/10;
		$x=0;
		$axis1[]=0;
		$axis2[]=0;
		while($x<$duration)
		{
			
			$value=$poly[0]*pow($x,3)+$poly[1]*pow($x,2)+$poly[2]*$x;
			$axis1[]=$x;
			$axis2[]=$value;
			$x=$x+$step;
			}
		$ts["x"]=$axis1;
		$ts["y"]=$axis2;
		$ts["type"]="scatter";
		
		return $ts;
		}	
	
	
	public function getProfileInfo($name)
	{
		
		$profile = Array();
		if($this->redis)
		  
			  $profile = $this->redis->hGetAll("profiles:9");
		
		return $profile;
		}
		
    
     public function getProfile($userid, $deviceid, $modeid, $type)
    {
		
	   if($type==3)
		$filename="u".$userid."d".$deviceid."m".$modeid.".prof";   
	   else if($type==1)
	   {	
		$result = Array();
		$qresult = $this->mysqli->query("select  id from  user_tasks  where deviceid=".$deviceid." and modeid=$modeid and ast <> 0 and status=5 and ltime=NULL order by ast desc limit 5");
		if ($qresult->num_rows > 0 )
			return $this->updateSingleRunProfile($userid, $deviceid, $modeid);
		$filename="u".$userid."d".$deviceid."m".$modeid.".prof";
		}
		else
			$filename="d".$deviceid."m".$modeid.".prof";
	   
	   if(file_exists("/var/www/emoncms/profiles/".$filename))
		   {
			$result["success"]=true;
			$result["profile"]=$filename;
			}
		 else
		 {
			$result["success"]=false;
			$result["message"]="profile not available";
	      }
	      return $result;
    }
    
    public function updateSingleRunProfile($userid, $deviceid, $modeid)
    {
		$time_threshold = 10*60000;
		
		$result = Array();
		$ast = Array();
		$endtime = Array();
		$qresult = $this->mysqli->query("select id, ast,ltime from  user_tasks  where deviceid=".$deviceid." and modeid=$modeid and ast <> 0 and status=4 order by ast desc limit 5");
		
		
		$ast[0]= time();
		if ($qresult->num_rows > 0)
		 {
			 
			 $i=0;
			 $feedid = $this->feed->get_id($userid, 'device'.$deviceid.'_in_kwh'); 
			 $profile = Array();
			 $duration =0;
			
			 $mpath="/var/www/emoncms/profiles";
			 $filename="u".$userid."d".$deviceid."m".$modeid;
			 $fp = fopen($mpath."/".$filename.".raw", 'w');

		 
			 while($row = $qresult->fetch_object())
			    {	
					
					
					  
					  $i= $i + 1;
					  $ast[$i] = $row->ast;
					  $temp =  $row->ltime;
					  if($temp==0)
					    $endtime[$i]=$ast[$i-1]*1000;
					  else
					    $endtime[$i]=$temp*1000;
					  
					  //echo "begin:".$ast[$i]." ".$endtime[$i]."\n";
					  
					  $start=$ast[$i]*1000;
					  $data = $this->feed->get_data_int($feedid,$start,$endtime[$i],180);
        
        
					  $ast[$i]=$data[0][0]/1000;
					  $offset=$data[0][0];
					  $eoffset = $data[0][1];
					  //echo var_dump($data);
					  $previous=0;
					  $j=0;
					  $endtime[$i]=$data[0][0];
					  //echo count($data)."\n";
					  
					  while($data[$j][1]==0 and $j<count($data))  
						$j=$j+1;
							 
							 
					 
					  while($j<count($data))
					   {
						 $incr = $data[$j][1]-$previous;
						 //echo "incr:".$incr."\n";
						 //if there is an increment the task is not finished
						 
						 if( ($data[$j][0]-$endtime[$i])< $time_threshold) // sample is not too far
						 {
							if($temp==0 and $incr > 0 and ($data[$j][0]-$endtime[$i])<$time_threshold)
								$endtime[$i]=$data[$j][0];
					
							
						    
							fputcsv($fp, [ceil($data[$j][0]-$offset)/1000, ($data[$j][1]-$eoffset)]);
							//echo $data[$j][0]." ".$data[$j][1]."\n";
							//the valued has  not changed for too much time
							if($temp == 0 and  $incr==0 and ($data[$j][0]-$endtime[$i])>$time_threshold)
								{	$silence = $data[$j][0]-$endtime[$i];
									//echo "AFTER: start:".$ast[$i]."end:".$endtime[$i]."silence:".$silence."\n";
									$j = count($data);
								
								}
							else 
								{
									$previous= $data[$j][1];
									
								}
							
							}// sample is not too far
							
							$j=$j+1;
					    }//end whle
						
					    if($temp==0 and ($endtime[$i]-$ast[$i]*1000)> 0)
					      {
							   $this->mysqli->query("update user_tasks set ltime=".($endtime[$i]/1000)." where id=".$row->id);
							   if($duration < ($endtime[$i]-$ast[$i]*1000))
								$duration =$endtime[$i]-$ast[$i]*1000;
						  }
					    
					    
				  }
						
						fclose($fp);
						shell_exec("python /var/www/emoncms/Modules/mas/bin/updateprof.py ".$filename);
						if($redis)
						{
						   $automata = Array();
						   $automata["profile_type"]="single run";
						   
						   $this->redis->hMSet("profiles:$filename",$automata);	
						}
						$result["success"]=true;
						$result["profile"]=$filename.".prof";
                        
			      
		}    
		else
		{
			$result["success"]=false;
			$result["message"]="profile not available";
			}
		 
		 return $result;
		}
    
    
    public function learn($userid,$node, $name, $time_now, $value)
        {
			/*
        //$dev=getdevice($namenode,$name)
        $dev=1;   
		
		//Get Running Task  
		$qresult = $this->mysqli->query("SELECT * FROM user_tasks  WHERE userid=$userid and deviceid=$dev and status=4");
		//$this->log->info("SELECT * FROM user_tasks  WHERE userid=$userid and deviceid=$dev and status=4");
		if($task = $qresult->fetch_array()) 	
        //$task=getRunningTask($device)<-- {'taskID':,'device':,'mode':,'ast':, 'ltime':}
        if($task['ast']){
         
        $t=$time_now-$task['ast'];
        $mode=$task['modeid'];
        
        $path="/var/www/emoncms/profiles";
        $filename="d".$dev."m".$mode;
        $cmd= "echo '$t,$value'  >>".$path."/".$filename.".temp";
        
		$this->log->info($cmd." ".$task['ast']);        
        shell_exec($cmd);
        
        if($value>0.01)
         {
         $ltime=$time_now;  
         $this->log->info("UPDATE user_tasks set ltime=$ltime where id=".$task['id']);
         $this->mysqli->query("UPDATE user_tasks set ltime=$ltime where id=".$task['id']);
         }
         else{
           if($time_now-$task['ltime']>20000)
        	   {
               	  $this->mysqli->query("UPDATE user_tasks set status=5 where id="+$task['id']);  
                  shell_exec("python /var/www/emoncms/Modules/mas/bin/updateprof.py ".$filename);
         		}
          	}
         
	    }
        
        $this->log->info("mas.learn() received userid= $userid time=$time_now, value=$value, node=$node, name=$name ");
        * */
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
    
    public function exists($id)
    {
      $id = (int) $id;
      $result = $this->mysqli->query("SELECT id FROM tasks WHERE `id` = '$id'");
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
    public function belongs_to_user($userid, $taskid)
    {
        $userid = (int) $userid;
        $taskid = (int) $taskid;

        $result = $this->mysqli->query("SELECT id FROM tasks WHERE userid = '$userid' AND id = '$taskid'");
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
  
  
   public function set_fields($id,$jsonstring)
    {
		
        $id = intval($id);
       
        $json=strtr ($jsonstring, array ("'" => '"'));
        $fields = json_decode(stripslashes($json),true);
        $success=false;
             
         foreach ( $fields as $key => $value ) {
			 
			    if($key=='AST')  $this->fakeschedule($id, $value);
			 
			 
				 if($key=='status')
				    $this->mysqli->query("UPDATE  user_tasks set status='$value' where  id=$id");
				 
			 
				 $this->mysqli->query("UPDATE user_task_par set value='$value' where taskid=$id and name='$key'");
				 
				 if($this->mysqli->affected_rows==0){
				     $this->mysqli->query("INSERT INTO user_task_par VALUES(NULL, $id, '$key', '$value')");
					 if($this->mysqli->affected_rows==0)  
						$success=true;
					}
				 else 
				   $success=true;
				}	
				$success==true? $success='success':  $success='error';
		 
          return "{'response': 'updateTask', 'result': '$success', 'taskID': '$id'}";
	}
  
  
    public function update($id,$jsonstring)
    {
        $id = intval($id);
       
        $json=strtr ($jsonstring, array ("'" => '"'));
        $fields = json_decode(stripslashes($json),true);
        $success=false;
             
         foreach ( $fields as $key => $value ) {
			 
			   
				 if($key=='status')
				    $this->mysqli->query("UPDATE  user_tasks set status='$value' where  id=$id");
				 
				 if($key=='AST'){
					 
					$value1 = strtotime($value);
				 	$this->mysqli->query("UPDATE  user_tasks set ast='$value1' where  id=$id");
				}
			 
				 $this->mysqli->query("UPDATE user_task_par set value='$value' where taskid=$id and name='$key'");
				 preg_match_all('!\d+!', $this->mysqli->info, $m);
				 if($m[0][0]==0){
				     $this->mysqli->query("INSERT INTO user_task_par VALUES(NULL, $id, '$key', '$value')");
					 if($this->mysqli->affected_rows!=0)  
						$success=true;
					}
				 else 
				   $success=true;
				}	
				$success==true? $success='success':  $success='error';
		 
          return "{'response': 'updateTask', 'result': '$success', 'taskID': '$id'}";
        
        
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
        
        
       
    }
    
    public function add($userid, $jsonstring)
    {
		   $json=strtr ($jsonstring, array ("'" => '"'));
		   $task = json_decode(stripslashes($json), true);
		
		   if($userid == null)
		    $userid=1;
		   error_log("add_task"); 
		
		   $u=$userid;
		   $d=$task['deviceID'];
		   $m=$task['mode'];
	
		   
		   $valid=true;
		   $status=0;
		   if($task['execution_type']==="single_run")
		    {
				  $type=1;
				  $status=0;
				  $this->devices->set_device_status($userid, $d, 0);
			  }
			else if($task['execution_type']==="periodic_run")
            {
				$type=2;
				$status=4;
				}
			else if($task['execution_type']==="e_car")
            {
				$type=3;
				}
             else
               {
				$message="'execution type not supported yet'";
				$valid=false;
				  }
				  
				  
			if($valid){
				$result = $this->mysqli->query("SHOW TABLE STATUS LIKE 'user_tasks'");
				$row = $result->fetch_array(1);
				$nextId = $row['Auto_increment']; 
				 
				
				$this->log->info("INSERT INTO user_tasks (id, type, status, deviceid, modeid, userid) VALUES ($nextId, $type, $status,".$task['deviceID'].",".$task['mode'].",".$userid.")");
				$inserts=$this->mysqli->query("INSERT INTO user_tasks (id, type, status, deviceid, modeid, userid) VALUES ($nextId, $type, $status,".$task['deviceID'].",".$task['mode'].",". $userid.")");
				if(!$inserts)
				{
					$valid=false;
					$message = 'INVALId query:' .mysql_error()."\n";		
				}
				else 
					{
				      
					  $task["profile"]="u".$u."d".$d."m".$m.".prof";
					  foreach ($task as $key => $value)  
						$this->mysqli->query("INSERT INTO user_task_par VALUES(NULL, $nextId, '$key', '$value')");
					  
					  $this->mysqli->query("INSERT INTO user_task_par VALUES(NULL, $nextId, 'AST', 'UNDEFINED')");
					  $this->mysqli->query("INSERT INTO user_task_par VALUES(NULL, $nextId, 'AET', 'UNDEFINED')");
					}
					
					
           if($valid)  
			 {
			  
			   $output=$this->getProfile($u,$d,$m,$type);
			   //echo var_dump($output);
			   $task['status']=$status;
			   if($output["success"]==true)
			   { $filename = "/var/www/emoncms/profiles/".$output["profile"];
                
                 
			   //$filename = "/var/www/emoncms/profiles/u".$u."d".$d."m".$m.".prof";
			   
			    if(file_exists($filename))
				 {	
					#$profile=file_get_contents($filename);			
					//$url="http://localhost:8808/add.json?taskid=".$nextId."&jsons={'meta-data':".$jsonstring.",'data': '['".$profile."]'}";
					
					$task['profile']=$filename;
					
					
					/*THIS IS NECESSARY MEANWHILE THE TASKSCHEDULER DOES NOT USE SPLINE*"
					*/
					
        			$ts = $this->getTSProfile($output["profile"]);
        			
        			$x = $ts["x"];
        			$y = $ts["y"];
        			$i=0;
        			$filename = $filename."_raw";
        			$task['profile']=$output["profile"];
        			$fp = fopen($filename,'w');
        			while($i<count($x))
        			{
						fwrite($fp,ceil($x[$i])." ".$y[$i]."\n");
						$i =$i+1;
					   }
					fclose($fp);
					
					}
					/*THIS IS NECESSARY MEANWHILE THE TASKSCHEDULER DOES NOT USE SPLINE*"
					*/
				 }
				 else
					{
						if($type==1)
						   $status=1;
						$this->update($nextId,'{"status": '.$status.', "AST":"'.$task["EST"].'"}');
						$task['AST']=$task["EST"];
						$task['status']=$status;
					}
					
					
					$jsonstring=json_encode($task);
					$jsonstring=strtr ($jsonstring, array ('"' => "'"));
					$url='http://localhost:8808/add.json?taskid='.$nextId.'&jsons="'.urlencode($jsonstring).'"';
					error_log("[add_task]".$url);
					$this->log->info($url);
					$this->restRequest($url);	
					
					
					$response=array();
					$response["response"]="addTask";
					$response["result"]="success";
					$response["taskID"]="$nextId";
					
			   //writeLoad
			    /**prototype code */ 
               return $response;
				}
			}
			
			$response=array();
			$response["response"]="addTask";
			$response["result"]="error";
			$response["message"]="$message";
			return $response;
		}


  public function getTask($userid, $taskid)
    {     
        $userid = (int) $userid;
       
        $qresult = $this->mysqli->query("select id, type, status  from user_tasks where id=$taskid ;");
      
       
          
         if($row = $qresult->fetch_object()) 
         {
			$response="{'response': 'getTask', 'result':'success', 'task': {'id': $row->id, 'status': $row->status, 'type': $row->type, ";
        
			$qresult = $this->mysqli->query("select name, value  from user_task_par where taskid=$taskid;");
       
         while($row = $qresult->fetch_object())
		{
			
			$response=$response.", '".$row->name."': '".$row->value."'";
			}
			
         $response=$response."}}";
	 }
	 else
	   $response = "{'response': 'getTask', 'result': 'error', 'message': 'taskid $taskid not found'}";
        
       
        return $response;
    }

    public function getlist($userid,$jsonstring)
    {   
		if($jsonstring!=null)
		{
		   $json=strtr ($jsonstring, array ("'" => '"'));
		   $loptions = json_decode($json, true);
	
		   
		}
		
        $userid = (int) $userid;
        $query="select id,type, status from user_tasks where userid= '$userid'";
        if(isset($loptions) and array_key_exists("status", $loptions))
             if($loptions['status'] == 10)
			    $query=$query." and status < 4";  		     
               else             
				$query=$query." and status=".$loptions['status'];  
        
        if(isset($loptions) and array_key_exists("execution_type", $loptions))
    			{
					$type = 0;				
				   if($loptions['execution_type']==="single_run")
					 $type=1;
				   else if($loptions['execution_type']==="periodic_run")
					 $type=2;
				   else if($loptions['execution_type']==="e_car")
					 $type=3;
				   if ($type>0) 
					$query=$query." and type=".$type;
				}
    	if(isset($loptions) and array_key_exists("deviceID", $loptions))
    			$query=$query." and deviceid=".$loptions['deviceID'];    
       
       
       $query = $query." order by id desc"; 
       
        if(isset($loptions) and array_key_exists("limit", $loptions))
        {
            $query = $query." limit 0,".$loptions['limit'];
            
            }
       
        $qresult = $this->mysqli->query($query);
        $userid = (int) $userid;
        /*if (!$this->redis->exists("user:drivers:$userid"))*/ 
        $this->load_to_redis($userid);
        
        /*$tasks = array();
        $driverids = $this->redis->sMembers("user:drivers:$userid");
        */
        $ra =  array("response"=>"listTasks", "result"=>"success");
        $tasks=array();
        
         while($row = $qresult->fetch_object())
		{
				
			$task=array();
			$task["id"]=$row->id; $task["type"]=$row->type; 
			$task["status"]=$row->status; 
			
			if(isset($loptions) and array_key_exists("info",$loptions))
			 if($loptions['info']==true)
			     {
					 
					 $presults = $this->mysqli->query("select name, value  from user_task_par where taskid=$row->id;");
					  while($par = $presults->fetch_object())
								$task[$par->name]=$par->value;
							
				}    
     		$tasks[]=$task;
			
			
			
		}
		   $ra["tasks"]=$tasks;
        
        /*
        foreach ($taksid as $id)
        {
          $row = $this->redis->hGetAll("driver:$id");
          
          $lastvalue = $this->redis->hmget("input:lastvalue:$id",array('time','value'));
          $row['time'] = $lastvalue['time'];
          $row['value'] = $lastvalue['value'];
          $drivers[] = $row;
        }*/
      
        
        return $ra;
        
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
    public function delete($userid, $taskid)
    {
		  
			$response= array();
		   $valid=true;
		   if(isset($taskid))
		   {
			 $this->mysqli->query("delete from user_task_par where  taskid=$taskid");  
		     $qresult=$this->mysqli->query("delete from user_tasks where userid=$userid and id=$taskid");
		   
		   $result = $this->mysqli->query("SELECT username FROM users WHERE id=$userid");
		   $data = $result->fetch_object();
		   $username= $data->username;
		   $loadsdir=$this->loadsdir;
		   
           if( $this->mysqli->affected_rows>0)  
           {
                /** this should be replaced from the previouse commented code*/
			   
			   /*
			   if (file_exists($loadsdir."/loads/".$username."-".$taskid.".metaload"))
					 unlink($loadsdir."/loads/".$username."-".$taskid.".metaload");
					
			   if (file_exists($loadsdir."/profiles/".$username."-".$taskid.".dataload"))
					 unlink($loadsdir."/profiles/".$username."-".$taskid.".dataload");
			    */
			   
				$url='http://localhost:8808/del.json?taskid='.$taskid;
				error_log($url);
				$this->log->info($url);
				$this->restRequest($url);
			    
			    
			    $response["result"]="success";
				$response["response"]="removeTask";
				$response["taskId"]="$taskid";
               return $response;
           }
           else
				 $message="taskid $taskid not found";
			}
			
				$response["result"]="error";
				$response["response"]="removeTask";
				$response["taskId"]="$message";
               return $response;
    }
    
     public function execute($userid, $taskid)
    {
		   $response= array();
		   $this->set_fields($taskid,'{"status":4}');
		   
		   if(isset($taskid))
		   {
			
		   $result = $this->mysqli->query("SELECT value FROM user_task_par WHERE taskid=$taskid and name='deviceID'");
		  
		  if( $data = $result->fetch_object())  
           {
			    
				$deviceid=$data->value;
				$AST=shell_exec("date ");
			    $AST=rtrim($AST,"\n");
                $baseurl='http://localhost/virtualDevices/device.php?json=';
                $url=$baseurl.urlencode('{"cmd": "set", "device": '.$deviceid.',"parameters": [{"name": "startedon", "value": "'.$AST.'"}]}');
				error_log("seturl".$url);
				$curl = curl_init();
				// Set some options - we are passing in a useragent too here
				curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $url
						));
				// Send the request & save response to $resp
				$resp = curl_exec($curl);
				// Close request to clear up some resources
				curl_close($curl); 
			    
			    
			    
				$response["result"]="success";
				$response["response"]="executeTask";
				$response["taskId"]="$taskid";
               return $response;
           }
           else
				 $message="taskid $taskid not found";
			}
		
			$response["result"]="error";
			$response["response"]="executeTask";
			$response["message"]="$message";
			
		  
			return $response;
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
   
   
 

	private function masinvoke($url)
	  {
		  
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		curl_close($curl);
		$resp=strtr ($resp, array ("'" => '"'));
		if($resp == "") return 0;
		
		$obj = json_decode(stripslashes($resp));
		if($obj->result == "success")
			return 1;
		else 
			return 0;
		}



    private function load_to_redis($userid)
    {
		/*
        $result = $this->mysqli->query("select user_drivers.id, type, name, status, description from user_drivers, driver where userid= $userid and user_drivers.type=driver.id;");
      
              
        while($row = $result->fetch_object()){
      
        $this->redis->sAdd("user:drivers:$userid", $row->id);    
	    $this->redis->hMSet("driver:$row->id",array(
	        'id'=>$row->id,
	        'type'=>$row->type,
	        'name'=>$row->name,
	        'description'=>$row->description,
	        'status'=>$row->status,
	        'userid'=>$userid
	        
	      ));
      }*/
    }
  /*******/
  private function saveLoadData($filename, $deviceid, $mode)
  {
	  
	   // Get cURL resource
	    
		//$url="http://cloud.cossmic.eu/cossmic/virtualdevices/device.php?json=".urlencode("{'cmd': 'getprofile', 'deviceid': $deviceid, 'mode': $mode, 'unit': 'e'}");
		$baseurl="http://localhost/virtualDevices/device.php?json=";
		$url=$baseurl.urlencode("{'cmd': 'getprofile', 'deviceid': $deviceid, 'mode': '".$mode."'}");
		
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt($curl, CURLOPT_USERPWD, "cossmichg:microgrid");
		curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url
				));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		curl_close($curl); 
		$loadfile = fopen($filename, "w");
	   	fwrite($loadfile, $resp);
		fclose($loadfile);
	  }
  
  /*******/
  public function fakeschedule($taskid,$AST)
  {
	  $result = $this->mysqli->query("SELECT username FROM user_tasks, users WHERE user_tasks.id = $taskid and users.id=user_tasks.userid");
      $row = $result->fetch_object();
      $username = $row->username;
	  $filename = $this->loadsdir."/loads/".$username."-".$taskid.".metaload";
	  copy($filename,"/tmp/".$username."-".$taskid.".metaload");
	  $temp=explode(":", $AST);
	  $AST=$temp[0]*3600+$temp[1]*60;
	  
	  file_put_contents("/tmp/".$username."-".$taskid.".metaload", "AST ".$AST, FILE_APPEND | LOCK_EX);
	  
	  copy("/tmp/".$username."-".$taskid.".metaload", $filename = $this->loadsdir."/schedule/".$username."-".$taskid.".metaload");
	  
	  }

   

   public function settings()
   {
	   $result = $this->mysqli->query("SELECT * FROM mas_par");
       $pars= array();
        while($row = $result->fetch_object())
        {
			$pars[] = $row;
			}
		return $pars;
       
	   }
	   
	   public function save_settings($id, $jsonstring)
    {
		
        
        $json=strtr ($jsonstring, array ("'" => '"'));
        $fields = json_decode(stripslashes($json),true);
        $success=false;
             
         	 
			     
				 $this->mysqli->query("UPDATE mas_par set value='".$fields['value']."'  where  id=$id");
				 
				 if($this->mysqli->affected_rows==0){
					
				     $this->mysqli->query("INSERT INTO mas_par VALUES($id, '".$fields['name']."', '".$fields['value']."')");
					 if($this->mysqli->affected_rows!=0)  
						$success=true;
					}
				 else 
				   $success=true;
					
				$success==true? $success='success':  $success='error';
		 
		   
          return array("response"=> "savesettings", "result"=> "$success", "parameter"=>"INSERT INTO mas_par VALUES($id, '".$fields['name']."', '".$fields['value']."')");
	}
	   
	   
	   private function  getDropboxdir()
    {/*
		 $this->mysqli->query("SELECT value from mas_par   where  id=0");
		 $row = $result->fetch_object();
		 while($row = $result->fetch_object())
          {
			  if("rows[0]"!= "Not Set")
			   
			   return 
			}
		*/		 
	}
	
	public function status($userid)
	{
		$result = array();
		
		if(file_exists("/tmp/spade.pid"))
		   $result["spade"]=1;
		else
		   $result["spade"]=0;
		
		if(file_exists("/tmp/tm.pid"))
		   $result["tm"]=1;
		else
		   $result["tm"]=0;
		   
		if(file_exists("/tmp/am.pid"))
		   $result["am"]=1;
		else
		   $result["am"]=0;
		
		return $result;
		
		}
	
	public function start($userid, $agent)
	{
		
		$result = array();
		$daemons= array();
		$result ["tm"] =1;
		if(!file_exists("/tmp/taskmanager.pid"))
			{
				//shell_exec("/var/www/emoncms/Modules/mas/bin/mas.sh stop");
				//shell_exec("/var/www/emoncms/Modules/mas/bin/mas.sh start");
				//if(!file_exists("/tmp/taskmanager.pid"))
					$result ["tm"] =0;
			}
		 
		 return $result;
		
		}
		
	public function stop($userid, $alg)
	{
		
		$records = $this->mysqli->query("SELECT username, apikey_write FROM users WHERE id=".$userid);
		$row=$records->fetch_object();
		$username=$row->username;
		$apikey=$row->apikey_write;
		$result = array();
		$result ["error"]="";
		$result ["result"] ="0";
		if($alg=="random")
		{
			if(!file_exists("/tmp/randomsched.pid"))
			{
				$result ["result"] ="3";
				$result ["error"] =$result ["error"]." random scheduler not running";
				
			}
		else	
			{
				shell_exec("/var/www/emoncms/Modules/mas/bin/randomsched.sh stop");	 
				
			}
		}
		if(!file_exists("/tmp/".$username.".pid"))
		{
			$result ["result"] ="3";
			$result ["error"] =$result ["error"]." updater not running";
			
			}
		else
		  {
			shell_exec("/var/www/emoncms/Modules/mas/bin/schedulerd.sh stop $username $apikey");
			$result ["error"]+=" MAS stopped";
			return $result;
		  }
		  
		return $result;
		
		}	
		
	
	
	public function restRequest($url)
	{
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url
				));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		curl_close($curl);
		$resp=strtr ($resp, array ("'" => '"'));
		
		return $resp;
	}
	
	public function deviceConnected($userid, $jsonstring)
	{
		//http://host/emoncms/mas/connection.json?json={device:137,'connection':'on'}
		$json=strtr ($jsonstring, array ("'" => '"'));
        $fields = json_decode(stripslashes($json),true);
        $deviceid = $fields['device'];
        $connection = $fields['connection'];
       
        $response=array();
		$response["cmd"]="connection";
		
		
			
        
        if($deviceid and $connection)
		{
		   if($connection==='off')
		    	{
					//delete current loads
					$tasks = $this->getlist($userid,"{'deviceID':$deviceid}");	
					$tasks=$tasks["tasks"];
					if($tasks)
						foreach ($tasks as $task)
						{
							$this->delete($task["id"]);
						}
					$response["result"]="success";
					$response["message"]="e-car tasks deleted";
				
					}
           $device = $this->devices->get_device_by_deviceid($userid,$deviceid);
	       $template=$this->devices->get_template($userid,$device["templateid"]);
	       
        if($template->operatingType==="e-car")
          {
			
			$configuration = array();
			$parameters=$this->devices->get_parameters($deviceid);
			
			
			foreach ($parameters as $parameter)
				{
					if($parameter["name"]==="minimum_energy_target")
						$configuration['minimum_energy_target']=$parameter["value"];
					else if($parameter["name"]==="capacity")		
						$configuration['capacity']=$parameter["value"];				
					else if($parameter["name"]==="MaxChargingPower")			
						$configuration['MaxChargingPower']=$parameter["value"];
					else if($parameter["name"]==="target_deadline")			
						$configuration['target_deadline']=$parameter["value"];
					else if($parameter["name"]==="energy_target")			
						$configuration['energy_target']=$parameter["value"];

			}
			
			if (array_key_exists("configuration",$fields))
			  {
				  $override=$fields['configuration'];
				 
				  foreach ($override as  $key => $parameter)
						$configuration[$key]=$parameter;
				
			 }
				
				
			$result = $this->scheduleECar0($userid, $deviceid, $configuration);			
			return $result;
           }
           else
           {
			   $response["result"]="failure";
			   $response["message"]="device type is not e-car";
			   }
          }
        else
        {
			$response["result"]="failure";
			$response["message"]="request format error";
			}
			
		
		return $response;
	}
        
        
	public function scheduleECar0($userid, $deviceid, $configuration)
	{
		//delete current loads
		$tasks = $this->getlist($userid,"{'deviceID':$deviceid}");	
		$tasks=$tasks["tasks"];
		
		if($tasks)
		foreach ($tasks as $task)
		{
			$this->delete($userid, $task["id"]);
			}
		
		//generate new loads
		//create load file 0
		//echo var_dump($configuration);
		 
		$mintarget=$configuration['minimum_energy_target']*$configuration['capacity']/100; 
		$duration = $mintarget*3600/$configuration['MaxChargingPower'];
		//$LST=$now+$deadline-$duration;
		$now = new DateTime();
		$EST = date('Y-m-d H:i',$now->getTimestamp());
		$now->add(new DateInterval('PT'.$duration.'S')); // adds 674165 secs
		$LST = date('Y-m-d H:i',$now->getTimestamp());
		
		//create file for fastest charge to mintarget; 
		$filename = "u".$userid."d".$deviceid."m0.prof";
		$fp = fopen("/var/www/emoncms/profiles/".$filename, 'w');
		fputcsv($fp,[0,0]);
		fputcsv($fp,[$duration,$mintarget]);
		fclose($fp);
		
		$result = array();
	
		$load = "{'EST': '$EST','LST': '$LST','deviceID': $deviceid, 'execution_type': 'e_car', 'mode' : 0,'profile':'$filename'}";
		//echo $load;
		
		$result[]=$this->add($userid,$load);
		
		//create profile to target; 
		$deadline = new DateTime($configuration['target_deadline']);
		$duration = $deadline->getTimestamp()-$now->getTimestamp();
		
		
		$filename = "u".$userid."d".$deviceid."m1.prof";
		$fp = fopen("/var/www/emoncms/profiles/".$filename, 'w');
		fputcsv($fp,[0,0]);
		fputcsv($fp,[$duration,$configuration['energy_target']*$configuration['capacity']/100]);
		fclose($fp);
		
		$EST=$LST;
		$LST=$configuration['target_deadline'];
		
		$load = "{'EST': '$EST','LST': '$LST','deviceID': $deviceid, 'execution_type': 'e_car', 'mode' : 1,'profile':'$filename'}";
		$result[]=$this->add($userid,$load);
		
		return $result;	
		}
	   
}
