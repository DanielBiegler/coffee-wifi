<?php
// For dev purposes enable all errors and warnings
error_reporting(E_ALL);

$path_gpio_power = '/sys/class/gpio/gpio2';
$path_gpio_cup_one = '/sys/class/gpio/gpio3';
$path_gpio_cup_two = '/sys/class/gpio/gpio4';

$GPIO_ON = '0';
$GPIO_OFF = '1';

$BUTTON_PRESS_DELAY = 500000; // in microseconds

$HTTP_RESPONSE_CODE_OK = 200;
$HTTP_RESPONSE_CODE_SERVERERROR = 500;


/**
 * Turns the coffee pins on and - after a delay - off again.
 * @return int Either response code $HTTP_RESPONSE_CODE_OK for success, or response code $HTTP_RESPONSE_CODE_SERVERERROR for failure.
 */
function make_coffee($num_cups)
{
    global $path_gpio_cup_one
    , $path_gpio_cup_two
    , $GPIO_ON
    , $GPIO_OFF
    , $BUTTON_PRESS_DELAY
    , $HTTP_RESPONSE_CODE_OK
    , $HTTP_RESPONSE_CODE_SERVERERROR;

    
    $path_gpio_cup_amount_value = NULL;
    if($num_cups === '1')
    {
        $path_gpio_cup_amount_value = $path_gpio_cup_one.'/value';
    } 
    elseif($num_cups === '2')
    {
        $path_gpio_cup_amount_value = $path_gpio_cup_two.'/value';
    }
    else
    {
        echo "Error: Invalid cup amount. The only valid options are either '1' or '2'.";
        return $HTTP_RESPONSE_CODE_SERVERERROR;
    }

    $currentValue = file_get_contents($path_gpio_cup_amount_value);
    $result = file_put_contents($path_gpio_cup_amount_value, $GPIO_ON);

    usleep($BUTTON_PRESS_DELAY);

    $currentValue = file_get_contents($path_gpio_cup_amount_value);
    $result = file_put_contents($path_gpio_cup_amount_value, $GPIO_OFF);

    if($result) {
        return $HTTP_RESPONSE_CODE_OK;
    } else {
        return $HTTP_RESPONSE_CODE_SERVERERROR;
    }
}


/**
 * Reads the current value of the pin responsible for power and toggles it.
 * @return int Either response code $HTTP_RESPONSE_CODE_OK for success, or response code $HTTP_RESPONSE_CODE_SERVERERROR for failure.
 */
function toggle_power()
{
    global $path_gpio_power
    , $GPIO_ON
    , $GPIO_OFF
    , $BUTTON_PRESS_DELAY
    , $HTTP_RESPONSE_CODE_OK
    , $HTTP_RESPONSE_CODE_SERVERERROR;

    $path_gpio_power_value = $path_gpio_power.'/value';

    $currentValue = file_get_contents($path_gpio_power_value);
    // [0] because the FIRST character should be either '1' or '0'
    // else we compare a longer string due to the last byte in the file
    $result = file_put_contents($path_gpio_power_value, $currentValue[0] === $GPIO_ON ? $GPIO_OFF : $GPIO_ON);

    usleep($BUTTON_PRESS_DELAY);

    $currentValue = file_get_contents($path_gpio_power_value);
    $result = file_put_contents($path_gpio_power_value, $currentValue[0] === $GPIO_ON ? $GPIO_OFF : $GPIO_ON);
    
    if($result) {
        return $HTTP_RESPONSE_CODE_OK;
    } else {
        return $HTTP_RESPONSE_CODE_SERVERERROR;
    }
}


if(isset($_POST['cups']))
{
    $cups = $_POST['cups'];
    if($cups === "1" || $cups === "2")
    {
        http_response_code(make_coffee($cups));
        die;
    }
    else
    {
        http_response_code($HTTP_RESPONSE_CODE_SERVERERROR);
        echo "Error: Invalid cup amount. The only valid options are either '1' or '2'.";
        die;
    }
}
elseif(isset($_POST['power']))
{
    if($_POST['power'] !== "toggle")
    {
        echo "Error: Invalid power value. The only valid option is 'toggle'.";
        http_response_code($HTTP_RESPONSE_CODE_SERVERERROR);
        die;
    }
    else
    {
        http_response_code(toggle_power());
        die;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kaffee Wifi</title>
    <link rel="stylesheet" href="./vendor/bulma-0.7.2-custom/bulma.custom-dark.css">
    <link rel="stylesheet" href="./vendor/fontawesome-free-5.5.0-web/css/all.min.css">
    <link name="theme-color" content="#000000">
</head>
<body>
    <section class="section">
        <div class="content">

            <button id="btn-two-cups" class="button is-info is-rounded is-large">
                <span class="icon">
                    <i class="fas fa-coffee"></i>
                </span>
                <span class="icon">
                    <i class="fas fa-coffee"></i>
                </span>
            </button>
        
            <button id="btn-one-cup" class="button is-info is-rounded is-large" style="margin: 0 10px;">
                <span class="icon">
                    <i class="fas fa-coffee"></i>
                </span>
            </button>

            <button id="btn-power" class="button is-primary is-rounded is-large">
                <span class="icon">
                    <i class="fas fa-power-off"></i>
                </span>
            </button>
    
            
        </div>
    </section>

    <script>

        /**
         * Schema:
         * <#id>: <post-request>
         */
        const buttons = {
            'btn-one-cup': 'cups=1',
            'btn-two-cups': 'cups=2',
            'btn-power': 'power=toggle'
        };

        // set each handler up, for all buttons inside `buttons`
        for(const id in buttons) {
            const button = document.getElementById(id);
            button.addEventListener('click', e => {
                button.classList.add('is-loading');

                function handleResponse() {
                    const xhttp = this;
                    const statusClass = xhttp.status === <?php echo $HTTP_RESPONSE_CODE_OK; ?> ? 'is-success' : 'is-danger';
                    button.classList.remove('is-loading');
                    button.classList.add(statusClass);
                    // show the result for some time
                    setTimeout(() => {
                        button.classList.remove(statusClass);
                    }, 800);
                }
                
                const xhttp = new XMLHttpRequest();
                xhttp.open('POST', '', true);
                xhttp.onload = handleResponse;
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhttp.send(buttons[id]);
            });
        }

    </script>
    
</body>
</html>
