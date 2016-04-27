<?php 
  global $path;
  /*$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => "http://localhost:8008/"
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		curl_close($curl);
		$resp=strtr ($resp, array ("'" => '"'));
	
   */
   
   $resp=Array();
   
?>
<ul>
<li>
<a href="/emoncms/input/view"> inputs</a>
</li>

<li>
<a href="/emoncms/devices/devices"> devices</a>
</li>

<li>
<a href="/emoncms/driver/node"> drivers</a>
</li>

<li>
<a href="/emoncms/feed/list"> feeds</a>
</li>


<li>
<a href="/emoncms/mas/node"> tasks</a>
</li>

<li>
<a href="/emoncms/mas/learning.html"> learning</a>
</li>

</ul>
