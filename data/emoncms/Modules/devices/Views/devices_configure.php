<?php 
	global $path; 
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/devices/Views/devices.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>

<style>
	input[type="text"] {
		width: 88%; 
	}
	
	#table td:nth-of-type(1) { width:5%;}
	#table td:nth-of-type(2) { width:25%;}
	#table td:nth-of-type(3) { width:60%;}
	#table td:nth-of-type(4) { width:8%; text-align: center; }
	
	#table td:nth-of-type(7) { width:15px; text-align: center; }
	#table td:nth-of-type(8) { width:15px; text-align: center; }
	#table td:nth-of-type(9) { width:15px; text-align: center; }
</style>

<br>
<div id="apihelphead"><div style="float:right;"><a href="api"><?php echo _('Devices API Help'); ?></a></div></div>

<div class="container">
	<div id="localheading"><h2><?php echo _($name.' configurations'); ?></h2></div>
	<div id="table"></div>
	
	<div id="noparameters" class="alert alert-block hide">
		<h4 class="alert-heading"><?php echo _('No configuration found'); ?></h4>
		<p><?php echo _('Devices parameters can provide a detailed configuration for your device. You may want to follow the <a href="api"> Device helper</a> as a guide for generating your request.'); ?></p>
	</div>
</div>

<script>
	var path = "<?php echo $path; ?>";

	// Extend table library field types
	for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

	table.element = "#table";

	table.fields = {
		//'id':{'type':"fixed"},
		'id':{'title':'<?php echo _("Id"); ?>','type':"fixed"},
		'name':{'title':'<?php echo _("Parameter"); ?>','type':"fixed"},
		'description':{'title':'<?php echo _('Description'); ?>','type':"fixed"},
		'value':{'title':'<?php echo _("Value"); ?>','type':"text"},
		
		// Actions
		'edit-action':{'title':'', 'type':"edit"}//,
		//'delete-action':{'title':'', 'type':"delete"}
	}
	
	update();
	
	function update()
	{
		table.data = devices.get_parameters(<?php echo $deviceid ?>);
		table.draw();
		if (table.data.length != 0) {
			$("#noparameters").hide();
			$("#apihelphead").show();			
			$("#localheading").show();
		} else {
			$("#noparameters").show();
			$("#localheading").hide();
			$("#apihelphead").hide(); 
		}
	}
	
	//	var updater = setInterval(update, 10000);
	/*
	$("#table").bind("onEdit", function(e){
		clearInterval(updater);
	});
	*/
	$("#table").bind("onSave", function(e,id,fields_to_update) {
		devices.set(id,fields_to_update);
	});
	/*
	$("#table").bind("onDelete", function(e,id){
		driver.remove(id); 
		update();
	});
	*/
</script>