<?php
//[ ] Add function start collector
//[ ] Add function stop collector
//[ ] Add function restart collector
//[ ] Add MQTT send value

//include_once "loxberry_system.php";
include_once "loxberry_io.php";
require_once "loxberry_log.php";
require_once "defines.php";
require_once "phpMQTT/phpMQTT.php";

// Create and start log
// Shutdown function
register_shutdown_function('shutdown');
function shutdown()
{
	global $log;
	
	if(isset($log)) {
		LOGEND("Processing finished");
	}
}

$log = LBLog::newLog( [ "name" => "EcoMeter2Lox", "stderr" => 1 ] );
LOGSTART("Start Logging");

function read_json_file($filename)
{
	// Get Commands from file

	if( !file_exists($filename) ) {
		//LOGDEB("get_commands: Commandfile missing, aborting");
		//LOGERR("Commandfile not found, aborting.");
	} else {
		//LOGDEB("get_commands: Read commandfile");
		$output = json_decode(file_get_contents($filename));
	}
	return $output;
}

function mqttpublish($data, $mqttsubtopic="")
{
	// Function to send data to mqtt
	
	// MQTT requires a unique client id
	$client_id = uniqid(gethostname()."_client");
	$creds = mqtt_connectiondetails();

	// Be careful about the required namespace on inctancing new objects:
	$mqtt = new Bluerhinos\phpMQTT($creds['brokerhost'],  $creds['brokerport'], $client_id);

    if( $mqtt->connect(true, NULL, $creds['brokeruser'], $creds['brokerpass'] ) ) {
		LOGDEB("mqttpublish: MQTT connection successful");
		LOGOK("MQTT: Connection successful.");

		if(is_object($data) or is_array($data)){
			foreach ($data as $key => $value) {
				if(is_object($value)) {
					foreach ($value as $skey => $svalue){
						if(is_object($svalue)) {
							foreach ($svalue as $sskey => $ssvalue){
								if(!empty($ssvalue)){ 
									if($sskey == "timestamp") { $ssvalue = epoch2lox(substr($ssvalue, 0, 10)); } //epochetime maxlength
									$mqtt->publish(MQTTTOPIC."$mqttsubtopic/$key/$skey/$sskey", $ssvalue, 0, 1);
									LOGDEB("mqttpublish: ".MQTTTOPIC."$mqttsubtopic/$key/$skey/$sskey: $ssvalue");
								}
							}
						} else {
							if(!empty($svalue)){ 
								if($skey == "timestamp") { $svalue = epoch2lox(substr($svalue, 0, 10)); } //epochetime maxlength
								$mqtt->publish(MQTTTOPIC."$mqttsubtopic/$key/$skey", $svalue, 0, 1);
								LOGDEB("mqttpublish: ".MQTTTOPIC."$mqttsubtopic/$key/$skey: $svalue");
							}
						}
					}
				} else {
					if(!empty($value)){
						if(is_array($value)){
							$value = implode(",", $value);
						}
						$countsubtopics = explode("/", $mqttsubtopic);
						if ($countsubtopics < 3) {
							$mqtt->publish(MQTTTOPIC."/summary$mqttsubtopic/$key", $value, 0, 1);
							LOGDEB("mqttpublish: ".MQTTTOPIC."/summary$mqttsubtopic/$key: $value");
						} else {
							$mqtt->publish(MQTTTOPIC."$mqttsubtopic/$key", $value, 0, 1);
							LOGDEB("mqttpublish: ".MQTTTOPIC."$mqttsubtopic/$key: $value");
						}
					}
				}
			}
		} else {
			$mqtt->publish(MQTTTOPIC."$mqttsubtopic", $data, 0, 1);
			LOGDEB("mqttpublish: ".MQTTTOPIC."$mqttsubtopic: $data");
		}
		//[x] Query timestamp added
		$mqtt->publish(MQTTTOPIC."/mqtt_timestamp", epoch2lox(time()), 0, 1);
		LOGDEB("mqttpublish: ".MQTTTOPIC."/mqtt_timestamp: ".epoch2lox(time()));
        $mqtt->close();
    } else {
		LOGDEB("mqttpublish: MQTT connection failed");
		LOGERR("MQTT: Connection failed.");
    }
}
?>