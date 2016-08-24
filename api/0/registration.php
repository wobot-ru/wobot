<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

//$_POST=$_GET;

$tarfs[3]='Демо';
$tarfs[5]='Базовый';
$tarfs[6]='Расширенный';
$tarfs[7]='Профессиональный';

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

$memcache_obj = new Memcache;

$memcache_obj->connect('localhost', 11211);
$out1=intval($memcache_obj->get('registration_'.$_POST['uid']));
if ($out1>5)
{
	$out['status']='fail';
	$errors[]=10;
	$out['errors']=$errors;
	echo json_encode($out);
	die();
}
else
{
	$out1++;
	$memcache_obj->set('registration_'.$_POST['uid'], $out1,0,30);
}
//if ((trim($_POST['user_email'])!='') && (mb_strlen(trim($_POST['user_pass']),'UTF-8')!=0) && ($_POST['user_name']!='') && ($_POST['user_contact']!=''))// && ($_POST['user_company']!='')
function check_pass($pass)
{
	$val_zag='/[А-ЯA-Z]/u';
	$val_dig='/[0-9]/u';
	if (mb_strlen($pass,'UTF-8')<5) return false;
	//if (!preg_match($val_zag,$pass)) return false;
	//if (!preg_match($val_dig,$pass)) return false;
	return true;
}

//$_POST=$_GET;

