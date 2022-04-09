<?php
require_once "loxberry_log.php";

define ("ECOMETER_CONFIG_FILE", "$lbpconfigdir/plugin_config.json");
define ("ECOMETER_DATA_FILE", "$lbpdatadir/collector_data.json"); 
define ("MQTTTOPIC", "$lbpplugindir");

// Template
$template_title = "EcoMeter2Lox " . LBSystem::pluginversion();
//[x] Set helplink
$helplink = ""; 
//"https://loxwiki.atlassian.net/l/c/CEeb8Rmh";

// The Navigation Bar
$navbar[1]['Name'] = "Settings";
$navbar[1]['URL'] = "index.php";

$navbar[98]['Name'] = "Pluginlog";
$navbar[98]['URL'] = "/admin/system/logmanager.cgi?package=$lbplogdir";
$navbar[98]['target'] = '_blank';

$navbar[99]['Name'] = "Collectorlog";
$navbar[99]['URL'] = "/admin/system/tools/logfile.cgi?logfile=$lbplogdir/collector.log&header=html&format=template";
$navbar[99]['target'] = '_blank';