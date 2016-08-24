<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
echo 1;
$db = new database();
$db->connect();
echo 2;
function get_realNick($url,$part)
{
	$cont=parseUrl($url);
	/*$part=addslashes($part);
	$part=str_replace('(','\(',$part);
	$part=str_replace(')','\)',$part);
	$part=str_replace('[','\[',$part);
	$part=str_replace(']','\]',$part);
	$part=str_replace('?','\?',$part);
	$part=str_replace('+','\+',$part);
	$part=str_replace('*','\*',$part);
	$part=str_replace('.','\.',$part);
	$part=preg_replace('/\s+/is',' ',$part);*/
	$part=explode("\n",$part);
	$part=$part[1];
	//echo $part;
	$rg='/(?<part>[а-яА-Яa-zA-Z]+\s[а-яА-Яa-zA-Z]+\s[а-яА-Яa-zA-Z]+)/isu';
	preg_match_all($rg,$part,$pp);
	if (count($pp['part'])==0)
	{
		$rg='/(?<part>[а-яА-Яa-zA-Z]+\s[а-яА-Яa-zA-Z]+)/isu';
		preg_match_all($rg,$part,$pp);
		if (count($pp['part'])==0)
		{
			$pp['part'][0]=$part;
			//echo 'NOOOOOO';
		}
	}
	//print_r($pp);
	$pp['part'][0]=preg_replace('/\s+/is',' ',$pp['part'][0]);
	$p1=preg_replace("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/is"," ",$part);
	$p1=preg_replace("/\s/is","",$p1);
	if ($p1!='')
	{
		$part=$pp['part'][0];
	}
	else
	{
		//echo 'GGG'.$part;
		$pp1=explode("\n",$part);
		//print_r($pp1);
		$part=$pp1[0];
		$pp['part'][0]=addslashes($part);
		$pp['part'][0]=str_replace('?','\?',$pp['part'][0]);
		$pp['part'][0]=str_replace('/','\/',$pp['part'][0]);
	}
	foreach ($pp['part'] as $item)
	{
		$regex='/<div.*?id=[\'"]ljcmt.*?[\'"].*?>.*?<span.*?class=[\'"]ljuser.*?[\'"].*?lj:user=[\'"](?<nick>.*?)[\'"].*?>.*?<\/span>.*?'.$item.'/isu';
		//echo $regex;
		preg_match_all($regex,$cont,$out);
		//echo $out['nick'][0].'///';
	}
	return $out['nick'][0];
}

//$query='SELECT * from blog_post WHERE order_id='.$blog['order_id'];
while (1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
	}
	$ressec=$db->query('SELECT post_id,post_link,post_content from blog_post WHERE post_host=\'livejournal.com\' AND blog_id=0 ORDER BY post_time DESC');
	echo 'SELECT * from blog_post WHERE post_host=\'livejournal.com\' AND blog_id=0 ORDER BY post_time DESC'."\n";
	echo 'NUM ROWS='.mysql_num_rows($ressec);
	while($blog=$db->fetch($ressec))
	{
		echo '('.$blog['post_link'].')'."\n";
		sleep(1);
		$nick=get_realNick($blog['post_link'],$blog['post_content']);
		if (($nick!='') && (strpos($nick, 'ext_')===false))
		{
			$ressec1=$db->query('SELECT blog_id from robot_blogs2 WHERE blog_login=\''.$nick.'\' AND blog_link=\'livejournal.com\' LIMIT 1');
			if (mysql_num_rows($ressec1)==0)
			{
				$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'livejournal.com\',\''.$nick.'\')');
				//$bb1=$db->fetch($ressec1);
				$bb1['blog_id']=$db->insert_id();
				$db->query('UPDATE blog_post SET blog_id='.$bb1['blog_id'].' WHERE post_id='.$blog['post_id']);
			}
			else
			{
				$bb=$db->fetch($ressec1);
				$db->query('UPDATE blog_post SET blog_id='.$bb['blog_id'].' WHERE post_id='.$blog['post_id']);
			}
		}
		else
		{
			$regex='/\:\/\/(?<nick>.*?)\./isu';
			preg_match_all($regex,$blog['post_link'],$out);
			$qnot_rn=$db->query('SELECT blog_id from robot_blogs2 WHERE blog_login=\''.$out['nick'][0].'\' AND blog_link=\'livejournal.com\' LIMIT 1');
			$blid=$db->fetch($qnot_rn);
			$db->query('UPDATE blog_post SET blog_id='.$blid['blog_id'].' WHERE post_id='.$blog['post_id']);
			echo 'UPDATE blog_post SET blog_id='.$blid['blog_id'].' WHERE post_id='.$blog['post_id']."\n";
		}
		if ($nick!='')
		{
			$cc++;
		}
		else
		{
			$cc1++;
		}
		echo $blog['post_link']."\n|".$nick."|\n".$cc.' '.$cc1."\n";//.$blog['post_content']."\n";
	}
	echo 'idle...';
	sleep(60);
}
?>
