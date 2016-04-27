<?php
global $path;
?>

<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">

<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.js"></script>

<style>
.selected_device {
        border: 1px solid #aaaaaa;
        background: #ffffff url("<?php echo $path; ?>Lib/jqueryui/images/ui-bg_glass_65_ffffff_1x400.png") 50% 50% repeat-x;
        font-weight: normal;
        color: #212121;
}
</style>

<a href="<?php echo $path; ?>cossmiccontrol/view/summary">Summary</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/homecontrol">Home control</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/settings">Settings</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/history">History</a>

<div style="width: 300px">
<div style="margin: 15px auto 30px auto; width: 300px">
    <label for="devicemenu">Select a device</label>
    <ul id="devicemenu"></ul>
</div>

<div style="width:300px">
    <div style="float: left; width: 150px"><button id="addbutton" style="width: 150px">Add device</button></div>
    <div style="float: right; width: 150px"><button id="removebutton" style="width: 150px">Remove device</button></div>
</div>
</div>


<?php
global $mysqli, $session;
$inputlist = [];
$userid = $session['userid'];

// get list of names of all the user's inputs
$result = $mysqli->query("SELECT name FROM input WHERE userid = '$userid'");
$i = 0;
while ($row = (array)$result->fetch_object()) {
    $inputlist[$i] = $row['name'];
    $i++;
}

$nr_inputs = count($inputlist);
?>

<script>
var inputlist = <?php echo json_encode($inputlist); ?>;
var nr_inputs = <?php echo $nr_inputs; ?>;

function create_device_menu(element_id) {
    device_menu = "";
    for (i = 0; i < nr_inputs; i++) {
	device_menu = device_menu + "<li>"+inputlist[i]+"</li>";
    }
    $(element_id).append(device_menu);
}

create_device_menu("#devicemenu");
$("button").button();

var prev_selected_device = null;

$("#devicemenu").menu({
    select: function(event, ui) {
	ui.item.addClass("selected_device");
	if (prev_selected_device) prev_selected_device.removeClass("selected_device");
	prev_selected_device = ui.item;
    }
});

$("#addbutton").click( function() {
});

$("#removebutton").click( function() {
});

</script>
