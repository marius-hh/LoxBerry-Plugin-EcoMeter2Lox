<?php
//Check if the collector is running. If its not running, it will be started.
include_once "loxberry_system.php";
require_once "$lbphtmlauthdir/defines.php";
require_once "$lbphtmlauthdir/inc.php";

collector_start();

//MQTT Transfer if enabled
$config = read_json_file(ECOMETER_CONFIG_FILE);
$data = read_json_file(ECOMETER_DATA_FILE);

if($config->MQTT_TRANSFER and isset($data))
{
    mqttpublish($data);
}
?>