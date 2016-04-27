<?php
global $path, $mysqli, $session;
$userid = $session['userid'];
$decomposedPath = explode("/", "$path");
$vdPath = "";
foreach($decomposedPath as &$value) {
    if((strcmp($value, "http:") == 0) || (strcmp($value, "https:") == 0)){
		$vdPath .= $value . "//";
    }else{
		if((empty($value) == false)  && (strcmp($value, "emoncms") !== 0)){
			$vdPath .= $value . "/";
		}
    }
}
/*
Pending
-- change ajax urls to proper address
--  userid is hardcoded in regard to virtual devices
*/
?>

<!-- Stylesheets -->
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/pure-0.5.0/pure-min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/css/scheduler.css">

<!-- Javascript imports -->
<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/cossmiccontrol/Views/json.js"></script>

<div id="settings">
	<!-- Row for the appliances and the configure containers -->
	<div class="row">
		<p>
		<div id="addDeviceDiv" class="pure-menu pure-menu-open">
			<a class="pure-menu-heading">Add Device</a>
      <input id="nodeNameInput" placeholder="Name"> 
			<select id="addDeviceList">
			</select>
      <a  href="#" onclick="clickAddDeviceListItem(event)">Add</a>
		</div>
		<p>

		

	</div>
	
	<p />
	

</div>

<script>



 $(document).ready( function () {
    initSettings();
	//scheduledTaskSetup();
	highlightPageLink();
} );

//function to find and add style to the link for the current page
function highlightPageLink(){
	var a = document.getElementsByTagName("a");
    for(var i=0;i<a.length;i++){
        if(a[i].href.split("#")[0] == window.location.href.split("#")[0]){
            a[i].id = "currentLink";
        }
    }
}

function initSettings(){
	//Ajax call to populate the add node list
	$.ajax({
        url: '<?php echo $vdPath; ?>virtualDevices/device.php',
        type: 'get',
        dataType: "json",
        data: {'json':'{"cmd":"templates"}'},
        success: function(output) {
			$.each(output.templates, function(idx, item){
				var id = item.id;
				var name = item.name;
				var type = item.type;
				var listItem = '<option temp-id="' + id + '" temp-name="' + name + '" temp-type="' + type 
				+  '">'+ name + ' - ' + type +'</option>';
				$("#addDeviceList").append(listItem);
		   });
        },
        error: function(xhr, desc, err) {
			console.log(xhr);
			console.log("Details: " + desc + "\nError:" + err);
        }
	}); // end ajax call

	
}

function clickAddDeviceListItem(event){
    var target = $('#addDeviceList').find(":selected");
    var templateType =  target.attr("temp-type");
    var templateName =  target.attr("temp-name");
    var templateId =  target.attr("temp-id");
    var nodeName =   $('#nodeNameInput').val();                
    $.ajax({
        url: '<?php echo $path; ?>driver/connectVMeter.json',
        type: 'get',
        dataType: "json",
         data: {'template':'{"id":"'+templateId+'","type":"'+templateType+'","name":"'+templateName+'"}', 'deviceName':''+nodeName+''},
        success: function(output) {
                console.log(output);
                alert("Device successfully added");
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log("Details: " + desc + "\nError:" + err);
        }
  }); // end ajax call
 
}

</script>