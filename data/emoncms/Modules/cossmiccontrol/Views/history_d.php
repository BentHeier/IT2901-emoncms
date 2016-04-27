<?php
global $mysqli, $session;
$feedlist_power = [];
$feedlist_kwh = [];
$userid = $session['userid'];

// get list of ids and names of all the user's power feeds
$qfeedlist = $mysqli->query("SELECT id,name FROM feeds WHERE name LIKE 'device%' AND name LIKE '%_in_power' AND userid = '$userid'");
$i = 0;
while ($rfeedlist = (array)$qfeedlist->fetch_object()) {
	$deviceid = str_replace("_in_power","",$rfeedlist['name']); //remove the _in_power suffix
	$deviceid = str_replace("device","",$deviceid);  //remove the device prefix
	
	if(!$qdevice = $mysqli->query("SELECT name,templateid FROM devices WHERE deviceid = '$deviceid'")) {
		die('There was an error running the query [' . $mysqli->error . ']');
	}
	
	if(empty($qdevice)) {
		$feedlist_power[$i]['devicename'] =  $deviceid;
		$feedlist_power[$i]['feedid'] = $rfeedlist['id'];
		
		$i++;
	}
	else {
		$rdevice = $qdevice->fetch_assoc();

		$templateid = $rdevice['templateid'];
		$qtemplate = $mysqli->query("SELECT productName FROM templates WHERE templateid = '$templateid' AND productType LIKE '%grid%'");
		if($qtemplate->num_rows == 0) {
			$feedlist_power[$i]['devicename'] = $rdevice['name'];
			$feedlist_power[$i]['feedid'] = $rfeedlist['id'];
			
			$i++;
		}
		$qtemplate->free();
	}
	$qdevice->free();
}
$qfeedlist->free();

// get list of ids and names of all the user's kWh/day feeds
$qfeedlist = $mysqli->query("SELECT id,name FROM feeds WHERE name LIKE 'device%' AND name LIKE '%_in_kwhd' AND userid = '$userid'");
$i = 0;
while ($rfeedlist = (array)$qfeedlist->fetch_object()) {
	$deviceid = str_replace("_in_kwhd","",$rfeedlist['name']); //remove the _in_power suffix
	$deviceid = str_replace("device","",$deviceid);  //remove the device prefix
	
	if(!$qdevice = $mysqli->query("SELECT name,templateid FROM devices WHERE deviceid = '$deviceid'")) {
		die('There was an error running the query [' . $mysqli->error . ']');
	}
	
	if(empty($qdevice)) {
		$feedlist_kwh[$i]['devicename'] =  $deviceid;
		$feedlist_kwh[$i]['feedid'] = $rfeedlist['id'];
		
		$i++;
	}
	else {
		$rdevice = $qdevice->fetch_assoc();

		$templateid = $rdevice['templateid'];
		$qtemplate = $mysqli->query("SELECT productName FROM templates WHERE templateid = '$templateid' AND productType LIKE '%grid%'");
		if($qtemplate->num_rows == 0) {
			$feedlist_kwh[$i]['devicename'] = $rdevice['name'];
			$feedlist_kwh[$i]['feedid'] = $rfeedlist['id'];
		
			$i++;
		}
		$qtemplate->free();
	}
	$qdevice->free();
}
$qfeedlist->free();

