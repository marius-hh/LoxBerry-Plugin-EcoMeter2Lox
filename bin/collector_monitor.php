<?php
//Check if the collector is running. If its not running, it will be started.
include_once "loxberry_system.php";
require_once "$lbphtmlauthdir/defines.php";

$log = LBLog::newLog( [ "name" => "Collector", "stderr" => 1 ] );
LOGSTART("Check if collector");

require_once "$lbphtmlauthdir/inc.php";

collector_start();

?>