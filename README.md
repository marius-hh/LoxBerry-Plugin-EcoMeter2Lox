# LoxBerry-Plugin-EcoMeter2Lox
This plugin collects EcoMeter data from a serial port. All data can be transferred via MQTT or be requested over HTTP. The subscription for MQTT is `ecometer2lox/#` and is automatically registered in the Loxberry MQTT gateway plugin.
## Example query
### Returns the last catched values from EcoMeter in json
`http://loxberry/plugins/ecometer2lox/index.php`