<?php

//http://xlxapi.rlx.lu/api.php?do=GetReflectorList

function get_opt($opt)
{
  switch ($opt) {
  // functions
    case 'ServiceUptime':
      return time() - filectime(get_opt('ProcessIDFile'));
      break;

  // harcoded options
    case 'ServerURL':
      return 'http://xlxapi.rlx.lu/api.php';
      break;

    case 'XMLFile':
      return '/var/log/xlxd.xml';
      break;

    case 'ProcessIDFile':
      return '/var/log/xlxd.pid';
      break;

// find on db
    case 'ReflectorName':
      return 'XLX123';
      break;
    case 'ReflectorHash':
      return 'nusASS7nbqkoclfi';
      break;
    case 'DashboardURL':
      return 'http://xlx.lu9abm.com/';
      break;
    case 'RefCountry':
      return 'Argentina';
      break;
    case 'RefComment':
      return 'D-Star Argentina';
      break;
    case 'OverrideIP':
      //return false;
      return '152.67.150.221';
      break;
  
    case 'PageTitle':
      return 'Dashboard';
      break;

    case 'IPmask':
      return '2';
      break;

    case 'UpdateData':
      return '5';
      break;

    case 'Contact':
      return 'https://lu9abm.com';
      break;

    case 'Telegram':
      //return 'https://t.me/XLX123_Group';
      return false;
      break;

    default:
      return false;
      break;
  }
}

function xml2json($xml)
{
  return json_encode(simplexml_load_string($xml));
}

function xml2array($xml)
{
  return json_decode(xml2json($xml), TRUE);
}

function GetElement($InputString, $ElementName)
{
  if (strpos($InputString, "<" . $ElementName . ">") === false) return false;
  if (strpos($InputString, "</" . $ElementName . ">") === false) return false;
  $Element = substr($InputString, strpos($InputString, "<" . $ElementName . ">") + strlen($ElementName) + 2, strpos($InputString, "</" . $ElementName . ">") - strpos($InputString, "<" . $ElementName . ">") - strlen($ElementName) - 2);
  return $Element;
}

function GetAllElements($InputString, $ElementName)
{
  $Elements = array();
  while (strpos($InputString, $ElementName) !== false) {
    $Elements[] = GetElement($InputString, $ElementName);
    $InputString = substr($InputString, strpos($InputString, "</" . $ElementName . ">") + strlen($ElementName) + 3, strlen($InputString));
  }
  return $Elements;
}

function get_reflector_list()
{
  $res = @fopen(get_opt('ServerURL') . "?do=GetReflectorList", "r");
  if ($res) {
    $list = '';
    while (!feof($res)) {
      $list .= fgets($res, 1024);
    }
    fclose($res);

    $refs = GetAllElements($list, 'reflector');

    $ret = [];
    $ret['timestamp'] = GetElement($list, 'timestamp');

    $fields = ['name', 'lastip', 'dashboardurl', 'uptime', 'lastcontact', 'country', 'comment'];
    foreach ($refs as $key => $ref) {
      if (empty($ref)) continue;
      $ret['reflectors'][$key] = [];
      foreach ($fields as $field) {
        $ret['reflectors'][$key][$field] = GetElement($ref, $field);
      }
    }

    return json_encode($ret);
  }
  fclose($res);
  return $res;
}

