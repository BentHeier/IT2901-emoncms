<?php 
  global $path;
  $curl = curl_init();
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
	
   
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/mas/Views/mas.js"></script>
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
<div id="apihelphead"><div style="float:right;"><a href="api"><?php echo _('MAS API Help'); ?></a></div></div>

<div class="container">
   
 <div id="localheading"><h2><?php echo _('MAS Settings'); ?></h2></div>
    <div id="nomassettings" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _('Dropbox directory is not set'); ?></h4>
        <p><?php echo _('Dropbox direcotry is needed to start MAS<a href="api"> MAS API</a> as a guide for generating your request.'); ?></p>
    </div>
      <div id="table"></div>
  
  

<!-- First Prototype Only-->


<script>

  var path = "<?php echo $path; ?>";

  // Extend table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
    'id':{'type':"fixed"},
    'name':{'title':'<?php echo _("Name"); ?>','type':"fixed"},
    'value':{'title':'<?php echo _("Value"); ?>','type':"text"},
	
	//Actions
	'edit-action':{'title':'', 'type':"edit"}
  }
  

  
   $("#table").bind("onSave", function(e,id,fields_to_update){
        mas.savesettings(id,fields_to_update);
        updater = setInterval(update, 10000);
    });
  //table.groupprefix = "Driver ";
  //table.groupby = 'id';

  update();

  function update()
  {
    table.data = mas.settings();
    table.draw();
    if (table.data.length != 0) {
      $("#nomassettings").hide();
      $("#apihelphead").show();      
      $("#localheading").show();
    } else {
      $("#nomassettings").show();
      $("#localheading").hide();
      $("#apihelphead").hide(); 
    }
  }



</script>
