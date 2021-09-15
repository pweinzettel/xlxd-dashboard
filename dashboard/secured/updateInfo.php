<?php
include_once './secured/functions.php';

$ServerURL = get_opt('ServerURL');
$ReflectorName = get_opt('ReflectorName');
$ServiceUptime = get_opt('ServiceUptime');
$ReflectorHash = get_opt('ReflectorHash');
$DashboardURL = get_opt('DashboardURL');
$RefCountry = urlencode(get_opt('RefCountry'));
$RefComment = urlencode(get_opt('RefComment'));
$OverrideIP = get_opt('OverrideIP');

echo $ServerURL."?ReflectorName=".$ReflectorName."&ReflectorUptime=".$ServiceUptime."&ReflectorHash=".$ReflectorHash."&DashboardURL=".$DashboardURL."&Country=".$RefCountry."&Comment=".$RefComment."&OverrideIP=".$OverrideIP;

$file_handle = @fopen($ServerURL."?ReflectorName=".$ReflectorName."&ReflectorUptime=".$ServiceUptime."&ReflectorHash=".$ReflectorHash."&DashboardURL=".$DashboardURL."&Country=".$RefCountry."&Comment=".$RefComment."&OverrideIP=".$OverrideIP, "r");

while (!feof($file_handle)) {
   $xml .= fgets($file_handle);
}
fclose($file_handle);

var_dump($xml);

?>