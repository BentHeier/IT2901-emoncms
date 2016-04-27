<?php
	global $session;

    $domain = "messages";
    bindtextdomain($domain, "Modules/input/locale");
    bind_textdomain_codeset($domain, 'UTF-8');

	//All users should have quick menu access to the CoSSMic control page
	
	$menu_left[] = array('name'=> dgettext($domain, "CoSSMic Dashboard"), 'path'=>"cossmiccontrol/view/dashboard" , 'session'=>"write", 'order' => 9 );
?>