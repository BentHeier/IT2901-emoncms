<?php

$schema['devices'] = array(
  'deviceid' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'name' => array('type' => 'varchar(45)', 'Null'=>'NO', 'default'=>''),
  'templateid' => array('type' => 'varchar(11)', 'Null'=>'NO'),
  'userid' => array('type' => 'int(11)', 'Null'=>'NO')
);

$schema['devices_nodes'] = array(
  'userid' => array('type' => 'int(11)', 'Null'=>'NO'),
  'deviceid' => array('type' => 'int(11)', 'Null'=>'NO'),
  'nodeid' => array('type' => 'int(11)', 'Null'=>'NO')
);

$schema['node_parameters'] = array(
  'nodeid' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'type' => array('type' => 'text', 'default'=>''),
  'driverid' => array('type' => 'varchar(45)', 'Null'=>'NO', 'default'=>''),
  'address' => array('type' => 'varchar(45)', 'Null'=>'NO', 'default'=>''),
  'userid' => array('type' => 'int(11)', 'Null'=>'NO')
);

$schema['templates'] = array(
  'templateid' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'productName' => array('type' => 'text', 'default'=>''),
  'productType' => array('type' => 'text', 'default'=>''),
  'operatingType' => array('type' => 'text', 'default'=>''),
  'description' => array('type' => 'text', 'default'=>''),
  'requiredNodeTypes' => array('type' => 'text'),
  'userid' => array('type' => 'int(11)', 'Null'=>'NO')
);

$schema['templates_modes'] = array(
  'modeid' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'templateid' => array('type' => 'int(11)', 'Null'=>'NO'),
  'modeName' => array('type' => 'text', 'default'=>'')
);

$schema['template_conf'] = array(
	'idtemplate' => array('type' => 'int(11)', 'Null'=>'NO'),
    'name' => array('type' => 'varchar(45)','Null'=>'NO','default'=>''),
    'description' => array('type' => 'text')
);

$schema['user_device_par'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'deviceid' => array('type' => 'int(11)', 'Null'=>'NO'),
  'name' => array('type' => 'varchar(45)','Null'=>'NO','default'=>''),
  'value' => array('type' => 'varchar(200)','Null'=>'NO','default'=>'')
);

?>
