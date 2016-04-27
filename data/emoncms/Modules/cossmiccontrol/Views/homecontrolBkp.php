<?php
global $path;
?>

<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">

<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/cossmiccontrol/Views/json.js"></script>

<a href="<?php echo $path; ?>cossmiccontrol/view/summary">Summary</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/homecontrol">Home control</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/settings">Settings</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/history">History</a>

<div style="margin: 15px auto 30px auto">
<table class="cossmictable" style="width:60%" id="controltable">
    <tr>
        <th>Device</th>
        <th>Status</th>
	<th>Current load</th>
        <th>Start time</th>
    </tr>
</table>
</div>

<?php
global $mysqli, $session;
$feedlist_power = [];
$userid = $session['userid'];

// get list of ids and names of all the user's device power feeds
$result = $mysqli->query("SELECT id,name FROM feeds WHERE name REGEXP '_power$' AND userid = '$userid'");
$i = 0;
while ($row = (array)$result->fetch_object()) {
    $feedlist_power[$i]['feedid'] = $row['id'];
    $feedlist_power[$i]['devicename'] = str_replace("_power","",$row['name']);
    $i++;
}

$nr_powerfeeds = count($feedlist_power);
?>

<script type="application/javascript">
var path = "<?php echo $path; ?>";
var feedlist_power = <?php echo json_encode($feedlist_power); ?>;
var nr_powerfeeds = <?php echo $nr_powerfeeds; ?>;

// CoSSMic control table
$('#controltable').ready(

function () {
        
    var theTable = "";
    for (var i = 0; i < nr_powerfeeds; i++) {
        theTable += '<tr>';
        theTable += '<td>' + feedlist_power[i].devicename + '</td>';
        theTable += '<td>on/off</td>';
        theTable += '<td>' + Math.round(get_feedvalue(feedlist_power[i].feedid)) + ' W</td>';
	theTable += '<td></td>';
        theTable += '</tr>';
    }
    $('#controltable').append(theTable);
});
</script>
