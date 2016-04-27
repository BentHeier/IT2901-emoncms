<?php global $path, $session, $user; ?>

<h2><?php echo _('Devices API'); ?></h2>
<h3><?php echo _('Apikey authentication'); ?></h3>
<p><?php echo _('If you want to call any of the following actions when your not logged in, add an apikey to the URL of your request: &apikey=APIKEY.'); ?></p>
<p><b><?php echo _('Read only:'); ?></b><br>
<input type="text" style="width:255px" readonly="readonly" value="<?php echo $user->get_apikey_read($session['userid']); ?>" />
</p>
<p><b><?php echo _('Read & Write:'); ?></b><br>
<input type="text" style="width:255px" readonly="readonly" value="<?php echo $user->get_apikey_write($session['userid']); ?>" />
</p>

<h3><?php echo _('Available HTML URLs'); ?></h3>
<table class="table">
  <tr><td><?php echo _('The devices list view'); ?></td><td><a href="<?php echo $path; ?>devices/devices"><?php echo $path; ?>devices/devices</a></td></tr>
  <tr><td><?php echo _('This page'); ?></td><td><a href="<?php echo $path; ?>devices/api"><?php echo $path; ?>devices/api</a></td></tr>
  <!--
  <tr><td><?php echo _('Input processing configuration page'); ?></td><td><a href="<?php echo $path; ?>input/process?inputid=1"><?php echo $path; ?>input/process?inputid=1</a></td></tr>
-->
</table>

<h3><?php echo _('Available JSON commands'); ?></h3>
<p><?php echo _('To use the json api the request url needs to include <b>.json</b>'); ?></p>

<p><b><?php echo _('Post data'); ?></b></p>
<table class="table">
    <tr><td><?php echo _('Post to node:'); ?></td><td><a href="<?php echo $path; ?>devices/post.json?driverid=MyDriver&address=abc&type=energyIn&value=200"><?php echo $path; ?>devices/post.json?driverid=MyDriver&address=abc&type=energyIn&value=200</a></td></tr>
    <tr><td><?php echo _('Set the input entry time manually'); ?></td><td><a href="<?php echo $path; ?>devices/post.json?driverid=MyDriver&address=abc&type=energyIn&value=200&time=<?php echo time(); ?>"><?php echo $path; ?>devices/post.json?driverid=MyDriver&address=abc&type=energyIn&value=200&time=<b>time=<?php echo time(); ?></b></a></td></tr>
</table>

<p><b><?php echo _('APIKEY'); ?></b><br>
<?php echo _('To post data from a remote device you will need to include in the request url your write apikey. This give your device write access to your emoncms account, allowing it to post data.'); ?></p>
<table class="table">
    <tr><td><?php echo _('For example using the first json type request above just add the apikey to the end like this:'); ?></td><td><a href="<?php echo $path; ?>devices/post.json?driverid=MyDriver&address=abc&type=energyIn&value=200&apikey=<?php echo $user->get_apikey_write($session['userid']); ?>"><?php echo $path; ?>devices/post.json?driverid=MyDriver&address=abc&type=energyIn&value=200<b>&apikey=<?php echo $user->get_apikey_write($session['userid']); ?></b></a></td></tr>
</table>

<p><b><?php echo _('Nodes'); ?></b></p>
<table class="table">
  <tr><td><?php echo _('Register a node:'); ?></td><td><a href="<?php echo $path; ?>devices/node/register.json?driverid=MyDriver&address=abc&type={energyIn,temperature}"><?php echo $path; ?>devices/node/register.json?driverid=MyDriver&address=abc&type={energyIn,temperature}</a></td></tr>
  <tr><td><?php echo _('List unassigned nodes'); ?></td><td><a href="<?php echo $path; ?>devices/node/getunassigned.json"><?php echo $path; ?>devices/node/getunassigned.json</a></td></tr>
  <tr><td><?php echo _('List unassigned nodes with a specific type'); ?></td><td><a href="<?php echo $path; ?>devices/node/getunassigned.json?type=temperature"><?php echo $path; ?>devices/node/getunassigned.json?type=temperature</a></td></tr>
  <tr><td><?php echo _('List unassigned nodes with specific types'); ?></td><td><a href="<?php echo $path; ?>devices/node/getunassigned.json?type={energyIn,temperature}"><?php echo $path; ?>devices/node/getunassigned.json?type={energyIn,temperature}</a></td></tr>
  <tr><td><?php echo _('Get a node'); ?></td><td><a href="<?php echo $path; ?>devices/node/get.json?driverid=MyDriver&address=abc"><?php echo $path; ?>devices/node/get.json?driverid=MyDriver&address=abc</a></td></tr>  
  <tr><td><?php echo _('Unregister a node'); ?></td><td><a href="<?php echo $path; ?>devices/node/unregister.json?nodeid=1"><?php echo $path; ?>devices/node/unregister.json?nodeid=1</a></td></tr>  
