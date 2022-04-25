<?php

//http://xlxapi.rlx.lu/api.php?do=GetReflectorList

function get_opt($opt)
{
  switch ($opt) {
// ??
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
      return 'D-Star Argentina -> https://t.me/XLX123_Feed';
      break;

    case 'OverrideIP':
      return false;
      //return '152.67.150.221';
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

    case 'TGFeed_link':
      return 'https://t.me/XLX123_Feed';
      break;

    case 'TGGroup_link':
      return 'https://t.me/XLX123_Group';
      break;

    case 'TGtoken':
      return '1221226800:AAFC88OHW40hA_iZVuoHmVKMV0SiP1WmV8k';
      break;

    case 'TGFeed':
      return '@XLX123_Feed';
      break;

    case 'NotifDelay': //seconds
      return '600'; //10 min?
      break;

  // functions
    case 'ServiceUptime':
      return time() - filectime(get_opt('ProcessIDFile'));
      break;

    case 'ReflectorName':
      $refname = trim(json_decode(file_get_contents(get_opt('JSONFile')))->refname);
      if (isset($refname)) return $refname;
      return 'XLX Ref';
      break;

  // harcoded options
    case 'ServerURL':
      return 'http://xlxapi.rlx.lu/api.php';
      break;

    case 'JSONFile':
      return '/var/log/xlxd.json';
      break;

    case 'ProcessIDFile':
      return '/var/log/xlxd.pid';
      break;

    default:
      return false;
      break;
  }
}

function GetElement($InputString, $ElementName) // todavia lo uso para reflector list
{
  if (strpos($InputString, "<" . $ElementName . ">") === false) return false;
  if (strpos($InputString, "</" . $ElementName . ">") === false) return false;
  $Element = substr($InputString, strpos($InputString, "<" . $ElementName . ">") + strlen($ElementName) + 2, strpos($InputString, "</" . $ElementName . ">") - strpos($InputString, "<" . $ElementName . ">") - strlen($ElementName) - 2);
  return $Element;
}

function GetAllElements($InputString, $ElementName) // todavia lo uso para reflector list
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
  $json = json_decode(file_get_contents(get_opt('JSONFile')));
  $ret = $json->heard_users;

  return json_encode($ret);
}

function get_repeaters_nodes()
{
  $json = json_decode(file_get_contents(get_opt('JSONFile')));
  $ret = $json->nodes;

  return json_encode($ret);
}

function get_peers()
{
  $json = json_decode(file_get_contents(get_opt('JSONFile')))->peers;
  for ($i = 0; $i < count($json); $i++) {
    $aux = explode('.', $json[$i]->ip);
    for ($j = 0; $j < get_opt('IPmask'); $j++) {
      $aux[$j] = '*';
    }
    $json[$i]->ip = implode('.', $aux);
  }
  $ret = $json;
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