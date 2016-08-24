<?php

$result = array('appid' => '1465945516978225');

if( is_file('/var/www/com/config_server.php') )
{
  require_once('/var/www/com/config_server.php');
  $fb_config = file_get_contents('http://188.120.239.225/api/service/get_at.php?server='.$config_server['server_ip'].'&type=fb');
  $result = json_decode($fb_config, true);
}

echo json_encode($result);
die;