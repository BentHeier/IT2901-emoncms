<?php
	global $session;

    $domain = "messages";
    bindtextdomain($domain, "Modules/input/locale");
    bind_textdomain_codeset($domain, 'UTF-8');

	//All users should have quick menu access to the Scheduler page.
	
	$menu_left[] = array('name'=> dgettext($domain, "Scheduler"), 'path'=>"cossmiccontrol/view/scheduler" , 'session'=>"write", 'order' => 10 );
?>