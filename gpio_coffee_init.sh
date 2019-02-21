#!/bin/bash

# Enable the needed pins.
echo 2 > '/sys/class/gpio/export';
echo 3 > '/sys/class/gpio/export';
echo 4 > '/sys/class/gpio/export';

# Change directions to "out".
echo 'out' > '/sys/class/gpio/gpio2/direction';
echo 'out' > '/sys/class/gpio/gpio3/direction';
echo 'out' > '/sys/class/gpio/gpio4/direction';

# Turn the pins off.
echo 1 > '/sys/class/gpio/gpio2/value';
echo 1 > '/sys/class/gpio/gpio3/value';
echo 1 > '/sys/class/gpio/gpio4/value';

# Change permissions so the webserver can write to the 'value'-files
chmod 777 '/sys/class/gpio/gpio2/value';
chmod 777 '/sys/class/gpio/gpio3/value';
chmod 777 '/sys/class/gpio/gpio4/value';