if ($_POST['share_token']!='')
{
	if (trim($_POST['user_email'])=='')
	{
		$errors[]=1;
	}
	else
	{
		//$qyet=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['user_email'].'\'');
		//if ($db->num_rows($qyet)!=0)
		//{
		//	$errors[]=2;
		//}
	}

	if ((mb_strlen(trim($_POST['user_pass']),'UTF-8')==0) || (!check_pass(trim($_POST['user_pass']))))
	{
		$errors[]=3;
	}

	if (trim($_POST['user_name'])=='')
	{
		$errors[]=4;
	}

	if (trim($_POST['user_contact']==''))
	{
		$errors[]=5;
	}
	elseif (preg_match('/[^\d]/isu', $_POST['user_contact']) || (mb_strlen($_POST['user_contact'],'UTF-8')<10) || (mb_strlen($_POST['user_contact'],'UTF-8')>11))
	{
		$errors[]=9;
	}

	if (trim($_POST['user_company'])=='')
	{
		$errors[]=6;
	}

	if (trim($_POST['user_position'])=='')
	{
		$errors[]=7;
	}

	$quser=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($_POST['share_email']).'\' AND user_id='.intval($_POST['suid']));
	if ($db->num_rows($quser)==0)
	{
		$errors[]=11;
	}
	else
	{
		$share_user=$db->fetch($quser);
		$qsharing=$db->query('SELECT * FROM blog_sharing WHERE user_id='.$_POST['suid'].' AND sharing_token=\''.$_POST['share_token'].'\'');
		if ($db->num_rows($qsharing)==0)
		{
			$errors[]=12;
		}
	}

	if (count($errors)==0)
	{
		$qw='UPDATE users SET user_pass=\''.addslashes(md5($_POST['user_pass'])).'\',user_active=2,user_name=\''.addslashes($_POST['user_name']).'\',user_company=\''.addslashes($_POST['user_company']).'\',user_contact=\''.addslashes($_POST['user_contact']).'\',user_position=\''.addslashes($_POST['user_position']).'\',user_mails=\''.addslashes($_POST['user_email']).'\',user_ctime='.time().',ref='.intval($ref).',user_promo=\''.addslashes(preg_replace('/[^а-яА-ЯёЁa-zA-Z0-9]/isu','',$_POST['user_promo'])).'\' WHERE user_id='.$share_user['user_id'];
		//echo $qw;
		//die();
		$db->query($qw);
		if (intval($_POST['tariff'])==3)
		{
			$qw1='INSERT INTO user_tariff (user_id,tariff_id,ut_date) VALUES ('.$_POST['suid'].','.intval($_POST['tariff']).','.mktime(0,0,0,date('n'),date('j')+14,date('Y')).')';
		}
		else
		{
			$qw1='INSERT INTO user_tariff (user_id,tariff_id,ut_date) VALUES ('.$_POST['suid'].','.intval($_POST['tariff']).','.mktime(0,0,0,date('n'),date('j')+14,date('Y')).')';
		}
		$db->query($qw1);
		$qq='UPDATE blog_sharing SET sharing_approve=1 WHERE user_id='.$_POST['suid'];
		$db->query($qq);
		$headers  = "From: noreply@wobot.ru\r\n"; 
		$headers .= "Bcc: noreply@wobot.ru\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
		//parseUrlmail('http://wobot.ru/mail_send.php','xydoshnik@gmail.com','Команда Wobot',urlencode("Пользователь ".$_POST['user_email']." зарегистрироваля в системе.<br>Тариф: ".$tarfs[intval($_POST['tariff'])]."<br>Пароль: ".$_POST['user_pass']."<br>ФИО: ".$_POST['user_name']."<br>Компания: ".$_POST['user_company']."<br>Контактные данные: ".$_POST['user_contact']),$headers);
		//parseUrlmail('http://wobot.ru/mail_send.php','r@wobot.co','Команда Wobot',urlencode("Пользователь ".$_POST['user_email']." зарегистрироваля в системе.<br>Тариф: ".$tarfs[intval($_POST['tariff'])]."<br>Пароль: ".$_POST['user_pass']."<br>ФИО: ".$_POST['user_name']."<br>Компания: ".$_POST['user_company']."<br>Контактные данные: ".$_POST['user_contact']),$headers);
		//parseUrlmail('http://wobot.ru/mail_send.php','s.vyaltsev@wobot.ru','Команда Wobot',urlencode("Пользователь ".$_POST['user_email']." зарегистрироваля в системе.<br>Тариф: ".$tarfs[intval($_POST['tariff'])]."<br>Пароль: ".$_POST['user_pass']."<br>ФИО: ".$_POST['user_name']."<br>Компания: ".$_POST['user_company']."<br>Контактные данные: ".$_POST['user_contact']),$headers);
		mail($_POST['user_email'],'Регистрация','Спасибо за регистрацию, для завершения регистрации просьба перейти по <a href=\'http://wobot.ru/registration.php?token='.md5($_POST['user_email'].md5($_POST['user_pass'])).intval($_POST['tariff']).'\'>ссылке</a><div><i><img src=\'http://wobot.ru/new/assets/logo.png\'></i></div>','noreply@wobot.ru');
		$out['status']='ok';
		echo json_encode($out);
		die();
	}
	else
	{
		$out['status']='fail';
		$out['errors']=$errors;
		echo json_encode($out);
		die();
	}
}
elseif ($_POST['token']=='')
{
	if (trim($_POST['user_email'])=='')
	{
		$errors[]=1;
	}
	else
	{
		$qyet=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['user_email'].'\'');
		if ($db->num_rows($qyet)!=0)
		{
			$errors[]=2;
		}
	}

	if ((mb_strlen(trim($_POST['user_pass']),'UTF-8')==0) || (!check_pass(trim($_POST['user_pass']))))
	{
		$errors[]=3;
	}

	if (trim($_POST['user_name'])=='')
	{
		$errors[]=4;
	}

	if (trim($_POST['user_contact']==''))
	{
		$errors[]=5;
	}
	elseif (preg_match('/[^\d]/isu', $_POST['user_contact']) || (mb_strlen($_POST['user_contact'],'UTF-8')<10) || (mb_strlen($_POST['user_contact'],'UTF-8')>11))
	{
		$errors[]=9;
	}

	if (trim($_POST['user_company'])=='')
	{
		$errors[]=6;
	}

	if (trim($_POST['user_position'])=='')
	{
		$errors[]=7;
	}

	if (count($errors)==0)
	{
		$qw='INSERT INTO users (user_email,user_pass,user_verify,user_active,user_name,user_company,user_contact,user_position,user_mails,user_ctime,ref'.($_GET['vk_id']!=''&&$_GET['vk_token']!=''?',vk_id,vk_token':'').',user_promo) VALUES (\''.addslashes($_POST['user_email']).'\',\''.addslashes(md5($_POST['user_pass'])).'\',\''.md5($_POST['user_email'].md5($_POST['user_pass'])).'\',1,\''.addslashes($_POST['user_name']).'\',\''.addslashes($_POST['user_company']).'\',\''.addslashes($_POST['user_contact']).'\',\''.addslashes($_POST['user_position']).'\',\''.addslashes($_POST['user_email']).'\','.time().','.intval($ref).($_GET['vk_id']!=''&&$_GET['vk_token']!=''?','.$_GET['vk_id'].',\''.addslashes($_GET['vk_token']).'\'':'').',\''.addslashes(preg_replace('/[^а-яА-ЯёЁa-zA-Z0-9]/isu','',$_POST['user_promo'])).'\')';
		//echo $qw;
		$db->query($qw);
		if (!isset($tarfs[$_POST['tariff']])) $_POST['tariff']=3;
		if (intval($_POST['tariff'])==3)
		{
			$qw1='INSERT INTO user_tariff (user_id,tariff_id,ut_date) VALUES ('.$db->insert_id().','.intval($_POST['tariff']).','.mktime(0,0,0,date('n'),date('j')+14,date('Y')).')';
		}
		else
		{
			$qw1='INSERT INTO user_tariff (user_id,tariff_id,ut_date) VALUES ('.$db->insert_id().','.intval($_POST['tariff']).',0)';
		}
		$db->query($qw1);
		$headers  = "From: noreply@wobot.ru\r\n"; 
		$headers .= "Bcc: noreply@wobot.ru\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
		//parseUrlmail('http://wobot.ru/mail_send.php','xydoshnik@gmail.com','Команда Wobot',urlencode("Пользователь ".$_POST['user_email']." зарегистрироваля в системе.<br>Тариф: ".$tarfs[intval($_POST['tariff'])]."<br>Пароль: ".$_POST['user_pass']."<br>ФИО: ".$_POST['user_name']."<br>Компания: ".$_POST['user_company']."<br>Контактные данные: ".$_POST['user_contact']),$headers);
		//parseUrlmail('http://wobot.ru/mail_send.php','r@wobot.co','Команда Wobot',urlencode("Пользователь ".$_POST['user_email']." зарегистрироваля в системе.<br>Тариф: ".$tarfs[intval($_POST['tariff'])]."<br>Пароль: ".$_POST['user_pass']."<br>ФИО: ".$_POST['user_name']."<br>Компания: ".$_POST['user_company']."<br>Контактные данные: ".$_POST['user_contact']),$headers);
		//parseUrlmail('http://wobot.ru/mail_send.php','s.vyaltsev@wobot.ru','Команда Wobot',urlencode("Пользователь ".$_POST['user_email']." зарегистрироваля в системе.<br>Тариф: ".$tarfs[intval($_POST['tariff'])]."<br>Пароль: ".$_POST['user_pass']."<br>ФИО: ".$_POST['user_name']."<br>Компания: ".$_POST['user_company']."<br>Контактные данные: ".$_POST['user_contact']),$headers);
		mail($_POST['user_email'],'Регистрация','Спасибо за регистрацию, для завершения регистрации просьба перейти по <a href=\'http://wobot.ru/registration.php?token='.md5($_POST['user_email'].md5($_POST['user_pass'])).intval($_POST['tariff']).'\'>ссылке</a><div><i><img src=\'http://wobot.ru/new/assets/logo.png\'></i></div>','noreply@wobot.ru');
		$out['status']='ok';
		echo json_encode($out);
		die();
	}
	else
	{
		$out['status']='fail';
		$out['errors']=$errors;
		echo json_encode($out);
		die();
	}
}
else
{
	$tr_id=intval($_POST['token'][mb_strlen($_POST['token'],'UTF-8')-1]);
	//echo $tr_id.'<br>';
	$_POST['token']=mb_substr($_POST['token'],0,mb_strlen($_POST['token'],'UTF-8')-1,'UTF-8');
	//echo $_GET['token'];
	$qw1=$db->query('SELECT * FROM users WHERE user_verify=\''.$_POST['token'].'\' LIMIT 1');
	$us=$db->fetch($qw1);
	if ($us['user_id']!='')
	{
		if ($tr_id==3)
		{
			$qw2=$db->query('UPDATE users SET user_active=2,user_verify=\'\' WHERE user_id='.$us['user_id']);
			//$qw1='INSERT INTO user_tariff (user_id,tariff_id,ut_date) VALUES ('.$us['user_id'].','.$tr_id.','.mktime(0,0,0,date('n'),date('j')+14,date('Y')).')';
		}
		else
		{
			$qw2=$db->query('UPDATE users SET user_active=3,user_verify=\'\' WHERE user_id='.$us['user_id']);
			//$qw1='INSERT INTO user_tariff (user_id,tariff_id,ut_date) VALUES ('.$us['user_id'].','.$tr_id.',0)';
		}
		$msg=$db->query('SELECT * FROM msg_tpl WHERE id=9');
		$ms=$db->fetch($msg);
		$text=$ms['message'];
		$title='Команда Wobot';
		//echo $title;
		$text="<p>
			<span style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>Приветствуем Вас! Спасибо за регистрацию в системе мониторинга социальных медиа Wobot.</span></p>
		<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
			</div>
		".($tr_id!=3?"":"	<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
				Сейчас вам доступен демонстрационный кабинет. Доступ в него будет открыт на 14 дней по введенному вами логину (почтовый ящик) и паролю.</div>
			<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
				</div>
			<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
				В демо-кабинете вам доступны заранее предустановленные темы мониторинга. Возможность редактирования выдачи и экспорта в нем ограничены.</div>
			<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
				</div>
			<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
				Для использования полной версии нашей системы пожалуйста ознакомьтесь с доступными тарифами -<i><a href='http://wobot.ru/tariff.php'>ссылка</a></i>.</div>
			<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
				</div>")."
		<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
			Если у вас возникнут вопросы по использованию системы - свяжитесь с нами по электронной почте <a class='daria-action' href='mailto:mail@wobot.ru'>mail@wobot.ru</a>, и мы незамедлительно ответим на все ваши вопросы.</div>
		<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
			</div>
		<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
			Команда Wobot.</div>
		<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
			</div>
		<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
			Присоединяйтесь в наше сообщество!<a class='daria-goto-anchor' href='http://www.facebook.com/wobot.ru' target='_blank'>http://www.facebook.com/wobot.ru</a></div>
		<div style='color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 15px; line-height: 21px; '>
			<div>
				</div>
			<div>
				<i><img src='http://www.wobot.ru/new/assets/logo.png'></i></div>
		</div>
		<p>
			</p>";
		mail($us['user_email'],$title,preg_replace('/\"/is','\'',preg_replace('/\&nbsp\;/is','',$text)),'noreply@wobot.ru');
		$out['status']='ok';
		echo json_encode($out);
		die();
	}
	else
	{
		$out['status']='fail';
		$errors[]=8;
		$out['errors']=$errors;
		echo json_encode($out);
		die();
	}
}
?>
