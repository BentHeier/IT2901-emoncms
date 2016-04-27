<?php

/*
	All Emoncms code is released under the GNU Affero General Public License.
	See COPYRIGHT.txt and LICENSE.txt.

	---------------------------------------------------------------------
	Emoncms - open source energy visualisation
	Part of the OpenEnergyMonitor project:
	http://openenergymonitor.org

*/

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function cossmiccontrol_controller()
{
	global $path, $session, $route;

	$result = false;

	// Load html,css,js pages to the client
	if ($route->format == 'html')
	{
		if ($route->action == 'view' && $session['write'])
		{
			if ($route->subaction == 'dashboard') $result = view("Modules/cossmiccontrol/Views/summary.php", array());
			if ($route->subaction == 'appliances') $result = view("Modules/cossmiccontrol/Views/appliances.php", array());
			if ($route->subaction == 'scheduler') $result = view("Modules/cossmiccontrol/Views/scheduler.php", array());
			if ($route->subaction == 'history') $result = view("Modules/cossmiccontrol/Views/history.php", array());
			if ($route->subaction == 'ranking') $result = view("Modules/cossmiccontrol/Views/ranking.php", array());
			if ($route->subaction == 'addMeter') $result = view("Modules/cossmiccontrol/Views/addMeter.php", array());
		}
	}

	// JSON API
	if ($route->format == 'json')
	{
		
	}

	return array('content'=>$result);
}
