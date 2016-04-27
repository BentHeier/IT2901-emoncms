<?
 $fh = fopen($parameters['fifo'], 'w');
 
 if ($fh) {
  for ($i = 0; $i < count($cmd->{'cmd'}); $i++) {
   fwrite($fh, $nodeid.";".$cmd->{'cmd'}[$i]->{'parameter'}.";".$cmd->{'cmd'}[$i]->{'value'}."\r\n");
  }
 
  fclose($fh);
  
  $result = "ok";
 }
 else {
  $result = "can't open FIFO file";
 }
?>