function get_users_modules()
{
  $xml = file_get_contents(get_opt('XMLFile'));

  $ret = [];
  $fields = ['Callsign', 'Via node', 'On module', 'Via peer', 'LastHeardTime'];
  $res = GetElement($xml, get_opt('ReflectorName') . '  heard users');
  $users = GetAllElements($res, 'STATION');
  $already = [];
  $callsign = '';

  foreach ($users as $key => $user) {
    if (empty($user)) continue;
    $callsign = GetElement($user, 'Callsign');
    if (in_array($callsign, $already)) {
      continue;
    } else {
      array_push($already, $callsign);
    }
    $ret['users'][$key] = [];
    foreach ($fields as $field) {
      $fieldt = str_replace(' ', '_', $field);
      $ret['users'][$key][$fieldt] = GetElement($user, $field);
    }
  }

  $fields = ['Callsign', 'LinkedModule'];

  $res = GetElement($xml, get_opt('ReflectorName') . '  linked nodes');
  $modules = GetAllElements($res, 'NODE');

  $ret['modules']['linked'] = [];
  $ret['modules']['name'] = [];
  $ret['modules']['id'] = [];
  foreach ($modules as $key => $module) {
    if (empty($module)) continue;
    $name = GetElement($module, 'Callsign');
    $linked = GetElement($module, 'LinkedModule');
    $lnkname = $linked; // . '</br>Nombre';

    if ( ! isset($ret['modules']['linked'][$linked]) ) {
      $ret['modules']['linked'][$linked] = [];
      $ret['modules']['name'][$linked] = [];
      array_push($ret['modules']['name'][$linked],$lnkname);
      array_push($ret['modules']['id'],$linked);
    }

    array_push($ret['modules']['linked'][$linked],$name);
  }

  return json_encode($ret);
}

function get_repeaters_nodes()
{
  $xml = file_get_contents(get_opt('XMLFile'));

  $ret = [];
  $fields = ['Callsign', 'IP', 'LinkedModule', 'Protocol', 'ConnectTime', 'LastHeardTime'];
  $res = GetElement($xml, get_opt('ReflectorName') . '  linked nodes');
  $nodes = GetAllElements($res, 'NODE');

  foreach ($nodes as $key => $node) {
    if (empty($node)) continue;
    $ret['nodes'][$key] = [];
    foreach ($fields as $field) {
      $val = GetElement($node, $field);
      $fieldt = str_replace(' ', '_', $field);
      if ($field == 'IP') {
        $aux = explode('.', $val);
        for ($i = 0; $i < get_opt('IPmask'); $i++) {
          $aux[$i] = '*';
        }
        $val = implode('.', $aux);
      }
      $ret['nodes'][$key][$fieldt] = $val;
    }
  }
  return json_encode($ret);
}

function get_peers()
{
  $xml = file_get_contents(get_opt('XMLFile'));

  $ret = [];
  $fields = ['Callsign', 'IP', 'LinkedModule', 'Protocol', 'ConnectTime', 'LastHeardTime'];
  $res = GetElement($xml, get_opt('ReflectorName') . '  linked peers');
  $peers = GetAllElements($res, 'PEER');

  foreach ($peers as $key => $peer) {
    if (empty($peer)) continue;
    $ret['peers'][$key] = [];
    foreach ($fields as $field) {
      $val = GetElement($peer, $field);
      $fieldt = str_replace(' ', '_', $field);
      if ($field == 'IP') {
        $aux = explode('.', $val);
        for ($i = 0; $i < get_opt('IPmask'); $i++) {
          $aux[$i] = '*';
        }
        $val = implode('.', $aux);
      }
      $ret['peers'][$key][$fieldt] = $val;
    }
  }
  return json_encode($ret);
}

function get_countries()
{
  $countries = file(__DIR__ . '/countries.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  foreach ($countries as $key => $country) {
    $aux[$key] = explode(';', $country);

    $ret[$key]['name'] = $aux[$key][0];
    $ret[$key]['flag'] = $aux[$key][1];
    $ret[$key]['calls'] = explode('-', $aux[$key][2]);
  }
  return json_encode($ret);
}

function CreateCode ($laenge) {
	$zeichen = "1234567890abcdefghijklmnopqrstuvwyxzABCDEFGHIJKLMNAOPQRSTUVWYXZ";
	mt_srand( (double) microtime() * 1000000);
	$out = "";
	for ($i=1;$i<=$laenge;$i++){ 
		$out .= $zeichen[mt_rand(0,(strlen($zeichen)-1))];
	}
	return $out;
}