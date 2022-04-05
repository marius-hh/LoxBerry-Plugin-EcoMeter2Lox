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
// Check if collector is running
$pid = collector_status();

if(isset($_GET['stop_collector']))
{
    collector_stop($pid);
    echo "<script> location.href='index.php'; </script>";
} elseif (isset($_GET['start_collector']))
{
    collector_start();
    echo "<script> location.href='index.php'; </script>";
}

if(isset($_POST['config_change']))
{
    $config['SERIAL_PORT'] = $_POST['SERIAL_PORT'];
    $config['MQTT_TRANSFER'] = $_POST['MQTT_TRANSFER'];
    // Write data to disk
    file_put_contents(ECOMETER_CONFIG_FILE, json_encode($config)); 

    //$pid = collector_status();

    if(!empty($pid)){
        //restart collector
        collector_stop($pid);

        while (!empty($pid)){
            $pid = collector_status();
            sleep (1);
        }
        collector_start();
        echo "<script> location.href='index.php'; </script>";
    }
}

if(!empty($pid)) {
?>

<p style="color:green">
    <b>The collector is running (PID: <?=$pid?>). (<a href="?stop_collector">stop collector</a>).</b>
</p><br>


<?php
} else {
?>
    <p style="color:red">
    <b>The collector is stopped. (<a href="?start_collector">start collector</a>).</b>
</p><br>


<?php
}

$config = read_json_file(ECOMETER_CONFIG_FILE);
?>

<!-- Config -->
<div class="wide">Config</div>
<form method="post" name="main_form" action="">
    <input type="hidden" name="config_change" value="1">
    <div style="display:flex; align-items: center; justify-content: center;">
        <div style="flex: 0 0 95%;padding:5px" data-role="fieldcontain">
            <label for="header">
                <strong>Serial Port</strong><br>
            </label>
            <input
                type="text"
                id="SERIAL_PORT"
                name="SERIAL_PORT"
                data-mini="true"
                value="<?=$config->SERIAL_PORT?>">
        </div>
    </div>
    
    <div style="display:flex; align-items: center; justify-content: center;">
        <div style="flex: 0 0 95%;padding:5px" data-role="fieldcontain">
            <label for="header">
                <strong>MQTT Transfer</strong><br>
            </label>
            <fieldset data-role="controlgroup" class="ui-controlgroup ui-controlgroup-vertical ui-corner-all">
                <div class="ui-controlgroup-controls ">
                    <div class="ui-checkbox">
                        <label for="MQTT_TRANSFER" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off ui-first-child ui-last-child">Enabled</label>
                        <input type="checkbox" name="MQTT_TRANSFER" id="MQTT_TRANSFER" class="refreshdisplay" <?php if($config->MQTT_TRANSFER){ echo " checked"; } ?>>
                    </div>
                    <p class="hint">If you check this box, ...</p>
                </div>
            </fieldset>
        </div>
    </div>

    <div style="flex: 0 0 95%;padding:5px" data-role="fieldcontain">
        <input type="submit" name="submit" value="Submit">
    </div>
</form>


<!-- MQTT -->
<div class="wide">MQTT</div>

<?php
	// Query MQTT Settings
	$mqttcred = mqtt_connectiondetails();
	if ( !isset($mqttcred) ) {
?>

<p style="color:red">
    <b>MQTT gateway not installed!</b>
</p>


<?php
	} elseif(!$config->MQTT_TRANSFER) {
?>


<p style="color:red">
    <b>MQTT Transfer is disabled!</b>
</p>

<?php
    } else {		
?>

<p>All data is transferred via MQTT. The subscription for this is
    <span class="mono"><?=MQTTTOPIC?>/#</span>
    and is automatically registered in the MQTT gateway plugin.</p>
<p style="color:green">
    <b>MQTT gateway found and it will be used.</b>
</p>


<?php
	}
	
LBWeb::lbfooter();
?>