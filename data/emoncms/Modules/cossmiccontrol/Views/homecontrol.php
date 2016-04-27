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

// debugging echo implode("-",$decomposedPath); echo sizeof($decomposedPath); echo $vdPath;
?>

<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>Modules/cossmiccontrol/Views/cossmiccontrol_view.css">

<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/cossmiccontrol/Views/json.js"></script>

<a href="<?php echo $path; ?>cossmiccontrol/view/summary">Summary</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/homecontrol">Home control</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/settings">Settings</a> | <a href="<?php echo $path; ?>cossmiccontrol/view/history">History</a>

<p>
<p>


<table class="table table-condensed table-hover" id="taskTable">
  <thead>
    <tr>
      <th>Device Name</th>
      <th>Status</th>
      <th>Program</th>
      <th>Earliest Start Time</th>
      <th>Latest Start Time</th>
      <th>Actual Start Time</th>
      <th>Actual Finishing Time</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>


<div style="margin: 15px auto 30px auto">
<table class="cossmictable" style="width:60%;visibility: hidden" id="controltable">
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

/* TODO uncomment when integrating Katharinas code

// get list of ids and names of all the user's device power feeds
$result = $mysqli->query("SELECT id,name FROM feeds WHERE name REGEXP '_power$' AND userid = '$userid'");
$i = 0;
while ($row = (array)$result->fetch_object()) {
    $feedlist_power[$i]['feedid'] = $row['id'];
    $feedlist_power[$i]['devicename'] = str_replace("_power","",$row['name']);
    $i++;
}

$nr_powerfeeds = count($feedlist_power);




*/

?>

<script type="application/javascript">
var path = "<?php echo $path; ?>";


//var feedlist_power = <?php /*echo json_encode($feedlist_power);*/ ?>;
//var nr_powerfeeds = <?php /*echo $nr_powerfeeds;*/ ?>;
/* TODO uncomment when integrating Katharinas code

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

*/
  $(document).ready( function () {
    homeControlSettings()
} );

function  homeControlSettings(){


    

    var listTaskJson = '?json={"status":10,"info":true}';
    
    $.when(
    
      $.ajax({
          url: '<?php echo $path; ?>mas/list.json' + listTaskJson,
          type: 'get',
          dataType: 'json'})
       ,
       $.ajax({
            url: '<?php echo $vdPath; ?>virtualDevices/device.php',
            type: 'get',
            dataType: "json",
            data: {'json':'{"cmd":"list","user":"1"}'}
            })
    )
    .then(function( resultListTask,resultListDevices) {
           console.log(JSON.stringify(resultListTask[0]));
           console.log(JSON.stringify(resultListDevices[0]));
           var deviceHash = new Object();
           $.each(resultListDevices[0].devicelist, function(idx, item){
                    deviceHash[item.id] = item.name;
           });
           $.each(resultListTask[0].tasks, function(idx, item){
                    var id = item.id;
                    var devId = item.deviceID;
                    var status;
                    switch(item.status) {
                      case "0":
                          status = "Not yet scheduled"
                          break;
                      case "1":
                          status = "Scheduled"
                          break;
                      case "4":
                          status = "Running"
                          break;
                      case "5":
                          status = "Completed"
                          break;
                      default:
                          status = "Undetermined"
                    }
                   
                    var mode = item.mode;
                    var est = item.EST;
                    var lst = item.LST;
                    var aet = item.AET;
                    var ast = item.AST;
                    var name = '';
                    if (deviceHash.hasOwnProperty(devId)) {
                      name = deviceHash[devId];
                    }
                     var htmlRow= '<tr task-id="' +id+ '" device-id="' + devId + '"><td>' + name + ' </td><td> ' + status + ' </td><td> ' + mode +  ' </td><td> ' + est + ' </td><td> ' + lst +
                    ' </td><td> ' + ast + ' </td><td>' + aet + '</td><td><a href="#" onclick="deleteTask('+id+',\''+aet+'\')"><i class="icon-trash"></i></a></td></tr>';
                     $("#taskTable > tbody").prepend(htmlRow);        
          });
          

   });

  
}

function deleteTask(id,aet){
  if (aet != "UNDEFINED"){
    window.alert("It is not possible to delete scheduled tasks ");
    return;
  }
      $.ajax({
        url: '<?php echo $path; ?>mas/delete.json?id=' + id,
        type: 'get',
        dataType: 'json',
        success: function(output) {
                console.log(output);
                if(null != output) {
                  if (output.result != "success"){
                     if(!output.result){ // task deleted
                        window.alert("It was not possible to delete the task, it probably has already been scheduled");
                     }
                     location.reload();
                  }
                }
                location.reload();
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log('Details: ' + desc + '\nError:' + err);
          window.alert("error when trying to delete the task");
        }
  }); // end ajax call
  
}


</script>
