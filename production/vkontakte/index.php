<?php

$result = array('appid' => '4451365');

if( is_file('/var/www/com/config_server.php') )
{
  require_once('/var/www/com/config_server.php');
  $vk_config = file_get_contents('http://188.120.239.225/api/service/get_at.php?server='.$config_server['server_ip'].'&type=vk');
  $result = json_decode($vk_config, true);
}

echo json_encode($result);
die;