</table>

<p><b><?php echo _('Templates'); ?></b></p>
<table class="table">
  <tr><td><?php echo _('Add a template:'); ?></td><td><a href="<?php echo $path; ?>devices/template/add.json?productname=MyWasherTemplate&producttype=washing-machine&operatingtype=single_run&requirednodetypes={energyIn,controller}&modes={60°C,30°C}"><?php echo $path; ?>devices/template/add.json?productname=MyWasherTemplate&producttype=washing-machine&operatingtype=single_run&requirednodetypes={energyIn,controller}&modes={60°C,30°C}</a></td></tr>
  <tr><td><?php echo _('List templates'); ?></td><td><a href="<?php echo $path; ?>devices/template/list.json"><?php echo $path; ?>devices/template/list.json</a></td></tr>
  <tr><td><?php echo _('Delete a template'); ?></td><td><a href="<?php echo $path; ?>devices/template/remove.json?templateid=1"><?php echo $path; ?>devices/template/remove.json?templateid=1</a></td></tr>
  <tr><td><?php echo _('Get template configuration'); ?></td><td><a href="<?php echo $path; ?>devices/template/conf.json?templateid=2"><?php echo $path; ?>devices/template/conf.json?templateid=1</a></td></tr>   
  <tr><td><?php echo _('Get template parameter info'); ?></td><td><a href="<?php echo $path; ?>devices/template/parameterinfo.json?templateid=2&name=capacity"><?php echo $path; ?>devices/template/parameterinfo.json?templateid=1&name=capacity</a></td></tr>  
</table>

<p><b><?php echo _('Devices'); ?></b></p>
<table class="table">
  <tr><td><?php echo _('Add a device:'); ?></td><td><a href="<?php echo $path; ?>devices/device/add.json?name=MyWasher&templateid=1&nodes={2,4,5}"><?php echo $path; ?>devices/device/add.json?name=MyWasher&templateid=1&nodes={2,4,5}</a></td></tr>
  <tr><td><?php echo _('List devices'); ?></td><td><a href="<?php echo $path; ?>devices/device/list.json"><?php echo $path; ?>devices/device/list.json</a></td></tr>
  <tr><td><?php echo _('Get device assigned with a node'); ?></td><td><a href="<?php echo $path; ?>devices/device/get.json?nodeid=2"><?php echo $path; ?>devices/device/get.json?nodeid=2</a></td></tr>  
  <tr><td><?php echo _('Set device status'); ?></td><td><a href="<?php echo $path; ?>devices/device/status.json?deviceid=1&status=1"><?php echo $path; ?>devices/device/status.json?deviceid=1&status=1</a></td></tr>  
  <tr><td><?php echo _('Delete a device'); ?></td><td><a href="<?php echo $path; ?>devices/device/remove.json?deviceid=1"><?php echo $path; ?>devices/device/remove.json?deviceid=1</a></td></tr>  
  <tr><td><?php echo _('Get device configuration'); ?></td><td><a href="<?php echo $path; ?>devices/device/parameters.json?deviceid=127"><?php echo $path; ?>devices/device/parameters.json?deviceid=127</a></td></tr>  
  <tr><td><?php echo _('Get device configuration and parameters descriptions'); ?></td><td><a href="<?php echo $path; ?>devices/device/parameters.json?deviceid=8&description=true"><?php echo $path; ?>devices/device/parameters.json?deviceid=8&description=true</a></td></tr>  
  <tr><td><?php echo _('Set device parameters'); ?></td><td><a href="<?php echo $path; ?>devices/device/setparameters.json?deviceid=2&fields={'orientation':43, 'position':56}"><?php echo $path; ?>devices/device/setparameters.json?deviceid=2&fields={'orientation':43, 'position':56}</a></td></tr>
</table>
