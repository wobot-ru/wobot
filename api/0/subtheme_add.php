<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();

//-------Права на проставления спама после шаринга------
$memcache = memcache_connect('localhost', 11211);
$priv=$memcache->get('blog_sharing');
$mpriv=json_decode($priv,true);
if ($priv=='')
{
	$qshare=$db->query('SELECT * FROM blog_sharing');
	while ($share=$db->fetch($qshare))
	{
		$mpriv[$share['order_id']][$share['user_id']]=$share['sharing_priv'];
	}
}
if ($mpriv[$_POST['order_id']][$user['user_id']]==1)
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}
//$_POST=$_GET;

if (($_POST['order_id']!='') && ($_POST['subtheme_name']!=''))
{
	$qisset=$db->query('SELECT order_id FROM blog_orders WHERE user_id='.$user['user_id'].' AND order_id='.intval($_POST['order_id']));
	if ($db->num_rows($qisset)==0)
	{
		$outmas['status']=3;
		echo json_encode($outmas);
		die();
	}
	$qsth=$db->query('SELECT * FROM blog_subthemes WHERE order_id='.intval($_POST['order_id']).' AND subtheme_name=\''.addslashes($_POST['subtheme_name']).'\'');
	if ($db->num_rows($qsth)==0)
	{
		foreach ($_POST as $key => $item)
		{
			if (preg_match('/word_/isu', $key))
			{
				$mw=explode('_',$key);
				$mword[]=$mw[1];
			}
			if (preg_match('/eword_/isu', $key))
			{
				$mew=explode('_',$key);
				$meword[]=$mew[1];
			}
			if (preg_match('/tag_/isu', $key))
			{
				$mt=explode('_',$key);
				$mtags[]=$mt[1];
			}
			if (preg_match('/src_/isu', $key))
			{
				$regex='/src_(?<src>.*)/isu';
				preg_match_all($regex, $key, $out);
				$msrcs[]=preg_replace('/_/isu', '.', $out['src'][0]);
			}
			if (preg_match('/auth_/isu', $key))
			{
				$ma=explode('_',$key);
				$mauth[]=$ma[1];
			}
			if (preg_match('/loc_/isu', $key))
			{
				$ml=explode('_',$key);
				$mloc[]=$ml[1];
			}
		}
		$settings['word']=$mword;
		$settings['eword']=$meword;
		$settings['tags']=$mtags;
		$settings['src']=$msrcs;
		$settings['auth']=$mauth;
		$settings['loc']=$mloc;
		$settings['tonal']['positive']=intval($_POST['positive']);
		$settings['tonal']['negative']=intval($_POST['negative']);
		$settings['tonal']['neutral']=intval($_POST['neutral']);
		$settings['tonal']['undefined']=intval($_POST['undefined']);
		$settings['suborder_start']=strtotime($_POST['start_time']);
		$settings['suborder_end']=strtotime($_POST['end_time']);
		$settings['lan']=$_POST['language'];
		//echo 'INSERT INTO blog_subthemes (order_id,subtheme_name,subtheme_settings) VALUES ('.intval($_POST['order_id']).',\''.addslashes($_POST['subtheme_name']).'\',\''.addslashes(json_encode($settings)).'\')';
		$db->query('INSERT INTO blog_subthemes (order_id,subtheme_name,subtheme_settings) VALUES ('.intval($_POST['order_id']).',\''.addslashes($_POST['subtheme_name']).'\',\''.addslashes(json_encode($settings)).'\')');
		$cont=parseUrl('http://188.120.239.225/tools/cashjob.php?order_id='.intval($_POST['order_id']).'&start='.strtotime($_POST['start_time']).'&end='.strtotime($_POST['end_time']));
		$outmas['subtheme_id']=$db->insert_id();
		$outmas['status']='ok';
		echo json_encode($outmas);
		die();
	}
	else
	{
		$outmas['status']=2;
		echo json_encode($outmas);
		die();
	}
}
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}

?>