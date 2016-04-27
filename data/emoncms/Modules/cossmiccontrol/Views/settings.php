<?php
global $path;
$decomposedPath = explode("/", "$path");
$vdPath = "";
foreach ($decomposedPath as &$value) {
    if ( (strcmp($value, "http:") == 0) || (strcmp($value, "https:") == 0)) {
      $vdPath .= $value . "//";
    }else{
      if ( (empty($value) == false)  && (strcmp($value, "emoncms") !== 0)) {
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

<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/pure-0.5.0/pure-min.css">

<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>


<style>
.selected_device {
        border: 1px solid #aaaaaa;
        background: #ffffff url("<?php echo $path; ?>Lib/jqueryui/images/ui-bg_glass_65_ffffff_1x400.png") 50% 50% repeat-x;
        font-weight: normal;
        color: #212121;
}

    .pure-u > div {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .l-box {
        padding: 0 0.5em;
    }
</style>

<a href="<?php echo $path; ?>cossmiccontrol/view/summary">Summary</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/homecontrol">Home control</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/settings">Settings</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/history">History</a>

<div class="pure-g">
    <div class="pure-u-1-3 l-box">
      <p>
      
      <div id="addDeviceDiv" class="pure-menu pure-menu-open">
          <a class="pure-menu-heading">Add Device</a>
          <ul id="addDeviceList">
          </ul>
      </div>
    
      <p>

      <div class="pure-menu pure-menu-open">
          <a class="pure-menu-heading">My Devices</a>
          <ul id="myDeviceList">
          <li class="pure-menu-heading">Configurable Devices</li>
          </ul>
      </div>

    
    </div>
    <div id="settingMidPane" class="pure-u-1-3 l-box">
    
        <div id="addDeviceForm">
          <legend>Specify your device</legend>
          <form class="form-inline">
            <label>Device Name  </label>  <input id="deviceNameInput" type="text" class="input"><p>
            <div class="input-prepend input-append">
              <div class="btn-group">
                <button class="btn dropdown-toggle" data-toggle="dropdown">
                  Current Modes
                  <span class="caret"></span>
                </button>
                <ul id="dropDownModeList" class="dropdown-menu">
                </ul>
              </div>
              <input class="span2" id="inputModeText" type="text" placeholder="Operational Mode">
              <button class="btn" id="addModeButton"  onclick="clickAddMode(event)" type="button">Add Mode</button>
            </div><p>
            <label id="templateSelectLabel">Template:</label> <select id="selectTemplate">
              </select> <p>
            <button class="btn" id="addDeviceButton"  onclick="clickAddDevice(event)" type="button">Add Device</button>
          </form>
        </div>
     
        <div id="configDeviceForm">
          <legend>Configure your device</legend>
          <form class="form-inline">
            <label>Device Name: </label>  <span id="deviceNameOnConfigPane"></span><br> 
            <label>Earliest Start Time: </label> 
            <div id="estConfigDatetimepicker" class="input-append">
              <input data-format="hh:mm" type="text" id="estInput">
              <span class="add-on">
                <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                </i>
              </span>
            </div> <br> 
            <label>Latest Start Time: </label>
            <div id="lstConfigDatetimepicker" class="input-append">
              <input data-format="hh:mm" type="text" id="lstInput">
              <span class="add-on">
                <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                </i>
              </span>
            </div>  <br> 
            <label id="configModeLabel">Program: </label> <select id="selectModeConfigList">
                <option>1</option>
                <option>2</option>
              </select><br> 
            <button class="btn" id="addTaskeButton"  onclick="clickAddTask(event)" type="button">Add Task</button>   
          </form>
        </div>



    </div>
    
    
    
    <div class="pure-u-1-3 l-box"></div>
</div>



<?php
global $mysqli, $session;  
$inputlist = []; 
$userid = $session['userid'];

// get list of names of all the user's inputs
/*$result = $mysqli->query("SELECT name FROM input WHERE userid = '$userid'");
$i = 0;
while ($row = (array)$result->fetch_object()) {
    $inputlist[$i] = $row['name'];
    $i++;
}

$nr_inputs = count($inputlist);*/
?>

<script>

  $("#addDeviceDiv").hide();
  $("#addDeviceForm").hide();
  $("#configDeviceForm").hide();


 $(document).ready( function () {
    initSettings()
} );

function initSettings(){



  $.ajax({
        url: '<?php echo $vdPath; ?>virtualDevices/device.php',
        type: 'get',
        dataType: "json",
        data: {'json':'{"cmd":"types"}'},
        success: function(output) {
                $.each(output.types, function(idx, item){
                    var id = item.id;
                    var name = item.name;
                    var type = item.type;
                    var category = item.category;
                    var listItem = '<li  dev-id="' + id + '" dev-name="' + name + '" dev-type="' + type 
                    + '"  dev-category="' + category 
                    + '"><a href="#" onclick="clickAddDeviceListItem(event)">'  + name +'</a></li>';
                    $("#addDeviceList").append(listItem);
               });
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log("Details: " + desc + "\nError:" + err);
        }
  }); // end ajax call

    $.ajax({
        url: '<?php echo $vdPath; ?>virtualDevices/device.php',
        type: 'get',
        dataType: "json",
        data: {'json':'{"cmd":"list","user":"1"}'},
        success: function(output) {
                console.log(output);
                console.log(JSON.stringify(output.devicelist));
                $.each(output.devicelist, function(idx, item){
                    var id = item.id;
                    var name = item.name;
                    var type = item.type;
                    var template = item.template;
                    var devClass = item.class;
                    var listItem = $('<li  dev-id="' + id + '" dev-name="' + name + '" dev-type="' + type 
                    + '"  dev-category="' + devClass +  '"  dev-template="' + template                   
                    + '"><a href="#">'  + name +'</a></li>');
                    if(devClass == "single run devices")  {
                      var link = listItem.find( "a" );
                      link.attr( "onclick", "clickMyDeviceListItem(event,"+ id +")" ); 
                      $("#myDeviceList").append(listItem);
                    }else{
                      listItem.addClass("pure-menu-disabled");
                      $("#myDeviceList").prepend(listItem);
                    }
               });


        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log("Details: " + desc + "\nError:" + err);
        }
  }); // end ajax call

  $('#estConfigDatetimepicker').datetimepicker({pickDate: false, pickSeconds: false});
   $('#lstConfigDatetimepicker').datetimepicker({pickDate: false, pickSeconds: false});
}

function clickAddDeviceListItem(event){

    var target = $( event.target );
    var itemLi = target.parent();
    var deviceType =  itemLi.attr("dev-name");
    $('#selectTemplate').empty();
    $.ajax({
        url: '<?php echo $vdPath; ?>virtualDevices/device.php',
        type: 'get',
        dataType: "json",
         data: {'json':'{"cmd":"templates"}'},
        success: function(output) {
                $.each(output.templates, function(idx, item){
                    if(item.type == deviceType){
                         $('#selectTemplate').append($("<option></option>").attr("value",item.id).text(item.name)); 
                    }
               });
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log("Details: " + desc + "\nError:" + err);
        }
  }); // end ajax call

  $("#configDeviceForm").hide();
  $("#addDeviceList").children().removeClass("pure-menu-selected");
  target.parent().addClass("pure-menu-selected");
  $("#addDeviceForm").show();
  cleanUpModes();
}

function clickAddMode(event){
      var mode = $('#inputModeText').val();
      var listItem = '<li data="' + mode +'"><a tabindex="-1" href="#" onclick="removeMode(event)">' + mode +'<i class="icon-remove"></i></a></li>';
      $("#dropDownModeList").append(listItem);

}

function removeMode(event){
  var target = $( event.target );
  target.parent().remove(); 
}

function cleanUpModes(){
      $("#dropDownModeList").empty();
}

function clickAddDevice(event){
  
  var templateId = $("#selectTemplate").val();; 
  var deviceName = $('#deviceNameInput').val();
  // TODO: template is currently hard coded for the dishwasher 43
  //var addVirtDevJson = "?json={'cmd':'add','template':'43','name:'"+ deviceName+"','user':'1'}"";
  var addVirtDevJson = '?json={"cmd":"add","template":"'+ templateId + '","name":"'+ deviceName+'","user":"1"}';
  
  //var arr = {json: {cmd: "add", template: "43", name: "tst", user: "1"}};
  
    $.ajax({
        url: '<?php echo $vdPath; ?>virtualDevices/device.php'+addVirtDevJson,
        type: 'get',
        dataType: 'html',
        success: function(output) {
                console.log(output);
                if(null != output) {
                  if (null != output.error ){
                     console.log(output.error);
                  }
                }
                location.reload();
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log('Details: ' + desc + '\nError:' + err);
        }
  }); // end ajax call
  
}


function clickMyDeviceListItem(event,deviceId){
     $("#addDeviceForm").hide();
     
     var target = $( event.target );
     $("#myDeviceList").children().removeClass("pure-menu-selected");
     target.parent().addClass("pure-menu-selected");
     var selectedListItem =  target.parent();
     $("#deviceNameOnConfigPane").text(selectedListItem.attr("dev-name"));
      $("#selectModeConfigList").empty();
      var getInfo = '?json={"cmd":"deviceinfo","user":"1","device":"'+ deviceId+'"}';
      
      $.ajax({
          url: '<?php echo $vdPath; ?>virtualDevices/device.php'+getInfo,
          type: 'get',
          dataType: 'json',
          success: function(output) {
                  console.log(JSON.stringify(output));
                  $.each(output.modes, function(idx, item){
                    $('#selectModeConfigList').append($("<option></option>").attr("value",item.name).text(item.name)); 
                  });
          },
          error: function(xhr, desc, err) {
            console.log(xhr);
            console.log('Details: ' + desc + '\nError:' + err);
          }
    }); // end ajax call

      
     $("#configDeviceForm").show();
}

function clickAddTask(event){

      // get selected device
      var selectedListItem = $("#myDeviceList").children(".pure-menu-selected");
      var deviceId = selectedListItem.attr("dev-id");
      var estString = $('#estInput').val();
      var lstString = $('#lstInput').val();
      var lstArray =  estString.split(":");
      var mode = $('#selectModeConfigList').val();
      
      if(null == mode) mode ="none";
      
      // check validity of inputed times
      var currentTime = new Date();
      var currHours = currentTime.getHours();
      var currMinutes = currentTime.getMinutes();
      if  (currHours <10)  currHours = "0" + currHours;// adjust currHours to hh format
      if  (currMinutes <10)  currMinutes = "0" + currMinutes;// adjust currMinutes to hh format
      var nowString =  currHours + ":"  +  currMinutes;

      // THIS COMPARRISSON ONLY WORK BECAUSE THE DATES ARE IN 24h format      
      if(nowString > estString){
        window.alert("cant have a start date earlier then current timestamp " + nowString);
        return;
      }else{
        if(estString >= lstString ) {
          window.alert("latest start time must be higher than earliest start time");
          return;
        }
      }
      // end of validity check

      
      //TODO: mode and execution type are currently hard coded
      var addTaskJson = '?json={"EST":"' + estString+ '","LST":"' + lstString + '","deviceID":"'+ deviceId+'","execution_type": "single_run","mode":"' +mode+'"}';
      console.log(addTaskJson);
      $.ajax({
        url: '<?php echo $path; ?>mas/add.json' + addTaskJson,
        type: 'get',
        dataType: 'html',
        success: function(output) {
                console.log(output);
                if(null != output) {
                  if (null != output.error ){
                     console.log(output.error);
                  }
                }
                window.location.href="<?php echo $path; ?>cossmiccontrol/view/homecontrol";

        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log('Details: ' + desc + '\nError:' + err);
        }
  }); // end ajax call 


}


</script>
