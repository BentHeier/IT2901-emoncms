<?php
global $mysqli, $session;

$userid = $session['userid'];

// get ids of the user's feeds for internal and external energy supply, grid feed-in, self-consumption, consumption, and generation (W and kWh/day)
$feednames = array("grid2household_power", "grid2household_kwhd", "pv2household_power", "pv2household_kwhd", 
		"pv2grid_power", "pv2grid_kwhd", "pv_power", "pv_kwhd", "consumption_power", "consumption_kwhd");

$feedids = array();
for ($i=0; $i<count($feednames); $i++) {
	$result = $mysqli->query("SELECT id FROM feeds WHERE name = '$feednames[$i]' AND userid = '$userid'");
	if ($row = (array)$result->fetch_object()) {
		$feedids[$feednames[$i]] = $row['id'];
	}
	else $feedids[$feednames[$i]] = 0;
}
?>

	<script>
		var timeWindowChanged = 0;
		var today1 = new Date();
		var today2 = new Date();
		var end_day_cg = today1.setHours(23,59,59,999);
		var end_month_cg = today1.setDate(days_in_month(today1.getMonth(),today1.getFullYear()));
		var end_year_cg = today1.setMonth(11);
		var end_total_cg = today1.getTime();
		var start_day_cg = today2.setHours(0,0,0,0);
		var start_month_cg = today2.setDate(1);
		var start_year_cg = today2.setMonth(0);
		var start_total_cg = today2.setFullYear(first_year);
		
		var path = "<?php echo $path; ?>";
		var feedids = <?php echo json_encode($feedids); ?>;
		
		// create plot lists (NEED TO BE UPDATED IF FEED NAMES ARE CHANGED)
		var plotlist_power_consumption = create_plotlist(feedids.grid2household_power, feedids.pv2household_power, feedids.consumption_power);
		var plotlist_power_generation = create_plotlist(feedids.pv2household_power, feedids.pv2grid_power, feedids.pv_power);
		
		var plotlist_kwh_consumption_month = create_plotlist(feedids.grid2household_kwhd, feedids.pv2household_kwhd, feedids.consumption_kwhd);
		var plotlist_kwh_generation_month = create_plotlist(feedids.pv2household_kwhd, feedids.pv2grid_kwhd, feedids.pv_kwhd);
		
		var plotlist_kwh_consumption_year = create_plotlist(feedids.grid2household_kwhd, feedids.pv2household_kwhd, feedids.consumption_kwhd);
		var plotlist_kwh_generation_year = create_plotlist(feedids.pv2household_kwhd, feedids.pv2grid_kwhd, feedids.pv_kwhd);
		
		var plotlist_kwh_consumption_total = create_plotlist(feedids.grid2household_kwhd, feedids.pv2household_kwhd, feedids.consumption_kwhd);
		var plotlist_kwh_generation_total = create_plotlist(feedids.pv2household_kwhd, feedids.pv2grid_kwhd, feedids.pv_kwhd);
		
		var consumption_on = true;
		var generation_on = true;
		var extenergysupply_on = true;
		var intpowersupply_on = true;
		var selfconsumption_on = true;
		var gridfeedin_on = true;
		
		// hard-code colors
		hardcode_color_consumption(plotlist_power_consumption);
		hardcode_color_consumption(plotlist_kwh_consumption_month);
		hardcode_color_consumption(plotlist_kwh_consumption_year);
		hardcode_color_consumption(plotlist_kwh_consumption_total);
		hardcode_color_generation(plotlist_power_generation);
		hardcode_color_generation(plotlist_kwh_generation_month);
		hardcode_color_generation(plotlist_kwh_generation_year);
		hardcode_color_generation(plotlist_kwh_generation_total);
		
		// get plot data
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_power_consumption, start_day_cg, end_day_cg, "day","#placeholder1_day", true, extenergysupply_on, intpowersupply_on);
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_power_generation, start_day_cg, end_day_cg, "day","#placeholder2_day", false, selfconsumption_on, gridfeedin_on);  
		//get_plotdata_mode(plotlist_power_consumption, start_day_cg, end_day_cg, "day");
		//get_plotdata_mode(plotlist_power_generation, start_day_cg, end_day_cg, "day");
		
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "month","#placeholder1_month", true, extenergysupply_on, intpowersupply_on);
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "month","#placeholder2_month", false, selfconsumption_on, gridfeedin_on);  
		
		//get_plotdata_mode(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "month");
		//get_plotdata_mode(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "month");
		
		
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "year","#placeholder1_year", true, extenergysupply_on, intpowersupply_on);
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "year","#placeholder2_year", false, selfconsumption_on, gridfeedin_on); 
		//get_plotdata_mode(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "year");
		//get_plotdata_mode(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "year");
		
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_kwh_consumption_total, start_total_cg, end_total_cg, "total","#placeholder1_total", true, extenergysupply_on, intpowersupply_on);
		timeWindowChanged = 1;
		get_plotdata_and_plot(plotlist_kwh_generation_total, start_total_cg, end_total_cg, "total","#placeholder2_total", false, selfconsumption_on, gridfeedin_on); 
		//get_plotdata_mode(plotlist_kwh_consumption_total, start_total_cg, end_total_cg, "total");
		//get_plotdata_mode(plotlist_kwh_generation_total, start_total_cg, end_total_cg, "total");
		
		
		
		
		// plot graphs in tab1
		//plot_mode(plotlist_power_consumption, "#placeholder1_day", true, extenergysupply_on, intpowersupply_on, "day", start_day_cg, end_day_cg);
		//plot_mode(plotlist_power_generation, "#placeholder2_day", false, selfconsumption_on, gridfeedin_on, "day", start_day_cg, end_day_cg);
		
		// on legend click redraw plots
		// day
		$("#consumption_day").click(function() {onConsumptionClick(plotlist_power_consumption, start_day_cg, end_day_cg, "#placeholder1_day", true, "#consumption_day", "#extenergysupply_day", "#intpowersupply_day", "day")});
		$("#extenergysupply_day").click(function() {onExtEnergySupplyClick(plotlist_power_consumption, start_day_cg, end_day_cg, "#placeholder1_day", true, "#consumption_day", "#extenergysupply_day", "day")});
		$("#intpowersupply_day").click(function() {onIntPowerSupplyClick(plotlist_power_consumption, start_day_cg, end_day_cg, "#placeholder1_day", true, "#consumption_day", "#intpowersupply_day", "day")});
		$("#generation_day").click(function() {onGenerationClick(plotlist_power_generation, start_day_cg, end_day_cg, "#placeholder2_day", false, "#generation_day", "#selfconsumption_day", "#gridfeedin_day", "day")});
		$("#selfconsumption_day").click(function() {onSelfConsumptionClick(plotlist_power_generation, start_day_cg, end_day_cg, "#placeholder2_day", false, "#generation_day", "#selfconsumption_day", "day")});
		$("#gridfeedin_day").click(function() {onGridFeedInClick(plotlist_power_generation, start_day_cg, end_day_cg, "#placeholder2_day", false, "#generation_day", "#gridfeedin_day", "day")});
		// month
		$("#consumption_month").click(function() {onConsumptionClick(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "#placeholder1_month", true, "#consumption_month", "#extenergysupply_month", "#intpowersupply_month", "month")});
		$("#extenergysupply_month").click(function() {onExtEnergySupplyClick(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "#placeholder1_month", true, "#consumption_month", "#extenergysupply_month", "month")});
		$("#intpowersupply_month").click(function() {onIntPowerSupplyClick(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "#placeholder1_month", true, "#consumption_month", "#intpowersupply_month", "month")});
		$("#generation_month").click(function() {onGenerationClick(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "#placeholder2_month", false, "#generation_month", "#selfconsumption_month", "#gridfeedin_month", "month")});
		$("#selfconsumption_month").click(function() {onSelfConsumptionClick(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "#placeholder2_month", false, "#generation_month", "#selfconsumption_month", "month")});
		$("#gridfeedin_month").click(function() {onGridFeedInClick(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "#placeholder2_month", false, "#generation_month", "#gridfeedin_month", "month")});
		// year
		$("#consumption_year").click(function() {onConsumptionClick(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "#placeholder1_year", true, "#consumption_year", "#extenergysupply_year", "#intpowersupply_year", "year")});
		$("#extenergysupply_year").click(function() {onExtEnergySupplyClick(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "#placeholder1_year", true, "#consumption_year", "#extenergysupply_year", "year")});
		$("#intpowersupply_year").click(function() {onIntPowerSupplyClick(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "#placeholder1_year", true, "#consumption_year", "#intpowersupply_year", "year")});
		$("#generation_year").click(function() {onGenerationClick(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "#placeholder2_year", false, "#generation_year", "#selfconsumption_year", "#gridfeedin_year", "year")});
		$("#selfconsumption_year").click(function() {onSelfConsumptionClick(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "#placeholder2_year", false, "#generation_year", "#selfconsumption_year", "year")});
		$("#gridfeedin_year").click(function() {onGridFeedInClick(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "#placeholder2_year", false, "#generation_year", "#gridfeedin_year", "year")});
		// total 
		$("#consumption_total").click(function() {onConsumptionClick(plotlist_kwh_consumption_total, start_total_cg, end_total_cg, "#placeholder1_total", true, "#consumption_total", "#extenergysupply_total", "#intpowersupply_total", "total")});
		$("#extenergysupply_total").click(function() {onExtEnergySupplyClick(plotlist_kwh_consumption_total, start_total_cg, end_total_cg, "#placeholder1_total", true, "#consumption_total", "#extenergysupply_total", "total")});
		$("#intpowersupply_total").click(function() {onIntPowerSupplyClick(plotlist_kwh_consumption_total, start_total_cg, end_total_cg, "#placeholder1_total", true, "#consumption_total", "#intpowersupply_total", "total")});
		$("#generation_total").click(function() {onGenerationClick(plotlist_kwh_generation_total, start_total_cg, end_total_cg, "#placeholder2_total", false, "#generation_total", "#selfconsumption_total", "#gridfeedin_total", "total")});
		$("#selfconsumption_total").click(function() {onSelfConsumptionClick(plotlist_kwh_generation_total, start_total_cg, end_total_cg, "#placeholder2_total", false, "#generation_total", "#selfconsumption_total", "total")});
		$("#gridfeedin_total").click(function() {onGridFeedInClick(plotlist_kwh_generation_total, start_total_cg, end_total_cg, "#placeholder2_total", false, "#generation_total", "#gridfeedin_total", "total")});
		
		// date change
		// day
		$('#prevbtn_day_cg').click(function () {
			end_day_cg = start_day_cg-1;
			start_day_cg = start_day_cg-24*3600000;
			timeWindowChanged = 1;
			get_plotdata_and_plot(plotlist_power_consumption, start_day_cg, end_day_cg, "day","#placeholder1_day", true, extenergysupply_on, intpowersupply_on);
			//plot_mode(plotlist_power_consumption, "#placeholder1_day", true, extenergysupply_on, intpowersupply_on, "day", start_day_cg, end_day_cg);
			timeWindowChanged = 1;
			get_plotdata_and_plot(plotlist_power_generation, start_day_cg, end_day_cg, "day","#placeholder2_day", false, selfconsumption_on, gridfeedin_on);
			//plot_mode(plotlist_power_generation, "#placeholder2_day", false, selfconsumption_on, gridfeedin_on, "day", start_day_cg, end_day_cg);
			startDate = new Date(start_day_cg);
			document.getElementById('date_from_calendar_cg').value = ds_format_date(startDate.getDate(),startDate.getMonth()+1,startDate.getFullYear());
		});
		
		$('#date_from_calendar_cg').datepicker( {
			onSelect: function(date) {
				newDate = new Date(date);
				start_day_cg = newDate.getTime();
				end_day_cg = newDate.setHours(23,59,59,999);
				timeWindowChanged = 1;
				//plot_mode(plotlist_power_consumption, "#placeholder1_day", true, extenergysupply_on, intpowersupply_on, "day", start_day_cg, end_day_cg);
				get_plotdata_and_plot(plotlist_power_consumption, start_day_cg, end_day_cg, "day","#placeholder1_day", true, extenergysupply_on, intpowersupply_on);
				timeWindowChanged = 1;
				//plot_mode(plotlist_power_generation, "#placeholder2_day", false, selfconsumption_on, gridfeedin_on, "day", start_day_cg, end_day_cg);
				get_plotdata_and_plot(plotlist_power_generation, start_day_cg, end_day_cg, "day","#placeholder2_day", false, selfconsumption_on, gridfeedin_on);
			}
		});
		
		$('#nextbtn_day_cg').click(function () {
			start_day_cg = end_day_cg+1;
			end_day_cg = end_day_cg+24*3600000;
			timeWindowChanged = 1;
			//plot_mode(plotlist_power_consumption, "#placeholder1_day", true, extenergysupply_on, intpowersupply_on, "day", start_day_cg, end_day_cg);
			get_plotdata_and_plot(plotlist_power_consumption, start_day_cg, end_day_cg, "day","#placeholder1_day", true, extenergysupply_on, intpowersupply_on);
			timeWindowChanged = 1;
			//plot_mode(plotlist_power_generation, "#placeholder2_day", false, selfconsumption_on, gridfeedin_on, "day", start_day_cg, end_day_cg);
			get_plotdata_and_plot(plotlist_power_generation, start_day_cg, end_day_cg, "day","#placeholder2_day", false, selfconsumption_on, gridfeedin_on);
			startDate = new Date(start_day_cg);
			document.getElementById('date_from_calendar').value = ds_format_date(startDate.getDate(),startDate.getMonth()+1,startDate.getFullYear());
		});
		
		// month
		$('#prevbtn_month_cg').click(function () {
			var m = $("#select_month_month_cg").val();
			var y = $("#select_month_year_cg").val();
			if (m == 0) {
				m = 11;
				y = y-1;
			}
			else {
				m = m-1;
			}
			start_month_cg = new Date(y,m,1).getTime();
			end_month_cg = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_consumption_month, "#placeholder1_month", true, extenergysupply_on, intpowersupply_on, "month", start_month_cg, end_month_cg);
			get_plotdata_and_plot(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "month","#placeholder1_month", true, extenergysupply_on, intpowersupply_on);
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_generation_month, "#placeholder2_month", false, selfconsumption_on, gridfeedin_on, "month", start_month_cg, end_month_cg);
			get_plotdata_and_plot(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "month","#placeholder2_month", false, selfconsumption_on, gridfeedin_on);
			$("#select_month_month_cg").val(m);
			$("#select_month_month_cg").selectmenu("refresh");
			$("#select_month_year_cg").val(y);
			$("#select_month_year_cg").selectmenu("refresh");
		});
		
		$("#select_month_month_cg").selectmenu({
			change: function() {
				var m = $("#select_month_month_cg").val();
				var y = $("#select_month_year_cg").val();
				start_month_cg = new Date(y,m,1).getTime();
				end_month_cg = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
				timeWindowChanged = 1;
				//plot_mode(plotlist_kwh_consumption_month, "#placeholder1_month", true, extenergysupply_on, intpowersupply_on, "month", start_month_cg, end_month_cg);
				get_plotdata_and_plot(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "month","#placeholder1_month", true, extenergysupply_on, intpowersupply_on);
				timeWindowChanged = 1;
				//plot_mode(plotlist_kwh_generation_month, "#placeholder2_month", false, selfconsumption_on, gridfeedin_on, "month", start_month_cg, end_month_cg);
				get_plotdata_and_plot(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "month","#placeholder2_month", false, selfconsumption_on, gridfeedin_on);
			}
		});
		$("#select_month_year_cg").selectmenu({
			change: function() {
				var m = $("#select_month_month_cg").val();
				var y = $("#select_month_year_cg").val();
				start_month_cg = new Date(y,m,1).getTime();
				end_month_cg = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
				timeWindowChanged = 1;
				get_plotdata_and_plot(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "month","#placeholder1_month", true, extenergysupply_on, intpowersupply_on);
				//plot_mode(plotlist_kwh_consumption_month, "#placeholder1_month", true, extenergysupply_on, intpowersupply_on, "month", start_month_cg, end_month_cg);
				timeWindowChanged = 1;
				//plot_mode(plotlist_kwh_generation_month, "#placeholder2_month", false, selfconsumption_on, gridfeedin_on, "month", start_month_cg, end_month_cg);
				get_plotdata_and_plot(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "month","#placeholder2_month", false, selfconsumption_on, gridfeedin_on);
			}
		});
		$('#nextbtn_month_cg').click(function () {
			var m = $("#select_month_month_cg").val();
			var y = $("#select_month_year_cg").val();
			if (m == 11) {
				m = 0;
				y++;
			}
			else {
				m++;
			}
			start_month_cg = new Date(y,m,1).getTime();
			end_month_cg = new Date(y,m,days_in_month(m,y),23,59,59,999).getTime();
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_consumption_month, "#placeholder1_month", true, extenergysupply_on, intpowersupply_on, "month", start_month_cg, end_month_cg);
			get_plotdata_and_plot(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "month","#placeholder1_month", true, extenergysupply_on, intpowersupply_on);
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_generation_month, "#placeholder2_month", false, selfconsumption_on, gridfeedin_on, "month", start_month_cg, end_month_cg);
			get_plotdata_and_plot(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "month","#placeholder2_month", false, selfconsumption_on, gridfeedin_on); 
			$("#select_month_month_cg").val(m);
			$("#select_month_month_cg").selectmenu("refresh");
			$("#select_month_year_cg").val(y);
			$("#select_month_year_cg").selectmenu("refresh");
		});
		
		// year
		$('#prevbtn_year_cg').click(function () {
			var y = $("#select_year_year_cg").val();
			if (y>first_year) { y = y-1; } else y = first_year;
			start_year_cg = new Date(y,0,1).getTime();
			end_year_cg = new Date(y,11,31,23,59,59,999).getTime();
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_consumption_year, "#placeholder1_year", true, extenergysupply_on, intpowersupply_on, "year", start_year_cg, end_year_cg);
			get_plotdata_and_plot(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "year","#placeholder1_year", true, extenergysupply_on, intpowersupply_on);
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_generation_year, "#placeholder2_year", false, selfconsumption_on, gridfeedin_on, "year", start_year_cg, end_year_cg);
			get_plotdata_and_plot(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "year","#placeholder2_year", false, selfconsumption_on, gridfeedin_on); 
			$("#select_year_year_cg").val(y);
			$("#select_year_year_cg").selectmenu("refresh");
		});
		
		$("#select_year_year_cg").selectmenu({
			change: function() {
				y = $("#select_year_year_cg").val();
				start_year_cg = new Date(y,0,1).getTime();
				end_year_cg = new Date(y,11,31,23,59,59,999).getTime();
				timeWindowChanged = 1;
				//plot_mode(plotlist_kwh_consumption_year, "#placeholder1_year", true, extenergysupply_on, intpowersupply_on, "year", start_year_cg, end_year_cg);
				get_plotdata_and_plot(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "year","#placeholder1_year", true, extenergysupply_on, intpowersupply_on);
				timeWindowChanged = 1;
				//plot_mode(plotlist_kwh_generation_year, "#placeholder2_year", false, selfconsumption_on, gridfeedin_on, "year", start_year_cg, end_year_cg);
				get_plotdata_and_plot(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "year","#placeholder2_year", false, selfconsumption_on, gridfeedin_on); 
			}
		});
		
		$('#nextbtn_year_cg').click(function () {
			var y = $("#select_year_year_cg").val();
			if (y<current_year) { y++; } else y = current_year;
			start_year_cg = new Date(y,0,1).getTime();
			end_year_cg = new Date(y,11,31,23,59,59,999).getTime();
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_consumption_year, "#placeholder1_year", true, extenergysupply_on, intpowersupply_on, "year", start_year_cg, end_year_cg);
			get_plotdata_and_plot(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "year","#placeholder1_year", true, extenergysupply_on, intpowersupply_on);
			timeWindowChanged = 1;
			//plot_mode(plotlist_kwh_generation_year, "#placeholder2_year", false, selfconsumption_on, gridfeedin_on, "year", start_year_cg, end_year_cg);
			get_plotdata_and_plot(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "year","#placeholder2_year", false, selfconsumption_on, gridfeedin_on); 
			$("#select_year_year_cg").val(y);
			$("#select_year_year_cg").selectmenu("refresh");
		});
		
		// tabs
		$("#tabs_cg").tabs();
		$('#tab1_cg').click(function() {
			consumption_on = generation_on = extenergysupply_on = intpowersupply_on = selfconsumption_on = gridfeedin_on = 1;
			//plot_mode(plotlist_power_consumption, "#placeholder1_day", true, extenergysupply_on, intpowersupply_on, "day", start_day_cg, end_day_cg);
			//plot_mode(plotlist_power_generation, "#placeholder2_day", false, selfconsumption_on, gridfeedin_on, "day", start_day_cg, end_day_cg);
			get_plotdata_and_plot(plotlist_power_consumption, start_day_cg, end_day_cg, "day","#placeholder1_day", true, extenergysupply_on, intpowersupply_on);
			get_plotdata_and_plot(plotlist_power_generation, start_day_cg, end_day_cg, "day","#placeholder2_day", false, selfconsumption_on, gridfeedin_on);
		});
		
		$('#tab2_cg').click(function() {
			consumption_on = generation_on = extenergysupply_on = intpowersupply_on = selfconsumption_on = gridfeedin_on = 1;
			//plot_mode(plotlist_kwh_consumption_month, "#placeholder1_month", true, extenergysupply_on, intpowersupply_on, "month", start_month_cg, end_month_cg);
			//plot_mode(plotlist_kwh_generation_month, "#placeholder2_month", false, selfconsumption_on, gridfeedin_on, "month", start_month_cg, end_month_cg);
			get_plotdata_and_plot(plotlist_kwh_consumption_month, start_month_cg, end_month_cg, "month","#placeholder1_month", true, extenergysupply_on, intpowersupply_on);
			get_plotdata_and_plot(plotlist_kwh_generation_month, start_month_cg, end_month_cg, "month","#placeholder2_month", false, selfconsumption_on, gridfeedin_on); 
		});
		
		$('#tab3_cg').click(function() {
			consumption_on = generation_on = extenergysupply_on = intpowersupply_on = selfconsumption_on = gridfeedin_on = 1;
			//plot_mode(plotlist_kwh_consumption_year, "#placeholder1_year", true, extenergysupply_on, intpowersupply_on, "year", start_year_cg, end_year_cg);
			//plot_mode(plotlist_kwh_generation_year, "#placeholder2_year", false, selfconsumption_on, gridfeedin_on, "year", start_year_cg, end_year_cg);
			get_plotdata_and_plot(plotlist_kwh_consumption_year, start_year_cg, end_year_cg, "year","#placeholder1_year", true, extenergysupply_on, intpowersupply_on);
			get_plotdata_and_plot(plotlist_kwh_generation_year, start_year_cg, end_year_cg, "year","#placeholder2_year", false, selfconsumption_on, gridfeedin_on)
		});
		
		$('#tab4_cg').click(function() {
			consumption_on = generation_on = extenergysupply_on = intpowersupply_on = selfconsumption_on = gridfeedin_on = 1;
			//plot_mode(plotlist_kwh_consumption_total, "#placeholder1_total", true, extenergysupply_on, intpowersupply_on, "total", start_total_cg, end_total_cg);
			//plot_mode(plotlist_kwh_generation_total, "#placeholder2_total", false, selfconsumption_on, gridfeedin_on, "total", start_total_cg, end_total_cg);
			get_plotdata_and_plot(plotlist_kwh_consumption_total, start_total_cg, end_total_cg, "total","#placeholder1_total", true, extenergysupply_on, intpowersupply_on);
			get_plotdata_and_plot(plotlist_kwh_generation_total, start_total_cg, end_total_cg, "total","#placeholder2_total", false, selfconsumption_on, gridfeedin_on);
		});
	</script>