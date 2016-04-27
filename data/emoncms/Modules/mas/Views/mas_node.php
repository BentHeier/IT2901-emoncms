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
   
 <div id="localheading"><h2><?php echo _('MASInterface'); ?></h2>
 <!--<a href="settings">Settings</a></div>-->

    <div id="notasks" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _('Check the MAS is started'); ?></h4>
        <p><?php echo _('MASInterface is the main entry point for interfacing with the task schediling service of Cossmic. Configure your device to post values here, you may want to follow the <a href="api"> MAS helper</a> as a guide for generating your request.'); ?></p>
    </div>
    <table style="margin-left=50" border=1 align=center onload=masStatus()>
		<tr>
			<td>
				<b>Module</b>
			</td>
			<td>
				<b>Status</b>
			</td>
			<td>
				<b>Action</b>
			</td>
		</tr>
		
		<tr>
			<td style=\"text-align:center\" >xmppd</td>
			<td style=\"text-align:center\" id=xmppds> undefined </td>
			<td style=\"text-align:center\" id=xmppda>undefined </td>		
		</tr>
			
		<tr>
			<td>task manager</td>
			<td style=\"text-align:center\"  id=tms> undefined </td>
			<td style=\"text-align:center\" id=tma> undefined </td>		
		</tr>
		
		<tr>
			<td> actor manager</td>
			<td style=\"text-align:center\" id=ams> undefined </td>
			<td style=\"text-align:center\" id=ama> undefined </td>		
		</tr>
		
		
		
    </table>
    <?php
		if(!$resp){
			?>
    <div style="display: none;" id=masweb>Open the Web Interface Here <a  href="http://localhost:8008/" target=_blank>WEBUI</a></div>
		<?php
		    }
		    else{
				?>
	<div  id=masweb>Open the Web Interface Here <a  href="http://localhost:8008/" target=_blank>WEBUI</a></div>
		<?php } ?>
</div>
<a href="http://cloud.cossmic.eu/cossmic/neighborhood/neighbourproduction.html"> Neighborhood production</a>  |  <a href="http://cloud.cossmic.eu/cossmic/neighborhood/neighbourconsumption.html"> Neighborhood Consumption</a>
    <div id="table"></div>
  
  
<script>
	
	function masStatus(){
		
		
		st=mas.status();
		console.log("status:"+st["tm"]);
		if(st["spade"]==1)
			{
				$("#xmppds").html("<div style=\"color:green;text-align:center\">running</div>");
			}
		else
			$("#xmppds").html("<div style=\"color:red;text-align:center\">not running</div>");
		
		if(st["tm"]==1)
			{
			 $("#tms").html("<div style=\"color:green;text-align:center\">running</div>");
			 $("#tma").html("<div style=\"text-align:center\"><a  href=\"#\" onclick=stopMAS(\"tm\")>stop</a></div>");
			}
		else
		{
			$("#tms").html("<div style=\"color:red;text-align:center\">not running</div>");
			 $("#tma").html("<div style=\"text-align:center\"><a href=\"#\" onclick=startMAS(\"tm\")>start</a></div>");
		}
		if(st["am"]==1)
		{
			$("#ams").html("<div style=\"color:green;text-align:center\">running</div>");
			$("#ama").html("<div style=\"color:red;text-align:center\"><a href=\"#\" onclick=stopMAS(\"am\")>stop</a></div>");
		}
		else
		{
			$("#ams").html("<div style=\"color:red;text-align:center\">not running</div>");
			$("#ama").html("<div style=\"text-align:center\"><a  href=\"#\" onclick=startMAS(\"am\")>start</a></div>");
		}
	}
	
function startMAS(agent)
	{
		mas.start(agent);
		masStatus();
	
		}
function stopMAS(agent)
	{
		mas.stop(agent);
		masStatus();
		}


  var path = "<?php echo $path; ?>";

  // Extend table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
    //'id':{'type':"fixed"},
    'id':{'title':'<?php echo _("TaskId:"); ?>','type':"fixed"},
    'EST':{'title':'<?php echo _("EST"); ?>','type':"fixed"},
    'LST':{'title':'<?php echo _('LST'); ?>','type':"fixed"},
	'status':{'title':'<?php echo _("Status:"); ?>','type':"text"},
	'AST':{'title':'<?php echo _("AST:"); ?>','type':"text"},
	
	//Actions
	'edit-action':{'title':'', 'type':"edit"},
	'delete-action':{'title':'', 'type':"delete"}
  }
  
  $("#table").bind("onDelete", function(e,id){
    mas.remove(id); 
    update();
  });
  
   $("#table").bind("onSave", function(e,id,fields_to_update){
        mas.set(id,fields_to_update);
        updater = setInterval(update, 10000);
    });
  //table.groupprefix = "Driver ";
  //table.groupby = 'id';

  update();

  function update()
  {
    table.data = mas.list();
    table.draw();
    if (table.data.length != 0) {
      $("#notasks").hide();
      $("#apihelphead").show();      
      $("#localheading").show();
    } else {
      $("#notasks").show();
      $("#localheading").hide();
      $("#apihelphead").hide(); 
    }
      masStatus();
  }



</script>
