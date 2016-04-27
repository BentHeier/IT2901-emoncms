<?php

$schema['driver'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'name' => array('type' => 'varchar(45)','Null'=>'NO','default'=>''),
  'description' => array('type' => 'text','default'=>'')
);

$schema['user_drivers'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'type' => array('type' => 'int(11)', 'Null'=>'NO'),
  'status' => array('type' => 'int(11)', 'Null'=>'NO', 'default'=>'0'),
  'userid' => array('type' => 'int(11)', 'Null'=>'NO', 'default'=>'0'),
);

$schema['user_driver_par'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'driverid' => array('type' => 'int(11)', 'Null'=>'NO'),
  'name' => array('type' => 'varchar(45)','Null'=>'NO','default'=>''),
  'value' => array('type' => 'varchar(200)','Null'=>'NO','default'=>'')
);

$schema['node_driver'] = array(
  'node' => array('type' => 'int(11)', 'Null'=>'NO'),
  'driver_id' => array('type' => 'int(11)', 'Null'=>'NO'),
  'user_id' => array('type' => 'int(11)', 'Null'=>'NO')
);
$schema['driver_parameters'] = array(
  'id_driver' => array('type' => 'int(11)', 'Null'=>'NO'),
    'name' => array('type' => 'varchar(45)','Null'=>'NO','default'=>'')
);

?>
