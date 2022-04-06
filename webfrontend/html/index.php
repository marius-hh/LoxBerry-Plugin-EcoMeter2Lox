<?php
//Check if the collector is running. If its not running, it will be started.
include_once "loxberry_system.php";
require_once "$lbphtmlauthdir/defines.php";
require_once "$lbphtmlauthdir/inc.php";

LOGDEB("Load config and data...");
$config = read_json_file(ECOMETER_CONFIG_FILE);
$data = read_json_file(ECOMETER_DATA_FILE);

//MQTT Transfer if enabled
if($config->MQTT_TRANSFER and isset($data))
{
    mqttpublish($data);
}

LOGDEB("Generate output...");
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
?>