<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$cproxy_yb=file_get_contents('http://91.218.246.79/api/service/getlist.php?type=yb&full=1');
$redis->set('proxy_yandex',$cproxy_yb);

$cproxy_tw=file_get_contents('http://91.218.246.79/api/service/getlist.php?type=tw&full=1');
$redis->set('proxy_twitter',$cproxy_tw);

$cproxy_vk=file_get_contents('http://91.218.246.79/api/service/getlist.php?type=vk&full=1');
$redis->set('proxy_vk',$cproxy_vk);

$cproxy=file_get_contents('http://91.218.246.79/api/service/getlist.php?full=1');
$redis->set('proxy_list',$cproxy);

$ctoken_fb=file_get_contents('http://91.218.246.79/api/service/get_token.php?type=fb');
$redis->set('at_fb',$ctoken_fb);

$ctoken_fb=file_get_contents('http://91.218.246.79/api/service/get_token.php?type=vk');
$redis->set('at_vk',$ctoken_fb);

$ctoken_fb=file_get_contents('http://91.218.246.79/api/service/get_token.php?type=gp');
$redis->set('at_gp',$ctoken_fb);

$ctoken_fb=file_get_contents('http://91.218.246.79/api/service/get_token.php?type=tw');
$redis->set('at_tw',$ctoken_fb);

?>
