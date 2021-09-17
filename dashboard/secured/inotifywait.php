<?php
include_once './secured/functions.php';
include_once './secured/telegram.php';

$NotifDelay = (int)get_opt('NotifDelay');
$TGFeed = get_opt('TGFeed');

// load users_modules data now and prev
$users_modules_json = get_opt('users_modules_json');
$users_modules_now = json_decode(get_users_modules(), true);
if (!file_exists($users_modules_json)) {
    file_put_contents($users_modules_json, json_encode($users_modules_now));
}
$users_modules_prev = json_decode(file_get_contents($users_modules_json), true);

// load users_modules data now and prev
$peers_json = get_opt('peers_json');
$peers_now = json_decode(get_peers(), true);
if (!file_exists($peers_json)) {
    file_put_contents($peers_json, json_encode($peers_now));
}
$peers_prev = json_decode(file_get_contents($peers_json), true);

// ini comparacion
$msg = "";

// busco peers offline
foreach ($peers_prev['peers'] as $ppeer) {
    $peer_off = false;
    $call_prev = str_replace(' ', '', $ppeer['Callsign']);
    $module_prev = str_replace(' ', '', $ppeer['LinkedModule']);
    foreach ($peers_now['peers'] as $peer) {
        $call = str_replace(' ', '', $peer['Callsign']);
        $module = str_replace(' ', '', $peer['LinkedModule']);

        if ($call == $call_prev && $module == $module_prev ) continue 2;
        $peer_off = true;
    }
    if ($peer_off) {
        $msg .= "Peer <b>".$call_prev."</b> offline from module <b>".$module_prev."</b>".PHP_EOL;
    }
}
// busco peers online
foreach ($peers_now['peers'] as $peer) {
    $peer_new = true;
    $call = str_replace(' ', '', $peer['Callsign']);
    $module = str_replace(' ', '', $peer['LinkedModule']);
    foreach ($peers_prev['peers'] as $ppeer) {
        $call_prev = str_replace(' ', '', $ppeer['Callsign']);
        $module_prev = str_replace(' ', '', $ppeer['LinkedModule']);

        if ($call != $call_prev || $module != $module_prev ) continue;
        $peer_new = false;
    }
    if ($peer_new) {
        $msg .= "Peer <b>".$call."</b> online on module <b>".$module."</b>".PHP_EOL;
    }
}


// busco modulos offline
foreach ($users_modules_prev['modules']['linked'] as $module => $nodes) {
    $nodes_now = isset($users_modules_now['modules']['linked'][$module]) ? $users_modules_now['modules']['linked'][$module] : [];
    $nodes_prev = isset($users_modules_prev['modules']['linked'][$module]) ? $users_modules_prev['modules']['linked'][$module] : [];

    $nodes_offline = array_diff($nodes_prev, $nodes_now);
    foreach ($nodes_offline as $node_offline) {
        $node = preg_replace('!\s+!', '-', $node_offline);
        $msg .= "Node <b>".$node."</b> offline from module <b>".$module."</b>".PHP_EOL;
    }
}

// busco modulos online
foreach ($users_modules_now['modules']['linked'] as $module => $nodes) {
    $nodes_now = isset($users_modules_now['modules']['linked'][$module]) ? $users_modules_now['modules']['linked'][$module] : [];
    $nodes_prev = isset($users_modules_prev['modules']['linked'][$module]) ? $users_modules_prev['modules']['linked'][$module] : [];

    $nodes_online = array_diff($nodes_now, $nodes_prev);
    foreach ($nodes_online as $node_online) {
        $node = preg_replace('!\s+!', '-', $node_online);
        $msg .= "Node <b>".$node."</b> online on module <b>".$module."</b>".PHP_EOL;
    }
}
if ($msg) {
    $res = tg_send($TGFeed, $msg);
    //echo $res;
}

// busco usuarios nuevos (entre una y otra comparacion con mas de $NotifDelay segundos de silencio)
foreach ($users_modules_now['users'] as $user) {
    foreach ($users_modules_prev['users'] as $puser) {
        if ($user['Callsign'] == $puser['Callsign'] ) {
            $call = str_replace(' ', '', $user['Callsign']);
            $module = str_replace(' ', '', $user['On_module']);
            $module_prev = str_replace(' ', '', $puser['On_module']);
            $node = preg_replace('!\s+!', '-', $user['Via_node']);
            $peer = str_replace(' ', '', $user['Via_peer']);
            $curr = strtotime($user['LastHeardTime']);
            $last = strtotime($puser['LastHeardTime']);
            if ($curr > $last+$NotifDelay || $module_prev != $module) {
                $msg = "<b>".$call."</b> online on module <b>".$module."</b> via <b>".$node."</b> > <b>".$peer."</b>";
                $res = tg_send($TGFeed, $msg);
            }
        }
    }
}
// fin comparacion

// guardar now como last
file_put_contents($peers_json, json_encode($peers_now));
file_put_contents($users_modules_json, json_encode($users_modules_now));

?>
