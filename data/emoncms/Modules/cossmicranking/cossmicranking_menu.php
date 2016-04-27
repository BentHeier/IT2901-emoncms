<?php
	global $session;

    $domain = "messages";
    bindtextdomain($domain, "Modules/input/locale");
    bind_textdomain_codeset($domain, 'UTF-8');

	//All users should have quick menu access to the Ranking page.
	
	$menu_left[] = array('name'=> dgettext($domain, "Ranking"), 'path'=>"cossmiccontrol/view/ranking" , 'session'=>"write", 'order' => 13 );
?>