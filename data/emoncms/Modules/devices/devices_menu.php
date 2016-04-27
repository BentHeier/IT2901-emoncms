<?php

  $domain = "messages";
  bindtextdomain($domain, "Modules/input/locale");
  bind_textdomain_codeset($domain, 'UTF-8');

  //$menu_left[] = array('name'=> dgettext($domain, "Devices"), 'path'=>"devices/devices" , 'session'=>"write", 'order' => 3 );

  if($user->get_admin($session['userid']) == 1){
		//$menu_left[] = array('name'=> dgettext($domain, "Driver"), 'path'=>"driver/node" , 'session'=>"write", 'order' => 3 );
	}
    else{
		$menu_left[] = array('name'=> dgettext($domain, "Devices"), 'path'=>"devices/devices" , 'session'=>"admin", 'order' => 3 );
	}
?>
