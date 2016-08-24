<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/bot/kernel.php');

$db=new database();
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

// print_r($_POST);
$msrc=explode(',',urldecode($_POST['urls']));
// print_r($msrc);
foreach ($msrc as $item)
{
	$is_yet=0;
	$hn=parse_url($item);
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
	// if (intval(validateURL(preg_replace('/[а-яё]/isu','w',$item)))==0)
	{
		//continue;
	}
	$qw=$db->query('SELECT * FROM user_src WHERE hn=\''.$hn.'\' LIMIT 1');
	$yet=check_src($hn);
	if ((mysql_num_rows($qw)!=0) || ($yet['in_base']!=0) || ($yet['in_azure']!=0))
	{
		//continue;
		$is_yet=1;
	}
	//echo $item.' ';
	if (($hn!='') && ($hn!='.'))
	{
		//echo $item.' ';
		if ($is_yet==0)
		{
			$qw=$db->query('INSERT INTO user_src (user_id,hn,fhn) VALUES (0,\''.$hn.'\',\''.$url.'\')');
		}
		else
		{
			//echo 'YET!!!';
			//echo 'INSERT INTO user_src (user_id,hn,fhn,count,update) VALUES (0,\''.$hn.'\',\''.$url.'\',0,2)'."\n";
			$qw=$db->query('INSERT INTO user_src (`user_id`,`hn`,`fhn`,`count`,`update`) VALUES (0,\''.$hn.'\',\''.$url.'\',0,2)');
		}
		if ($is_yet==0)
		{
			parseUrl('http://188.120.239.225/tools/simpl_addsrc.php?src='.$db->insert_id());
		}
		//$mas['status']='ok';
		//echo json_encode($mas);
	}
}

?>