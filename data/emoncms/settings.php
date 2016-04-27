<?php

    /*

    Database connection settings

    */

    $server   = "localhost";
    $database = "emoncms";
    $username = "emoncms";
    $password = "eg2Bai5okaiz";

    $redis_enabled = true;
    
    $feed_settings = array(

        'enable_mysql_all'=>true,
        
        'timestore'=>array(
            'adminkey'=>"_TS_ADMINKEY_"
        ),

        'graphite'=>array(
            'port'=>0,
            'host'=>0
        ),
        
        // The default data directory is /var/lib/phpfiwa,phpfina,phptimeseries on windows or shared hosting you will likely need to specify a different data directory.
        // Make sure that emoncms has write permission's to the datadirectory folders
        
        'phpfiwa'=>array(
            'datadir'=>'/var/lib/phpfiwa/'
        ),
        'phpfina'=>array(
            'datadir'=>'/var/lib/phpfina/'
        ),
        'phptimeseries'=>array(
            'datadir'=>'/var/lib/phptimeseries/'
        ),
        'phptimestore'=>array(
            'datadir'=>'/var/lib/phptimestore/'
        )
    );
    
    // (OPTIONAL) Used by password reset feature
    $smtp_email_settings = array(
      'host'=>"_SMTP_HOST_",
      'username'=>"_SMTP_USER_",
      'password'=>"_SMTP_PASSWORD_",
      'from'=>array('_SMTP_EMAIL_ADDR_' => '_SMTP_EMAIL_NAME_')
    );

    // To enable / disable password reset set to either true / false
    // default value of " _ENABLE_PASSWORD_RESET_ " required for .deb only
    $enable_password_reset = FALSE;
    
    // Checks for limiting garbage data?
    $max_node_id_limit = 32;

    /*

    Default router settings - in absence of stated path

    */

    // Default controller and action if none are specified and user is anonymous
    $default_controller = "user";
    $default_action = "login";

    // Default controller and action if none are specified and user is logged in
    $default_controller_auth = "user";
    $default_action_auth = "view";

    // Public profile functionality
    $public_profile_enabled = TRUE;
    $public_profile_controller = "dashboard";
    $public_profile_action = "view";

    /*

    Other

    */

    // Theme location
    $theme = "basic";

    // Error processing
    $display_errors = TRUE;

    // Allow user register in emoncms
    $allowusersregister = TRUE;

    // Enable remember me feature - needs more testing
    $enable_rememberme = TRUE;

    // Skip database setup test - set to false once database has been setup.
    $dbtest = TRUE;

    // Log4PHP configuration
    $log4php_configPath = 'logconfig.xml';
    
    /*
    
    CoSSMic settings - This section is for configuring the CoSSMic sync process to post data into a cloud emoncms.
    
    */
    
    // Address of the cloud emoncms
    $cossmic['host'] = 'http://cloud.cossmic.eu/emoncms/';
    
    // API Key. Each Neighbourhood has its own user and API Key at the cloud emoncms
    $cossmic['apikey'] = 'YOUR_NBH_API_KEY';
    
    // Specify the ID of this household. This ID will be used as the NodeID in the cloud emoncms.
    // Convention: KN04 = node 4, KN10 = node 10, ...
    $cossmic['node'] = 0;
    
    // Household configuration
    // The PV system does not feed-in directly into to grid, but its energy will first be consumed in the household
    $cossmic['household_selfconsumption'] = TRUE;
