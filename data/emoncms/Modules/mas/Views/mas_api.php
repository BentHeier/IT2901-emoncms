<?php global $path, $session, $user; ?>

<h2><?php echo _('MAS API'); ?></h2>
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
  <tr><td><?php echo _('This page'); ?></td><td><a href="<?php echo $path; ?>mas/api"><?php echo $path; ?>mas/api</a></td></tr>
  <!--
  <tr><td><?php echo _('Input processing configuration page'); ?></td><td><a href="<?php echo $path; ?>input/process?inputid=1"><?php echo $path; ?>input/process?inputid=1</a></td></tr>
-->
</table>

<h3><?php echo _('Available JSON commands'); ?></h3>
<p><?php echo _('To use the json api the request url needs to include <b>.json</b>'); ?></p>

<p><b><?php echo _('Tasks Management'); ?></b></p>
<table class="table">
	
  <?php
   
   $date = new DateTime();
   $EST = $date->format('Y-m-d H:i');
   $date->add(new DateInterval('PT3M'));
   $LST = $date->format('Y-m-d H:i');
  
  ?>
  <tr><td><?php echo _('Add a single-run'); ?></td><td><a href="<?php echo $path; ?>mas/add.json?json={'EST': '<?php echo $EST?>','LST': '<?php echo $LST?>','deviceID': '137', 'execution_type': 'single_run', 'mode' : 1}"><?php echo $path; ?>mas/add.json?json={'EST': '<?php echo $EST;?>','LST': '<?php echo $LST;?>','deviceID': '137', 'execution_type': 'single_run', 'mode' : 1}</a></td></tr>
   
  <tr><td><?php echo _('List tasks ids and status'); ?></td><td><a href="<?php echo $path; ?>mas/list.json"><?php echo $path; ?>mas/list.json</a></td></tr>
  <tr><td><?php echo _('List tasks with  status=0 and getting all info'); ?></td><td><a href="<?php echo $path; ?>mas/list.json?json={'status':0, 'info':true}"><?php echo $path; ?>mas/list.json?json={'status':0, 'info':true}</a></td></tr>
  
  
  <tr><td><?php echo _('Delete a task'); ?></td><td><a href="<?php echo $path; ?>mas/delete.json?id=1"><?php echo $path; ?>mas/delete.json?id=1</a></td></tr>  
  <tr><td><?php echo _('Get task Info'); ?></td><td><a href="<?php echo $path; ?>mas/get.json?id=1"><?php echo $path; ?>mas/get.json?id=1</a></td></tr>  
  <tr><td><?php echo _('Update a task'); ?></td><td><a href="<?php echo $path; ?>mas/update.json?id=44&json={'status':1, 'AST':'2015-05-14 18:00'}"><?php echo $path; ?>mas/update.json?id=44&json={'status':1, 'AST':'18:00'}</a></td></tr>  
  
  <!--
  <tr><td><?php echo _('CSV format:'); ?></td><td><a href="<?php echo $path; ?>input/post.json?csv=100,200,300"><?php echo $path; ?>input/post.json?csv=100,200,300</a></td></tr>  
  <tr><td><?php echo _('Assign inputs to a node group'); ?></td><td><a href="<?php echo $path; ?>input/post.json?node=1&csv=100,200,300"><?php echo $path; ?>input/post.json?<b>node=1</b>&csv=100,200,300</a></td></tr>  
  <tr><td><?php echo _('Set the input entry time manually'); ?></td><td><a href="<?php echo $path; ?>input/post.json?time=<?php echo time(); ?>&node=1&csv=100,200,300"><?php echo $path; ?>input/post.json?<b>time=<?php echo time(); ?></b>&node=1&csv=100,200,300</a></td></tr>  
-->
</table>

<p><b><?php echo _('Device Management'); ?></b></p>
<table class="table">
  <tr><td><?php echo _('Connect a device'); ?></td><td><a href="<?php echo $path; ?>mas/connection.json?json={'device':137,'connection':'on'}"><?php echo $path; ?>mas/connection.json?json={'device':137,'connection':'on'}</a></td></tr>
  <tr><td><?php echo _('Override e-car default configuration'); ?></td><td><a href="<?php echo $path; ?>mas/connection.json?json={'device':137,'connection':'on', 'configuration':{'minimum_energy_target':30,'target_deadline':'2015-09-22 21:00','energy_target':70}}"><?php echo $path; ?>mas/connection.json?json={'device':137,'connection':'on','configuration':{'minimum_energy_target':30,'target_deadline':'2015-09-22 21:00','energy_target':70}}</a></td></tr>
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
