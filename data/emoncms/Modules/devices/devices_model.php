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


class Devices
{
	private $mysqli;
	private $input;
	private $redis;
	private $feed;
	private $process;
	
	
	public function __construct($mysqli,$redis,$input, $feed, $process) {
		$this->mysqli = $mysqli;
		$this->input = $input;
		$this->redis = $redis;
		$this->feed = $feed;
		$this->process = $process;
	}
	
	
	public function exists($deviceid) {
		$deviceid = (int) $deviceid;
		$result = $this->mysqli->query("SELECT deviceid FROM devices WHERE `deviceid` = '$deviceid'");
		if ($result->num_rows > 0) {
			return true; 
		}
		else {
			return false;
		}
	}
	
	public function belongs_to_user( $userid, $deviceid) {
		$userid = (int) $userid;
		$deviceid = (int) $deviceid;

		$result = $this->mysqli->query("SELECT deviceid FROM devices WHERE userid = '$userid' AND deviceid = '$deviceid'");
		if ($result->fetch_array()) {
			return true; 
		}
		else {
			return false;
		}
	}
	
	public function add_template($userid, $productName, $productType, $operatingType, $description, $requiredNodeTypes, $modes) {
		$requiredNodeTypes = implode(',', $requiredNodeTypes);
		$modes = array_unique($modes);
		
		$this->mysqli->query("INSERT INTO templates (productName, productType, operatingType, description, requiredNodeTypes, userid) VALUES ('".$this->mysqli->real_escape_string($productName)."', '".$this->mysqli->real_escape_string($productType)."', '".$this->mysqli->real_escape_string($operatingType)."', '".$this->mysqli->real_escape_string($description)."', '".$this->mysqli->real_escape_string($requiredNodeTypes)."', $userid)");
		$templateid= $this->mysqli->insert_id;
		
		foreach ($modes as $mode) {
			$this->mysqli->query("INSERT INTO templates_modes (templateid, modeName) VALUES ($templateid, '".$this->mysqli->real_escape_string($mode)."')");
		}
		
		return array('success'=>true, 'templateid'=>$templateid);
	}
	
