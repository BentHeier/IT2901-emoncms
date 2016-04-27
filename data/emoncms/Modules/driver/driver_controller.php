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

function driver_controller()
{
  //return array('content'=>"ok");
  
  global $mysqli, $redis, $user, $session, $route, $feed_settings;

  // There are no actions in the input module that can be performed with less than write privileges
  if (!$session['write']) return array('content'=>false);

  global $feed, $timestore_adminkey;
  $result = false;
  
  include "Modules/feed/feed_model.php";
  $feed = new Feed($mysqli,$redis, $feed_settings);

  require "Modules/input/input_model.php"; // 295
  $input = new Input($mysqli,$redis, $feed);



  require "Modules/input/process_model.php"; // 886
  $process = new Process($mysqli,$input,$feed);
  
  include "Modules/devices/devices_model.php";
  $devices = new Devices($mysqli,$redis, $input, $feed, $process);
  
  include "Modules/driver/driver_model.php";
  $driver = new Driver($mysqli,$redis, $input, $feed, $process, $devices);
  

  if ($route->format == 'html')
  {
    if ($route->action == 'api') $result = view("Modules/driver/Views/driver_api.php", array());
    if ($route->action == 'node') $result =  view("Modules/driver/Views/driver_node.php", array());
    if ($route->action == 'vdriver') $result =  view("Modules/driver/Views/virtual-driver.php", array());
    if ($route->action == 'vmeter') $result =  view("Modules/driver/Views/virtual-meter.php", array());
    
    if ($route->action == 'configure') 
    {
      $result = view("Modules/driver/Views/configure.php", 
      array(
          'driverid'=> intval(get('driverid'))/*, 
          'processlist' => $process->get_process_list(),
          'inputlist' => $input->getlist($session['userid']),
          'feedlist'=> $feed->get_user_feeds($session['userid'],0)*/
      ));
    }
     if ($route->action == 'startstop') 
    {
      $result = view("Modules/driver/Views/startstop.php", 
      array(
          'driverid'=> intval(get('driverid')),
          'deviceid'=> intval(get('deviceid'))
            /*, 
          'processlist' => $process->get_process_list(),
          'inputlist' => $input->getlist($session['userid']),
          'feedlist'=> $feed->get_user_feeds($session['userid'],0)*/
      ));
    }
    
  }

  if ($route->format == 'json')
  {
	
	if($route->action == 'vdevice')
	{
		
		 $result=$driver->createDevice($_GET, $session["userid"]);
		 
		
		}
	if($route->action == 'connectVMeter')
	{
		
		 $result=$driver->connectVMeter($_GET, $session["userid"]);
		
	}
	
	if($route->action == 'updateVHM')
	{
		
		 $result=$driver->updateVHMeter($session["userid"]);
		
	}	
	
	  
	  
	if ($route->action == 'cmd')
	{
		 $node = get('node');
		 $cmd = json_decode(get('json'));  
		 
		 $userid = $session['userid'];
		 $query="SELECT driver_id from node_driver where node=$node and user_id=$userid";
	
		 $res=$mysqli->query($query);	 
		 $row=$res->fetch_object();
	     $result=$driver->execCmd($node,$row->driver_id,$cmd);
			   
	
		 
		}  
	  
    // input/post.json?node=10&csv=100,200,300
    // input/post.json?node=10&json={power:100,solar:200}
    // input/bulk.json?data=[[0,10,100,200],[5,10,100,200],[10,10,100,200]]

    // input/bulk.json?data=[[0,16,1137],[2,17,1437,3164],[4,19,1412,3077]] 
    // The first number of each node is the time offset, so for the first node it is 0 which means the packet 
    // for the first node arrived at 0 seconds. The second node arrived at 2 seconds and 3rd 4 seconds. 
    // The second number is the node id, this is the unqiue identifer for the wireless node. 
    // All the numbers after the first two are data values. The first node here (node 16) has only once data value: 1137.
/*     
    if ($route->action == 'bulk')
    {
      $valid = true;
      $data = json_decode(get('data'));
    
      $userid = $session['userid'];
      $dbinputs = $input->get_inputs($userid);

      $len = count($data);
      if ($len>0)
      {
        if (isset($data[$len-1][0])) 
        {
          $offset = (int) $data[$len-1][0];
          if ($offset>=0)
          {
            $start_time = time() - $offset;
   
            foreach ($data as $item)
            {
              if (count($item)>2)
              {
                //check for correct time format
                $itemtime = (int) $item[0];
                if ($itemtime>=0)
                {
                  $time = $start_time + (int) $itemtime;
                  $nodeid = $item[1];

                  $inputs = array();
                  $name = 1;
                  for ($i=2; $i<count($item); $i++)
                  {
                    $value = (float) $item[$i];
                    $inputs[$name] = $value;
                    $name ++;
                  }
                  
                  $tmp = array();
                  foreach ($inputs as $name => $value) 
                  {
                    if (!isset($dbinputs[$nodeid][$name])) {
                      $inputid = $input->create_input($userid, $nodeid, $name);
                      $dbinputs[$nodeid][$name] = true;
                      $dbinputs[$nodeid][$name] = array('id'=>$inputid, 'processList'=>'');
                      $input->set_timevalue($dbinputs[$nodeid][$name]['id'],$time,$value);
                    } else {
                      $inputid = $dbinputs[$nodeid][$name]['id'];
                      $input->set_timevalue($dbinputs[$nodeid][$name]['id'],$time,$value);
                      
                      if ($dbinputs[$nodeid][$name]['processList']) $tmp[] = array('value'=>$value,'processList'=>$dbinputs[$nodeid][$name]['processList']);
                    }
                  }
                  
                  foreach ($tmp as $i) $process->input($time,$i['value'],$i['processList']);        
                   
                } else { $valid = false; $error = "Format error, time index given is negative"; }
              } else { $valid = false; $error = "Format error, bulk item needs at least 3 values"; }
            }
          } else { $valid = false; $error = "Format error, time index given is negative"; }
        } else { $valid = false; $error = "Format error, last item in bulk data does not contain any data"; }
      } else { $valid = false; $error = "Format error, json string supplied is not valid"; }
      
      if ($valid) $result = 'ok'; else $result = "Error: $error\n";
    }

    // input/post.json?node=10&json={power1:100,power2:200,power3:300}
    // input/post.json?node=10&csv=100,200,300
	*/	
    if ($route->action == 'add')
    {
      
     $datain = false;
      // code below processes input regardless of json or csv type
      if (isset($_GET['json'])) $datain = get('json'); 
              
      if ($datain!="")
      {
        $json = preg_replace('/[^\w\s-.:,]/','',$datain);
        $datapairs = explode(',', $json);

        $csvi = 0;
        $description="";
        for ($i=0; $i<count($datapairs); $i++)
        {
          $keyvalue = explode(':', $datapairs[$i]);
          
          if (isset($keyvalue[1])) {
            if ($keyvalue[0]!='type' or $keyvalue[0]!='description') {$valid = false; $error = "Format error, json key missing or invalid character"; }
            
            if ($keyvalue[0]=='type')
              $driverid=$keyvalue[1];
            else 
               $description=$keyvalue[1];
            
          } 
          
          
        }


        $userid = $session['userid'];
       
        $result = $driver->add($userid, $driverid, $description);
        $valid = true;
      }
      else
      {
        $valid = false; $error = "Request contains no data via csv, json or data tag";
      }        
      
      if (!$valid)   $result = "Error: $error\n";
    }
    
 /*
    if ($route->action == "clean") $result = $input->clean($session['userid']);
    */
    if ($route->action == "list") $result = $driver->getlist($session['userid']);
    
    
    //allocate a node 
    if ($route->action == "nodes") 
    	 $result = $driver->driversNodes($session['userid']);
	   
	  
	if ($route->action == "release" or $route->action == "connect") {
	if (isset($_GET['json']))
		  { $datain = get('json'); 
          
			$json = preg_replace('/[^\w\s-.: ,]/','',$datain);
			$datapairs = explode(',', $json);
			for ($i=0; $i<count($datapairs); $i++)
            {
				$keyvalue = explode(':', $datapairs[$i]);  
				//echo $keyvalue[0]." ".$keyvalue[1];     
				if (isset($keyvalue[1]))
				   if($keyvalue[0]=='driverID')			   
				         $driverid=$keyvalue[1];
				   else  if($keyvalue[0]=='nodeID')
				         $nodeid=$keyvalue[1];
				 }
				 if ($route->action == "release")
				   $result = $driver->release($session['userid'],$driverid, $nodeid);
				 else 
				   $result = $driver->connect($session['userid'],$driverid, $nodeid);
		      }   	 
		  else  $result = -1;
	  }    
	  
if ($route->action == "reserve") 
	if (isset($_GET['driverID']))		  
	    $result = $driver->reserve($session['userid'], $_GET['driverID']);
   else  $result = -1;
	  
    
    //get parameters of a driver
    if ($route->action == "parameters")
       {
		   if (isset($_GET['driverid']))
		      $result = $driver->get_parameters($_GET['driverid']);
		} 
	if ($route->action == "startstop")
       {
		   if (!isset($_GET['driverid']))
		     $driverid=Null;
		   else
		     $driverid =$_GET['driverid'];
		   if(!isset($_GET['deviceid'])) 
		      $deviceid= Null;
		   else
			  $deviceid =$_GET['deviceid'];
		    
			$result = $driver->startstop($driverid, $deviceid);
		} 	
		
    /*
    if ($route->action == "getinputs") $result = $input->get_inputs($session['userid']);
*/
    if (isset($_GET['driverid']) && $driver->belongs_to_user($session['userid'],get("driverid")))
    {
      if ($route->action == "delete") $result = $driver->delete($session['userid'],get("driverid"));

      if ($route->action == 'set') $result = $driver->set_fields(get('driverid'),get('fields'));
      if ($route->action == 'setparameters') $result = $driver->set_parameters(get('driverid'),get('fields'));
/*
      if ($route->action == "process")
      { 
        if ($route->subaction == "add") $result = $input->add_process($process,$session['userid'], get('inputid'), get('processid'), get('arg'), get('newfeedname'), get('newfeedinterval'));
        if ($route->subaction == "list") $result = $input->get_processlist_desc($process, get("inputid"));
        if ($route->subaction == "delete") $result = $input->delete_process(get("inputid"),get('processid'));
        if ($route->subaction == "move") $result = $input->move_process(get("inputid"),get('processid'),get('moveby'));
        if ($route->subaction == "reset") $result = $input->reset_process(get("inputid"));
      }
      * */
    }
    
     if (isset($_GET['parameterid']))
               if ($route->action == 'set') $result = $driver->set_parameter(get('parameterid'),get('fields'));


  } 

  return array('content'=>$result);
}

?>
