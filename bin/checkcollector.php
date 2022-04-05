<?php
//Check if the collector is running. If its not running, it will be started.
include_once "loxberry_system.php";
require_once "$lbphtmlauthdir/defines.php";
require_once "$lbphtmlauthdir/inc.php";

$pid = "false";
$pidfile = "$lbpconfigdir/collector.pid";

if (file_exists( "$pidfile" )){
    $pid = trim(file_get_contents($pidfile));
}

if (file_exists( "/proc/$pid" )){
    //process with a pid = $pid is running
    //print("Collector running (PID: $pid)...\n");
    LOGOK("checkcollector: Collector running (PID: $pid)...");
} else {
    //print("Collector down, trying to start collector...\n");
    LOGINF("checkcollector: Collector down, trying to start collector...");
    $pid = trim(shell_exec("$lbpbindir/collector.py 2>/dev/null >/dev/null & echo $!"));
    if(!empty($pid)) {
        file_put_contents($pidfile, $pid);
        //print("Collector started, neu PID: $pid");
        LOGOK("checkcollector: Collector started, neu PID: $pid");
    } else {
        //print("Could not start collector...\n");
        LOGERR("Could not start collector...");
    }
}
?>