<?

require_once('com/db.php');
require_once('com/config.php');
require_once('bot/kernel.php');

$db=new database();
$db->connect();
//http://hideme.ru/api/proxylist.php?out=plain&code=85243059&maxtime=2000&type=h
$cont=parseUrl('http://hideme.ru/api/proxylist.php?out=plain&code=682278237957507&maxtime=2000&type=h');
$mproxy=explode("\n",$cont);

foreach ($mproxy as $key => $value) {
	if ($value=='') continue;
	$isset=$db->query('SELECT * FROM tp_proxys WHERE proxy=\''.trim($value).'\'');
	if ($db->num_rows($isset)==0)
	{
		echo 'INSERT INTO tp_proxys (proxy) VALUES (\''.trim($value).'\')'."\n";
		$db->query('INSERT INTO tp_proxys (proxy) VALUES (\''.trim($value).'\')');
	}
}

//print_r($mproxy);

?>