<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

function validateURL($url)
{
$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
return preg_match($pattern, $url);
}

function check_src($hn)
{
	global $db;
	$outmas['in_base']=0;
	$outmas['in_azure']=0;
	$qw=$db->query('SELECT * FROM blog_post WHERE post_host=\''.$hn.'\' LIMIT 1');
	//echo 'SELECT * FROM blog_post WHERE post_host=\''.$hn.'\' LIMIT 1';
	$count=$db->fetch($qw);
	if ($count>0)
	{
		$outmas['in_base']=1;
	}
	$cont=parseUrl('http://wobotrest.cloudapp.net/contains.aspx?domain='.$hn);
	if ($cont=='yes')
	{
		$outmas['in_azure']=1;
	}
	return $outmas;
}
auth();
if (!$loged) die();
/*{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if (intval($_GET['order_id'])==0)
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
$mas[1]='en';
$mas[2]='ru';
$mas[4]='az';
$type=$db->query('SELECT * FROM blog_post WHERE order_id='.intval($_GET['order_id']).' LIMIT 1');
while ($rw1 = $db->fetch($type)) 
{
	$type=$mas[$rw1['order_lang']];
}*/

//$rs=$db->query('INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($_GET['link']).'\',\''.$type.'\')');
$descriptorspec=array(
	0 => array("file","/var/www/project/crawler/gg.log","a"),
	1 => array("file","/var/www/project/crawler/gg.log","a"),
	2 => array("file","/var/www/project/crawler/gg.log","a")
	);

$cwd='/var/www/project/crawler';
$end=array();

//echo intval(validateURL($_POST['Url']));
if (intval(validateURL(preg_replace('/[а-яё]/isu','w',$_POST['Url'])))==0)
{
	$mas['status']='fail';
	echo json_encode($mas);
	die();
}
//print_r($_POST);
$_POST['Url']=$_POST['url'];
$hn=parse_url($_POST['Url']);
if ($hn['host']!='')
{
	$url=($hn['scheme']==''?'http':$hn['scheme']).'://'.$hn['host'].'/';
	//echo $url;
	$hn=$hn['host'];
	$ahn=explode('.',$hn);
	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh = $ahn[count($ahn)-2];
}
else
{
	$url=($hn['scheme']==''?'http':$hn['scheme']).'://'.$hn['path'].'/';
	//echo $url;
	$hn=$hn['path'];
	$ahn=explode('.',$hn);
	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh = $ahn[count($ahn)-2];
}
//print_r($hn);
//echo 'SELECT * FROM user_src WHERE hn=\''.$hn.'\'';
$qw=$db->query('SELECT * FROM user_src WHERE hn=\''.$hn.'\' AND user_id='.$user['user_id']);
//echo mysql_num_rows($qw).'!';
//print_r($hn);
$yet=check_src($hn);
if ((mysql_num_rows($qw)!=0) || ($yet['in_base']!=0) || ($yet['in_azure']!=0))
{
	$mas['status']='fail2';
	echo json_encode($mas);
	die();
}
//echo $hn;
//echo $url;
if (($hn!='') && ($hn!='.'))
{
	//echo $url.'  cd /var/www/project/crawler && php simply_addsrc.php '.($url).' &';
	//die();
	//$process=proc_open('cd /var/www/project/crawler && php simply_addsrc.php '.urlencode($url).' &',$descriptorspec,$pipes,$cwd,$end);
	//echo 'http://bmstu.wobot.ru/tools/simpl_addsrc.php?src='.($url);
	//die();
	$qw=$db->query('INSERT INTO user_src (user_id,hn,fhn) VALUES ('.$user['user_id'].',\''.$hn.'\',\''.$url.'\')');
	parseUrl('http://188.120.239.225/tools/simpl_addsrc.php?src='.$db->insert_id());
	//echo 'http://bmstu.wobot.ru/tools/simpl_addsrc.php?src='.$db->insert_id();
	//die();
	$mas['status']='ok';
	echo json_encode($mas);
	//echo 'php /var/www/project/crawler/simply_addsrc.php '.$hn['scheme'].'://'.$hn['host'].'/'.' &';
}

//echo intval(is_resource($process));

/*if (is_resource($process))
{
	$return_value=proc_close($process);
	//echo $return_value;
	$mas['status']='ok';
	$qw=$db->query('INSERT INTO user_src (user_id,hn) VALUES ('.$user['user_id'].',\''.$hn.'\')');
	echo json_encode($mas);
}
else
{
	$mas['status']='fail';
	echo json_encode($mas);
}*/


?>