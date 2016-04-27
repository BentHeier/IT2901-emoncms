<?php
include 'connectDB.php';
session_start();
$query="select trial_id.id, device_type.type,trial_id.name from trial_id, device_type where device_type.id=trial_id.type;";
$res = mysqli_query($con,$query) or die("Bad SQL");

 while($row = mysqli_fetch_array($res))	
    echo "{ templates: { id: '".$row[0]."', type: '".$row[1]."', name: '".$row[2]."'}}";
 

?>
<h2>List Templates</h2>


<h3>Smart Meter templates</h3>
select id,name form trial_id where  type=1;

Show template:<br> ---> Create table template info
<h3>Solar Panel templates</h3>

Show template:<br>
Show template:<br>
<h2>Configure an household</h2>

Create a Smart Meter<br>


http://localhost/addDevice.php?json="{type: 1, name: mySmartMeter, template: CS1}"
{deviceId: 1}



Create a Bulb<br>
  http://localhost/addDevice.php?json="{type: 1, name: mybulb, powerin: 100, smartmeter: 1, apikey:ss24 }"
{deviceId: 2}


Create a Storage<br>
  http://localhost/addDevice.php?json="{type: 2, name: mystorage, smartmeter: 1, capacity: 100, apikey:ss24 }"



Create a Solar Panel<br>
  http://localhost/addDevice.php?json="{type: 3, template=CS1, name: pv1,  apikey:ss24 }"
  
<h2>List Devices</h2>



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
<H3>Delete Devices</H3>
