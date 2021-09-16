<?php
include_once './secured/functions.php';

$json_file = get_opt('users_modules_json');
$now = json_decode(get_users_modules());
$NotifDelay = get_opt('users_modules_json');

if (!file_exists($json_file)) {
    file_put_contents($json_file, json_encode($now));
    exit();
}
$prev = json_decode(file_get_contents($json_file), true);

// ini comparacion
echo "asdasd".PHP_EOL;

// fin comparacion

// guardar now como last
file_put_contents($json_file, json_encode($now));

?>
