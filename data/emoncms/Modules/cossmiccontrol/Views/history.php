<?php
global $path;
?>

	<!-- Stylesheets -->
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">
	
	<!-- Javascripts -->
	<script type="application/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.time.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.stack.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.axislabels.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.orderBars.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Modules/cossmiccontrol/Views/history.js"></script>
	<script type="application/javascript" src="<?php echo $path; ?>Modules/cossmiccontrol/Views/json.js"></script>
	
	<script>
		var path = "<?php echo $path; ?>";
		var first_year = 2013;
		var today = new Date();
		var current_year = today.getFullYear();
		var current_month = today.getMonth();
	</script>
	
	<!-- consumption/generation div -->
	<div id="historydiv">
		<div class="row">
			<div id="historyGraph" class="panel span12 bcolor">
				<div class="panel-heading">History</div>
				<table style="width:100%">
					<tr>
						<td>
							<div id="tabs_cg">
								<ul>
									<li id="tab1_cg"><a href="#tabs-1-cg">Day</a>
									</li>
									<li id="tab2_cg"><a href="#tabs-2-cg">Month</a>
									</li>
									<li id="tab3_cg"><a href="#tabs-3-cg">Year</a>
									</li>
									<li id="tab4_cg"><a href="#tabs-4-cg">Total</a>
									</li>
								</ul>
								<div id="tabs-1-cg">
									<div class="content">
										<table>
											<tr>
												<td>
													<div class="demo-container" style="height: 770px; width: 100%">
														<div style="height:700px; width:100%">
															<div class="y-axis-label">Power [kW]</div>
															<div class="placeholders">
																<div id="placeholder1_day" class="demo-placeholder" style="height: 50%"></div>
																<div id="placeholder2_day" class="demo-placeholder" style="height: 50%"></div>
															</div>
														</div>
														<div id = "choices_day_cg">
															<table class="legend">
																<tr>
																	<td id="consumption_day" style="cursor:pointer">
																		<div style="float:left;">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/consumption.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Daily consumption</div>
																	</td>
																	<td id="extenergysupply_day" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FF0000; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid consumption</div>
																	</td>
																	<td id="intpowersupply_day" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="selfconsumption_day" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="gridfeedin_day" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FFFF00; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid feed-in</div>
																	</td>
																	<td id="generation_day" style="cursor:pointer">
																		<div style="float:left">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/generationRevised.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Daily production</div>
																	</td>
																</tr>
															</table>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<table style="width:100%">
														<tr>
															<td align="center">
																<span>
																	<input id="prevbtn_day_cg" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/prevbtn.gif"></input>
																	<input id="date_from_calendar_cg" type="text" readonly="readonly" style="cursor:text; text-align:center"></input>
																	<script type="text/javascript">
																		var today = new Date();
																		document.getElementById('date_from_calendar_cg').value = ds_format_date(today.getDate(),today.getMonth()+1,today.getFullYear());
																	</script>
																	<input id="nextbtn_day_cg" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/nextbtn.gif"></input>
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
		
								<div id="tabs-2-cg">
									<div class="content">
										<table>
											<tr>
												<td>
													<div class="demo-container" style="height: 770px; width: 100%">
														<div style="height:700px; width:100%">
															<div class="y-axis-label">Energy [kWh]</div>
															<div class="placeholders">
																<div id="placeholder1_month" class="demo-placeholder" style="height: 50%"></div>
																<div id="placeholder2_month" class="demo-placeholder" style="height: 50%"></div>
															</div>
														</div>
														<div id="choices_month_cg">
															<table class="legend">
																<tr>
																	<td id="consumption_month" style="cursor:pointer">
																		<div style="float:left">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/consumption.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Monthly consumption</div>
																	</td>
																	<td id="extenergysupply_month" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FF0000; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid consumption</div>
																	</td>
																	<td id="intpowersupply_month" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="selfconsumption_month" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="gridfeedin_month" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FFFF00; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid feed-in</div>
																	</td>
																	<td id="generation_month" style="cursor:pointer">
																		<div style="float:left">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/generationRevised.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Monthly production</div>
																	</td>
																</tr>
															</table>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<table style="width:100%">
														<tr>
															<td align="center">
																<span>
																	<input id="prevbtn_month_cg" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/prevbtn.gif"></input>
																	<select id="select_month_month_cg">
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
																	<select id="select_month_year_cg">
																	</select>
																	<script type="text/javascript">
																		create_year_dropdown("#select_month_year_cg", first_year, current_year);
																									// preselect current month
																		(document.getElementById('select_month_month_cg')).value = current_month;
																		// preselect current year
																		(document.getElementById('select_month_year_cg')).value = current_year;
																	</script>
																	<input id="nextbtn_month_cg" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/nextbtn.gif"></input>
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
								
								<div id="tabs-3-cg">
									<div class="content">
										<table>
											<tr>
												<td>
													<div class="demo-container" style="height: 770px; width: 100%">
														<div style="height:700px; width:100%">
															<div class="y-axis-label">Energy [kWh]</div>
															<div class="placeholders">
																<div id="placeholder1_year" class="demo-placeholder" style="height: 50%"></div>
																<div id="placeholder2_year" class="demo-placeholder" style="height: 50%"></div>
															</div>
														</div>
														<div id="choices_year_cg">
															<table class="legend">
																<tr>
																	<td id="consumption_year" style="cursor:pointer">
																		<div style="float:left">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/consumption.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Yearly consumption</div>
																	</td>
																	<td id="extenergysupply_year" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FF0000; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid consumption</div>
																	</td>
																	<td id="intpowersupply_year" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="selfconsumption_year" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="gridfeedin_year" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FFFF00; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid feed-in</div>
																	</td>
																	<td id="generation_year" style="cursor:pointer">
																		<div style="float:left">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/generationRevised.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Yearly production</div>
																	</td>
																</tr>
															</table>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<table style="width:100%">
														<tr>
															<td align="center">
																<span>
																	<input id="prevbtn_year_cg" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/prevbtn.gif"></input>
																	<select id="select_year_year_cg">
																	</select>
																	<script type="text/javascript">
																		create_year_dropdown("#select_year_year_cg", first_year, current_year);
																		// preselect current year
																		(document.getElementById('select_year_year_cg')).value = current_year;
																	</script>
																	<input id="nextbtn_year_cg" type="image" style="height:16px; width:20px" src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/nextbtn.gif"></input>
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
							
								<div id="tabs-4-cg">
									<div class="content">
										<table>
											<tr>
												<td>
													<div class="demo-container" style="height: 770px; width: 100%">
														<div style="height:700px; width:100%">
															<div class="y-axis-label">Energy [kWh]</div>
															<div class="placeholders">
																<div id="placeholder1_total" class="demo-placeholder" style="height: 50%"></div>
																<div id="placeholder2_total" class="demo-placeholder" style="height: 50%"></div>
															</div>
														</div>
														<div id="choices_total_cg">
															<table class="legend">
																<tr>
																	<td id="consumption_total" style="cursor:pointer">
																		<div style="float:left">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/consumption.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Total consumption</div>
																	</td>
																	<td id="extenergysupply_total" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FF0000; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid consumption</div>
																	</td>
																	<td id="intpowersupply_total" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="selfconsumption_total" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#088A08; border:1px solid #000000;"></div>
																		<div class="legend-text">Self-consumption</div>
																	</td>
																	<td id="gridfeedin_total" style="cursor:pointer">
																		<div style="float:left; width:12px; height:12px; background-color:#FFFF00; border:1px solid #000000;"></div>
																		<div class="legend-text">Grid feed-in</div>
																	</td>
																	<td id="generation_total" style="cursor:pointer">
																		<div style="float:left">
																			<img src="<?php echo $path; ?>/Modules/cossmiccontrol/Views/generationRevised.png" style="width:13px; height:13px; border:1px solid #000000;">
																		</div>
																		<div class="legend-text">Total production</div>
																	</td>
																</tr>
															</table>
														</div>
													</div>
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
		</div>
	</div>
	
	<script type="text/javascript">
	
		$(document).ready( function(){
			highlightPageLink();
		});
		
		//function to find what page we are on and add the currentLink id to that navbar link to highlight current page
		function highlightPageLink(){
			//Dirty change of the first tab color (since that's where user will start)
			document.getElementById("tab1_cg").style.background = "#1192d3 url(\"images/ui-bg_glass_75_e6e6e6_1x400.png\") 50% 50% repeat-x";
		
			var a = document.getElementsByTagName("a");
			for(var i=0;i<a.length;i++){
				if(a[i].href.split("#")[0] == window.location.href.split("#")[0]){
					a[i].id = "currentLink";
				}
				
				//Adds an onclick event listener to all 4 tabs, firing the tabClicked function whenever one of the tabs are selected
				if(a[i].href.split("#")[1] == "tabs-1-cg"){
					a[i].addEventListener("click", tabClicked, false);
				}
				if(a[i].href.split("#")[1] == "tabs-2-cg"){
					a[i].addEventListener("click", tabClicked, false);
				}
				if(a[i].href.split("#")[1] == "tabs-3-cg"){
					a[i].addEventListener("click", tabClicked, false);
				}
				if(a[i].href.split("#")[1] == "tabs-4-cg"){
					a[i].addEventListener("click", tabClicked, false);
				}
			}
		}
		
		//Function to change the color of the selected tab in order to highlight where the user is
		function tabClicked(event){
			event = event;
			var target = event.target.parentElement;
			for(var y=1;y<5;y++){
				if(target.id == "tab"+y+"_cg"){
					document.getElementById(target.id).style.background = "#1192d3 url(\"images/ui-bg_glass_75_e6e6e6_1x400.png\") 50% 50% repeat-x";
				}
				else{
					document.getElementById("tab"+y+"_cg").style.background = "#e6e6e6 url(\"images/ui-bg_glass_75_e6e6e6_1x400.png\") 50% 50% repeat-x";
				}
			}
		}
		
	</script>

<?php
require "Modules/cossmiccontrol/Views/history_cg.php";
//require "Modules/cossmiccontrol/Views/history_d.php";
?>