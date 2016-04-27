--
-- Table structure for table `templates`
--

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES (1,'Main meter','grid','input','missing','energyOut',9);
INSERT INTO `templates` VALUES (2,'Main meter (bidirectional)','grid','input','missing','energyOut,energyIn',1);

INSERT INTO `templates` VALUES (3,'Solar panel','photovoltaic','input','missing','energyOut',1);
INSERT INTO `templates` VALUES (4,'Virtual panel','photovoltaic','input','missing','powerOut',1);

INSERT INTO `templates` VALUES (5,'Heat pump','heat-pump','continuously_run','missing','controller,energyIn',1);
INSERT INTO `templates` VALUES (6,'Air conditioner','air-conditioner','continuously_run','missing','controller,energyIn',1);
INSERT INTO `templates` VALUES (7,'Freezer','freezer','continuously_run','missing','controller,energyIn',1);
INSERT INTO `templates` VALUES (8,'Refrigerator','refrigerator','continuously_run','missing','controller,energyIn',1);

INSERT INTO `templates` VALUES (9,'Electric vehicle','electric-vehicle','ev','missing','controller,energyIn,soc',1);

INSERT INTO `templates` VALUES (10,'Dishwasher (Default)','dishwasher','single_run','missing','controller,energyIn',1);
INSERT INTO `templates` VALUES (11,'Washing machine (Default)','washing-machine','single_run','missing','controller,energyIn',1);
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates_modes`
--

--
-- Dumping data for table `templates_modes`
--

LOCK TABLES `templates_modes` WRITE;
/*!40000 ALTER TABLE `templates_modes` DISABLE KEYS */;
INSERT INTO `templates_modes` VALUES (1,1,'default');
INSERT INTO `templates_modes` VALUES (2,2,'default');
INSERT INTO `templates_modes` VALUES (3,3,'default');
INSERT INTO `templates_modes` VALUES (4,4,'default');
INSERT INTO `templates_modes` VALUES (5,5,'default');
INSERT INTO `templates_modes` VALUES (6,6,'default');
INSERT INTO `templates_modes` VALUES (7,7,'default');
INSERT INTO `templates_modes` VALUES (8,8,'default');
INSERT INTO `templates_modes` VALUES (9,9,'default');
INSERT INTO `templates_modes` VALUES (10,10,'65 degree');
INSERT INTO `templates_modes` VALUES (11,10,'50 degree');
INSERT INTO `templates_modes` VALUES (12,10,'40 degree');
INSERT INTO `templates_modes` VALUES (13,11,'60 degree');
INSERT INTO `templates_modes` VALUES (14,11,'40 degree');
INSERT INTO `templates_modes` VALUES (15,11,'30 degree');
INSERT INTO `templates_modes` VALUES (16,11,'20 degree');
/*!40000 ALTER TABLE `templates_modes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_conf`
--

--
-- Dumping data for table `template_conf`
--

LOCK TABLES `template_conf` WRITE;
/*!40000 ALTER TABLE `template_conf` DISABLE KEYS */;
INSERT INTO `template_conf` VALUES (3,'Hco','missing'),(3,'Umpp0','missing'),(3,'Impp0','missing'),(3,'tco_mpp','missing'),(3,'eta_dirt','missing'),(3,'eta_field','missing'),(3,'eta_inv','missing'),(3,'mow','missing'),(3,'moh','missing'),(3,'npar','missing'),(3,'nser','missing'),(3,'tilt','missing'),(3,'orientation','missing');
INSERT INTO `template_conf` VALUES (4,'Hco','missing'),(4,'Umpp0','missing'),(4,'Impp0','missing'),(4,'tco_mpp','missing'),(4,'eta_dirt','missing'),(4,'eta_field','missing'),(4,'eta_inv','missing'),(4,'mow','missing'),(4,'moh','missing'),(4,'npar','missing'),(4,'nser','missing'),(4,'tilt','missing'),(4,'orientation','missing');
INSERT INTO `template_conf` VALUES (9,'capacity','Energy capacity from datasheet in kWh'),(9,'ChargingEfficiency','Actual capacity, as a percentage of the capacity'),(9,'MinimumChargingLevel','Minimum charging level, which allows the storage to work properly, as a percentage of the capacity'),(9,'MaxChargingPower','Maximum charging power from datasheet in kW'),(9,'MinChargingPower','Minimum charging power from datasheet in kW'),(9,'minimum_energy_target','Desired energy level to be charged as soon as possible, as a percentage of the capacity'),(9,'energy_target','Desired energy level to be reached within the target deadline, as a percentage of the capacity'),(9,'target_deadline','Deadline within which the user would like the storage to be charged at least to the energy target, as a time of the day [HH:MM]');
/*!40000 ALTER TABLE `template_conf` ENABLE KEYS */;
UNLOCK TABLES;