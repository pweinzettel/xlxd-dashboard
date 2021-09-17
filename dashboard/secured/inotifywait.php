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

// busco modulos offline
foreach ($prev['modules']['linked'] as $module => $nodes) {
    $nodes_now = isset($now['modules']['linked'][$module]) ? $now['modules']['linked'][$module] : [];
    $nodes_prev = isset($prev['modules']['linked'][$module]) ? $prev['modules']['linked'][$module] : [];

    $nodes_offline = array_diff($nodes_prev, $nodes_now);
    if (sizeof($nodes_offline) < 1) continue;
    foreach ($nodes_offline as $node_offline) {
        $node = preg_replace('!\s+!', '-', $node_offline);
        $msg = "Node <b>".$node."</b> offline from module <b>".$module."</b>";
        $res = tg_send($TGchat, $msg);
    }
}

// busco modulos online
foreach ($now['modules']['linked'] as $module => $nodes) {
    $nodes_now = isset($now['modules']['linked'][$module]) ? $now['modules']['linked'][$module] : [];
    $nodes_prev = isset($prev['modules']['linked'][$module]) ? $prev['modules']['linked'][$module] : [];

    $nodes_online = array_diff($nodes_now, $nodes_prev);
    if (sizeof($nodes_online) < 1) continue;
    foreach ($nodes_online as $node_online) {
        $node = preg_replace('!\s+!', '-', $node_online);
        $msg = "Node <b>".$node."</b> online on module <b>".$module."</b>";
        $res = tg_send($TGchat, $msg);
    }
}

// busco usuarios nuevos (entre una y otra comparacion con mas de $NotifDelay segundos de silencio)
foreach ($now['users'] as $user) {
    foreach ($prev['users'] as $puser) {
        if ($user['Callsign'] == $puser['Callsign'] ) {
            $call = str_replace(' ', '', $user['Callsign']);
            $module = str_replace(' ', '', $user['On_module']);
            $node = preg_replace('!\s+!', '-', $user['Via_node']);
            $peer = str_replace(' ', '', $user['Via_peer']);
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