$nr_devices = count($feedlist_power);
?>

	<script type="application/javascript">
	
		var timeWindowChanged = 0;
		var today1 = new Date();
		var today2 = new Date();
		var end_day_d = today1.setHours(23,59,59,999);
		var end_month_d = today1.setDate(days_in_month(today1.getMonth(),today1.getFullYear()));
		var end_year_d = today1.setMonth(11);
		var end_total_d = today1.getTime();
		var start_day_d = today2.setHours(0,0,0,0);
		var start_month_d = today2.setDate(1);
		var start_year_d = today2.setMonth(0);
		var start_total_d = today2.setFullYear(first_year);
		
		var prevTabChoices = $('#choices_day_d');
		
		var nr_devices = <?php echo $nr_devices; ?>;
		// power and kWh feed lists
		var path = "<?php echo $path; ?>";
		var feedlist_power = <?php echo json_encode($feedlist_power); ?>;
		var feedlist_kwh = <?php echo json_encode($feedlist_kwh); ?>;
		// convert feed lists to plot lists
		var plotlist_power = convert_to_plotlist(feedlist_power);
		var plotlist_wh = convert_to_plotlist(feedlist_kwh);
		var plotlist_kwh_year = convert_to_plotlist(feedlist_kwh);
		var plotlist_kwh_total = convert_to_plotlist(feedlist_kwh);
		
		// hard-code color indices
		// THIS CODE WILL FAIL FOR MORE THAN 20 feeds!! TODO: add error handling"
		hardcode_color(plotlist_power);
		hardcode_color(plotlist_wh);
		hardcode_color(plotlist_kwh_year);
		hardcode_color(plotlist_kwh_total);
		
		$(document).ready( function () {
			//build the checkbox
			var choiceContainerHtml = "<table>";
			$.each(plotlist_power, function (key, val) {
				choiceContainerHtml = choiceContainerHtml+'<tr><td><div style="float:left; margin-top:2px; margin-left:10px; width:12px; height:12px; background-color:'+val.plot.color+'; border:1px solid #000000;"></div></div><input type="checkbox" name="' + key +
				'" checked="checked" style="float:left"></input>' +
				'<label style="display: inline-block; float: left">' + val.plot.label + '</label></td></tr>';
				i++;
			});
			choiceContainerHtml = choiceContainerHtml + "</table>";
			$("#choices_day_d").html(choiceContainerHtml);
			
			// populate the graphs 
			timeWindowChanged = 1;
			devices_get_plotdata_and_plot("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
			timeWindowChanged = 1;
			devices_get_plotdata_and_plot("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices); 
			timeWindowChanged = 1;
			devices_get_plotdata_and_plot("#choices_total_d", plotlist_kwh_total, start_total_d, end_total_d, "Meter reading consumption meter [kWh]", "#placeholder_total", "total", nr_devices);
			timeWindowChanged = 1;
			devices_get_plotdata_and_plot("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [kWh]", "#placeholder_month", "month", nr_devices);
			
			/*get_plotdata_mode(plotlist_wh, start_month_d, end_month_d, "month");
			for (var i in plotlist_wh) {
			 scalarMultiplyY(plotlist_wh[i].plot.data,1000);
			} */
			
			//kathrinas code for populating 
			/* get_plotdata_mode(plotlist_power, start_day_d, end_day_d, "day");
			get_plotdata_mode(plotlist_wh, start_month_d, end_month_d, "month");
			get_plotdata_mode(plotlist_kwh_year, start_year_d, end_year_d, "year");
			get_plotdata_mode(plotlist_kwh_total, start_total_d, end_total_d, "total");
			// multiply kWh by 1000 to convert to Wh for Wh plot list
			for (var i in plotlist_wh) {
			 scalarMultiplyY(plotlist_wh[i].plot.data,1000);
			}
			
			// insert checkboxes and legend, draw plot in tab 1
			var plotdata = [];
			for(var i in plotlist_power) {
			 if ( plotlist_power[i].plot.data)
			 {
			  plotdata.push(plotlist_power[i].plot);
			 }
			}
			var plot = define_plot(plotdata, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
			plot.draw();*/ 
			
			// redraw plot in tab 1 as devices are ticked and unticked
			$("#choices_day_d").find("input").change(function(){
				if (this.checked) {
					this.setAttribute("checked","checked");
				}
				else this.removeAttribute("checked");
				plotAccordingToChoices("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [kW]", "#placeholder_day", "day", nr_devices);
			});
			
			// date change
			// day
			$('#prevbtn_day_d').click(function () {
				end_day_d = start_day_d-1;
				start_day_d = start_day_d-24*3600000;
				timeWindowChanged = 1;
				//get_plotdata_mode(plotlist_power, start_day_d, end_day_d, "day");
				//plotAccordingToChoices("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
				devices_get_plotdata_and_plot("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
				startDate = new Date(start_day_d);
				document.getElementById('date_from_calendar_d').value = ds_format_date(startDate.getDate(),startDate.getMonth()+1,startDate.getFullYear());
			});
			
			$('#date_from_calendar_d').datepicker( {
				onSelect: function(date) {
					newDate = new Date(date);
					start_day_d = newDate.getTime();
					end_day_d = newDate.setHours(23,59,59,999);
					timeWindowChanged = 1;
					//get_plotdata_mode(plotlist_power, start_day_d, end_day_d, "day");
					//plotAccordingToChoices("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
					devices_get_plotdata_and_plot("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
				}
			});
			
			$('#nextbtn_day_d').click(function () {
				start_day_d = end_day_d+1;
				end_day_d = end_day_d+24*3600000;
				timeWindowChanged = 1;
				//get_plotdata_mode(plotlist_power, start_day_d, end_day_d, "day");
				//plotAccordingToChoices("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
				devices_get_plotdata_and_plot("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
				startDate = new Date(start_day_d);
				document.getElementById('date_from_calendar_d').value = ds_format_date(startDate.getDate(),startDate.getMonth()+1,startDate.getFullYear());
			});
			
			// month
			$('#prevbtn_month_d').click(function () {
				var m = $("#select_month_month_d").val();
				var y = $("#select_month_year_d").val();
				if (m == 0) {
					m = 11;
					y = y-1;
				} else {
					m = m-1;
				}
				start_month_d = new Date(y,m,1).getTime();
				end_month_d = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
				timeWindowChanged = 1;
				devices_get_plotdata_and_plot("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [kWh]", "#placeholder_month", "month", nr_devices);
				/* get_plotdata_mode(plotlist_wh, start_month_d, end_month_d, "month");
				for (var i in plotlist_wh) {
					scalarMultiplyY(plotlist_wh[i].plot.data,1000);
				}
				plotAccordingToChoices("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [Wh]", "#placeholder_month", "month", nr_devices);*/
				$("#select_month_month_d").val(m);
				$("#select_month_month_d").selectmenu("refresh");
				$("#select_month_year_d").val(y);
				$("#select_month_year_d").selectmenu("refresh");
			});
			
			$("#select_month_month_d").selectmenu({
				change: function() {
					var m = $("#select_month_month_d").val();
					var y = $("#select_month_year_d").val();
					start_month_d = new Date(y,m,1).getTime();
					end_month_d = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
					timeWindowChanged = 1;
					devices_get_plotdata_and_plot("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [kWh]", "#placeholder_month", "month", nr_devices);
					/*get_plotdata_mode(plotlist_wh, start_month_d, end_month_d, "month");
					for (var i in plotlist_wh) {
						scalarMultiplyY(plotlist_wh[i].plot.data,1000);
					}
					plotAccordingToChoices("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [Wh]", "#placeholder_month", "month", nr_devices);*/
				}
			});
			
			$("#select_month_year_d").selectmenu({
				change: function() {
					var m = $("#select_month_month_d").val();
					var y = $("#select_month_year_d").val();
					start_month_d = new Date(y,m,1).getTime();
					end_month_d = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
					timeWindowChanged = 1;
					devices_get_plotdata_and_plot("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [kWh]", "#placeholder_month", "month", nr_devices);
					/*get_plotdata_mode(plotlist_wh, start_month_d, end_month_d, "month");
					for (var i in plotlist_wh) {
						scalarMultiplyY(plotlist_wh[i].plot.data,1000);
			  		}  
			  		plotAccordingToChoices("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [Wh]", "#placeholder_month", "month", nr_devices);*/
			 	}
			});
			
			$('#nextbtn_month_d').click(function () {
				var m = $("#select_month_month_d").val();
				var y = $("#select_month_year_d").val();
				if (m == 11) {
					m = 0;
					y++;
				} else {
					m++;
				}
				start_month_d = new Date(y,m,1).getTime();
				end_month_d = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
				timeWindowChanged = 1;
				devices_get_plotdata_and_plot("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [kWh]", "#placeholder_month", "month", nr_devices);
				/*get_plotdata_mode(plotlist_wh, start_month_d, end_month_d, "month");
				for (var i in plotlist_wh) {
			 		scalarMultiplyY(plotlist_wh[i].plot.data,1000);
				}
				plotAccordingToChoices("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [Wh]", "#placeholder_month", "month", nr_devices);*/
				$("#select_month_month_d").val(m);
				$("#select_month_month_d").selectmenu("refresh");
				$("#select_month_year_d").val(y);
				$("#select_month_year_d").selectmenu("refresh");
			});
			
			// year
			$('#prevbtn_year_d').click(function () {
				var y = $("#select_year_year_d").val();
				if (y>first_year) { y = y-1; } else y = first_year;
				start_year_d = new Date(y,0,1).getTime();
				end_year_d = new Date(y,11,31,23,59,59,999).getTime();
				timeWindowChanged = 1;
				devices_get_plotdata_and_plot("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
				//get_plotdata_mode(plotlist_kwh_year, start_year_d, end_year_d, "year");
				//plotAccordingToChoices("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
				$("#select_year_year_d").val(y);
				$("#select_year_year_d").selectmenu("refresh");
			});
			
			$("#select_year_year_d").selectmenu({
				change: function() {
					y = $("#select_year_year_d").val();
					start_year_d = new Date(y,0,1).getTime();
					end_year_d = new Date(y,11,31,23,59,59,999).getTime();
					timeWindowChanged = 1;
					devices_get_plotdata_and_plot("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
					//get_plotdata_mode(plotlist_kwh_year, start_year_d, end_year_d, "year");
					//plotAccordingToChoices("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
				}
			});
			
			$('#nextbtn_year_d').click(function () {
			 var y = $("#select_year_year_d").val();
			 if (y<current_year) { y++; } else y = current_year;
			 start_year_d = new Date(y,0,1).getTime();
			 end_year_d = new Date(y,11,31,23,59,59,999).getTime();
			 timeWindowChanged = 1;
			 devices_get_plotdata_and_plot("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
			 //get_plotdata_mode(plotlist_kwh_year, start_year_d, end_year_d, "year");
			 //plotAccordingToChoices("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
			 $("#select_year_year_d").val(y);
			 $("#select_year_year_d").selectmenu("refresh");
			});
			
			// tabs
			$("#tabs_d").tabs();
			$('#tab1_d').click(function() {
				prevTabChoicesHtml = prevTabChoices.html();
				if(prevTabChoicesHtml) {
					$("#choices_day_d").html(prevTabChoicesHtml);
				}
				prevTabChoices = $('#choices_day_d');
				plotAccordingToChoices("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
				$('#choices_day_d').find("input").change(function() {
					if (this.checked) {
						this.setAttribute("checked","checked");
				 	}
				 	else this.removeAttribute("checked");
					plotAccordingToChoices("#choices_day_d", plotlist_power, start_day_d, end_day_d, "Load power [W]", "#placeholder_day", "day", nr_devices);
				});
			});
			
			$('#tab2_d').click(function() {
				prevTabChoicesHtml = prevTabChoices.html();
				if(prevTabChoicesHtml) {
					$("#choices_month_d").html(prevTabChoicesHtml);
				}
				prevTabChoices = $('#choices_month_d');
				plotAccordingToChoices("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [kWh]", "#placeholder_month", "month", nr_devices);
				$('#choices_month_d').find("input").change(function() {
					if (this.checked) {
						this.setAttribute("checked","checked");
					}
					else this.removeAttribute("checked");
					plotAccordingToChoices("#choices_month_d", plotlist_wh, start_month_d, end_month_d, "Meter reading consumption meter [kWh]", "#placeholder_month", "month", nr_devices);
				});
			});
			
			$('#tab3_d').click(function() {
				prevTabChoicesHtml = prevTabChoices.html();
				if(prevTabChoicesHtml) {
					$("#choices_year_d").html(prevTabChoicesHtml);
				}
				prevTabChoices = $('#choices_year_d');
				plotAccordingToChoices("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
				$('#choices_year_d').find("input").change(function() {
					if (this.checked) {
						this.setAttribute("checked","checked");
					}
					else this.removeAttribute("checked");
					plotAccordingToChoices("#choices_year_d", plotlist_kwh_year, start_year_d, end_year_d, "Meter reading consumption meter [kWh]", "#placeholder_year", "year", nr_devices);
				});
			});
			
			$('#tab4_d').click(function() {
				prevTabChoicesHtml = prevTabChoices.html();
				if(prevTabChoicesHtml) {
					$("#choices_total_d").html(prevTabChoicesHtml);
				}
				prevTabChoices = $('#choices_total_d');
				plotAccordingToChoices("#choices_total_d", plotlist_kwh_total, start_total_d, end_total_d, "Meter reading consumption meter [kWh]", "#placeholder_total", "total", nr_devices);
				$('#choices_total_d').find("input").change(function() {
					if (this.checked) {
						this.setAttribute("checked","checked");
					}
					else this.removeAttribute("checked");
					plotAccordingToChoices("#choices_total_d", plotlist_kwh_total, start_total_d, end_total_d, "Meter reading consumption meter [kWh]", "#placeholder_total", "total", nr_devices);
				});
			});
		});
		
	</script>