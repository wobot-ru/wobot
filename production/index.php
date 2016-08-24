<?
//error_reporting(-1);
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

//require_once('/var/www/api/0/auth.php');
date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();	

$new_pass=0;
$nomail=0;
//востановление
function parseUrlmail($url,$to,$subj,$body,$from)
{
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='mail='.$to.'&title='.$subj.'&content='.$body.'&from='.$from;
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
 /*$header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;*/
  return $content;
}

$all_form='';
/*$all_form='
			<form method="POST" style="padding: 40px 30px; margin: 40px 30px; position: relative; left: 0px; top: 60px;">
			<table>
			<tr>
			<td style="color: white; padding-left: 7px">
			Изменение пароля:
			</td>
			</tr>
			<tr>
			<td>
				<input type="hidden" name="token" value="'.$_GET['token'].'"><input type="hidden" name="uid" value="'.$_GET['uid'].'"><input type="password" name="pass" placeholder="Новый пароль">
			</td>
			</tr>
			<tr>
			<td>
				<input type="password" name="ver_pass" placeholder="Повторите пароль">
			</td>
			</tr>
			<tr>
			<td>
			<input type="submit" value="изменить" class="button small" style="margin-top: 7px; margin-top: 7px; border-radius: 4px; width: 228px;">
			</td>
			</tr>
			</table>
			</form>';
*/

function check_pass($pass)
{
	$val_zag='/[А-ЯA-Z]/u';
	$val_dig='/[0-9]/u';
	if (mb_strlen($pass,'UTF-8')<6) return false;
	if (!preg_match($val_zag,$pass)) return false;
	//if (!preg_match($val_dig,$pass)) return false;
	return true;
}

