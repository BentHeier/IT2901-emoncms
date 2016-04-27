<?php

// Get cURL resource
$url=$parameters['url']."?device=".$parameters['device']."&json=".json_encode($cmd);
error_log("$url");
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
$result="ok";

?>
