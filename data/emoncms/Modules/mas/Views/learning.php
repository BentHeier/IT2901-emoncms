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

<script type="text/javascript" src="<?php echo $path; ?>Modules/mas/script/plotly-latest.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/mas/Views/mas.js"></script>



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






<div id="apihelphead"><div style="float:right;"><a href="api"><?php echo _('MAS API Help'); ?></a></div></div>

<div class="container">
 <h2> Available Device Profiles</h2>
 <div id="table"></div> 
 <div id="pinfo"></div>
 
 <div id="ts">
	 </div>
	 
</div>

<script>

function viewprofile(id)
{
 
   /*
	var profile = mas.profile(id);
	
	$('#pinfo').html('<table><tr><td><b>Profile Type</b></td><td>'+profile["profile_type"]+'</td></tr></table>');
	*/

   

	var tsprofile = mas.tsprofile(id);

	var data = [tsprofile];
	
	Plotly.newPlot('ts', data);
} 

</script>

<script>
	
	 

	
 var path = "<?php echo $path; ?>";
 
 
 
 
 
   // Extend table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
    'id':{'type':"fixed"},
    'deviceid':{'title':'<?php echo _("device"); ?>','type':"fixed"},
    'modeid':{'title':'<?php echo _("mode"); ?>','type':"fixed"},
    //'type':{'title':'<?php echo _('type'); ?>','type':"fixed"},
	
	//Actions
	'view-action':{'title':'', 'type':"iconjs", 'link':'viewprofile'},
  }
  
  
  
  //table.groupprefix = "Driver ";
  //table.groupby = 'id';

  update();

  function update()
  {
    table.data = mas.profiles();
    table.draw();
  }
 
 
 
 
</script>