if ((($_POST['pass']!='') || ($_POST['ver_pass']!='')) && $_GET['mp']!=1)
{
	//echo 1;
	if ($_POST['pass']==$_POST['ver_pass'])
	{
		// echo 2;
		// echo 'SELECT * FROM users WHERE user_id='.$_POST['uid'].' AND user_pass=\''.$_POST['ctoken'].'\' LIMIT 1';
		$i=$db->query('SELECT * FROM users WHERE user_id='.$_POST['uid'].' AND user_pass=\''.$_POST['ctoken'].'\' LIMIT 1');
		$user=$db->fetch($i);
		if ($user['user_email']!='')
		{
			// echo 3;
			if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($user['user_email'])))
			{
				// echo 4;
				if (check_pass(trim($_POST['pass'])))
				{
					// echo 5;
					$headers='noreply@wobot.ru';
					// $headers  = "From: noreply@wobot.ru\r\n"; 
					// $headers .= "Bcc: noreply@wobot.ru\r\n";
					// $headers .= "MIME-Version: 1.0" . "\r\n";
					// $headers .= "Content-type: text/html; charset=utf-8"."\r\n";
					$i=$db->query('UPDATE users SET user_pass=\''.md5($_POST['pass']).'\' WHERE user_id='.$_POST['uid'].' AND user_pass=\''.$_POST['ctoken'].'\'');
					parseUrlmail('http://188.120.239.225/api/service/sendmail.php',$user['user_email'],'Команда Wobot',urlencode('<html><body>Ваш пароль успешно изменен на: '.$_POST['pass'].'<br>Спасибо за использование нашего сервиса!</body></html>'),$headers);
					$all_form= '<div class="msgBox confirm" style="position: absolute; width: 310px; margin-top: 176px; margin-left: 11px; background: white; padding: 10px; border-radius: 13px;">Поздравляю ваш пароль успешно изменен! <br><a href="http://production.wobot.ru">Перейти к авторизации</a></div>';
					$new_pass_good=1;
					// echo $new_pass_good;
					$new_pass=1;
				}
				else
				{
					$new_pass=1;
					$all_form='
					<div class="msgBox error" style="position: absolute; margin-top: 24px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Вы ввели некорректный пароль. Пожалуйста, введите еще раз. Пароль должен содержать не менее 6 символов и хотя бы 1 заглавную букву.</div>'.$all_form;
					//die();
				}
			}
			else
			{
				$new_pass=1;
				$all_form= '
				<div class="msgBox error" style="position: absolute; margin-top: 24px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Вы ввели не почту! Пожалуйста введите еще раз.</div>'.$all_form;
				//die();
			}
		}
		else
		{
            // die('123');
			$new_pass=1;
			$all_form= '
			<div class="msgBox error" style="position: absolute; margin-top: 24px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Вы ввели пустой пароль! Пожалуйста введите еще раз.</div>'.$all_form;
			//die();
		}
	}
	else
	{
		$new_pass=1;
		$all_form= '
		<div class="msgBox error" style="position: absolute; margin-top: 37px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Введенные пароли не совпадают.</div>'.$all_form;
		//die();
	}
	//die();
}
elseif ($_POST['mail']!='')
{
	$i=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['mail'].'\' LIMIT 1');
	$user=$db->fetch($i);
	if ($user['user_email']!='')
	{
		$headers='noreply@wobot.ru';
		// $headers  = "From: noreply@wobot.ru\r\n"; 
		// $headers .= "Bcc: noreply@wobot.ru\r\n";
		// $headers .= "MIME-Version: 1.0" . "\r\n";
		// $headers .= "Content-type: text/html; charset=utf-8"."\r\n";
		parseUrlmail('http://188.120.239.225/api/service/sendmail.php',$user['user_email'],'Команда Wobot',urlencode('<html><body>Здравствуйте! <br>Вы запросили восстановление пароля к своему личному кабинету в системе Wobot. Следуйте <a href=\'http://production.wobot.ru/?ctoken='.$user['user_pass'].'&uid='.$user['user_id'].'\'>далее по ссылке</a></body></html>'),$headers);
		//echo '<div class="msgBox confirm">Вам на почту выслано сообщение.</div>';
		//die();
		$nomail='<div class="msgBox error" style="position: absolute; margin-top: 37px; width: 329px; padding: 10px; background: white; border-radius: 13px;">На указанный e-mail высланы инструкции по смене пароля.</div>';
	}
	else
	{
		$nomail='<div class="msgBox error" style="position: absolute; margin-top: 37px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Пользователь с указанным email не найден. Пожалуйста, проверьте введенный адрес.</div>';
		/*echo '
		<form method="POST">
		<table cellpadding="5" style="padding-bottom: 100px;">
		<tr>
		<td>
		Введите ваш e-mail:
		</td>
		<td>
		<input type="text" name="mail">
		</td>
		<td><input type="submit" value="восстановить" class="button small" style="margin-top: 7px; border-radius: 4px;"></td>
		</tr>

		</table>
		</form>
		';*/
	}
}
else
if (($_GET['token']!='') && ($_GET['uid']!=''))
{
	$i=$db->query('SELECT * FROM users WHERE user_pass=\''.$_GET['token'].'\' AND user_id='.$_GET['uid'].' LIMIT 1');
	$user=$db->fetch($i);
	if ($user['user_pass']!='')
	{
		$new_pass=1;
		
		//die();
	}
}
else
{
/*echo '
<form method="POST">
<table cellpadding="5" style="padding-bottom: 100px;">
<tr>
<td>
Введите ваш e-mail:
</td>
<td>
<input type="text" name="mail">
</td>
<td><input type="submit" value="Восстановить" class="button small" style="margin-top: 7px;"></td>
</tr>

</table>
</form>
';*/
}

