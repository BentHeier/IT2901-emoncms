<?php global $path, $session, $user; ?>

<h2><?php echo _('Driver API'); ?></h2>
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
  <tr><td><?php echo _('The driver list view'); ?></td><td><a href="<?php echo $path; ?>driver/node"><?php echo $path; ?>driver/node</a></td></tr>
  <tr><td><?php echo _('This page'); ?></td><td><a href="<?php echo $path; ?>driver/api"><?php echo $path; ?>driver/api</a></td></tr>
  <!--
  <tr><td><?php echo _('Input processing configuration page'); ?></td><td><a href="<?php echo $path; ?>input/process?inputid=1"><?php echo $path; ?>input/process?inputid=1</a></td></tr>
-->
</table>

<h3><?php echo _('Available JSON commands'); ?></h3>
<p><?php echo _('To use the json api the request url needs to include <b>.json</b>'); ?></p>

<p><b><?php echo _('Add driver'); ?></b></p>
<table class="table">
  <tr><td><?php echo _('JSON format:'); ?></td><td><a href="<?php echo $path; ?>driver/add.json?json={type:1}"><?php echo $path; ?>driver/add.json?json={type:1}</a></td></tr>
   <tr><td><?php echo _('List drivers'); ?></td><td><a href="<?php echo $path; ?>driver/list.json"><?php echo $path; ?>driver/list.json</a></td></tr>
  <tr><td><?php echo _('Delete a driver'); ?></td><td><a href="<?php echo $path; ?>driver/delete.json?driverid=1"><?php echo $path; ?>driver/delete.json?driverid=1</a></td></tr>  
  <tr><td><?php echo _('Set driver parameters'); ?></td><td><a href="<?php echo $path; ?>driver/setparameters.json?driverid=6&fields={'polling':2, 'user':26}"><?php echo $path; ?>driver/setparameters.json?driverid=6&fields={'polling':2, 'user':26}</a></td></tr>  

  <!--
  <tr><td><?php echo _('CSV format:'); ?></td><td><a href="<?php echo $path; ?>input/post.json?csv=100,200,300"><?php echo $path; ?>input/post.json?csv=100,200,300</a></td></tr>  
  <tr><td><?php echo _('Assign inputs to a node group'); ?></td><td><a href="<?php echo $path; ?>input/post.json?node=1&csv=100,200,300"><?php echo $path; ?>input/post.json?<b>node=1</b>&csv=100,200,300</a></td></tr>  
  <tr><td><?php echo _('Set the input entry time manually'); ?></td><td><a href="<?php echo $path; ?>input/post.json?time=<?php echo time(); ?>&node=1&csv=100,200,300"><?php echo $path; ?>input/post.json?<b>time=<?php echo time(); ?></b>&node=1&csv=100,200,300</a></td></tr>  
-->
</table>

<p><b><?php echo _('APIKEY'); ?></b><br>
<?php echo _('To post data from a remote device you will need to include in the request url your write apikey. This give your device write access to your emoncms account, allowing it to post data.'); ?></p>
<table class="table">
  <tr><td><?php echo _('For example using the first json type request above just add the apikey to the end like this:'); ?></td><td><a href="<?php echo $path; ?>input/post.json?json={power:200}&apikey=<?php echo $user->get_apikey_write($session['userid']); ?>"><?php echo $path; ?>input/post.json?json={power:200}<b>&apikey=<?php echo $user->get_apikey_write($session['userid']); ?></b></a></td></tr>
</table>



<p><b><?php echo _('Submit a command'); ?></b></p>
<table class="table">
  <tr><td><?php echo _('Submit'); ?></td><td><a href="<?php echo $path; ?>driver/cmd.json?node=2&json={%22cmd%22:%20[{%22parameter%22:%22state%22,%22value%22:%221%22},{%22parameter%22:%22power_in%22,%20%22value%22:%2220%22}]}"><?php echo $path; ?>driver/cmd.json?node=2&json={"cmd":[{"parameter":"state","value":"1"},{"parameter":"power_in", "value":"20"}]}</a></td></tr>
  </table>
