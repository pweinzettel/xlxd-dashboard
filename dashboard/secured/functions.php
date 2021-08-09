<?php

function get_opt($opt)
{
  switch ($opt) {
    case 'ServerURL':
      return 'http://xlxapi.rlx.lu/api.php';
      break;

    case 'XMLFile':
      return '/var/log/xlxd.xml';
      break;

    case 'PIDFile':
      return '/var/log/xlxd.pid';
      break;

    case 'REFname':
      return 'XLX123';
      break;

    case 'PageTitle':
      return 'Dashboard';
      break;

    case 'IPmask':
      return '2';
      break;

    case 'UpdateData':
      return '2';
      break;

    case 'Contact':
      return 'https://lu9abm.com';
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
  $res = GetElement($xml, get_opt('REFname') . '  heard users');
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
  return json_encode($ret);
}

function get_repeaters_nodes()
{
  $xml = file_get_contents(get_opt('XMLFile'));

  $ret = [];
  $fields = ['Callsign', 'IP', 'LinkedModule', 'Protocol', 'ConnectTime', 'LastHeardTime'];
  $res = GetElement($xml, get_opt('REFname') . '  linked nodes');
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
  $res = GetElement($xml, get_opt('REFname') . '  linked peers');
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