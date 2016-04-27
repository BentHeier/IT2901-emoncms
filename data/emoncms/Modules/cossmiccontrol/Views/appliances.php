<?php
global $path, $mysqli, $session;
$decomposedPath = explode("/", "$path");
$vdPath = "";
foreach ($decomposedPath as &$value) {
	if ( (strcmp($value, "http:") == 0) || (strcmp($value, "https:") == 0)) {
			$vdPath .= $value . "//";
	}
	else {
		if ( (empty($value) == false)&& (strcmp($value, "emoncms") !== 0)) {
			$vdPath .= $value . "/";
		}
	}
}
// debugging echo implode("-",$decomposedPath); echo sizeof($decomposedPath); echo $vdPath;
?>

	<!-- Stylesheets -->
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/font-awesome-4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/icons/customIcons.css">
	
	<!-- Javascripts -->
	<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo $path; ?>Modules/cossmiccontrol/Views/json.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.time.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.stack.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.axislabels.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.orderBars.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Modules/cossmiccontrol/Views/history.js"></script>
	<script type="text/javascript" src="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
	
	<script>
		var path = "<?php echo $path; ?>";
		var first_year = 2013;
		var today = new Date();
		var current_year = today.getFullYear();
		var current_month = today.getMonth();
	</script>
	
	<div id="appliances">
		<div class="row">
			<div class="span2">
				<!-- Narrow panel containing the list of all household appliances -->
					<div id="deviceListPanel" class="panel span2">
					<div class="panel-heading">Appliances</div>
					<ul id="deviceList" class="nav nav-list"></ul>
				</div>
				<p>
				<!-- Narrow panel for adding devices -->
				<div id="addDevicePanel" class="panel span2">
					<div class="panel-heading">Unassigned Nodes</div>
					<ul id="unassignedNodesList"class="nav nav-list"></ul>
					<button class="btn btn-block" id="addNewDeviceButton"onclick="addDevice()" type="button">Add Device</button>
				</div>
				<p>
				<div class="span2">
					<button class="btn btn-block" id="showGraphButton"onclick="showGraphs()" type="button">Show Graphs</button>
				</div>
			</div>
	
			<div class="span10">
				<!-- Panel spanning the remaining page width, containing the graph of appliances power consumption -->
				<div id="deviceGraph" class="panel span10 bcolor">
					<div class="panel-heading">Appliance power consumption</div>
					<!-- graph goes here -->
					<table style="width:100%">
						<tr>
							<td>
								<div id="tabs_d" class="tabs">
									<ul>
										<li id="tab1_d"><a href="#tabs-1-d">Day</a>
										</li>
										<li id="tab2_d"><a href="#tabs-2-d">Month</a>
										</li>
										<li id="tab3_d"><a href="#tabs-3-d">Year</a>
										</li>
										<li id="tab4_d"><a href="#tabs-4-d">Total</a>
										</li>
									</ul>
									
									<div id="tabs-1-d">
										<div class="content">
											<table>
												<tr>
													<td>
														<div class="demo-container">
														 <div id="placeholder_day" class="demo-placeholder"></div>
														</div>
													</td>
													<td style="width:20%">
														<div id = "choices_day_d"></div>
													</td>
												</tr>
												<tr>
													<td>
														<table style="width:100%">
															<tr>
																<td align="center">
																	<span>
																		<input id="prevbtn_day_d" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/prevbtn.gif"></input>
																		<input id="date_from_calendar_d" type="text" readonly="readonly" style="cursor:text; text-align:center"></input>
																		<script type="text/javascript">
																			var today = new Date();
																			document.getElementById('date_from_calendar_d').value = ds_format_date(today.getDate(),today.getMonth()+1,today.getFullYear());
																		</script>
																		<input id="nextbtn_day_d" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/nextbtn.gif"></input>
																	</span>
																</td>
															</tr>
														</table>
													</td>
													<td></td>
												</tr>
											</table>
										</div>
									</div>
		
									<div id="tabs-2-d">
										<div class="content">
											<table>
												<tr>
													<td>
														<div class="demo-container">
															<div id="placeholder_month" class="demo-placeholder"></div>
														</div>
													</td>
													<td style="width:20%">
														<div id = "choices_month_d"></div>
													</td>
												</tr>
												<tr>
													<td>
														<table style="width:100%">
															<tr>
																<td align="center">
																	<span>
																		<input id="prevbtn_month_d" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/prevbtn.gif"></input>
																		<select id="select_month_month_d">
																			<option value="0">January</option>
																			<option value="1">February</option>
																			<option value="2">March</option>
																			<option value="3">April</option>
																			<option value="4">May</option>
																			<option value="5">June</option>
																			<option value="6">July</option>
																			<option value="7">August</option>
																			<option value="8">September</option>
																			<option value="9">October</option>
																			<option value="10">November</option>
																			<option value="11">December</option>
																		</select>
																			<select id="select_month_year_d">
																		</select>
																		<script type="text/javascript">
																			create_year_dropdown("#select_month_year_d", first_year, current_year);
																										// preselect current month
																			(document.getElementById('select_month_month_d')).value = current_month;
																			// preselect current year
																			(document.getElementById('select_month_year_d')).value = current_year;
																		</script>
																		<input id="nextbtn_month_d" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/nextbtn.gif"></input>
																	</span>
																</td>
															</tr>
														</table>
													</td>
												<td></td>
												</tr>
											</table>
										</div>
									</div>
									
									<div id="tabs-3-d">
										<div class="content">
											<table>
												<tr>
													<td>
														<div class="demo-container">
															<div id="placeholder_year" class="demo-placeholder"></div>
														</div>
													</td>
													<td style="width:20%">
														<div id = "choices_year_d"></div>
													</td>
												</tr>
												<tr>
													<td>
														<table style="width:100%">
															<tr>
																<td align="center">
																	<span>
																		<input id="prevbtn_year_d" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/prevbtn.gif"></input>
																		<select id="select_year_year_d"></select>
																		<script type="text/javascript">
																			create_year_dropdown("#select_year_year_d", first_year, current_year);
																			// preselect current year
																			(document.getElementById('select_year_year_d')).value = current_year;
																		</script>
																		<input id="nextbtn_year_d" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/nextbtn.gif"></input>
																	</span>
																</td>
															</tr>
														</table>
													</td>
													<td></td>
												</tr>
											</table>
										</div>
									</div>
									
									<div id="tabs-4-d">
										<div class="content">
											<table>
												<tr>
													<td>
														<div class="demo-container">
															<div id="placeholder_total" class="demo-placeholder"></div>
														</div>
													</td>
													<td style="width:20%">
														<div id = "choices_total_d"></div>
													</td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<!-- end of deviceGraph div -->
		
				<!-- Adding devices interaction -->
				<div id="addDeviceFullPanel" class="span10">
					<div id="selectTemplateDiv" class="panel span6 bcolor">
						Choose a template:
						<select class="span4" id="templateSelectList"></select>
					</div>
					<div id="selectNodeDiv"class="panel span6 bcolor">
						<label>Assign Nodes:</label>
							<ul class="unstyled" id="nodeTypeList"></ul>
							<form class="form-inline">
						<label>Device Name:</label>
						<input id="assignedDeviceName" type="text" placeholder="Add a name for your device">
						<button id="confirmButton" class="btn btn-success" type="button" onclick="confirmAssignment()">Confirm</button>
						</form>
					</div>
				</div>
		 	</div>	
				
			<!-- -->
			<div class="span10">
				<div id="configDevices"class="panel span6 bcolor">
			</div></div>
		</div>
	</div>
	
	<script type="text/javascript">
	
		$(document).ready( function () {
			showGraphs();
			initList();
			highlightPageLink();
			$('#chargeDeadlineDatetimepicker').datetimepicker({pickDate: false, pickSeconds: false});
		});
		
		function initList() {
			//Ajax call to get all the devices and stuff them in the deviceList. Only need the name since no computing is to be done on them
			$.ajax({
				url: '<?php echo $vdPath; ?>emoncms/devices/device/list.json',
				type: 'get',
				success: function(output) {
					$.each(output, function(idx, item){
		  				var listItem = $('<li><a  href="#" onclick="configDevice(' + item.id + ',\''+ item.name + '\');">' + item.name +'</li>');
		  				$("#deviceList").append(listItem);
					});
				},
				error: function(xhr, desc, err) {
							console.log(xhr);
							console.log("Details: " + desc + "\nError:" + err);
				}
			}); // end ajax call
		
			// get unassigned devices
			$.ajax({
				url: '<?php echo $path; ?>devices/node/getunassigned.json',
				type: 'get',
				dataType: 'json',
				success: function(output) {
					// if the unassignedNodeList is empty, I remove the addDevice button as there areno unassigned nodes
					if(output.length == 0){
						$("#addDevicePanel").hide();
					}else{
						$.each(output, function(idx, item){
							var listItem = $('<li>' + item.address +'</li>');
							// depending on the type, we add some icons
							if( ($.inArray("powerOut", item.type) > -1) || ($.inArray("energyOut", item.type) > -1) ) {
								$(listItem).prepend('<i class="fa fa-sun-o title="energy generation node""></i>');
							}
							if( ($.inArray("powerIn", item.type) > -1) || ($.inArray("energyIn", item.type) > -1) ) {
								$(listItem).prepend('<i class="fa fa-plug" title="consuming node"></i>');
							}
							if( ($.inArray("controller", item.type) > -1)  ) {
								$(listItem).prepend('<i class="fa fa-gear" title="control node"></i>');
							}              
							if( ($.inArray("temperature", item.type) > -1)) {
								$(listItem).prepend('<i class="icon-temperature" title="temperature node"></i>');
							}
							$("#unassignedNodesList").append(listItem);
						});
					}
				},
				error: function(xhr, desc, err){
					console.log(xhr);
					console.log("Details: " + desc + "\nError:" + err);
				}
			}); // end ajax call
		}
	
		//function to find what page we are on and add the currentLink id to that navbar link to highlight current page
		function highlightPageLink(){
			//Dirty change of the first tab color (since that's where user will start)
			document.getElementById("tab1_d").style.background = "#1192d3 url(\"images/ui-bg_glass_75_e6e6e6_1x400.png\") 50% 50% repeat-x";
		
			var a = document.getElementsByTagName("a");
			for(var i=0;i<a.length;i++){
				if(a[i].href.split("#")[0] == window.location.href.split("#")[0]){
					a[i].id = "currentLink";
				}
				//Sections below add an onclick event listener to the tabs of the graph, firing the tabClicked function when one of the tabs are selected
				if(a[i].href.split("#")[1] == "tabs-1-d"){
					a[i].addEventListener("click", tabClicked, false);
				}
				if(a[i].href.split("#")[1] == "tabs-2-d"){
					a[i].addEventListener("click", tabClicked, false);
				}
				if(a[i].href.split("#")[1] == "tabs-3-d"){
					a[i].addEventListener("click", tabClicked, false);
				}
				if(a[i].href.split("#")[1] == "tabs-4-d"){
					a[i].addEventListener("click", tabClicked, false);
				}
			}
		}
	
		//Function to change the color of the selected tab in order to highlight where user is
		function tabClicked(event){
			event = event;
			var target = event.target.parentElement;
			for(var y=1;y<5;y++){
				if(target.id == "tab"+y+"_d"){
					document.getElementById(target.id).style.background = "#1192d3 url(\"images/ui-bg_glass_75_e6e6e6_1x400.png\") 50% 50% repeat-x";;
				}
				else{
					document.getElementById("tab"+y+"_d").style.background = "#e6e6e6 url(\"images/ui-bg_glass_75_e6e6e6_1x400.png\") 50% 50% repeat-x";
				}
			}
		}
	
		//Load the list of templates
		function loadTemplateList(){
			$.ajax({
				url: '<?php echo $path; ?>devices/template/list.json',
				type: 'get',
				dataType: "json",
				success: function(output) {
					var defaultItem = '<option value="" disabled selected style="display:none;">...</option>';
					$("#templateSelectList").append(defaultItem);
					
					$.each(output, function(idx, item){
						var id = item.id;
						var name = item.productName;
						var type = item.productType;
						var operatingType =item.operatingType;
						var requiredNodes = item.requiredNodeTypes;
						var listItem = '<option temp-id="' + id + '" temp-name="' + name + '" temp-type="' + type + '" temp-opType="' + operatingType 
							+ '" temp-nodes="' + requiredNodes +'">'+ name + ' - ' + type +'</option>';
							
						$("#templateSelectList").append(listItem);
				 	});
					
				},
				error: function(xhr, desc, err) {
					console.log(xhr);
					console.log("Details: " + desc + "\nError:" + err);
				}
			}); // end ajax call
	
			// set the funtion for the select
			$("#templateSelectList" ).change(function() {
				$(".nodeListClass").remove(); // clean up node selection
				
				var target = $('#templateSelectList').find(":selected");
				var templateType =target.attr("temp-type");
				var templateName =target.attr("temp-name");
				var templateId =target.attr("temp-id");
				var templateNodes =target.attr("temp-nodes");
				var operatingType =target.attr("temp-opType");
			
				var nodeList = templateNodes.split(",");
				console.log(nodeList);
				 // for each node we a list with a new select
				$.each(nodeList, function(idx, item){
					var nodeType = item;
					// TODO: validate node type
					// create a list item for that node type
					var listItem = '<li class="nodeListClass" id="item' + nodeType + '">' + nodeType +': <select id="select' + nodeType + '"></select></li>';
					$("#nodeTypeList").append(listItem);
					// retrieve all the possible unassigned nodes of such type
					$.ajax({
				 		url: '<?php echo $path; ?>devices/node/getunassigned.json',
						type: 'get',
						dataType: 'json',
						ntype: nodeType,
						data: {type: "'"+ nodeType + "'"}, 
						success: function(output) {
						var type = this.ntype;
							$.each(output, function(idx, item){
								var id = item.nodeid;
								var address = item.address;
								var type = item.type;
								var listItem = '<option instance-id="' + id + '" instance-address="' + address + '">'+ address + '</option>';
								$("#select" + nodeType).append(listItem);
							});
						},
						error: function(xhr, desc, err){
									console.log(xhr);
									console.log("Details: " + desc + "\nError:" + err);
						} 
					}); // end ajax call
				}); // finished traversing the node types
		 
				// special for e-car
				//if(operatingType.localeCompare("e-car") == 0){
				// $("#configDevices").css("visibility", 'visible');
				//}
		
				$("#selectNodeDiv").css("visibility", 'visible');
				$("#confirmButton").show();
				$('#assignedDeviceName').val('');
			});
		}
	
		function addDevice(){
			$(".nodeListClass").remove();
			$("#deviceGraph").hide();
			$("#configDevices").css("visibility", 'hidden');
			loadTemplateList();
			$("#addDeviceFullPanel").show();
		}
	
		function configDevice(id,device){
			//Ajax call to get the device information
			$.ajax({
				url: '<?php echo $vdPath; ?>emoncms/devices/device/parameters.json',
				data: {'deviceid': id, 'description': 'true'},
				deviceName : device,
				deviceid : id,
				type: 'get',
				success: function(output) {
					if(null == output || output.length == 0 ){
						populateNoDeviceConfig();
						return;
					}
					// else
					$("#configDevices" ).empty();
					var text = '<div class="panel-heading">' + this.deviceName + '</div><form class="form-horizontal">';
								for(var i=0; i < output.length; i++){
					console.log(output[i].name);
									var listItem = '<div class="control-group"><label class="control-label" data-name="' + output[i].name +'" for="configParam' + output[i].id + '">'
					 + output[i].name +'</label><div class="controls"><input class="config-input" type="text" value="' + output[i].value + '" id="configParam' + output[i].id + '">';
                   //listItem += '<img class = "helpIcon" id="tooltipParam' + output[i].id + '" src = "../../images/help-icon-white.png" data-original-title="test" data-placement="right"  data-toggle="tooltip"/>';
                  if(null != output[i].description){
                   listItem += '<img class = "helpIcon" id="tooltipParam' + output[i].id + '" src = "../../images/help-icon-white.png" data-original-title="' + output[i].description + '" data-placement="right"  data-toggle="tooltip"/>';
                  }
                  listItem += '</div></div>';     
									text+=listItem;
							 };
					 text+='<button id="saveConfigurationButton" class="btn" onclick="saveDeviceConfiguration('+ id +');">Save</button></form>';
					$("#configDevices" ).append(text);
          for(var i=0; i < output.length; i++){
            $('#tooltipParam'+ output[i].id).tooltip();
          }
					showConfigPane();
				},
				error: function(xhr, desc, err) {
							console.log(xhr);
							populateNoDeviceConfig();
				}
			}); // end ajax call
		}
	
		// save the configuration of the device
		function saveDeviceConfiguration(id){
			//http://127.0.0.1:4567/emoncms/devices/device/setparameters.json?deviceid=2&fields={[{'name':'orientation', 'value':43}]}
	 
			var fieldsText = '{';
			var confFromDom = $("#configDevices").find(".control-group");
			$.each(confFromDom, function(idx, item){
				var domLabel = $(item).find('.control-label');
				var labelItem = domLabel[0];
				var dominput = $(item).find('.config-input');
				var inputItem = dominput[0];
				var paramName =$(labelItem).attr("data-name");
				var paramVal = $(inputItem).val();
				fieldsText+='"'+ paramName +'":"'+ paramVal +'",';
			});
			
			fieldsText=fieldsText.substr(0,fieldsText.length -1);
			fieldsText+='}';
			$.ajax({
				url: '<?php echo $vdPath; ?>emoncms/devices/device/setparameters.json',
				data: {
					'deviceid': id,
					'fields' : fieldsText
				},
				type: 'get',
				success: function(output) {
					alert("Device successfully configured");
					location.reload();
				},
				error: function(xhr, desc, err) {
							console.log(xhr);
				}
			}); // end ajax call
		}
	
		// fill the config device div with the
		// information that the device is not
		// configurabel
		function populateNoDeviceConfig() {
			$("#configDevices" ).empty();
			var text = '<div> The device you clicked cannot be configured.</div>';
			$("#configDevices" ).append(text);
			showConfigPane();
		}
		
		// hide other panes and show the config one
		function showConfigPane(){
			$("#deviceGraph").hide();
			$("#selectNodeDiv").css("visibility", 'hidden');
			$("#addDeviceFullPanel").hide();
			$("#confirmButton").hide();
			$("#configDevices").css("visibility", 'visible');
      $("#showGraphButton").show();
		}
		
		// hide other panes and show the graphs one
		function showGraphs(){
      $("#showGraphButton").hide();
			$("#deviceGraph").show();
			$("#selectNodeDiv").css("visibility", 'hidden');
			$("#addDeviceFullPanel").hide();
			$("#confirmButton").hide();
			$("#configDevices").css("visibility", 'hidden');
		}
		
		function confirmAssignment(){
			var template = $('#templateSelectList').find(":selected");
			var nodes = [];
			var nodesSelects = $('.nodeListClass').find(":selected");
			var nameDevice = $('#assignedDeviceName').val();
			if(!template || !nodesSelects || !nameDevice){
		 		alertAssignementError("template, name or node is not selected");
		 		return;
		 	}
	
			var templateId =template.attr("temp-id");
	
			$.each(nodesSelects, function(idx, item){
				var id = nodesSelects.attr("instance-id");
				nodes.push(id);
			});
	 
			// creating the nodesString
			var nodesString;
			if(nodes.length == 0){
				alertAssignementError("no nodes are selected");
				return;
			}
			else if (nodes.length == 1) {
				nodesString = "{" + nodes[0] +"}";
			}
			else {
				nodesString = "{" + nodes[0];
				for(i=1;i<nodes.length;i++){
					nodesString += "," + nodes[i];
				}
			}
			nodesString += "}";
			
			// adding
			$.ajax({
				url: '<?php echo $path; ?>devices/device/add.json',
				type: 'get',
				dataType: 'json',
				data: {name:nameDevice , templateid:templateId, nodes: nodesString }, 
				success: function(output) {
					console.log(output);
					alert("Device successfully assigned");
					location.reload();
					//cancelAddDevice();// I'm not really canceling the addition, but rather redrawing the gui
				},
				error: function(xhr, desc, err) {
					console.log(xhr);
					console.log("Details: " + desc + "\nError:" + err);
					alertAssignementError("could not assign device");
				}
			}); // end ajax call
		}
	
		function alertAssignementError(str){
			alert("Could not assign device because " + str);
		}
		
	</script>

<?php
require "Modules/cossmiccontrol/Views/history_d.php";
?>
