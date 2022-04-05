<?php

define ("ECOMETER_CONFIG_FILE", "$lbpconfigdir/plugun_config.json");
define ("ECOMETER_DATA_FILE", "$lbpdatadir/collector_data.json"); 
define ("MQTTTOPIC", "${lbpplugindir}");

// Template
$template_title = "EcoMeter2Lox " . LBSystem::pluginversion();
//[ ] Set helplink
$helplink = ""; 
//"https://loxwiki.atlassian.net/l/c/CEeb8Rmh";

// Command URI
$lbzeurl ="http://&lt;user&gt;:&lt;pass&gt;@".LBSystem::get_localip()."/admin/plugins/".LBPPLUGINDIR."/tesla_command.php";

// The Navigation Bar
$navbar[1]['Name'] = "Settings";
$navbar[1]['URL'] = 'index.php';
 
//$navbar[2]['Name'] = "Test queries";
//$navbar[2]['URL'] = 'testqueries.php';


$navbar[98]['Name'] = "Logfiles";
$navbar[98]['URL'] = '/admin/system/logmanager.cgi?package='.LBPPLUGINDIR;
$navbar[98]['target'] = '_blank';

$navbar[99]['Name'] = "Collector Log";
$navbar[99]['URL'] = "/admin/system/tools/logfile.cgi?logfile=$lbplogdir/collector.log&header=html&format=template";
$navbar[99]['target'] = '_blank';