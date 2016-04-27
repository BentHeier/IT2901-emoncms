<?php
    global $session, $user;
  
    if ($session['write']) $apikey = "?apikey=".$user->get_apikey_write($session['userid']); else $apikey = "";
  
	//Using the get_admin function in the user model to check if the user is an admin. 
	//If the user is an admin he will not get the myelectric menu (considering all end-systems are supposed to have only one account and this is an admin account from what we have heard)
  
	if($user->get_admin($session['userid']) == 1){
		
	}
    else{
		$menu_left[] = array('name'=>"My Electric", 'path'=>"myelectric".$apikey , 'session'=>"admin", 'order' => 1 );
	}
?>