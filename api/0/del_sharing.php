<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

function parseUrlmail($url,$to,$subj,$body,$from)
{

$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='to='.$to.'&subject='.$subj.'&body='.$body.'&from='.$from;
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );
 $header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $content;
}

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

if (($mpriv[$_POST['order_id']][$user['user_id']]!=3) && (isset($mpriv[$_POST['order_id']][$user['user_id']])))
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}
//$_POST=$_GET;

if (($_POST['order_id']!='') && ($_POST['mails']!=''))
{
	$qorder=$db->query('SELECT * FROM blog_orders WHERE user_id='.$user['user_id'].' AND order_id='.intval($_POST['order_id']));
	$order=$db->fetch($qorder);
	if (intval($order['order_id'])!=0)
	{
		$i=0;
		$m_mail=explode(',', $_POST['mails']);
		foreach ($m_mail as $item)
		{
			$qus=$db->query('SELECT * FROM users WHERE user_email=\''.trim(addslashes($item)).'\' LIMIT 1');
			while ($us=$db->fetch($qus))
			{
				$db->query('DELETE FROM blog_sharing WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$us['user_id']);
				//echo 'DELETE FROM blog_sharing WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$us['user_id'];
			}
		}
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