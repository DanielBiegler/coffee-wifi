<?php
$path_gpio = '/sys/class/gpio';
$path_gpio_export = '/sys/class/gpio/export';

$path_gpio_cup_one = '/sys/class/gpio/gpio2';
$path_gpio_cup_two = '/sys/class/gpio/gpio3';
$path_gpio_power = '/sys/class/gpio/gpio4';

function make_coffee($num_cups)
{
    global $path_gpio_cup_one, $path_gpio_cup_two;
    
    if($num_cups === '1')
    {
        $path_gpio_cup_one_value = $path_gpio_cup_one.'/value';
        $currentValue = file_get_contents($path_gpio_cup_one_value);
        $result = file_put_contents($path_gpio_cup_one_value, $currentValue[0] === "0" ? 1 : 0);

        if($result) {
            return 200;
        } else {
            return 500;
        }
    } 
    elseif($num_cups === '2')
    {
        $path_gpio_cup_two_value = $path_gpio_cup_two.'/value';
        $currentValue = file_get_contents($path_gpio_cup_two_value);
        $result = file_put_contents($path_gpio_cup_two_value, $currentValue[0] === "0" ? 1 : 0);

        if($result) {
            return 200;
        } else {
            return 500;
        }
    }
    else
    {
        echo "Error: Invalid cup amount. The only valid options are either '1' or '2'.";
        return 503;
    }
}


function toggle_power()
{
    global $path_gpio_power;
    $path_gpio_power_value = $path_gpio_power.'/value';

    $currentValue = file_get_contents($path_gpio_power_value);
    $result = file_put_contents($path_gpio_power_value, $currentValue[0] === "0" ? 1 : 0);
    if($result) {
        return 200;
    } else {
        return 500;
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
        http_response_code(400);
        echo "Error: Invalid cup amount. The only valid options are either '1' or '2'.";
        die;
    }
}
elseif(isset($_POST['power']))
{
    if($_POST['power'] !== "toggle")
    {
        echo "Error: Invalid power value. The only valid option is 'toggle'.";
        http_response_code(503);
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
    <section class="section" style="position: absolute; bottom: 200px; right: 20px;">
        <div class="content has-text-right">

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


        const btnOneCup = document.getElementById('btn-one-cup');
        btnOneCup.addEventListener('click', e => {
            function handleResponse() {
                let xhttp = this;
                if(xhttp.status === 200) {
                    setTimeout(() => {
                        btnOneCup.classList.remove('is-loading');
                        btnOneCup.classList.add('is-success');
                        setTimeout(() => {
                            btnOneCup.classList.remove('is-success');
                        }, 800);
                    }, 350);
                } else {
                    setTimeout(() => {
                        btnOneCup.classList.remove('is-loading');
                        btnOneCup.classList.add('is-danger');
                        setTimeout(() => {
                            btnOneCup.classList.remove('is-danger');
                        }, 800);
                    }, 350);
                }
            }
            
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', '/', true);
            xhttp.onload = handleResponse;
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send('cups=1');
        });


        const btnTwoCups = document.getElementById('btn-two-cups');
        btnTwoCups.addEventListener('click', e => {
            function handleResponse() {
                let xhttp = this;
                if(xhttp.status === 200) {
                    setTimeout(() => {
                        btnTwoCups.classList.remove('is-loading');
                        btnTwoCups.classList.add('is-success');
                        setTimeout(() => {
                            btnTwoCups.classList.remove('is-success');
                        }, 800);
                    }, 350);
                } else {
                    setTimeout(() => {
                        btnTwoCups.classList.remove('is-loading');
                        btnTwoCups.classList.add('is-danger');
                        setTimeout(() => {
                            btnTwoCups.classList.remove('is-danger');
                        }, 800);
                    }, 350);
                }
            }
            
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', '/', true);
            xhttp.onload = handleResponse;
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send("cups=2");
        });


        const btnPower = document.getElementById('btn-power');
        btnPower.addEventListener('click', e => {
            function handleResponse() {
                let xhttp = this;
                if(xhttp.status === 200) {
                    setTimeout(() => {
                        btnPower.classList.remove('is-loading');
                        btnPower.classList.add('is-success');
                        setTimeout(() => {
                            btnPower.classList.remove('is-success');
                        }, 800);
                    }, 350);
                } else {
                    setTimeout(() => {
                        btnPower.classList.remove('is-loading');
                        btnPower.classList.add('is-danger');
                        setTimeout(() => {
                            btnPower.classList.remove('is-danger');
                        }, 800);
                    }, 350);
                }
            }
            
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', '/', true);
            xhttp.onload = handleResponse;
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send("power=toggle");
        });
                
        
        // Make buttons pleasant
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', e => {
                button.classList.add('is-loading');
            });
        });
    </script>
    
</body>
</html>
