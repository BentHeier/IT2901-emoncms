<?
	$fh = fopen('/var/cossmic/driver/lib/cul-pipe', 'w');
	
	if ($fh) {
		fwrite($fh, "set ".$cmd['address']." ".$cmd['status']."\r\n");
		fclose($fh);

		$result = "ok";
	}
	else {
		$result = "can't open FIFO file";
	}
?>