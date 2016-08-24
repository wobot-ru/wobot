<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

$memcache = memcache_connect('localhost', 11211);

function isValidEmail($email){ 
     $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$"; 

     if (eregi($pattern, $email)){ 
        return true; 
     } 
     else { 
        return false; 
     }    
} 

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
	$outmas['status']='fail1';
	echo json_encode($outmas);
	die();
}


foreach ($_POST as $key => $item)
{
	if (preg_match('/priv_/isu', $key))
	{
		$regex='/priv\_(?<priv>\d+)/isu';
		preg_match_all($regex, $key, $out);
		if (intval($out['priv'][0])>intval($tpriv)) $tpriv=intval($out['priv'][0]);
		//$tpriv.=$zap.$out['priv'][0];
		//$zap=',';
	}
}

if (($_POST['order_id']!='') && ($_POST['share_email']!='') && (($_POST['priv_1']!='') || ($_POST['priv_2']!='') || ($_POST['priv_3']!='')))
{
	$qisset=$db->query('SELECT order_id FROM blog_orders WHERE user_id='.$user['user_id'].' AND order_id='.intval($_POST['order_id']));
	if ($db->num_rows($qisset)==0)
	{
		$outmas['status']=3;
		echo json_encode($outmas);
		die();
	}
	$qorder=$db->query('SELECT order_id,order_name FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$user['user_id']);
	$order=$db->fetch($qorder);
	if (intval($order['order_id'])!=0)
	{
		$memails=explode(',', trim($_POST['share_email']));
		foreach ($memails as $item)
		{
			if (!isValidEmail($item))
			{
				$outmas['status']=5;
				echo json_encode($outmas);
				die();
			}
			if ($item==$user['user_email'])
			{
				$outmas['status']=4;
				echo json_encode($outmas);
				die();
			}
			$quser=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($item).'\' LIMIT 1');
			if ($db->num_rows($quser)!=0)
			{
				$qruser=$db->fetch($quser);
				$headers  = "From: noreply@wobot.ru\r\n"; 
				$headers .= "Bcc: noreply@wobot.ru\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
				//echo 'INSERT INTO blog_sharing (order_id,user_id,sharing_priv,sharing_approve) VALUES ('.intval($_POST['order_id']).','.$user['user_id'].',(\''.$tpriv.'\'),1)';
				$qissetshar=$db->query('SELECT * FROM blog_sharing WHERE order_id='.intval($_POST['order_id']).' AND user_id='.intval($qruser['user_id']));
				if ($db->num_rows($qissetshar)==0)
				{
					$db->query('INSERT INTO blog_sharing (order_id,user_id,sharing_priv,sharing_approve) VALUES ('.intval($_POST['order_id']).','.$qruser['user_id'].',(\''.$tpriv.'\'),1)');
					mail($item,'Команда Вобот',("Пользователь ".$user['user_email']." поделился с вами темой \"".$order['order_name']."\""),$headers);
				}
				else
				{
					$db->query('UPDATE blog_sharing SET sharing_priv=\''.$tpriv.'\' WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$qruser['user_id']);
					mail($item,'Команда Вобот',("Пользователь ".$user['user_email']." поделился с вами темой \"".$order['order_name']."\""),$headers);
				}
			}
			else
			{
				$db->query('INSERT INTO users (user_email,user_active) VALUES (\''.addslashes($item).'\',1)');
				$uid=$db->insert_id();
				$db->query('INSERT INTO blog_sharing (order_id,user_id,sharing_priv,sharing_token) VALUES ('.intval($_POST['order_id']).','.$uid.',(\''.$tpriv.'\'),\''.md5($item.' '.$_POST['order_id'].' '.$tpriv).'\')');
				$headers  = "From: noreply@wobot.ru\r\n"; 
				$headers .= "Bcc: noreply@wobot.ru\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
				mail($item,'Команда Вобот',('Пользователь '.$user['user_email'].' поделился с вами темой '.$order['order_name'].', чтобы приступить к пользованию системой пройдите <a href=\'http://link.php?share_token='.md5($item.' '.$_POST['order_id'].' '.$tpriv).'&share_email='.$item.'&suid='.$uid.'\'>регистрацию</a>'),$headers);	
				//echo 'http://link.php?share_token='.md5($item.' '.$_POST['order_id'].' '.$tpriv).'&share_email='.$item.'&suid='.$uid;
				//$outmas['status']='ok';
				//echo json_encode($outmas);
				//die();
			}
			$priv=$memcache->get('blog_sharing');
			$mpriv=json_decode($priv,true);
			$mpriv[intval($_POST['order_id'])][$qruser['user_id']]=$tpriv;
			$memcache->set('blog_sharing', json_encode($mpriv), MEMCACHE_COMPRESSED, 86400);
		}
		$outmas['status']='ok';
		echo json_encode($outmas);
		die();
	}
	else
	{
		$qsharorder=$db->query('SELECT * FROM blog_sharing WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$user['user_id'].' AND FIND_IN_SET(\'3\',sharing_approve)>0');
		$shareorder=$db->fetch($qsharorder);
		if (intval($shareorder['order_id'])!=0)
		{
			$memails=explode(',', trim($_POST['share_email']));
			foreach ($memails as $item)
			{
				if (!isValidEmail($item)) continue;
				$quser=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($item).'\' LIMIT 1');
				if ($db->num_rows($quser)!=0)
				{
					$qruser=$db->fetch($quser);
					$headers  = "From: noreply@wobot.ru\r\n"; 
					$headers .= "Bcc: noreply@wobot.ru\r\n";
					$headers .= "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
					//echo 'INSERT INTO blog_sharing (order_id,user_id,sharing_priv,sharing_approve) VALUES ('.intval($_POST['order_id']).','.$user['user_id'].',(\''.$tpriv.'\'),1)';
					$qissetshar=$db->query('SELECT * FROM blog_sharing WHERE order_id='.intval($_POST['order_id']).' AND user_id='.intval($qruser['user_id']));
					if ($db->num_rows($qissetshar)==0)
					{
						$db->query('INSERT INTO blog_sharing (order_id,user_id,sharing_priv,sharing_approve) VALUES ('.intval($_POST['order_id']).','.$qruser['user_id'].',(\''.$tpriv.'\'),1)');
						mail($item,'Команда Вобот',("Пользователь ".$user['user_email']." поделился с вами темой \"".$order['order_name']."\""),$headers);
					}
					else
					{
						$db->query('UPDATE blog_sharing SET sharing_priv=\''.$tpriv.'\' WHERE order_id='.intval($_POST['order_id'].' AND user_id='.$user['user_id']));
						mail($item,'Команда Вобот',("Пользователь ".$user['user_email']." поделился с вами темой \"".$order['order_name']."\""),$headers);
					}
				}
				else
				{
					$db->query('INSERT INTO users (user_email,user_active) VALUES (\''.addslashes($item).'\',1)');
					$uid=$db->insert_id();
					$db->query('INSERT INTO blog_sharing (order_id,user_id,sharing_priv,sharing_token) VALUES ('.intval($_POST['order_id']).','.$uid.',(\''.$tpriv.'\'),\''.md5($item.' '.$_POST['order_id'].' '.$tpriv).'\')');
					$headers  = "From: noreply@wobot.ru\r\n"; 
					$headers .= "Bcc: noreply@wobot.ru\r\n";
					$headers .= "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
					mail($item,'Команда Вобот',('Пользователь '.$user['user_email'].' поделился с вами темой '.$order['order_name'].', чтобы приступить к пользованию системой пройдите <a href=\'http://link.php?share_token='.md5($item.' '.$_POST['order_id'].' '.$tpriv).'&share_email='.$item.'&suid='.$uid.'\'>регистрацию</a>'),$headers);	
					//echo 'http://link.php?share_token='.md5($item.' '.$_POST['order_id'].' '.$tpriv).'&share_email='.$item.'&suid='.$uid;
					//$outmas['status']='ok';
					//echo json_encode($outmas);
					//die();
				}
				$priv=$memcache->get('blog_sharing');
				$mpriv=json_decode($priv,true);
				$mpriv[intval($_POST['order_id'])][$qruser['user_id']]=$tpriv;
				$memcache->set('blog_sharing', json_encode($mpriv), MEMCACHE_COMPRESSED, 86400);
			}
		}
		else
		{
			$out['status']=2;
			echo json_encode($out);
			die();
		}
	}
}
else
{
	$out['status']=1;
	echo json_encode($out);
	die();
}


?>