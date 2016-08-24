<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
// set_log('error',$_POST);

header('Location: http://beta.wobot.ru');
?>