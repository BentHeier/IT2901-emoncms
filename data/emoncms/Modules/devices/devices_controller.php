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

function devices_controller() 
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
    
    include "Modules/mas/mas_model.php";
    $mas = new MASInterface($mysqli,$redis, $feed);

	require "Modules/input/process_model.php"; // 886
    $process = Process::makeProcess($mysqli,$input,$feed,$redis,$mas);
    $process->set_timezone_offset($user->get_timezone($session['userid']));
	
	include "Modules/devices/devices_model.php";
	$devices = new Devices($mysqli,$redis, $input, $feed, $process);
	
	
	function getArray($string) {
		$json = preg_replace('/[^\w\s-.:,]/','', $string);
		$data = explode(',', $json);
		$return = Array();
		
		foreach($data as $d) $return[] = trim($d);
		
		return array_unique($return);
	}
		
	if ($route->format == 'html')
	{
		if ($route->action == 'api') $result = view("Modules/devices/Views/devices_api.php", array());
		if ($route->action == 'view') $result = view("Modules/devices/Views/devices_view.php", array());
		if ($route->action == 'configure') {
			$device = $devices->get_device_by_deviceid($session['userid'], get('deviceid'));
			
			$result = view("Modules/devices/Views/devices_configure.php", 
			array(
				'deviceid'=> $device['deviceid'],
				'name' => $device['name']
			));
		}
		if ($route->action == 'toggle') {
			$device = $devices->get_device_by_deviceid($session['userid'], get('deviceid'));

			$status = -1;
			$feedid = $feed->get_id($session['userid'], 'device'.$device['deviceid'].'_status');
			if ($feedid != false) {
				$status = $feed->get_timevalue($feedid)['value'];
			}
			
			$result = view("Modules/devices/Views/devices_toggle.php", 
			array(
				'deviceid'=> $device['deviceid'],
				'driverid'=> $device['deviceid'],
				'name' => $device['name'],
				'status' => $status
			));
		}
	}

	if ($route->format == 'json') {
		if ($route->action == "template") {
			if ($route->subaction == "add") {
				$result = $devices->add_template($session['userid'], get('productname'), get('producttype'), get('operatingtype'), get('description'), getArray(get('requirednodetypes')), getArray(get('modes')));
			}
			if ($route->subaction == "remove") {
			  $result = $devices->remove_template($session['userid'], get('templateid'));
			}
			if ($route->subaction == "list") {
				$result = $devices->list_templates($session['userid']);
			}
			if ($route->subaction == "get" and get('templateid') != null) {
				$result = $devices->get_template($session['userid'], get('templateid'));
			}
			if ($route->subaction == "conf" and get('templateid') != null) {
				$result = $devices->get_template_conf($session['userid'], get('templateid'));
			}
			if ($route->subaction == "parameterinfo" and get('templateid') != null and get('name') != null) {
				$result = $devices->get_parameter_info($session['userid'], get('templateid'),get('name'));
			}
	
				
		}
		
		if ($route->action == "registernode") {
			$result = $devices->register_node($session['userid'], get('driverid'), get('address'), getArray(get('type')));
		}
		  
		if ($route->action == "node") {
			if ($route->subaction == "register") {
				$result = $devices->register_node($session['userid'], get('driverid'), get('address'), getArray(get('type')));
			}
			if ($route->subaction == "unregister") {
				$result = $devices->unregister_node($session['userid'], get('nodeid'));
			}
			if ($route->subaction == "getunassigned") {
				$result = $devices->get_unassigned_nodes($session['userid'], (get('type') ? getArray(get('type')) : false));
			}
			if ($route->subaction == "get" and get('driverid') != null and get('address') != null) {
				$result = $devices->get_nodeid($session['userid'], get('driverid'), get('address'));
			}
			if ($route->subaction == "get" and get('nodeid') != null) {
				$result = $devices->get_node($session['userid'], get('nodeid'));
			}
		}
		  
		if ($route->action == "getunassignednodes") {
			$result = $devices->get_unassigned_nodes($session['userid'], (get('type') ? getArray(get('type')) : false));
		}
		  
		if ($route->action == "device") {
			if ($route->subaction == "add") {
				error_log("templateid:".get('templateid'));
				$result = $devices->add_device($session['userid'], get('name'), get('templateid'), getArray(get('nodes')));
			}
			if ($route->subaction == "remove") {
				$result = $devices->remove_device($session['userid'], get('deviceid'));
			}
			if ($route->subaction == "list") {
				$result = $devices->list_devices($session['userid']);
			}
			if ($route->subaction == "status") {
				$result = $devices->set_device_status($session['userid'], get('deviceid'), get('status'));
			}
			if ($route->subaction == "get" and get('nodeid') != null) {
				$result = $devices->get_device_by_nodeid($session['userid'], get('nodeid'));
			}
			if ($route->subaction == "get" and get('deviceid') != null) {
				$result = $devices->get_device_by_deviceid($session['userid'], get('deviceid'));
			}
			 //get parameters of a device
			if ($route->subaction == "parameters" and get('deviceid') != null) {
				if(get('description') != null)
					$result = $devices->get_parameters($_GET['deviceid'], True);
				else 
					$result = $devices->get_parameters($_GET['deviceid'], False);
			} 
		
	        if ($route->subaction == 'set' and get('parameterid') != null) {
				$result = $devices->set_parameter(get('parameterid'),get('fields'));
			}
			
			if ($route->subaction == 'setparameters'and get('deviceid') and get('fields')) {
				$result = $devices->set_parameters(get('deviceid'),get('fields'));
			}
		}
		
		if ($route->action == "post") {
			$result = $devices->post_value($session['userid'], get('driverid'), get('address'), get('type'), get('value'), (get('time') != null) ? get('time') : time());
		}
		  
		if ($route->action == "reserve") {
			if (isset($_GET['driverID']))		  
				$result = $driver->reserve($session['userid'], $_GET['driverID']);
			else
				$result = -1;
		}
	   
		if ($route->action == "startstop") {
			if (isset($_GET['driverid']))
				$result = $driver->startstop($_GET['driverid']);
		} 	
		
	    /*
	    if ($route->action == "getinputs") $result = $input->get_inputs($session['userid']);
		*/
	    /*if (isset($_GET['driverid']) && $driver->belongs_to_user($session['userid'],get("driverid")))
	    {
	      if ($route->action == "delete") $result = $driver->delete($session['userid'],get("driverid"));
	
	      if ($route->action == 'set') $result = $driver->set_fields(get('driverid'),get('fields'));
	      if ($route->action == 'setparameters') $result = $driver->set_parameters(get('driverid'),get('fields'));*/
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
	    //}
  	}

  	return array('content'=>$result);
}
?>
