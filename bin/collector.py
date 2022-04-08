#!/usr/bin/env python3
import datetime
import logging
from logging.handlers import RotatingFileHandler
import threading
import os

import serial
import struct
import json

# 
# Hier die entsprechende Schnittstelle definieren
#[x] Get serial port from config file
#ECOMETER_SERIAL_PORT   = "/dev/ttyUSB0"

# Ab hier muss nichts mehr geändert werden
#[ ] Get Workingdirectory, log, data
ECOMETER_LOG_FILE    = "/opt/loxberry/log/plugins/ecometer2lox/collector.log"
ECOMETER_DATA_FILE   = "/opt/loxberry/data/plugins/ecometer2lox/collector_data.json"
ECOMETER_CONFIG_FILE = "/opt/loxberry/config/plugins/ecometer2lox/plugin_config.json"

def setup_logger():
    logger = logging.getLogger("EcoMeter")
    logger.setLevel(logging.DEBUG)
    file_handler = RotatingFileHandler(ECOMETER_LOG_FILE, maxBytes=100000, backupCount=1)
    file_handler.setFormatter(
        logging.Formatter(
            "%(asctime)s - %(name)s - %(levelname)s - %(message)s"
        )
    )
    logger.addHandler(file_handler)
    return logger

LOGGER = setup_logger()

try:
    with open(ECOMETER_CONFIG_FILE) as cf:
        config = json.load(cf)
        LOGGER.info("Read config file...")
        ECOMETER_SERIAL_PORT = config['SERIAL_PORT']
        cf.close()
except Exception:
    # writing failed, ignore
    LOGGER.error("Read config file failed, ignore...")

_ecometer_result = []
_ecometer_lock = threading.Lock()

def set_ecometer_result(result):
    global _ecometer_result
    with _ecometer_lock:
        _ecometer_result = result

        try:
            with open(ECOMETER_DATA_FILE, "w") as f:
                json.dump(result, f)
                LOGGER.info("Data saved...")
        except Exception:
            # writing failed, ignore
            LOGGER.exception("writing failed, ignore...")

def serial_read_loop():
    with serial.Serial(ECOMETER_SERIAL_PORT, 115200) as connection:
        while True:
            # Make sure that are no old bytes left in the input buffer.
            connection.reset_input_buffer()
            LOGGER.info("Waiting for Data...")

            # Make sure, we find the beginning of a block.
            data = connection.read(22)
            LOGGER.info("Data was recived")
            LOGGER.debug("data: %s", data)

            (magic, _length, _command, _flags,
                hour, minute, second, _start, _end,
                temperature, ul_lage, usable_level, capacity, _crc
            ) = struct.unpack(">2shbb3bhhb4h", data)
            
            result = {
                    "time": f"{hour:02d}:{minute:02d}:{second:02d}", 
                    "temp_f": temperature - 40, 
                    "temp_c": round((temperature - 40 - 32) / 1.8), 
                    "ullage": ul_lage,
                    "useablelevel": usable_level, 
                    "useablecapacity": capacity, 
                    "useablepercent": round(usable_level / capacity * 100), 
                    "timestamp": int(datetime.datetime.now().timestamp())
                }
            
            LOGGER.debug("time: %s, temp_f: %s, temp_c: %s, ullage: %s, useablelevel: %s, useablecapacity: %s, useablepercent: %s, timestamp: %s", f"{hour:02d}:{minute:02d}:{second:02d}", temperature, int((temperature - 40 - 32) / 1.8), ul_lage, usable_level, capacity, int(usable_level / capacity * 100), int(datetime.datetime.now().timestamp()))
            set_ecometer_result(result)
            #[ ] Get Workingdirectory, log, data
            os.system('php ./mqtt_transfer.php')

def main():
    try:
        ECOMETER_SERIAL_PORT
    except NameError:
        LOGGER.error("Serial port not valid")
        quit()

    LOGGER.info("Initizalize (%s)...", ECOMETER_SERIAL_PORT)
    serial_read_loop()

if __name__ == "__main__":
    main()