	public function remove_template($userid, $templateid) {
		if (!is_numeric($templateid)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$qresult = $this->mysqli->query("SELECT deviceid FROM devices WHERE templateid = $templateid");
		if ($qresult->num_rows > 0) return array('success'=>false, 'message'=>'Template is currently assigned to a device');
		
		$this->mysqli->query("DELETE FROM templates WHERE userid = $userid AND templateid = $templateid");
		if ($this->mysqli->affected_rows == 1) {
			$this->mysqli->query("DELETE FROM templates_modes WHERE templateid = $templateid");
			
			return array('success'=>true, 'message'=>'Template deleted');
		} else {
			return array('success'=>false, 'message'=>'Template does not exist or insufficient permissions');
		}
	}
		
	public function get_template($userid, $templateid) {
		if (!is_numeric($templateid)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$qresult = $this->mysqli->query("SELECT * FROM templates WHERE userid = $userid and templateid=".$templateid);
		
		$templates = array();
		if($row=$qresult->fetch_object()) {
			$row->id = $row->templateid;
			$row->requiredNodeTypes = explode(',', $row->requiredNodeTypes);
			$row->modes = $this->get_template_modes($userid, $templateid);
			return $row;
		}
		
		return $templates;
	}	
		
	public function list_templates($userid) {
		$qresult = $this->mysqli->query("SELECT * FROM templates WHERE userid = $userid");
		
		$templates = array();
		while($row=$qresult->fetch_object()) {
			$row->id = $row->templateid;
			$row->requiredNodeTypes = explode(',', $row->requiredNodeTypes);
			$row->modes = $this->get_template_modes($userid, $row->templateid);
			$templates[] = $row;
		}
		
		return $templates;
	}

	public function get_template_conf($userid, $templateid) {
		$qresult_parameters = $this->mysqli->query("SELECT name, description FROM template_conf WHERE idtemplate = ".$templateid);
		$conf = array();
		while($parameter = $qresult_parameters->fetch_object()) {
			$conf[] = $parameter;
		}
		
		return $conf;
	}

	public function get_parameter_info($userid, $templateid,$name) {
		$qresult_parameters = $this->mysqli->query("SELECT name, description FROM template_conf WHERE idtemplate = ".$templateid." and name='".$name."'");
		$conf = array();
		if($parameter = $qresult_parameters->fetch_object())
			$conf[] = $parameter;
		else 
			$conf[$name] = "missing";	
		
		return $conf;
	}
	
	private function get_template_modes($userid, $templateid) {
		$qresult_modes = $this->mysqli->query("SELECT modeName FROM templates_modes WHERE templateid = ".$templateid);
		$modes = array();
		while($row_modes = $qresult_modes->fetch_object()) {
			$modes[] = $row_modes->modeName;
		}
		
		return $modes;
	}
	
	public function register_node($userid, $driverid, $address, $type)
	{
		$node = $this->get_nodeid($userid, $driverid, $address);
		if ($node['success']) return array('success'=>false, 'message'=>'Node is already registered');
		
		 $this->mysqli->query("INSERT INTO node_parameters (type, driverid, address, userid) VALUES ('".implode(',', $type)."', '".$this->mysqli->real_escape_string($driverid)."', '".$this->mysqli->real_escape_string($address)."', $userid)");
		 $nodeid= $this->mysqli->insert_id;
		
		foreach ($type as $t) {
			if ($t == 'controller') $t = 'status';
			$this->input->create_input($userid, $nodeid, $t);
		}
		
		return array('success'=>true, 'nodeid'=>$nodeid, 'message'=>'Node successfully registered');
		
	}
	
	public function unregister_node($userid, $nodeid)
	{
		if (!is_numeric($nodeid)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$qresult = $this->mysqli->query("SELECT deviceid FROM devices_nodes WHERE nodeid = $nodeid");
		if ($qresult->num_rows > 0) return array('success'=>false, 'message'=>'Node is currently assigned to a device');
		
		$this->mysqli->query("DELETE FROM node_parameters WHERE userid = $userid AND nodeid = $nodeid");
		
		if ($this->mysqli->affected_rows == 1) {
			return array('success'=>true, 'message'=>'Node unregistered');
		}
		else {
			return array('success'=>false, 'message'=>'Node does not exist');
		}
	}
	
	public function get_nodeid($userid, $driverid, $address) {
		
		$qresult = $this->mysqli->query("SELECT * FROM node_parameters WHERE userid = $userid AND driverid = '".$this->mysqli->real_escape_string($driverid)."' AND address = '".$this->mysqli->real_escape_string($address)."'");
		
		if ($qresult->num_rows == 0) return array('success'=>false, 'message'=>'No matching node');
		
		$node = $qresult->fetch_object();
		
		return array('success'=>true, 'nodeid'=>intval($node->nodeid));
	}
	
	public function get_node($userid, $nodeid) {
		if (!is_numeric($nodeid)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$qresult = $this->mysqli->query("SELECT nodeid, type, driverid, address FROM node_parameters WHERE userid = $userid AND nodeid = $nodeid");
		
		if ($qresult->num_rows == 0) false;
		
		$node = $qresult->fetch_object();
		
		$node->nodeid = intval($node->nodeid);
		
		return $node;
	}
		
	public function get_unassigned_nodes($userid, $type = false) {
		if ($type) {
			$sql = "";
			foreach ($type as $t) $sql.= " AND FIND_IN_SET('".$this->mysqli->real_escape_string($t)."', p.type) > 0";
		}
		
		$qresult = $this->mysqli->query("SELECT p.nodeid, p.type, p.address FROM node_parameters p LEFT JOIN devices_nodes n ON n.userid = $userid AND n.nodeid = p.nodeid WHERE p.userid = $userid".($type ? $sql : "")." GROUP BY p.nodeid HAVING COUNT(n.nodeid) = 0");
		
		$nodes = array();
		while($row=$qresult->fetch_object()) {
			$row->type =explode(',', $row->type);
			$nodes[] = $row;
		}
		
		return $nodes;
	}
	
	private function set_household_processes($userid, $name, $type) {
		$qresult = $this->mysqli->query("SELECT n.nodeid, p.type FROM devices_nodes n LEFT JOIN node_parameters p ON p.nodeid = n.nodeid AND p.userid = $userid LEFT JOIN devices d ON n.deviceid = d.deviceid AND d.templateid = '".$name."' WHERE n.userid = $userid AND NOT ISNULL(d.deviceid)");
		
		$nodes = Array();
		while ($node = $qresult->fetch_object()) {
			if (in_array($type, explode(',', $node->type))) {
				$nodes[] = $node->nodeid;
			}
		}
		
		if (count($nodes) == 0) return false;
		
		if ($type == 'energyIn') $feed = 'in';
		elseif ($type == 'energyOut') $feed = 'out';
		else return false;
		
		$feedname = $name.'_'.$feed;
		if ($name == 'pv') $feedname = 'pv';
		
		$feed_kwh = $this->feed->get_id($userid, $name.'_'.$feed.'_kwh');
		$feed_power = $this->feed->get_id($userid, $name.'_'.$feed.'_power');
		$feed_kwhd = $this->feed->get_id($userid, $name.'_'.$feed.'_kwhd');
		
		$inputs = $this->input->get_inputs($userid);
		
		$processlist = '33:0,';
		foreach ($nodes as $node) {
			$processlist.= '11:'.$inputs[$node][$type]['id'].',';
		}
		$processlist.= '1:'.$feed_kwh.',21:'.$feed_power.',5:'.$feed_kwhd;
		
		foreach ($nodes as $node) {
			$this->input->set_processlist($inputs[$node][$type]['id'], $processlist);
		}
		
		return true;
	}
	
	public function add_device($userid, $name, $templateid, $nodes) {
		if (!is_numeric($templateid) and $templateid != "grid" and $templateid != "storage" and $templateid != "pv") return array('success'=>false, 'message'=>'Invalid input parameter');
		
		if (is_numeric($templateid)) {
			$qresult = $this->mysqli->query("SELECT requiredNodeTypes FROM templates WHERE templateid = $templateid AND $userid = $userid");
			$template = $qresult->fetch_object();
			$requiredNodeTypes = explode(',', $template->requiredNodeTypes);
		}
			
		$qresult = $this->mysqli->query("SELECT nodeid, type FROM node_parameters WHERE $userid = $userid AND FIND_IN_SET(nodeid, '".$this->mysqli->real_escape_string(implode(',', $nodes))."') > 0");
		$selectedNodeTypes = Array();
		$selectedNodes = Array();
		while ($node = $qresult->fetch_object()) {
			$selectedNodeTypes = array_merge($selectedNodeTypes, explode(',', $node->type));
			$selectedNodes[$node->nodeid] = explode(',', $node->type);
			
			if ($this->get_device_by_nodeid($userid, $node->nodeid)) {
				return array('success'=>false, 'message'=>'Node '.$node->nodeid.' is already assigned to a device');
			}
		}
		
		$selectedNodeTypes = array_unique($selectedNodeTypes);
		
		if (is_numeric($templateid) and count($diff = array_diff($requiredNodeTypes, $selectedNodeTypes))) {
			return array('success'=>false, 'message'=>'No nodes selected for '.implode(", ", $diff));
		}
		elseif ($templateid == 'grid' and in_array('energyOut', $selectedNodeTypes)) {
			$this->mysqli->query("INSERT INTO devices (name, templateid, userid) VALUES ('Grid', 'grid', $userid)");
			$deviceid = $this->mysqli->insert_id;
			
			while ($node = each($selectedNodes)) {
				$this->mysqli->query("INSERT INTO devices_nodes (userid, deviceid, nodeid) VALUES ($userid, $deviceid, ".$node['key'].")");
			}
			
			$this->set_household_processes($userid, 'grid', 'energyIn');
			$this->set_household_processes($userid, 'grid', 'energyOut');
			
			return array('success'=>true, 'deviceid'=>$deviceid, 'message'=>'Device successfully added');
		}
		elseif ($templateid == 'pv' and in_array('energyOut', $selectedNodeTypes) ) {
			$this->mysqli->query("INSERT INTO devices (name, templateid, userid) VALUES ('PV', 'pv', $userid)");
			$deviceid = $this->mysqli->insert_id;
			
			while ($node = each($selectedNodes)) {
				$this->mysqli->query("INSERT INTO devices_nodes (userid, deviceid, nodeid) VALUES ($userid, $deviceid, ".$node['key'].")");
			}
			
			$this->set_household_processes($userid, 'pv', 'energyOut');
			
			return array('success'=>true, 'deviceid'=>$deviceid, 'message'=>'Device successfully added');
		}
		elseif ($templateid == 'storage' and in_array('energyIn', $selectedNodeTypes) and in_array('energyOut', $selectedNodeTypes)) {
			$this->mysqli->query("INSERT INTO devices (name, templateid, userid) VALUES ('Storage', 'storage', $userid)");
			$deviceid = $this->mysqli->insert_id;
			
			while ($node = each($selectedNodes)) {
				$this->mysqli->query("INSERT INTO devices_nodes (userid, deviceid, nodeid) VALUES ($userid, $deviceid, ".$node['key'].")");
			}
			
			$this->set_household_processes($userid, 'storage', 'energyIn');
			$this->set_household_processes($userid, 'storage', 'energyOut');
			
			return array('success'=>true, 'deviceid'=>$deviceid, 'message'=>'Device successfully added');
		}
		elseif (!is_numeric($templateid)) {
			return array('success'=>false, 'message'=>'Required nodes for '.$templateid.' not selected');
		}
		else {
			$this->mysqli->query("INSERT INTO devices (name, templateid, userid) VALUES ('".$this->mysqli->real_escape_string($name)."', $templateid, $userid)");
			$deviceid = $this->mysqli->insert_id;
			
			$feedid = Array();
			
			if (in_array('energyIn', $selectedNodeTypes) OR in_array('powerIn', $selectedNodeTypes)) {
				$feedid['energyIn_kwh'] = $this->feed->create($userid, $name, 'device'.$deviceid.'_in_kwh', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
				$feedid['energyIn_power'] = $this->feed->create($userid, $name,'device'.$deviceid.'_in_power', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
				$feedid['energyIn_kwhd'] = $this->feed->create($userid, $name,'device'.$deviceid.'_in_kwhd', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
			}
			if (in_array('energyOut', $selectedNodeTypes) OR in_array('powerOut', $selectedNodeTypes)) {
				$feedid['energyOut_kwh'] = $this->feed->create($userid, $name,'device'.$deviceid.'_out_kwh', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
				$feedid['energyOut_power'] = $this->feed->create($userid, $name,'device'.$deviceid.'_out_power', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
				$feedid['energyOut_kwhd'] = $this->feed->create($userid, $name,'device'.$deviceid.'_out_kwhd', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
			}
			if (in_array('soc', $selectedNodeTypes)) {
				$feedid['soc'] = $this->feed->create($userid, $name,'device'.$deviceid.'_soc', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
			}
			if (in_array('temperature', $selectedNodeTypes)) {
				$feedid['temperature'] = $this->feed->create($userid, $name,'device'.$deviceid.'_temperature', DataType::REALTIME, Engine::PHPTIMESERIES, 'tag=')['feedid'];
			}
			if (in_array('flag', $selectedNodeTypes)) {
				$feedid['flag'] = $this->feed->create($userid, $name,'device'.$deviceid.'_flag', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
			}
			if (in_array('controller', $selectedNodeTypes)) {
				$feedid['status'] = $this->feed->create($userid, $name,'device'.$deviceid.'_status', DataType::REALTIME, Engine::PHPTIMESERIES, false)['feedid'];
			}
			
			while ($node = each($selectedNodes)) {
				$this->mysqli->query("INSERT INTO devices_nodes (userid, deviceid, nodeid) VALUES ($userid, $deviceid, ".$node['key'].")");
				
				$inputs = $this->input->get_inputs($userid);
				$inputs = $inputs[$node['key']];
				
				if (in_array('energyIn', $node['value'])) {
					$this->input->add_process($this->process, $userid, $inputs['energyIn']['id'], 1, $feedid['energyIn_kwh']);
					$this->input->add_process($this->process, $userid, $inputs['energyIn']['id'], 21, $feedid['energyIn_power']);
					$this->input->add_process($this->process, $userid, $inputs['energyIn']['id'], 5, $feedid['energyIn_kwhd']);
				}
				if (in_array('powerIn', $node['value'])) {
					
					
					$this->input->add_process($this->process, $userid, $inputs['powerIn']['id'], 1, $feedid['energyIn_power']);
					$this->input->add_process($this->process, $userid, $inputs['powerIn']['id'], 4, $feedid['energyIn_kwh']);
					$this->input->add_process($this->process, $userid, $inputs['powerIn']['id'], 5, $feedid['energyIn_kwhd']);
					
					$eInid=$this->input->create_input($userid, $node['key'], "energyIn");			
					$this->input->add_process($this->process, $userid, $eInid, 29, $feedid['energyIn_kwh']);
					
				}
				if (in_array('energyOut', $node['value'])) {
					$this->input->add_process($this->process, $userid, $inputs['energyOut']['id'], 1, $feedid['energyOut_kwh']);
					$this->input->add_process($this->process, $userid, $inputs['energyOut']['id'], 21, $feedid['energyOut_power']);
					$this->input->add_process($this->process, $userid, $inputs['energyOut']['id'], 5, $feedid['energyOut_kwhd']);
				}
				if (in_array('powerOut', $node['value'])) {
					
					
					$this->input->add_process($this->process, $userid, $inputs['powerOut']['id'], 1, $feedid['energyOut_power']);
					$this->input->add_process($this->process, $userid, $inputs['powerOut']['id'], 4, $feedid['energyOut_kwh']);
					$this->input->add_process($this->process, $userid, $inputs['powerOut']['id'], 5, $feedid['energyOut_kwhd']);
					
					$eOutid=$this->input->create_input($userid, $node['key'], "energyOut");			
					$this->input->add_process($this->process, $userid, $eOutid, 29, $feedid['energyOut_kwh']);
					
				}
				if (in_array('soc', $node['value'])) {
					$this->input->add_process($this->process, $userid, $inputs['soc']['id'], 1, $feedid['soc']);
				}
				if (in_array('temperature', $node['value'])) {
					$this->input->add_process($this->process, $userid, $inputs['temperature']['id'], 1, $feedid['temperature']);
				}
				if (in_array('flag', $node['value'])) {
					$this->input->add_process($this->process, $userid, $inputs['flag']['id'], 1, $feedid['flag']);
				}
				if (in_array('controller', $node['value'])) {
					$this->input->add_process($this->process, $userid, $inputs['status']['id'], 1, $feedid['status']);
				}
			}
			
			$qresult=$this->mysqli->query("select name from template_conf where idtemplate=$templateid");
		 
			while($row=$qresult->fetch_object())
			{
				$this->mysqli->query("INSERT INTO  user_device_par (deviceid, name,value) VALUES ($deviceid, '$row->name','')");
			}
			
			return array('success'=>true, 'deviceid'=>$deviceid, 'message'=>'Device successfully added');
		}
	}
	
	public function get_device_by_nodeid($userid, $nodeid) {
		if (!is_numeric($nodeid)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$qresult = $this->mysqli->query("SELECT deviceid FROM devices_nodes WHERE userid = $userid AND nodeid = $nodeid");
		
		if ($qresult->num_rows == 0) return false;
		
		$device = $qresult->fetch_object();
		
		return array('success'=>true, 'deviceid'=>intval($device->deviceid));
	}
	
	public function get_device_by_deviceid($userid, $deviceid) {
		if (!is_numeric($deviceid)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$qresult = $this->mysqli->query("SELECT deviceid, name, templateid FROM devices WHERE userid = $userid AND deviceid = $deviceid");
		
		if ($qresult->num_rows == 0) return false;
		
		$device = $qresult->fetch_assoc();
		
		$qresult = $this->mysqli->query("SELECT * FROM devices_nodes WHERE userid = $userid AND deviceid = $deviceid");
		
		$nodes = Array();
		while ($node = $qresult->fetch_object()) {
			$nodes[] = intval($node->nodeid);
		}
		
		$device['deviceid'] = intval($device['deviceid']);
		$device['templateid'] = intval($device['templateid']);
		$device['nodes'] = $nodes;
		
		return $device;
	}
	
	public function remove_device($userid, $deviceid) {
		if (!is_numeric($deviceid)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$this->mysqli->query("DELETE FROM devices WHERE userid = $userid AND deviceid = $deviceid");
		if ($this->mysqli->affected_rows == 1) {
			
			$qresult = $this->mysqli->query("SELECT nodeid FROM devices_nodes WHERE userid = $userid AND deviceid = $deviceid");
			$inputs = $this->input->get_inputs($userid);
			
			while ($node = $qresult->fetch_object()) {
				foreach ($inputs[$node->nodeid] as $i) {
					$this->input->reset_process($i['id']);
				}
			}
			$this->mysqli->query("DELETE FROM devices_nodes WHERE userid = $userid AND deviceid = $deviceid");
			
			return array('success'=>true, 'message'=>'Device deleted');
		} else {
			return array('success'=>false, 'message'=>'Device does not exist or insufficient permissions');
		}
	}
		
	public function list_devices($userid) {
		$qresult = $this->mysqli->query("SELECT d.deviceid AS id, d.name, t.productType, t.operatingType, p.nodeid AS controller FROM devices d  JOIN templates t ON d.templateid=t.templateid JOIN devices_nodes n ON n.userid = $userid AND n.deviceid = d.deviceid  LEFT JOIN node_parameters p ON p.nodeid = n.nodeid AND FIND_IN_SET('controller', p.type) > 0 GROUP BY id ORDER BY id ASC");
		
		$devices = array();
		while($row=$qresult->fetch_object()) {
			$status = -1;
			$feedid = $this->feed->get_id($userid, 'device'.$row->id.'_status');
			if ($feedid != false) {
				$status = $this->feed->get_timevalue($feedid)['value'];
			}
			$row->status = $status;
			
			$devices[] = $row;
		}
		
		return $devices;
	}
	
	public function set_device_status($userid, $deviceid, $status) {
		if (!$this->belongs_to_user($userid, $deviceid)) return array('success'=>false, 'message'=>'Insufficient permissions');
		
		$qresult = $this->mysqli->query("SELECT p.address, p.driverid FROM node_parameters p LEFT JOIN devices_nodes n ON n.userid = $userid AND p.nodeid = n.nodeid WHERE n.deviceid = $deviceid AND FIND_IN_SET('controller', p.type) > 0");
		echo $this->mysqli->error;
		
		while ($row = $qresult->fetch_object()) {
			$cmd = Array('address' => $row->address, 'command' => 'set', 'status' => $status);
			
			include "Modules/devices/$row->driverid/cmd.php";
		}
		
		return array('success'=>true);
	}
	
	public function post_value($userid, $driverid, $address, $type, $value, $time) {
		if (!is_numeric($value) or !is_numeric($time)) return array('success'=>false, 'message'=>'Invalid input parameter');
		
		$mtype = ($type == 'status') ? 'controller' : $type;
		
		$qresult = $this->mysqli->query("SELECT nodeid FROM node_parameters WHERE userid = $userid AND driverid = '".$this->mysqli->real_escape_string($driverid)."' AND address = '".$this->mysqli->real_escape_string($address)."' AND FIND_IN_SET('".$this->mysqli->real_escape_string($mtype)."', type) > 0");
		if ($qresult->num_rows == 0) {
			return array('success'=>false);
		}
		
		$node = $qresult->fetch_object();
		$inputs = $this->input->get_inputs($userid);
		
		$this->input->set_timevalue($inputs[$node->nodeid][$type]['id'], $time, $value);
		if ($inputs[$node->nodeid][$type]['processList']) $this->process->input($time, $value, $inputs[$node->nodeid][$type]['processList']);
		
		return array('success'=>true);
	}
	
	
	public function get_parameters($deviceid,$description) {
		$descriptions = array();
		if($description==True) {
			$qresult = $this->mysqli->query("select template_conf.name,template_conf.description from devices, template_conf where devices.templateid=template_conf.idtemplate and  deviceid=".$deviceid);
			$descriptions = array();
			while ($row = $qresult->fetch_object()) {
				$descriptions[$row->name]=$row->description;
			}
		}
		
		$deviceid = (int) $deviceid;
	  
		if (!$this->redis->exists("user:devices:params:$deviceid")) {
			$this->load_params_to_redis($deviceid);
		}
		
		$params = array();
		$paramsids = $this->redis->sMembers("user:devices:params:$deviceid");
		
		foreach ($paramsids as $id) {
			$row = $this->redis->hGetAll("devices:params:$id");
			if(array_key_exists($row['name'],$descriptions))
				$row['description']=$descriptions[$row['name']];
				$params[] = $row;

		}
		
		return $params;
	}
	
	public function set_parameter($id,$fields) {
		$id = intval($id);
		
		$fields = json_decode(stripslashes($fields));
		
		//error_log("parameter:".$fields->value);
			 
		if (isset($fields->value)) {
			$value=preg_replace('/[^\w\s-]/','',$fields->value);
		}
		//error_log("parameter:".$value);
		$this->mysqli->query("UPDATE user_device_par SET value='".$fields->value."' WHERE `id` = '$id'");
		
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
		
		if ($this->mysqli->affected_rows>0) {
			$this->load_param_to_redis($id);
			
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
		
		foreach ($fields as $name => $value) {
			$this->mysqli->query("UPDATE user_device_par SET value='".$value."' WHERE `deviceid` = '$id' and name='".$name."'");
			$result = $this->mysqli->query("SELECT id FROM user_device_par WHERE `deviceid` = '$id' AND name='".$name."'");
			
			if($row = $result->fetch_object()) {
				$this->load_param_to_redis($row->id);
			}
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
	
	private function load_param_to_redis($paramid)
	{
		$result=$this->mysqli->query("SELECT * FROM user_device_par  WHERE `id` = '$paramid'");
		
		if($row = $result->fetch_object()) {
			$this->redis->hMSet("devices:params:$row->id",array(
				'id'=>$row->id,
				'name'=>$row->name,
				'value'=>$row->value
			));
		}
	}
	
	private function load_params_to_redis($deviceid)
	{
		$result = $this->mysqli->query("SELECT id,name,value FROM user_device_par WHERE `deviceid` = '$deviceid'");
		
		while($row = $result->fetch_object()){
			$this->redis->sAdd("user:devices:params:$deviceid", $row->id);
			$this->redis->hMSet("devices:params:$row->id",array(
				'id'=>$row->id,
				'name'=>$row->name,
				'value'=>$row->value
			));
		}
	}
}