<?php
global $path;
?>

<!-- Stylesheets -->
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/gamification/Ranking/segmentedControl.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/gamification/Ranking/ranking.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/gamification/Ranking/rank_progress.css">

<!-- Javascripts -->
<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.js"></script>

<!-- Exposes the $path to the ranking js -->
<script type="text/javascript"> var path = "<?php echo $path; ?>"; </script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/gamification/Ranking/ranking.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/gamification/Ranking/rank_progress.js"></script>

<div id="rankings">
	<!-- First row of ranking panels -->
	<div class="row">
		<div class="panel span6 rankingContainer" id="leaderboard">
			<div class="panel-heading">Leaderboard</div>
			<div class="panelContainer" id="leaderBoardContainer">
				
				<div class="segmented-control durationPicker">
					<input onclick="refreshLeaderboard()" type="radio" name="sc-1-1" id="month-input">
				    <input onclick="refreshLeaderboard()" type="radio" name="sc-1-1" id="quarter-input" checked>
				    <input onclick="refreshLeaderboard()" type="radio" name="sc-1-1" id="year-input">
				
				    <label for="month-input" data-value="Month">Month</label>
				    <label for="quarter-input" data-value="Quarter">Quarter</label>
				    <label for="year-input" data-value="Year">Year</label>
				</div>

				<div id="leaderboardListContainer"></div>
			</div>
		</div>
		<div class="panel span6 rankingContainer" id="achievements">
			<div class="panel-heading">Achievements</div>
			<div class="panelContainer" id="achievementsContainer"></div>
		</div>
	</div>  
	<br />
	<!-- Second row of ranking panels -->
	<div class="row">
		<div class="panel rankingContainer" id="rank">
			<div class="panel-heading">Rank</div>
			<div class="panelContainer" id="rankContainer"></div>
		</div>
	</div>
<!--
	<div class="row">
		<div class="panel span6 rankingContainer" id="rank">
			<div class="panel-heading">Rank</div>
			<ul class="panelContainer"  id="pvprodList"></ul>
		</div>
		<div class="panel span6 rankingContainer" id="misc">
			<div class="panel-heading">Miscellaneous</div>
			<ul class="panelContainer"  id="sharingList"></ul>
		</div>
	</div>
-->
</div>
