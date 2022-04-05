<?php
//[ ] Add Collector on/off
//[ ] Add Switch on/off

require_once "loxberry_system.php";
require_once "loxberry_web.php";
require_once "defines.php";
require_once "inc.php";

$navbar[1]['active'] = True;

// Print LoxBerry header
$L = LBSystem::readlanguage("language.ini");
LBWeb::lbheader($template_title, $helplink, $helptemplate);
?>

<style>
    .mono {
        font-family: monospace;
        font-size: 110%;
        font-weight: bold;
        color: green;

    }
</style>

<!-- Status -->
<div class="wide">Status</div>

<?php
//var_dump($_POST);
if(isset($_POST['config_change'])){
    $config['SERIAL_PORT'] = $_POST['SERIAL_PORT'];
    // Write data to disk
    file_put_contents(ECOMETER_CONFIG_FILE, json_encode($config)); 
}

//Check if pid
$running = false;
$pid = "false";
$pidfile = "$lbpconfigdir/collector.pid";

if (file_exists( "$pidfile" )){
    $pid = trim(file_get_contents($pidfile));
}

if (file_exists( "/proc/$pid" )){
    //process with a pid = $pid is running
    $running = true;
}

if($running == "true") {
?>

<p style="color:green">
    <b>The Collector is running (PID: <?=$pid?>). (<a href="?stop_collector">stop collector</a>).</b>
</p><br>


<?php
} else {
?>
    <p style="color:red">
    <b>The Collector is not running (PID:<?=$pid?>). (<a href="?start_collector">start collector</a>).</b>
</p><br>


<?php
}

$config = read_json_file(ECOMETER_CONFIG_FILE);
//echo $config->SERIAL_PORT;
?>

<!-- Config -->
<div class="wide">Config</div>
<form method="post" name="main_form" action="">
    <input type="hidden" name="config_change" value="1">
    <div style="display:flex; align-items: center; justify-content: center;">
        <div style="flex: 0 0 95%;padding:5px" data-role="fieldcontain">
            <label for="summarylink">
                <strong>Serial Port</strong><br>
                <span class="hint">???</span></label>
            <input
                type="text"
                id="summarylink"
                name="SERIAL_PORT"
                data-mini="true"
                value="<?=$config->SERIAL_PORT?>">
        </div>
    </div>
    <div style="flex: 0 0 95%;padding:5px" data-role="fieldcontain">
        <input type="submit" name="submit" value="Submit">
    </div>
</form>


<!-- MQTT -->
<div class="wide">MQTT</div>
<p>All data is transferred via MQTT. The subscription for this is
    <span class="mono">ecometer2lox/#</span>
    and is automatically registered in the MQTT gateway plugin.</p>

<?php
	// Query MQTT Settings
	$mqttcred = mqtt_connectiondetails();
	if ( !isset($mqttcred) ) {
?>

<p style="color:red">
    <b>MQTT gateway not installed!</b>
</p>

<?php
	} else {		
?>

<p style="color:green">
    <b>MQTT gateway found and it will be used.</b>
</p>

<?php
	}
	
LBWeb::lbfooter();
?>