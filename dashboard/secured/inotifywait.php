<?php
include_once './secured/functions.php';
include_once './secured/telegram.php';

$json_file = get_opt('users_modules_json');
$now = json_decode(get_users_modules(), true);
$NotifDelay = (int)get_opt('NotifDelay');
$TGchat = get_opt('TGchat');

if (!file_exists($json_file)) {
    file_put_contents($json_file, json_encode($now));
    exit();
}
$prev = json_decode(file_get_contents($json_file), true);

// ini comparacion

foreach ($now['users'] as $user) {
    foreach ($prev['users'] as $puser) {
        if ($user['Callsign'] == $puser['Callsign'] ) {
            $call = str_replace(' ', '', $user['Callsign']);
            $module = str_replace(' ', '', $user['On_module']);
            $node = preg_replace('!\s+!', '-', $user['Via_node']);
            $peer = str_replace(' ', '', $user['Via_peer']);
            echo $call.PHP_EOL;
            $curr = strtotime($user['LastHeardTime']);
            $last = strtotime($puser['LastHeardTime']);
            if ($curr > $last+$NotifDelay) {
                $msg = "<b>".$call."</b> online on module <b>".$module."</b> via <b>".$node."</b> > <b>".$peer."</b>";
                $res = tg_send($TGchat, $msg);
            }
        }
    }
}
// fin comparacion

// guardar now como last
file_put_contents($json_file, json_encode($now));

?>
