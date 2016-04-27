<?php 
	global $path; 
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/devices/Views/devices.js"></script>

<style>
	input[type="text"] {
		width: 88%;	
	}
	
	<?php 
		if ($status == 1) {
			$action = "Stopping";
		}
		else if ($status == 0) {
			$action = "Starting";
		}
		else {
			$action = "Error";
		}
	?>
</style>

<br>
<div id="apihelphead"><div style="float:right;"><a href="api"><?php echo _('Device API Help'); ?></a></div></div>

<div class="container">
	<div id="localheading"><h2><?php echo _($action.' '.$name); ?></h2></div>
	
	<div id="loading"><img src="/emoncms/images/loader.gif"/></div>
	<div id="message"></div>

    <div id="nocontroller" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _($action); ?></h4>
        <p><?php echo _($name.' cannot be controlled'); ?></p>
    </div>
</div>

<script>
	var path = "<?php echo $path; ?>";

	// Extend table library field types
	toggle();
 	
	function toggle() {
		var status = <?php echo $status; ?>;
		if (status != -1) {
			$("#nocontroller").hide();
			$("#apihelphead").show();			
			$("#localheading").show();
			
			var result=devices.set_status(<?php echo $deviceid?>, 1-status);
			$("#loading").hide();
			
			msg='';
			if(result)
				msg='successful'
			else 
				msg='failed'
				
			$("#message").html('<?php echo $action.' '.$name?> <b>'+msg+'</b> <a href="<?php echo "/emoncms/devices/view"?>">back</a>');
			
		} else {
			$("#nocontroller").show();
			$("#localheading").hide();
			$("#apihelphead").hide(); 
			
			$("#loading").hide();
			$("#message").hide();
		}
	}

	//	var updater = setInterval(startstop, 2000);
	/*
	$("#table").bind("onEdit", function(e){
		clearInterval(updater);
	});

	$("#table").bind("onSave", function(e,id,fields_to_update){
		input.set(id,fields_to_update); 
		updater = setInterval(update, 10000);
	});

	$("#table").bind("onDelete", function(e,id){
		driver.remove(id); 
		update();
	});
	*/
</script>