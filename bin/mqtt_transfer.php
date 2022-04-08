<?php
//Check if the collector is running. If its not running, it will be started.
include_once "loxberry_system.php";
require_once "$lbphtmlauthdir/defines.php";

$log = LBLog::newLog( [ "name" => "Collector", "stderr" => 1 ] );
LOGSTART("MQTT transfer");

require_once "$lbphtmlauthdir/inc.php";

//MQTT Transfer if enabled
$config = read_json_file(ECOMETER_CONFIG_FILE);
$data = read_json_file(ECOMETER_DATA_FILE);

if($config->MQTT_TRANSFER and isset($data))
{
    mqttpublish($data);
}
?>