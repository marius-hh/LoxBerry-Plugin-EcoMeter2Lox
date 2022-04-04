<?php
//Check if the collector is running. If its not running, it will be started.
include_once "loxberry_system.php";

$pid = "false";
$pidfile = "$lbpconfigdir/collector.pid";

if (file_exists( "$pidfile" )){
    $pid = trim(file_get_contents($pidfile));
}

if (file_exists( "/proc/$pid" )){
    //process with a pid = $pid is running
    print("Collector running (PID: $pid)...\n");
} else {
    print("Collector down, trying to start collector...\n");
    $pid = shell_exec("$lbpbindir/collector.py 2>/dev/null >/dev/null & echo $!");
    if(!empty($pid)) {
        file_put_contents($pidfile, trim($pid));
        print("Collector started, neu PID: $pid");
    } else {
        print("Could not start collector...\n");
    }
}
?>