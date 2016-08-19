<?
include("/home/wobot/GeoIp.php");
$geo = JB_GeoIp::factory($_SERVER);
$geo->getCity();
$geo->getCountryName();
$geo->getLongitude();
var_dump($_SERVER);
?>
