<html>
<body>
<table border=1>
<tr>
<th> Device Type</th>
<th style="colspan:10; align:center"> Device ID</th>
</tr>
<?php
include 'connectDB.php';
$query="SELECT * from device_list";
$res = mysqli_query($con,$query) or die("Bad SQL");
$num=mysqli_num_rows($res);

while($row = mysqli_fetch_array($res)){
	$device_type = $row[1];
	$device_id=$row[0];
	
	echo "<tr><td>".$device_type."</td>";
	echo "<td style=\"align:center\"><a  href=\"".$device_type.".php?id=".$device_id."\">".$device_id."</a></td></tr>";
}

?>


</table>
<div id="addResource">
</div>
<?php
include 'connectDB.php';
$query="SELECT type from device_type";
$res = mysqli_query($con,$query) or die("Bad SQL");
?>




<script>
function add() {
   if(document.getElementById('addResources').value=="Show Add Resource")
	{
		var html = "<br><br><form action=\"addDevice.php\" method=\"POST\">Type of Device: <select name=\"deviceType\"><?php while($row = mysqli_fetch_array($res)){ ?><option value=\"<?php echo $row[0]; ?>\"><?php echo $row[0]; }?></option></select><br>ID<input type=\"text\" name =\"deviceId\"/><br><input type=\"submit\" value=\"Add\"/></form>";
		document.getElementById('addResource').innerHTML= html;
		document.getElementById('addResources').value="Hide Add Resource";
	}
	else
	{
		document.getElementById('addResource').innerHTML= "";
		document.getElementById('addResources').value="Show Add Resource";
	}
	//alert("Ciao");
}
</script>


<input type = "button" id ="addResources" value = "Show Add Resource" onclick="add();"/>
</body>
</html>
