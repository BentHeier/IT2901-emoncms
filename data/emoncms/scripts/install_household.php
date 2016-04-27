<?php
	$apikey = 'YOUR_API_KEY';
	 
	$cloudurl = 'http://cloud.cossmic.eu/emoncms/';
	$cloudapikey = 'YOUR_NBH_API_KEY';
	$household_id = '0';
	

	class EMon {
		private $ch;
		private $url;
		private $apikey;
		
		function __construct($url, $apikey) {
			$this->url = $url;
			$this->apikey = $apikey;
			
			// initialize CURL
			$this->ch = curl_init();
 
			// set basic parameters
			curl_setopt($this->ch, CURLOPT_HEADER, 0);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		}
		
		function getInputs() {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'input/list.json?apikey='.$this->apikey);
			$data = curl_exec($this->ch);
			
			return json_decode($data);
		}
		
		function getInputID($nodeID, $name) {
			$inputs = $this->getInputs();
			
			foreach ($inputs as $input) {
			 if ($input->{'nodeid'} == $nodeID and $input->{'name'} == $name) return intval($input->{'id'});
			}
			
			return -1;
		}
		
		function writeInput($nodeid, $name, $value, $timestamp) {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'input/post.json?apikey='.$this->apikey.'&node='.$nodeid.'&time='.$timestamp.'&json={'.$name.':'.$value.'}');
			$data = curl_exec($this->ch);
			
			return ($data == "ok");
		}
		
		function addFeed($name, $tag, $datatype, $engine) {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'feed/create.json?apikey='.$this->apikey.'&tag='.$tag.'&name='.$name.'&datatype='.$datatype.'&engine='.$engine);
			$data = json_decode(curl_exec($this->ch));
			
			if ($data->{'success'} == 1) return intval($data->{'feedid'});
			
			return false;
		}
		
		function addInputProcess($inputID, $processID, $arg) {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'input/process/add.json?apikey='.$this->apikey.'&inputid='.$inputID.'&processid='.$processID.'&arg='.$arg);
			$data = json_decode(curl_exec($this->ch));
			
			if ($data->{'success'} == 1) return true;
			
			return false;
		}
		
		function resetInputProcess($inputID) {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'input/process/reset.json?apikey='.$this->apikey.'&inputid='.$inputID);
			$data = json_decode(curl_exec($this->ch));
		}
		
		function getFeedID($name) {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'feed/getid.json?apikey='.$this->apikey.'&name='.$name);
			$data = curl_exec($this->ch);
			
			return intval(str_replace("\"", "", $data));
		}
		
		function getFeedValue($id) {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'feed/value.json?apikey='.$this->apikey.'&id='.$id);
			$data = curl_exec($this->ch);
			
			return floatval(str_replace("\"", "", $data));
		}
		
		function getFeedTimeValue($id) {
			curl_setopt($this->ch, CURLOPT_URL, $this->url.'feed/timevalue.json?apikey='.$this->apikey.'&id='.$id);
			$data = json_decode(curl_exec($this->ch));
			
			return Array('time' => intval($data->{'time'}), 'value' => floatval($data->{'value'}));
		}
		
		function close() {
			curl_close($this->ch);
		}
	}
 
	$emon = new EMon('http://localhost/emoncms/', $apikey);
	$cloud = new EMon($cloudurl, $cloudapikey);
 
	echo "Installing household feeds...\r\n";
 
	$inputs = Array('grid_out', 'grid_in', 'storage_out', 'storage_in', 'pv', 
			'grid2household', 'grid2storage', 'storage2grid', 'storage2household', 'pv2grid', 'pv2household', 'pv2storage', 'consumption');
		
	function create_input_and_feeds($emon, $node, $input) {
		if ($node) {
			$prefix = $node.'_';
			$tag = 'Node:' + $node;
		}
		else {
			$prefix = '';
			$tag = 'Household';
		}
		
		$id = $emon->getInputID($node, $input);
		if ($id == -1) {
			echo "Creating input ".$input."...\r\n";
			$emon->writeInput($node, $input, 0, 0);
			$id = $emon->getInputID($node, $input);
		}

		$feed_kwh_name = $prefix.$input.'_kwh';
		$feed_kwh = $emon->getFeedID($feed_kwh_name);
		if (!$feed_kwh) {
			echo "Creating feed ".$feed_kwh_name."...\r\n";
			$feed_kwh = $emon->addFeed($feed_kwh_name, $tag, 1, 2);
		}

		$feed_power_name = $prefix.$input.'_power';
		$feed_power = $emon->getFeedID($feed_power_name);
		if (!$feed_power) {
			echo "Creating feed ".$feed_power_name."...\r\n";
			$feed_power = $emon->addFeed($feed_power_name, $tag, 1, 2);
		}

		$feed_kwhd_name = $prefix.$input.'_kwhd';
		$feed_kwhd = $emon->getFeedID($feed_kwhd_name);
		if (!$feed_kwhd) {
			echo "Creating feed ".$feed_kwhd_name."...\r\n";
			$feed_kwhd = $emon->addFeed($feed_kwhd_name, $tag, 2, 2);
		}
		
		return array('id' => $id, 'kwh' => $feed_kwh, 'power' => $feed_power, 'kwhd' => $feed_kwhd);
	}
		
	for ($i = 0; $i < count($inputs); $i++) {
		$input = $inputs[$i];
		
		$result = create_input_and_feeds($emon, 0, $input);
		
		echo "Set process list for input ".$input."...\r\n";
		$emon->resetInputProcess($result['id']);
		$emon->addInputProcess($result['id'], 35, $result['id']);
		$emon->addInputProcess($result['id'], 1, $result['kwh']);
		$emon->addInputProcess($result['id'], 21, $result['power']);
		$emon->addInputProcess($result['id'], 5, $result['kwhd']);
	}
		
	echo "Setting up household input...\r\n";
	$id = $emon->getInputID(0, "household");
	if ($id == -1) {
		$emon->writeInput(0, "household", 0, 0);
		$id = $emon->getInputID(0, "household");
	}
	$emon->resetInputProcess($id);
	$emon->addInputProcess($id, 39, 0);
	 
	echo "\r\nInstalling household feeds in the cloud...\r\n";
	for ($i = 0; $i < count($inputs); $i++) {
		$input = $inputs[$i];
		
		$result = create_input_and_feeds($cloud, $household_id, $input);
		
		echo "Set process list for input ".$input."...\r\n";
		$cloud->resetInputProcess($result['id']);
		$cloud->addInputProcess($result['id'], 1, $result['kwh']);
		$cloud->addInputProcess($result['id'], 21, $result['power']);
		$cloud->addInputProcess($result['id'], 5, $result['kwhd']);
	}
	
	echo "\r\nInstallation done. Please copy the following line into crontab:\r\n\r\n";
	echo "* * * * * curl --silent --request GET 'http://localhost/emoncms/input/post.json?node=0&json={household:0}&apikey=".$apikey."' >/dev/null\r\n";
?>