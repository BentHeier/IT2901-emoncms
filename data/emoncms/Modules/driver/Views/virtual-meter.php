<?php 
  global $path, $session; 
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/driver/Views/driver.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>
<style>
input[type="text"] {
     width: 88%; 
}

#table td:nth-of-type(1) { width:5%;}
#table td:nth-of-type(2) { width:10%;}
#table td:nth-of-type(3) { width:25%;}

#table td:nth-of-type(7) { width:30px; text-align: center; }
#table td:nth-of-type(8) { width:30px; text-align: center; }
#table td:nth-of-type(9) { width:30px; text-align: center; }
</style>

<br>

<?php


#include 'connectDB.php';
/* TUTTI I TEMPLATES */
$url = "http://localhost/virtualDevices/device.php?json={%22cmd%22:%20%22templates%22}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
$jsonString = json_decode($output);
$templateVector = $jsonString->templates;


/*SOLO I TEMPLATES DEI DEVICE CHE L'UTENTE HA!!!
$url = "http://localhost/virtualDevices/device.php?json={%27cmd%27:%20%27list%27,%20%27user%27:%20%271%27}";  //PRENDERE L'ID USER
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "cossmichg:microgrid");
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
$jsonString = json_decode($output);
$deviceList = $jsonString->devicelist;
*/
?>
	
		<div id="template">
			<h2>Template Section</h2>	
			<form action="connectVMeter.json" METHOD ="GET">
			<label>Select the template (name - type):<br>
			<select name="template" size="20" style="width: 400px;" ><!--multiple="multiple">-->
				<?php
					//TUTTI I TEMPLATES
					echo "<option value=\"{'type':'VH Meter','id':0,'name':'VirtualHMeter'}\">Virtual Household Meter</option>";
				
					foreach($templateVector as $temp)
					{
						echo "<option value=\"{'type':'".$temp->type."','id':".$temp->id.",'name':'".$temp->name."'}\">".$temp->name." - ".$temp->type."</option>";
					}
					
					
				?>
			</select>
			</label><br>
			<label>Insert Device Name<br> <input type="text" name="deviceName">
			</label><br>
			
			<input type="submit" value="Next">
			</form>
		</div>
		
	
