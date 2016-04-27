<?php
function php_to_string($php_file, $new_GET = false, $new_POST = false) {
    // replacing $_GET, $_POST if necessary
    if($new_GET) {
        $old_GET = $_GET;            
        $_GET = $new_GET;
    }
    if($new_POST) {
        $old_POST = $_POST;            
        $_POST = $new_POST;
    }
    ob_start();
    include($php_file);
    // restoring $_GET, $_POST if necessary
    if(isset($old_GET)) {
       $_GET = $old_GET;
    }
    if(isset($old_POST)) {
       $_POST = $old_POST;
    }
    return ob_get_clean();
}

?>
<h2>List Device Type</h2>
  http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'types'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'types'}"));
?>
<h2>List Templates</h2>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates'}"));
?>

<h3>Smart Meter templates</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '4'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '4'}"));
?>
<h3>Solar Panel templates</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '3'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '3'}"));
?>
<h3>Washing Machine templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '12'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '12'}"));
?>
<h3>Dishwasher templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '5'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '5'}"));
?>
<h3>Boiler templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '8'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '8'}"));
?>
<h3>TV templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '9'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '9'}"));
?>
<h3>Video templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '10'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '10'}"));
?>
<h3>PC templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '11'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '11'}"));
?>
<h3>Battery templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '1'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '1'}"));
?>
<h3>Bulb templates</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templates', 'type': '2'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templates', 'type': '2'}"));
?>
<h3>Show Solar Panel template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '15'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '15'}"));
?><br>

<h3>Show DishWasher template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '43'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '43'}"));
?><br>

<h3>Show Washing Machine template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '66'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '66'}"));
?><br>

<h3>Show Boiler template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '62'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '62'}"));
?><br>

<h3>Show TV template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '63'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '63'}"));
?><br>

<h3>Show Video template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '64'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '64'}"));
?><br>

<h3>Show PC template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '65'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '65'}"));
?><br>

<h3>Show Battery template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '1'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '1'}"));
?><br>

<h3>Show Bulb template information</h3>
 http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'templateinfo', 'id': '59'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'templateinfo', 'id': '59'}"));
?><br>

<h2>Configure an household</h2>

<h3>Create a Smart Meter</h3>

http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '50', 'name': 'sm1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add', 'template': '50', 'name': 'sm1', 'user': '0'}"));
?><br>

<h3>Create a Solar Panel</h3>

http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '15', 'name': 'sp1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '15', 'name': 'sp1', 'user': '0'}"));
?><br>
<h3>Create a Dishwasher</h3>

http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add', 'type': '5', 'template': '43', 'name': 'mydw', 'user': '0'}<br>
<?php
 $jsonString= php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '43', 'name': 'mydw', 'user': '0'}"));
 echo $jsonString;
 $jsonString = preg_replace('/[^\w.:\{\},\ ]/','"',$jsonString);
 $jsonString = json_decode($jsonString);
 $washingMachine=$jsonString->id;
?><br>



<h3>Create a Boiler</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '62', 'name': 'boiler1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '62', 'name': 'boiler1', 'user': '0'}"));
?><br>
 
<h3>Create a TV</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '63', 'name': 'TV1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '63', 'name': 'TV1', 'user': '0'}"));
?><br>

<h3>Create a Video</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '64', 'name': 'video1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '64', 'name': 'video1', 'user': '0'}"));
?><br>

<h3>Create a PC</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '65', 'name': 'PC1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '65', 'name': 'PC1', 'user': '0'}"));
?><br>

<h3>Create a Battery</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '1', 'name': 'battery1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '1', 'name': 'battery', 'user': '0'}"));
?><br>

<h3>Create a Bulb</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'add',   'template': '59', 'name': 'bulb1', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'add',  'template': '59', 'name': 'bulb1', 'user': '0'}"));
?><br>



<h2>List Devices</h2>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'list', 'user': '1'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'list', 'user': '1'}"));
?><br>



<h2>Read from device</h2>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'read', 'user': '1', 'device': '1', 'date':'20140709 15:20:00'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'read', 'user': '1', 'device': '3', 'date': '20140709 15:20:00'}"));
?><br>


<h2>Read from device (date interval)</h2>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd':'readinterval','user':'3','device':'756','date1':'20140503 15:20:00','date2':'20140504 15:20:00'}<br>
<?php
  echo php_to_string("device.php",Array('json'=>"{'cmd': 'readinterval', 'user': '3', 'device': '756', 'date1': '20140503 15:20:00', 'date2':'20140504 15:20:00'}"));
?><br>

<h2>Get Load</h2>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'getload', 'template': '43', 'unit': 'e'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'getload', 'template': '43', 'unit': 'e'}"));
?><br>


<h2>Set Device Parameter</h2>

<h2>Start device action</h2>

http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'run', 'user': '0', 'device': ' <?php echo $washingMachine?>', 'action':'start', 'parameters': {'parameter': 'name', 'value': 'v'}, }<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'run', 'user': '0', 'device': '".$washingMachine."', 'date': '20140709  15:20:00'}"));
?><br>

<h3>Create Drivers</h3>
    Allocate node for virtualmeter
<h3>Create Feeds</h3>
<h3>Start Drivers</h3>
<h3>Register Virtual Switch</h3>
<h3>Show Raw Power</h3>

<h3>Show Kwh</h3>
<h3>Show Kwhxd</h3>

<H3>Start Agents</H3>

<H3>Stop Agents</H3>
<H3>Stop Drivers</H3>
<H3>Delete Drivers</H3>
<H3>Delete Inputs</H3>

<h3>Delete a Device</h3>

http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'remove', 'user': '0', 'device': '33'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'remove', 'user': '0', 'device': '33'}"));
?><br>

<h3>Delete all Devices</h3>
http://cloud.cossmic.eu/cossmic/marco/virtualDevices/device.php?json={'cmd': 'removeall', 'user': '0'}<br>
<?php
 echo php_to_string("device.php",Array('json'=>"{'cmd': 'removeall', 'user': '0'}"));
?><br>