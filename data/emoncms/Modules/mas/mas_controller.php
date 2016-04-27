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

function mas_controller()
{
  //return array('content'=>"ok");
  
  global $mysqli, $redis, $user, $session, $route, $feed_settings, $path;

  // There are no actions in the input module that can be performed with less than write privileges
  if (!$session['write']) return array('content'=>false);

  global $feed, $timestore_adminkey;
  $result = false;


  require "Modules/input/input_model.php"; // 295
  $input = new Input($mysqli,$redis, $feed);

  require "Modules/input/process_model.php"; // 886
  $process = new Process($mysqli,$input,$feed);
  
  
  include "Modules/devices/devices_model.php";
  $devices = new Devices($mysqli,$redis, $input, $feed, $process);

  include "Modules/feed/feed_model.php";
  $feed = new Feed($mysqli,$redis, $feed_settings);

  include "Modules/mas/mas_model.php";
  $mas = new MASInterface($mysqli,$redis, $feed);
  $mas->set_devices($devices);


  //require "Modules/input/process_model.php"; // 886
  //$process = new Process($mysqli,$input,$feed);
  
  

  if ($route->format == 'html')
  {
    if ($route->action == 'api') $result = view("Modules/mas/Views/mas_api.php", array());
    if ($route->action == 'node') $result =  view("Modules/mas/Views/mas_node.php", array());
    if ($route->action == 'settings') $result =  view("Modules/mas/Views/mas_settings.php", array());
    if ($route->action == 'learning') $result =  view("Modules/mas/Views/learning.php", array());
     if ($route->action == 'index') $result =  view("Modules/mas/Views/index.php", array());
   
     
    
  }
  
  if ($route->format == 'json')
  { 
	$userid = $session['userid'];
	
	if ($route->action == "start")
		$result =  $mas->start($userid, $_GET['agent']);
	else if ($route->action == "stop")
		$result =  $mas->stop($userid, $_GET['agent']);
	else if ($route->action == "check")
		$result =  $mas->check($userid);
	else if ($route->action == "status")
		$result =  $mas->status($userid);
	else if ($route->action == "profiles")
		$result =  $mas->profiles($userid);
	else if ($route->action == "tsprofile")
		$result =  $mas->getTSProfile($_GET['name']);
	else if ($route->action == "profileinfo" && isset($_GET['name']))
		$result =  $mas->getProfileInfo($_GET['name']);
		
	else if ($route->action == "list")
    {
	       if (!isset($_GET['json']))
	         $result =  $mas->getlist($session['userid'], NULL);
	       else
	        {
				$datain = get('json'); 
	            $valid=checkJSON('add',$datain);
			    if(!$valid)
				  $error = "JSON format error";
				else    
					$result =  $mas->getlist($userid, $datain);
	       }
           
	 }
	elseif ($route->action == "settings")
    {
	      
	    $result =  $mas->settings();
	      
           
	 }
	 elseif($route->action == "delete")
	 {
		  if (isset($_GET['id']))
		    $result =  $mas->delete($session['userid'],$_GET['id']);
		  else
		    $result = "{'response': 'deleteTask', 'result': 'failure', 'message': 'taskid is mandatory'}";
		 }
	 elseif($route->action == "execute")
	 {
		  if (isset($_GET['id']))
		    $result =  $mas->execute($session['userid'],$_GET['id']);
		  else
		    $result = "{'response': 'deleteTask', 'result': 'failure', 'message': 'taskid is mandatory'}";
		 }
	  elseif($route->action == "get")
	 {
		  if (isset($_GET['id']))
		    $result =  $mas->getTask($session['userid'],$_GET['id']);
		  else
		    $result = "{'response': 'getTask', 'result': 'failure', 'message': 'taskid is mandatory'}";
		  
		  }
	 elseif($route->action == "connection")
	 {
		  if (isset($_GET['json']))
		    $result =  $mas->deviceConnected($session['userid'],$_GET['json']);
		  else
		    $result = "{'response': 'connection', 'result': 'failure', 'message': 'json parameter is mandatory'}";
		  
		  }
	 elseif($route->action == "profile")
	 {
		  if (isset($_GET['device']) and isset($_GET['mode']))
		    $result =  $mas->getProfile($session['userid'],$_GET['device'],$_GET['mode']);
		  else
		   {
			    $result = Array();
			    $result["response"]="profile";
			    $result["result"]="failure";
			    $result["message"]="device, modeid are mandatory";
		  
		  }
	  }
	 else{ 
	  
	  $datain = false;
      // code below processes input regardless of json or csv type
      if (isset($_GET['json'])) $datain = get('json'); 
              
      if ($datain!="")
      {
       //$json = preg_replace('/[^\w\s-.:,]/','',$datain);
       //$datapairs = explode(',', $json);

        $csvi = 0;
       
    
		if ($route->action == 'add')
		{    
			$valid=checkJSON('add',$datain);
			if(!$valid)
				$error = "JSON format error";
			else    
				$result =  $mas->add($userid, $datain);
		
		}
		else if ($route->action == 'update')
		{    
		
			$valid=checkJSON('update',$datain) && isset($_GET['id']);
			if(!$valid)
				$error = "JSON format error";
			else    
				$result =  $mas->update($_GET['id'], $datain);
		
		}
		
		else if ($route->action == 'set') 
		{
			$valid=checkJSON('set',$datain) && isset($_GET['id']);
			if(!$valid)
				$error = "JSON format error";
			else    
				$result =  $mas->set_fields($_GET['id'], $datain);
		
		}
        else if ($route->action == 'savesettings') 
		{
			$valid=checkJSON('savesettings',$datain) && isset($_GET['id']);
			
			if(!$valid)
				$error = "JSON format error";
			else    
				$result =  $mas->save_settings($_GET['id'],$datain);
		
		}
    
	   }
	   else
	     $result = "Error: no input data provided\n"; 
	

   }
 
 /*
    if ($route->action == "clean") $result = $input->clean($session['userid']);
    */
   
    
    
		
    /*
    if ($route->action == "getinputs") $result = $input->get_inputs($session['userid']);

    if (isset($_GET['driverid']) && $driver->belongs_to_user($session['userid'],get("driverid")))
    {
      if ($route->action == "delete") $result = $driver->delete($session['userid'],get("driverid"));

     if ($route->action == "process")
      { 
        if ($route->subaction == "add") $result = $input->add_process($process,$session['userid'], get('inputid'), get('processid'), get('arg'), get('newfeedname'), get('newfeedinterval'));
        if ($route->subaction == "list") $result = $input->get_processlist_desc($process, get("inputid"));
        if ($route->subaction == "delete") $result = $input->delete_process(get("inputid"),get('processid'));
        if ($route->subaction == "move") $result = $input->move_process(get("inputid"),get('processid'),get('moveby'));
        if ($route->subaction == "reset") $result = $input->reset_process(get("inputid"));
      }
      
    }
    
     if (isset($_GET['parameterid']))
               if ($route->action == 'set') $result = $driver->set_parameters(get('parameterid'),get('fields'));
* */

  } 
  return array('content'=>$result);
}

function checkJSON($type, $request)
{ /*
	 for ($i=0; $i<count($datapairs); $i++)
        {
          $keyvalue = explode(':', $datapairs[$i]);
          
          if (isset($keyvalue[1])) {
            if ($keyvalue[0]=='execution_run') {
				$type=$keyvalue[1];
			
            
          } 
          
          
        }*/
	
	return true;}

?>