//востановление

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//die();
//Временно отключаю возможность для тестирования!!!
function auth1()
{
	global $db,$user,$loged,$new_pass_good,$all_form,$config;
	//setcookie("token", "");
	//setcookie("user_id", "");
	//print_r($_COOKIE);
	
	if (isset($_POST['token']) && isset($_POST['user_id']))
	{
		setcookie("token", $_POST['token']);
		setcookie("user_id", $_POST['user_id']);
		$res=$db->query('SELECT * FROM users WHERE user_id='.intval($_POST['user_id']).' AND (user_active=0 OR user_active=2 OR user_active=3) LIMIT 1');
		$row = $db->fetch($res);
		if (intval($row['user_id'])!=0)
		{
			if ((md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']))==$_POST['token'])
			{
				$user=$row;
				$loged=1;
				$reslog=$db->query('INSERT INTO blog_log (user_id,log_ip,log_time) VALUES ('.$row['user_id'].',\''.getRealIpAddr().'\','.time().')');
				header('Location: ./themes_list.html');
			}
		}
	}
	
	if (isset($_COOKIE['token']) && ($_COOKIE['token']!=''))
	{
		//echo $_COOKIE['token'];
		$res=$db->query('SELECT * FROM users WHERE user_id='.intval($_COOKIE['user_id']).' AND (user_active=0 OR user_active=2) LIMIT 1');
		$row = $db->fetch($res);
		if (intval($row['user_id'])!=0)
		{
			if ((md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']))==$_COOKIE['token'])
			{
				$user=$row;
				$loged=1;
				$reslog=$db->query('INSERT INTO blog_log (user_id,log_ip,log_time) VALUES ('.$row['user_id'].',\''.getRealIpAddr().'\','.time().')');
				header('Location: ./themes_list.html');
			}
		}
	}
	if (isset($_POST['login']) && (isset($_POST['password'])))
	{
		//echo 'gg';
		$res=$db->query('SELECT * FROM users WHERE LOWER(user_email)=\''.mb_strtolower($_POST['login'],'UTF-8').'\' and user_pass=\''.md5($_POST['password']).'\' and user_active!=1 LIMIT 1');
		//echo 'SELECT * FROM users WHERE LOWER(user_email)=\''.mb_strtolower($_POST['login'],'UTF-8').'\' and user_pass=\''.md5($_POST['password']).'\' and user_active!=1 LIMIT 1';
		//die();
		$row = $db->fetch($res);
		if ((intval($row['user_id'])!=0) && (mb_strtolower($_POST['login'],'UTF-8')==mb_strtolower($row['user_email'],'UTF-8')))
		{
			setcookie("token", md5(mb_strtolower($_POST['login'],'UTF-8').':'.md5($_POST['password'])));
			setcookie("user_id", $row['user_id']);
			$reslog=$db->query('INSERT INTO blog_log (user_id,log_ip,log_time) VALUES ('.$row['user_id'].',\''.getRealIpAddr().'\','.time().')');
			//$_SESSION['user_email']=$_POST['login'];
			//$_SESSION['user_pass']=md5($_POST['password']);
			//$_SESSION['user_id']=$row['user_id'];
			//print_r($_SESSION);
			header('Location: ./themes_list.html');
			$loged=1;
		}
		else
		{
			foreach ($config['servers'] as $kserver => $server)
			{
				$mlogin=json_decode(file_get_contents('http://'.$server.'/api/0/login?login='.$_POST['login'].'&pass='.$_POST['password']),true);
				if ($mlogin['status']=='ok')
				{
					header('Location: http://'.$config['host_servers'][$kserver].'?redirect_user_id='.$mlogin['user_id'].'&redirect_token='.$mlogin['token']);
					die();
				}
			}
			header('Location: ./?wrongpass');
			die();
		}
	}
	if (isset($_GET['redirect_token']) && ($_GET['redirect_user_id']!=''))
	{
		//echo $_COOKIE['token'];
		$res=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['redirect_user_id']).' AND (user_active=0 OR user_active=2) LIMIT 1');
		$row = $db->fetch($res);
		if (intval($row['user_id'])!=0)
		{
			if ((md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']))==$_GET['redirect_token'])
			{
				$user=$row;
				$loged=1;
				setcookie("token", md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']));
				setcookie("user_id", $row['user_id']);
				$reslog=$db->query('INSERT INTO blog_log (user_id,log_ip,log_time) VALUES ('.$row['user_id'].',\''.getRealIpAddr().'\','.time().')');
				header('Location: ./themes_list.html');
				die();
			}
		}
	}

	if (!$loged)
	{
		$template='<form method="POST" style="padding: 40px 30px; margin: 40px 30px; position: relative; left: 0px; top: 60px;">
		<fieldset>
		<input type="hidden" name="user_login" value="'.rand().'">
		<table>
		<tr>
		<td>
		<div class="clearfix">
		<input type="text" name="login" id="user_email" placeholder="Логин" autofocus required>
		</div>
		</td>
		</tr>
		<tr>
		<td>
		<div class="clearfix">
		<input type="password" name="password" id="password" placeholder="Пароль" required>
		</div>
		</td>
		</tr>
		<tr>
		<td>
		<input type="submit" name="login1" value="войти" style="width: 231px; border-radius: 4px;">
		</fieldset>
		</td>
		</tr>
		<tr>
		<td>
		<p style="margin: 10px; text-align: right;">
		<a href="http://production.wobot.ru/?recover=1" style="color: #333; text-decoration: none;">Забыли пароль?</a>
		</p>
		</td>
		</tr>
		</table>
		</form>';
		if($_GET['recover']==1){
			$template='<form method="POST" style="padding: 40px 30px; margin: 40px 30px; position: relative; left: 0px; top: 60px;">
			<table>
			<tr>
			<td>
			<div class="clearfix" style="color:white">
			&nbsp;&nbsp;&nbsp;Восстановить пароль
			</div>
			</td>
			</tr>
			<tr>
			<td>
			<div class="clearfix">
			<input type="text" name="mail" placeholder="E-mail" autofocus required>
			</div>
			</td>
			</tr>
			<tr>
			<td>
			<div class="clearfix">
			<input type="submit" value="Восстановить" style="width: 231px; border-radius: 4px;">
			</div>
			</td>
			</tr>

			</table>
			</form>';
			if(is_numeric($GLOBALS['nomail'])){

			}
			else{
				$template=$GLOBALS['nomail'].$template;
			}

		}


		if($GLOBALS['new_pass']==1){
			$template=$GLOBALS['all_form'];
		}


        if (isset($_POST['setnewpass']) && intval($_POST['uid']) != 0) {
$new_pass=0;

            if (($_POST['pass'] != '') || ($_POST['ver_pass'] != '')) {

                
                //echo 1;
                if ($_POST['pass'] == $_POST['ver_pass']) {
                    //echo 2;
                    //TODO: тут проверка на инъекцию!!
                    $i = $db->query('SELECT * FROM users WHERE user_id=' . $_POST['uid'] . ' AND user_pass=\'' . md5($_POST['token']) . '\' LIMIT 1');
                    //  echo 'SELECT * FROM users WHERE user_id=' . $_POST['uid'] . ' AND user_pass=\'' . $_POST['token'] . '\' LIMIT 1';
                    $user = $db->fetch($i);
                    if ($user['user_email'] != '') {
                        //echo 3;
                        if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is', trim($user['user_email']))) {
                            //echo 4;
                            if (check_pass(trim($_POST['pass']))) {
                                //echo 5;
                                //$headers = "From: noreply@wobot.ru\r\n";
                                //$headers .= "Bcc: noreply@wobot.ru\r\n";
                                //$headers .= "MIME-Version: 1.0" . "\r\n";
                                //$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
                                $i = $db->query('UPDATE users SET user_pass=\'' . md5($_POST['pass']) . '\' WHERE user_id=' . $_POST['uid'] . ' AND user_pass=\'' . md5($_POST['token']) . '\'');
                                //echo 'UPDATE users SET user_pass=\'' . md5($_POST['pass']) . '\' WHERE user_id=' . $_POST['uid'] . ' AND user_pass=\'' . md5($_POST['token']) . '\'';
                                //die();
                                //mail($user['user_email'], 'Команда Wobot', ('<html><body>Ваш пароль успешно изменен на: ' . $_POST['pass'] . '<br>Спасибо за использование нашего сервиса!</body></html>'), $headers);
                                $all_form = '<div class="msgBox confirm" style="position: absolute; width: 310px; margin-top: 176px; margin-left: 11px; background: white; padding: 10px; border-radius: 13px;">Поздравляю ваш пароль успешно изменен! <br><a href="http://production.wobot.ru">Перейти к авторизации</a></div>';
                                $new_pass = 1;
                            }
                            else
                            {

                                $all_form = '<div class="msgBox error" style="position: absolute; margin-top: 24px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Вы ввели некорректный пароль. Пожалуйста, введите еще раз. Пароль должен содержать не менее 6 символов и хотя бы 1 заглавную букву.</div>' . $all_form;
                                //die();
                            }
                        }
                        else
                        {

                            //$all_form = ' <div class="msgBox error" style="position: absolute; margin-top: 24px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Вы ввели не почту! Пожалуйста введите еще раз.</div>' . $all_form;
                            //die();
                        }
                    }
                    else
                    {

                        $all_form = '<div class="msgBox error" style="position: absolute; margin-top: 24px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Некорректная ссылка.</div>';
                        //die();
                    }
                }
                else
                {

                    $all_form = '<div class="msgBox error" style="position: absolute; margin-top: 37px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Введенные пароли не совпадают.</div>';
                    //die();
                }
                //die();
            }
            else
            {
                $all_form = '<div class="msgBox error" style="position: absolute; margin-top: 37px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Укажите новый пароль.</div>';
            }

        }
        elseif (isset($_POST['setnewpass']))
        {
            $all_form = '<div class="msgBox error" style="position: absolute; margin-top: 37px; width: 329px; padding: 10px; background: white; border-radius: 13px;">Недопустимые параметры. Обратитесь к администратору.</div>';
        }

        if ($_GET['mp'] == 1 && $new_pass != 1) {

            //$template=$GLOBALS['all_form'];
            $template = $all_form.' <form method="POST" action="?mp=1&uid='.$_GET['uid'].'&token='.$_GET['token'].'" style="padding: 40px 30px; margin: 40px 30px; position: relative; left: 0px; top: 60px;">
			<table>
			<tr>
			<td style="color: white; padding-left: 7px">
			Задайте пароль:
			</td>
			</tr>
			<tr>
			<td>
			<input type="password" name="pass" placeholder="Ваш пароль" class="vtip" title="Пароль должен состоять минимум из 6 символов:<br>содержать хотя бы одно букву латиницей и хотя бы одну цифру.">
				<input type="hidden" name="token" value="' . $_GET['token'] . '"><input type="hidden" name="uid" value="' . $_GET['uid'] . '">
			</td>
			</tr>
			<tr>
			<td>
				<input type="password" name="ver_pass" placeholder="Повторите пароль" class="vtip" title="Пароль должен состоять минимум из 6 символов:<br>содержать хотя бы одно букву латиницей и хотя бы одну цифру.">
			</td>
			</tr>
			<tr>
			<td>
			<input type="submit" value="сохранить" class="button small" style="margin-top: 7px; margin-top: 7px; border-radius: 4px; width: 228px;">
			<input type="hidden" name="token" value="' . $_GET['token'] . '" />
            <input type="hidden" name="uid" value="' . $_GET['uid'] . '" />
            <input type="hidden" name="setnewpass" value="1" />
			</td>
			</tr>
			</table>
			</form>';
        }
        else
        {
            // $template = $all_form;
        }



		/*
		$past = time() - 3600;
		foreach ( $_COOKIE as $key => $value )
		{
		    setcookie( $key, $value, $past, '/' );
		}*/

				$errpass='';
		if (isset($_GET['wrongpass']))
		{
			$errpass= '
		<div class="msgBox error" style="position: absolute; margin-top: 37px; width: 329px; padding: 10px; background: white; border-radius: 13px; text-align: center;">Неверные пара логин/пароль.</div>';
		}
		// echo $new_pass_good;
		if (($new_pass_good==1) && ($_GET['ctoken']!='') && ($_GET['uid']!=''))
		{
			$template='<div class="msgBox confirm" style="position: absolute; width: 310px; margin-top: 176px; margin-left: 11px; background: white; padding: 10px; border-radius: 13px;">Поздравляю ваш пароль успешно изменен! <br><a href="http://production.wobot.ru">Перейти к авторизации</a></div>';
		}
		elseif (($_GET['ctoken']!='') && ($_GET['uid']!=''))
		{
			$template=$all_form.'
			<form method="POST" style="padding: 40px 30px; margin: 40px 30px; position: relative; left: 0px; top: 60px;">
			<table>
			<tr>
			<td style="color: white; padding-left: 7px">
			Изменение пароля:
			</td>
			</tr>
			<tr>
			<td>
				<input type="hidden" name="ctoken" value="'.$_GET['ctoken'].'"><input type="hidden" name="uid" value="'.$_GET['uid'].'"><input type="password" name="pass" placeholder="Новый пароль">
			</td>
			</tr>
			<tr>
			<td>
				<input type="password" name="ver_pass" placeholder="Повторите пароль">
			</td>
			</tr>
			<tr>
			<td>
			<input type="submit" value="изменить" class="button small" style="margin-top: 7px; margin-top: 7px; border-radius: 4px; width: 228px;">
			</td>
			</tr>
			</table>
			</form>';
		}
	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="/css/bootstrap.css" rel="stylesheet">
		<style>
		body { margin: 0; padding: 0 }
		table { margin: 0; padding: 0 }
		td { margin: 0; padding: 0 }
		.login-form { padding: 40px 30px; margin: 40px 30px; background-image: url(\'/img/loginform.png\'); background-position: center; background-repeat: no-repeat; width: 350px; height: 456px; }
		input { background: #eee; border: 0; border: 1px solid #eee; color: #444; font-size: 20px; text-align: center; padding: 5px; margin: 5px; width: 220px; }
		</style>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="js/jquery.placeholder.min.js"></script>
		<script src="js/vtip.js"></script>
		<link href="/css/vtip.css" rel="stylesheet">
		<script type="text/javascript">
			$(document).ready(function(){
				$("input[placeholder], textarea[placeholder]").placeholder();
			});
		</script>
		</head>
		<body style="background: #000000 url(\'/img/login-bg.jpg\') center no-repeat;">
		<center>
		<table height="100%">
		<tr>
		<td>
		<p style="margin: 30px; text-align: center; font-weight: bold;">
		<a href="http://wobot.ru" style="color: #333; text-decoration: none;">← Вернуться на сайт Wobot</a>
		</p>
		</td>
		</tr>
		<td>
		<div class="login-form">
		'.$errpass.'
		'.$template.'
		</div>
		</td>
		</table>
		</center>

		</body>
		</html>
	';

	}
}
auth1();
//session_destroy();
/*$login="mail@wobot.ru";
$password="wobot2011";
<script>
<tr>
<td align="right">
</td>
</tr>
if (document.getElementById(\'user_email\').value==\'\')
document.getElementById(\'user_email\').value=\'логин\';
if (document.getElementById(\'password\').value==\'\')
document.getElementById(\'password\').value=\'пароль\';
</script>
if ((strlen($_POST['login'])>0)&&(md5($_POST['login'].':'.$_POST['password'])==md5($login.':'.$password)))
{
	setcookie("token", md5($_POST['login'].':'.$_POST['password']));
	setcookie("user_id", 61);
	echo 'ok';
}*/


?>
