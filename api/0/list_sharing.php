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

//$_POST=$_GET;

if ($_POST['order_id']!='')
{
	$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
	$order=$db->fetch($qorder);
	if (intval($order['order_id'])!=0)
	{
		$i=0;
		$qusers=$db->query('SELECT * FROM blog_sharing as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.order_id='.intval($_POST['order_id']));
		while ($users=$db->fetch($qusers))
		{
			if ($users['user_email']=='') continue;
			if (($users['user_id']==$user['user_id']) && ($users['sharing_priv']!=3)) $not_access=1;
			//print_r($users['sharing_approve']);
			$outmas['users'][$i]['login']=$users['user_email'];
			$outmas['users'][$i]['id']=$users['user_id'];
			$outmas['users'][$i]['fio']=$users['user_name'];
			$outmas['users'][$i]['priv']=$users['sharing_priv'];
			$outmas['users'][$i]['approve']=($users['sharing_approve']==''?1:$users['sharing_approve']);
			$i++;
		}
		if ($not_access==1)
		{
			unset($outmas);
			$outmas['status']=2;
			echo json_encode($outmas);
			die();
		}
		$outmas['status']='ok';
		echo json_encode($outmas);
		die();
	}
	else
	{
		unset($outmas);
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