<?php
include 'connectDB.php';
session_start();

if(isset($_POST['username']))
{
	$username=$_POST['username'];
	$password=$_POST['password'];
	
	}
	
else {	
?>
<h1>Login</h1>
<form action=test.php method=POST>
	Username: <input type=text name=username><br>
	Password: <input type=password name=username<br>
	<a href="" >New user</a>
</form>

<?php
}
?>
