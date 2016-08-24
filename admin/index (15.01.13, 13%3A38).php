<?
require_once('com/config.php');
require_once('com/db.php');
require_once('com/auth.php');
require_once('bot/kernel.php');

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();

function query($url,$post)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post);

	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_output = curl_exec ($ch);

	curl_close ($ch);

	return $server_output;
}

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

$db = new database();
$db->connect();

auth();
if (!$loged) die();

if ($_POST['action']=='adduser')
{
	//echo intval(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($_POST['user_email'])));
	if ((trim($_POST['user_email'])!='') && (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($_POST['user_email']))))
	{
		if (($_POST['user_email']!='')&&($_POST['user_pass']!='')&&($_POST['user_name']!='')&&($_POST['user_contact']!='')&&($_POST['user_company']!=''))
		{
			$yet=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($_POST['user_email']).'\' LIMIT 1');
			if (mysql_num_rows($yet)==0)
			{
				$db->query('INSERT INTO users (user_email, user_pass, user_name, user_contact, user_company, user_money, user_ctime) values ("'.addslashes($_POST['user_email']).'","'.md5($_POST['user_pass']).'","'.addslashes($_POST['user_name']).'","'.addslashes($_POST['user_contact']).'","'.addslashes($_POST['user_company']).'","'.intval($_POST['user_money']).'", "'.time().'")');
				header('Location: /admin/?user_id='.$db->insert_id().'#mod_user');
				//echo 'Пользователь добавлен<br>';
			}
			else
			{
				//echo 'Такой пользователь уже существует<br>';
			}
		}
		else
		{
			//echo 'Введены не все поля<br>';
		}
	}
	else
	{
		//echo 'Логин для входа может быть только электронной почтой';
	}
}
elseif ($_POST['action']=='edituser')
{
	if ((intval($_POST['user_id'])!=0)&&($_POST['user_email']!='')&&($_POST['user_name']!='')&&($_POST['user_contact']!='')&&($_POST['user_company']!=''))
	{
		if ($_GET['topipe']==1)
		{
			$token='3e36afca3851ac8a611c9f62a34c7ab35b015fbd';
			$owner_id='71072';
			$user_company=$_POST['user_company'];
			$user_fio=$_POST['user_name'];
			$user_email=$_POST['user_email'];
			$user_phone=$_POST['user_contact'];
			$res=json_decode(query('https://api.pipedrive.com/v1/organizations?api_token='.$token,'name='.urlencode($user_company).'&owner_id='.intval($owner_id)),true);
			$org_id=$res['data']['id'];
			//echo "org_id: ".$org_id."\n";

			//curl --data "name=username&owner_id=71072&email=test%40test.ru&phone=%2B79035386138&org_id=5" https://api.pipedrive.com/v1/persons?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
			//{"success":true,"data":{"id":9}}
			$res=json_decode(query('https://api.pipedrive.com/v1/persons?api_token='.$token,'name='.urlencode($user_fio).'&owner_id='.intval($owner_id).'&email='.urlencode($user_email).'&phone='.urlencode($user_phone).'&org_id='.intval($org_id)),true);
			$person_id=$res['data']['id'];
			//echo "person_id: ".$person_id."\n";

			//curl --data "title=ТЕСТКОМПАНИdeal&value=7500&currency=RUB&user_id=71072&person_id=9&org_id=5&visible_to=0" https://api.pipedrive.com/v1/deals?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
			//{"success":true,"data":{"id":5}}
			$res=json_decode(query('https://api.pipedrive.com/v1/deals?api_token='.$token,'title='.urlencode($user_company.' deal').'&value=7500&currency=RUB&user_id='.intval($owner_id).'&person_id='.intval($person_id).'&org_id='.intval($org_id).'&visible_to=0'),true);
			$deal_id=$res['data']['id'];
			//echo "deal_id: ".$deal_id."\n";
		}
		if (strlen($_POST['user_pass'])>0)
		$db->query('UPDATE users set user_email="'.addslashes($_POST['user_email']).'", user_pass="'.md5($_POST['user_pass']).'", user_name="'.addslashes($_POST['user_name']).'", user_contact="'.addslashes($_POST['user_contact']).'", user_company="'.addslashes($_POST['user_company']).'", user_money="'.intval($_POST['user_money']).'" WHERE user_id='.intval($_POST['user_id']));
		else
		$db->query('UPDATE users set user_email="'.addslashes($_POST['user_email']).'", user_name="'.addslashes($_POST['user_name']).'", user_contact="'.addslashes($_POST['user_contact']).'", user_company="'.addslashes($_POST['user_company']).'", user_money="'.intval($_POST['user_money']).'" WHERE user_id='.intval($_POST['user_id']));
		//echo 'Пользователь обновлен<br>';
	}
	else
	{
		//echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='editactive')
{
	//echo 'UPDATE users SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']);
	if ((intval($_POST['us_active'])>=0) && (intval($_GET['user_id'])>0))
	{
		$db->query('UPDATE users SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']));
		//echo 'Статус пользователя изменен';
	}
	else
	{
		//echo 'Статус пользователя не удалось изменить';
	}
	//echo 'UPDATE blog_orders SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']);
}
elseif ($_POST['action']=='addut')
{
	/*
	Array
	(
	    [action] => addut
	    [ut_id] => addut
	    [user_id] => 108
	    [tariff_id] => 4
	)
	*/
	if ((intval($_GET['user_id'])!=0)&&(intval($_POST['tariff_id'])!=0))
	{
		$db->query('INSERT INTO user_tariff (user_id, tariff_id, ut_date) values ('.intval($_GET['user_id']).', '.intval($_POST['tariff_id']).', '.mktime(0,0,0,date('n')+1,date('j'),date('Y')).')');
		//echo 'Тариф добавлен<br>';
	}
	else
	{
		//echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='editut')
{
	/*
	Array
	(
	    [action] => editut
	    [ut_id] => 67
	    [user_id] => 66
	    [tariff_id] => 4
	)
	*/
	if ((intval($_POST['ut_id'])!=0)&&(intval($_POST['user_id'])!=0)&&(intval($_POST['tariff_id'])!=0))
	{
		$db->query('UPDATE user_tariff set user_id='.intval($_POST['user_id']).', tariff_id='.intval($_POST['tariff_id']).', ut_date='.strtotime($_POST['ut_date']).' WHERE ut_id='.intval($_POST['ut_id']));
		if ($_POST['user_active']!=0)
		{
			$db->query('UPDATE users SET user_active=2 WHERE user_id='.intval($_POST['user_id']));
		}
		//echo 'UPDATE user_tariff set user_id='.intval($_POST['user_id']).', tariff_id='.intval($_POST['tariff_id']).', '.time().' WHERE ut_id='.intval($_POST['ut_id']);
		//echo 'Тариф обновлен<br>';
	}
	else
	{
		//echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='deleteut')
{
	/*
	Array
	(
	    [action] => deleteut
	    [ut_id] => 67
	)
	*/
	if ((intval($_POST['ut_id'])!=0))
	{
		//$db->query('DELETE FROM user_tariff WHERE ut_id='.intval($_POST['ut_id']));
		echo 'DELETE FROM user_tariff WHERE ut_id='.intval($_POST['ut_id']);
		$qorders=$db->query('SELECT order_id FROM blog_orders WHERE ut_id='.intval($_POST['ut_id']));
		while ($orders=$db->fetch($qorders))
		{
			//$db->query('DELETE FROM blog_orders WHERE order_id='.intval($orders['order_id']));
			echo 'DELETE FROM blog_orders WHERE order_id='.intval($orders['order_id']);
			//$db->query('DELETE FROM blog_post WHERE order_id='.intval($orders['order_id']));
			echo 'DELETE FROM blog_post WHERE order_id='.intval($orders['order_id']);
			//$db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orders['order_id']));
			echo 'DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orders['order_id']);
		}
		//echo 'Тариф удален<br>';
	}
	else
	{
		//echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='deleteorder')
{
	if (intval($_POST['order_id'])>0)
	{
		//$db->query('DELETE FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
		echo 'DELETE FROM blog_orders WHERE order_id='.intval($_POST['order_id']);
		//$db->query('DELETE FROM blog_post WHERE order_id='.intval($_POST['order_id']));
		echo 'DELETE FROM blog_post WHERE order_id='.intval($_POST['order_id']);
		//$db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($_POST['order_id']));
		echo 'DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($_POST['order_id']);
		//echo 'Тема удалена';
	}
	else
	{
		//echo 'Тема не была удалена';
	}
}
elseif ($_POST['action']=='addorder')
{
	/*
	Array
	(
    [action] => addorder
    [user_id] => 67
    [ut_id] => 3
    [order_id] => addorder
    [order_name] => 
    [order_keyword] => 
    [order_start] => 
    [order_end] => 
    [ful_com] => 1
    [order_engage] => 1
    [order_fb_rt] => 1
	)
	*/
	if ((trim($_POST['order_name'])!='')&&($_POST['order_start']!='')&&($_POST['order_end']!='')&&(intval($_POST['ut_id'])!=0)&&(trim($_POST['order_keyword'])!=''))
	{
		if (strtotime($_POST['order_start'])<=strtotime($_POST['order_end']))
		{
			$db->query('INSERT INTO blog_orders (order_date,user_id, ut_id, order_id, order_name, order_keyword, order_start, order_end, third_sources, ful_com, order_engage, order_fb_rt, order_nastr, youtube_last, order_lang) values ("'.time().'","'.intval($_POST['user_id']).'", "'.intval($_POST['ut_id']).'", "'.intval($_POST['order_id']).'", "'.addslashes($_POST['order_name']).'", "'.addslashes($_POST['order_keyword']).'", "'.strtotime($_POST['order_start']).'", "'.strtotime($_POST['order_end']).'", 1, "'.intval($_POST['ful_com']).'", "'.intval($_POST['order_engage']).'", "'.intval($_POST['order_fb_rt']).'", "'.intval($_POST['order_nastr']).'", "'.intval($_POST['youtube_last']).'", 2)');
			parseUrl('http://188.120.239.225/tools/charge.php?order_id='.$db->insert_id());
		}
		//echo 'INSERT INTO blog_orders (order_date,user_id, ut_id, order_id, order_name, order_keyword, order_start, order_end, third_sources, ful_com, order_engage, order_fb_rt, order_nastr, youtube_last, order_lang) values ("'.time().'","'.intval($_POST['user_id']).'", "'.intval($_POST['ut_id']).'", "'.intval($_POST['order_id']).'", "'.addslashes($_POST['order_name']).'", "'.addslashes($_POST['order_keyword']).'", "'.strtotime($_POST['order_start']).'", "'.strtotime($_POST['order_end']).'", 1, "'.intval($_POST['ful_com']).'", "'.intval($_POST['order_engage']).'", "'.intval($_POST['order_fb_rt']).'", "'.intval($_POST['order_nastr']).'", "'.intval($_POST['youtube_last']).'", 2)';
		//echo 'Тариф добавлен<br>';
	}
	else
	{
		//echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='editorder')
{
	/*
	Array
	(
    [action] => editorder
    [order_id] => 330
    [order_name] => Бауцентр | Бауцентор | Бауцентер
    [order_keyword] => Бауцентр | Бауцентор | Бауцентер
    [order_start] => 28.04.2011
    [order_end] => 19.05.2011
    [ut_id] => 3
	)
	*/
	if ((intval($_POST['order_id'])!=0)&&($_POST['order_name']!='')&&($_POST['order_keyword']!='')&&($_POST['order_start']!='')&&($_POST['order_end']!='')&&(intval($_POST['ut_id'])!=0))
	{
		list($date_d,$date_m,$date_y)=explode('.',$_POST['order_start']);
		$order_start=mktime(0,0,0,intval($date_m),intval($date_d),intval($date_y));
		list($date_d,$date_m,$date_y)=explode('.',$_POST['order_end']);
		$order_end=mktime(0,0,0,intval($date_m),intval($date_d),intval($date_y));		
		
		//echo 'UPDATE blog_orders SET order_name="'.addslashes($_POST['order_name']).'", order_keyword="'.addslashes($_POST['order_keyword']).'", order_start='.$order_start.', order_end='.$order_end.' WHERE order_id='.intval($_POST['order_id']);
		$db->query('UPDATE blog_orders SET order_name="'.addslashes($_POST['order_name']).'", order_keyword="'.addslashes($_POST['order_keyword']).'", order_start='.$order_start.', order_end='.$order_end.', ful_com="'.intval($_POST['ful_com']).'", order_engage="'.intval($_POST['order_engage']).'", order_nastr="'.intval($_POST['order_nastr']).'", youtube_last="'.intval($_POST['youtube_last']).'",order_lang="'.$_POST['ord_lan'].'",ut_id="'.$_POST['ut_id'].'" WHERE order_id='.intval($_POST['order_id']));
		$upd_ord=$db->query('SELECT order_start,order_end FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
		$ordd=$db->fetch($upd_ord);
		if (($_POST['order_start']!=$ordd['order_start']) || ($_POST['order_end']!=$ordd['order_end']))
		{
			$cont=parseUrl('http://188.120.239.225/tools/cashjob.php?order_id='.intval($_POST['order_id']));
		}
		//echo 'Тариф обновлен<br>';
	}
	else
	{
		//echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='editactive')
{
	//echo 'UPDATE users SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']);
	if ((intval($_POST['us_active'])>=0) && (intval($_GET['user_id'])>0))
	{
		$db->query('UPDATE users SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']));
		//echo 'Статус пользователя изменен';
	}
	else
	{
		//echo 'Статус пользователя не удалось изменить';
	}
	//echo 'UPDATE blog_orders SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']);
}
elseif ($_POST['action']=='recoverorder')
{
	//print_r($_POST);
	if ((intval($_POST['user_id'])>0) && (intval($_POST['order_id'])>0))
	{
		$db->query('UPDATE blog_orders SET user_id='.intval($_POST['user_id']).' WHERE order_id='.intval($_POST['order_id']));
		parseUrl('http://188.120.239.225/tools/charge.php?order_id='.intval($_POST['order_id']));
		//echo 'Выбранная вами тема успешно разблокирована';
	}
	else
	{
		//echo 'Выбранную вами тему разблокировать не удалось';
	}
	//echo 'UPDATE blog_orders SET user_id='.intval($_POST['user_id']).' WHERE order_id='.intval($_POST['order_id']);
	
}
elseif ($_POST['action']=='blockorder')
{
	if (intval($_POST['order_id'])>0)
	{
		$db->query('UPDATE blog_orders SET user_id=0 WHERE order_id='.intval($_POST['order_id']));
		//echo 'Выбранная вами тема успешно заблокирована';
	}
	else
	{
		//echo 'Выбранную вами тему заблокировать не удалось';
	}
	//echo 'UPDATE blog_orders SET user_id=0 WHERE order_id='.intval($_POST['order_id']);
}
elseif ($_POST['action']=='refreshcash')
{
	//print_r($_POST);
	$cont=parseUrl('http://188.120.239.225/tools/cashjob.php?order_id='.intval($_POST['order_id']));
	//echo 'http://bmstu.wobot.ru/tools/cashjob.php?order_id='.intval($_POST['order_id']);
	$mas=json_decode($cont,true);
	if ($mas['status']=='ok')
	{
		//echo 'Обновление кеша запущено';
	}
	else
	{
		//echo 'Обновление кеша не удалось запустить';
	}
}
elseif ($_POST['action']=='refreshpost')
{
	//print_r($_POST);
	$qorid=$db->query('UPDATE blog_orders SET third_sources=1 WHERE order_id='.$_POST['order_id']); 
	//echo 'UPDATE blog_orders SET third_sourcess=1 WHERE order_id='.$_POST['order_id'];
	//echo 'Обновление запроса запущено';
}
elseif (($_POST['action']=='deleteuser') || (intval($_GET['delete_user_id'])!=0))
{
	//print_r($_POST);
	if ($_POST['action']=='deleteuser')
	{
		$qorid=$db->query('SELECT order_id FROM blog_orders as a LEFT JOIN user_tariff as b ON a.ut_id=b.ut_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_POST['user_id']));
		while ($orid=$db->fetch($qorid))
		{
			//$db->query('DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']));
			echo 'DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']);
			//$db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orid['order_id']));
			echo 'DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orid['order_id']);
		}
		//$db->query('DELETE FROM blog_orders WHERE user_id='.intval($_POST['user_id']));
		echo 'DELETE FROM blog_orders WHERE user_id='.intval($_POST['user_id']);
		//$db->query('DELETE FROM users WHERE user_id='.intval($_POST['user_id']));
		echo 'DELETE FROM users WHERE user_id='.intval($_POST['user_id']);
		//$db->query('DELETE FROM user_tariff WHERE user_id='.intval($_POST['user_id']));
		echo 'DELETE FROM user_tariff WHERE user_id='.intval($_POST['user_id']);
	}
	elseif (intval($_GET['delete_user_id'])!=0)
	{
		$qorid=$db->query('SELECT order_id FROM blog_orders as a LEFT JOIN user_tariff as b ON a.ut_id=b.ut_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_GET['delete_user_id']));
		while ($orid=$db->fetch($qorid))
		{
		//	$db->query('DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']));
		echo 'DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']);
		//	$db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orid['order_id']));
		echo 'DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orid['order_id']);
		}
		//$db->query('DELETE FROM blog_orders WHERE user_id='.intval($_GET['delete_user_id']));
		echo 'DELETE FROM blog_orders WHERE user_id='.intval($_GET['delete_user_id']);
		//$db->query('DELETE FROM users WHERE user_id='.intval($_GET['delete_user_id']));
		echo 'DELETE FROM users WHERE user_id='.intval($_GET['delete_user_id']);
		//$db->query('DELETE FROM user_tariff WHERE user_id='.intval($_GET['delete_user_id']));
		echo 'DELETE FROM user_tariff WHERE user_id='.intval($_GET['delete_user_id']);
	}
}
elseif ($_POST['action']=='addtariff')
{
	if (($_POST['tariff_name']!='')&&($_POST['tariff_desc']!='')&&($_POST['tariff_price']!='')&&($_POST['tariff_quot']!=''))
	{
		$db->query('INSERT INTO blog_tariff (tariff_name,tariff_desc,tariff_price,tariff_quot) VALUES (\''.addslashes($_POST['tariff_name']).'\',\''.addslashes($_POST['tariff_desc']).'\','.$_POST['tariff_price'].','.$_POST['tariff_quot'].')');
	}
}
elseif ($_POST['action']=='chtariff')
{
	if (($_POST['tariff_name']!='')&&($_POST['tariff_desc']!='')&&($_POST['tariff_price']!='')&&($_POST['tariff_quot']!=''))
	{
		$db->query('UPDATE blog_tariff SET tariff_name=\''.addslashes($_POST['tariff_name']).'\',tariff_desc=\''.addslashes($_POST['tariff_desc']).'\',tariff_price=\''.$_POST['tariff_price'].'\',tariff_quot=\''.$_POST['tariff_quot'].'\' WHERE tariff_id='.intval($_POST['tariff_id']));
	}
}
elseif ($_POST['action']=='deletetariff')
{
	//$db->query('DELETE FROM blog_tariff WHERE tariff_id='.intval($_POST['tariff_id']));
	echo 'DELETE FROM blog_tariff WHERE tariff_id='.intval($_POST['tariff_id']);
}
elseif ($_POST['action']=='addnewsrc')
{
	$msrc=explode(',',$_POST['srcs']);
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
		if (intval(validateURL(preg_replace('/[а-яё]/isu','w',$item)))==0)
		{
			continue;
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
			//echo 'INSERT INTO user_src (user_id,hn,fhn) VALUES (0,\''.$hn.'\',\''.$url.'\')';
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
}
elseif ($_GET['toPD']!='')
{
	$qustopd=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['toPD']).' LIMIT 1');
	$ustopd=$db->fetch($qustopd);
	$token='3e36afca3851ac8a611c9f62a34c7ab35b015fbd';
	$owner_id='71072';
	$user_company=$ustopd['user_company'];
	$user_fio=$ustopd['user_name'];
	$user_email=$ustopd['user_email'];
	$user_phone=$ustopd['user_contact'];
	$res=json_decode(query('https://api.pipedrive.com/v1/organizations?api_token='.$token,'name='.urlencode($user_company).'&owner_id='.intval($owner_id)),true);
	$org_id=$res['data']['id'];
	//echo "org_id: ".$org_id."\n";

	//curl --data "name=username&owner_id=71072&email=test%40test.ru&phone=%2B79035386138&org_id=5" https://api.pipedrive.com/v1/persons?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
	//{"success":true,"data":{"id":9}}
	$res=json_decode(query('https://api.pipedrive.com/v1/persons?api_token='.$token,'name='.urlencode($user_fio).'&owner_id='.intval($owner_id).'&email='.urlencode($user_email).'&phone='.urlencode($user_phone).'&org_id='.intval($org_id)),true);
	$person_id=$res['data']['id'];
	//echo "person_id: ".$person_id."\n";

	//curl --data "title=ТЕСТКОМПАНИdeal&value=7500&currency=RUB&user_id=71072&person_id=9&org_id=5&visible_to=0" https://api.pipedrive.com/v1/deals?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
	//{"success":true,"data":{"id":5}}
	$res=json_decode(query('https://api.pipedrive.com/v1/deals?api_token='.$token,'title='.urlencode($user_company.' deal').'&value=7500&currency=RUB&user_id='.intval($owner_id).'&person_id='.intval($person_id).'&org_id='.intval($org_id).'&visible_to=0'),true);
	$deal_id=$res['data']['id'];
	//echo "deal_id: ".$deal_id."\n";
}

$res=$db->query('SELECT * FROM blog_tariff');
while($tariffs[] = $db->fetch($res)) {};
$quser=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['user_id']).' LIMIT 1');
$user=$db->fetch($quser);
$res=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as t ON ut.tariff_id=t.tariff_id WHERE ut.user_id='.intval($_GET['user_id']));
while($uts[] = $db->fetch($res)) {};

echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>WOBOT &copy; Панель администратора (&beta;-version)</title>
    <meta name="description" content="" />
	<meta name="keywords" content="Wobot реклама анализ раскрутка баннер" />
	<meta name="author" content="Wobot media" />
	<meta name="robots" content="all" />

    <!-- Le styles -->
    <link type="text/css" href="css/bootstrap.css" rel="stylesheet" />
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link type="text/css" href="css/bootstrap-responsive.css" rel="stylesheet" />
    <link type="text/css" href="css/additional_styles.css" rel="stylesheet" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- wobot scripts and css -->
    
    <script type="text/javascript" src="http://188.120.239.225/js/jQuery.js"></script>
	<script type="text/javascript" src="http://188.120.239.225/js/jquery.flot.js"></script>
	<script type="text/javascript" src="http://188.120.239.225/js/jquery.cookie.js"></script>

	<script type="text/javascript" src="http://188.120.239.225/js/jquery.flot.selection.js"></script>
	<script type="text/javascript" src="http://188.120.239.225/js/jquery.fancybox-1.3.0.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="http://188.120.239.225/css/jquery.fancybox-1.3.0.css" media="screen" />
	<link type="text/css" href="http://188.120.239.225/css/smoothness/jquery-ui-1.8.9.custom.css" rel="stylesheet" /> 
	<script type="text/javascript" src="http://188.120.239.225/js/jquery-ui-1.8.9.custom.min.js"></script> 
	<script type="text/javascript" src="http://188.120.239.225/js/jquery.blockUI.js"></script>

	<script type="text/javascript" src="http://188.120.239.225/js/jquery-ui.js"></script>
	<script type="text/javascript" src="http://188.120.239.225/js/jquery.pagination.js"></script>
	<script src="http://188.120.239.225/js/jquery.tipsy.js" type="text/javascript"></script>
	<script type="text/javascript" src="http://188.120.239.225/js/jquery.ui.datepicker-ru.js"></script>
    
    <!--bootstrap scripts and css -->

    <script src="http://code.jquery.com/jquery-latest.js"></script> <!-- убрать или заменить на файл с jQuery-->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){ 
            //установка ширины и высоты textarea в таблице тарифов
                $(".tariff tr td input[type=text]").each(function(){
                    $(this).addClass("input-medium").css("height","56");
                });
                $(".tariff tr td input[name=tariff_price]").each(function(){
                    $(this).css("width","80");
                });
                $(".tariff tr td input[name=tariff_quot]").each(function(){
                    $(this).css("width","80");
                });
                $(".tariff tr td input[type=submit]").each(function(){
                    $(this).addClass("btn").css("width","140");
                });
                /*$(".damnedp tbody tr td p").each(function(){
                    $(this).removeAttr("style");
                });*/
                //панель вкладок
                $(\'#myTab a:first\').tab(\'show\'); 
                $(\'#myTab a\').click(function (e) {
                    e.preventDefault();
                    $(this).tab(\'show\');
                });
                //выбор вкладки, если в хеше содержится id вкладки, при входе на страницу (обновлении)
                function selectTabOnReady(){
                    var arr=["#demo", "#new_user", "#mod_user", "#last_reg", "#client_list", "#new_res", "#special_ord", "#tariffs"];
                    if(arr.some(function(hash){return hash==location.hash;})){
                        $("#myTab a[href="+location.hash+"]").tab("show");
                    }
                }
                selectTabOnReady();
                //выбор вкладки при изменением хеша в URL
                $(window).bind(\'hashchange\', function() {
                    var arr=["#demo", "#new_user", "#mod_user" ,"#last_reg", "#client_list", "#new_res", "#special_ord", "#tariffs"];
                    if(arr.some(function(hash){return hash==location.hash;})){
                        $("#myTab a[href="+location.hash+"]").tab("show");
                    }
                });
                
                //панель вкладок (алфавит) в списке клиентов
                $(\'#alf_tabs a:first\').tab(\'show\'); 
                $(\'#alf_tabs a\').click(function (e) {
                    e.preventDefault();
                    $(this).tab(\'show\');
                });
                
                //показать окно дебага
                $(\'#debug\').click(function(){
                    $("#debug_textarea").show();
                    $(window).scrollTop($(\'#debug_textarea\').position().top);
                });
                //скрыть окно дебага
                $(\'#from_debug\').click(function(){
                    $("#debug_textarea").hide();
                    $(window).scrollTop("0px");
                });
                //изменение порядка сортировки
                $("#sort_date, #sort_alf").click(function(){
                    if($("i", this).attr("class")=="icon-arrow-up"){
						if ($(this).attr("id")=="sort_date")
						{
							window.location = "http://188.120.239.225/admin/?user_id='.$_GET['user_id'].'&sort=ctime&type=asc#client_list";
						}
						else
						{
							window.location = "http://188.120.239.225/admin/?user_id='.$_GET['user_id'].'&sort=alph&type=asc#client_list";
						}
                        $("i", this).removeClass("icon-arrow-up").addClass("icon-arrow-down");
                    }
                    else{
						if ($(this).attr("id")=="sort_date")
						{
							window.location = "http://188.120.239.225/admin/?user_id='.$_GET['user_id'].'&sort=ctime&type=desc#client_list";
						}
						else
						{
							window.location = "http://188.120.239.225/admin/?user_id='.$_GET['user_id'].'&sort=alph&type=desc#client_list";
						}
                        $("i", this).removeClass("icon-arrow-down").addClass("icon-arrow-up");
                    }
                });
	     });
    </script>
  </head>

  <body>
    <!--Верхняя панель -->
    <div class="navbar navbar-inverse navbar-fixed-top"> 
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <!--<a class="brand" href="#">Wobot Admin</a>-->
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="/admin/"><img src="http://wobot.ru/img/map-marker.png" title="logo" alt="logo" border="0" width="30" height="30" /></a></li>
              <li><a target="_blank" href="http://188.120.239.225/new/adminfaq" target="_blank">FAQ</a></li>
              <li><a target="_blank" href="http://wobot.ru/awstats/" target="_blank">Статистика wobot.ru</a></li>
              <li><a target="_blank" href="http://188.120.239.225/tools/regi.php?hero=1" target="_blank">Статистика по демо кабинетам</a></li>
              <li><a target="_blank" href="http://bmstu.wobot.ru/social/export/" target="_blank">Выгрузка из групп</a></li>
              <!--<li><a href="https://www.facebook.com/dialog/oauth?client_id=158565747504200&redirect_uri=http%3A%2F%2F188.120.239.225%2Fnew%2F&state=e544c75112dc0e5eacd455208bf4a6e6">Зайти Facebook</a></li>-->
              <!--<li><a href="http://188.120.239.225/tools/editmsg/editmsg.php">Отредактировать отправляемые на почту сообщения</a></li>-->
              <li><a target="_blank" href="http://188.120.239.225/tools/editmsg/subscr_send.php">Рассылка</a></li>
              <li><a target="_blank" href="http://188.120.239.225/tools/promo.php">PROMO</a></li>
              <li><a id="debug">Отладка</a></li>
              <li><a target="_blank" href="http://ec2-54-247-33-208.eu-west-1.compute.amazonaws.com/gpview/" target="_blank">GRAPH</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div> <!-- Верхняя панель -->

    <div class="container main_container">
      <div class="row tab_row">
        <span class="span12">
            <ul class="nav nav-tabs main_tabs" id="myTab"> <!--вкладки-->
                <li class="active"><a href="#demo">Последние кабинеты</a></li>
                <li><a href="#new_user">Добавление пользователей</a></li>
                <li><a href="#mod_user">Редактир. пользователей</a></li>
                <!--<li><a href="#last_reg">Демо-кабинеты</a></li>-->
                <li><a href="#client_list">Список клиентов</a></li>
                <li><a href="#new_res">Добавление ресурсов</a></li>
                <li><a href="#special_ord">Специальные предложения</a></li>
                <li><a href="#tariffs">Тарифы</a></li> <!--вкладки-->
            </ul>
            <div class="tab-content"> <!--содержимое вкладок-->
                <div class="row tab-pane" id="new_res"> <!--добавление ресурсов-->
                    <div class="inner-tab-container">
                        <span style="margin-left: 30px; margin-top: 20px;">Добавление новых ресурсов(ресурсы вводим через запятую пример:<b>http://www.yandex.ru,http://www.google.com</b></span>
                        <form method="post" style="margin-top: 15px" action="?'.($_GET['user_id']!=''?'user_id='.$_GET['user_id']:'').'#new_res"><input type="hidden" name="action" value="addnewsrc" />
                            <textarea style="width: 90%; height: 50px; margin-left: 20px;" name="srcs"></textarea>
                            <div style="text-align: right;">
                                <input class="btn" style="margin-right: 100px;" type="submit" value="Добавить" />
                            </div>
                        </form>
                        <span style="margin-left: 30px;">Добавленные ресурсы:</span>
                        <div style=" width: 90%; height: 500px; overflow-y: scroll; background-color: #FFFFFF; margin-left: 20px; margin-top: 10px;">
                            <div style="padding-left: 40px; padding-top: 20px;">
                                <table class="table table-striped add_res">
								';
								$srclog=$db->query('SELECT * FROM user_src WHERE user_id=0 ORDER BY id DESC');
								$src_log_i=0;
								while ($log_src=$db->fetch($srclog))
								{
									echo '<tr><td>'.($src_log_i+1).'.</td><td>'.$log_src['hn'].'</td><td>'.($log_src['update']==0?'Собирается':(($log_src['update']==2)?'Уже существует':($log_src['count']==0?'Нельзя добавить':'Добавлен'))).'</td></tr>';
									$src_log_i++;
								}
								echo '
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!--добавление ресурсов-->
                
                <div class="row tab-pane" id="new_user"> <!--добавление пользователя-->
                    <div class="inner-tab-container">
                    	<span style="padding-left: 30px;"><b>Добавление пользователя:</b></span>
                    	<form method="post" style="padding-left: 20px; padding-top: 10px;">
                    	<input type="hidden" name="action" value="adduser"/>
                    	<table>
                        	<tr>
                        	   <td>E-mail*: </td><td><input type="text" name="user_email"/></td>
                        	</tr>
                        	<tr>
                        	   <td>Пароль*: </td><td><input type="text" name="user_pass"/></td>
                        	</tr>
                        	<tr>
                        	   <td>Контактное лицо*: </td><td><input type="text" name="user_name"/></td>
                        	</tr>
                        	<tr>
                        	   <td>Номер телефона*: </td><td><input type="text" name="user_contact"/></td>
                        	</tr>
                        	<tr>
                        	   <td>Название компании*: </td><td><input type="text" name="user_company"/></td>
                        	</tr>
                        	<tr>
                        	   <td>Баланс счета: </td><td><input type="text" name="user_money"/></td>
                        	</tr>
                    	</table>
                    	<input style="margin-left: 240px;" class="btn" type="submit" value="Добавить"/>
                    	</form>
                    </div>
                </div> <!--добавление пользователя-->
                
                <div class="row tab-pane" id="tariffs"> <!--Тарифы-->
                    <div class="inner-tab-container">			
            			<table class="table-condensed tariff">
                			<tr>
                			     <td>Название тарифа</td><td>Описание</td><td>Цена</td><td>Ограничение</td><td>Действия</td>
                			</tr>';
							foreach ($tariffs as $key => $item)
							{
								if ($item['tariff_name']=='') continue;
								echo '
								<tr>
	                				<form method="post" action="?#tariffs">
	                				<input type="hidden" name="action" value="chtariff" />
									<input type="hidden" name="tariff_id" value="'.$item['tariff_id'].'">
	                				<td><input type="text" name="tariff_name" value="'.$item['tariff_name'].'"/></td>
	                				<td><textarea name="tariff_desc">'.$item['tariff_desc'].'</textarea></td>
	                				<td><input type="text" name="tariff_price" value="'.$item['tariff_price'].'"/></td>
	                				<td><input type="text" name="tariff_quot" value="'.$item['tariff_quot'].'"/></td>
	                				<td>
	                                <input type="submit" value="применить" class="btn" />
	                				</form>
	                				<form style="display: inline;" method="post" action="" onsubmit="return confirm(\'Удалить тариф?\');">		
	                				<input type="hidden" name="action" value="deletetariff"/>
	                				<input type="hidden" name="tariff_id" value="'.$item['tariff_id'].'"/>
	                				<input type="submit" value="удалить" class="btn"/></td>
	                				</form>
	            				</tr>';
							}
							echo '
            				<tr>
            				<form method="post" action="?#tariffs">
            				<input type="hidden" name="action" value="addtariff">
            				<td><input type="text" name="tariff_name" value=""></td>
            				<td><textarea name="tariff_desc"></textarea></td>
            				<td><input type="text" name="tariff_price" value=""></td>
            				<td><input type="text" name="tariff_quot" value=""></td>
            				<td><input type="submit" value="добавить"></td>
            				</form>
            				</tr>
            			</table>
                    </div>
                </div> <!--Тарифы-->
                
            <div class="row tab-pane" id="special_ord"> <!--Специальные предложения-->
                <div class="inner-tab-container">
                    <div>
                        <h5>Оплаты спецпредложений http://bit.ly/wobotpay750</h5>
                    </div>
                    <div> 
	                   <table class="table table-striped damnedp" style="width: 840px;">
                            <tr class="dark_head"> <!-- в таблице не нужны теги р, нужно как в первых 2х строках просто текст -->
                                <td>№</td>
                                <td>Время</td>
                                <td>Статус</td>
                                <td>Сумма</td>
                            </tr>';
							$qbilling=$db->query('SELECT * FROM billing WHERE user_id=0 and status>0');
							$bil_id=1;
							$status_bil[-1]='Ошибка платежа';
							$status_bil[0]='Выставлен счет';
							$status_bil[1]='Ошибка платежа';
							$status_bil[2]='Проведен';
							while ($bil=$db->fetch($qbilling))
							{
								echo '<tr><td>905'.$bil['bill_id'].'</td><td>'.date('d.m.y H:i:s',$bil['date']).'</td><td>'.$status_bil[$bil['status']].'</td><td>'.intval($bil['money']).'</td></tr>';
								$bil_id++;
							}
							echo '
                            </table>
                    </div>
                </div>
            </div><!--Специальные предложения-->
            
            <div class="row tab-pane" id="last_reg"> <!-- последние регистрации -->
            <div class="inner-tab-container">
                <table class="table table-striped centred_icons">
                <tr class="dark_head">
                    <td>Статус</td>
                    <td >Почта</td> 
                    <td>Телефон</td>
                    <td>Контактное лицо</td>
                    <td>Компания</td>
                    <td>Дата регистрации</td>
                    <td>Дата окончания</td>
                    <td>Запрос</td>
                    <td ></td>
                    <td></td>
                </tr>
                <tr>
                    <td><i class="icon-time" title="Не подтвержден"></td>
                    <td>Почтапочтка@почта.пч</td>
                    <td>(495)555-5555</td>
                    <td>Вася Пупкин Весильевич</td>
                    <td>Компания Васи</td>
                    <td>18.18.18 18.18.18</td>
                    <td>18.18.18 18.18.18</td>
                    <td>Очень очень очень очень длинный запрос</td>
                    <td><a href="?user_id=521"><i class="icon-pencil" title="редактировать"></i></a></td>
                    <td><a href="?user_id=521"><i class="icon-remove" title="Удалить"></i></a></td>
                </tr>
                <tr>
                    <td><i class="icon-ok" title="Подтвержден"></td>
                    <td>Почтапочтка@почта.пч</td>
                    <td>(495)555-5555</td>
                    <td>Вася Пупкин</td>
                    <td>Компания Васи</td>
                    <td>18.18.18</td>
                    <td>18.18.18 18.18.18</td>
                    <td>Очень очень очень очень длинный запрос</td>
                    <td><a href="?user_id=521"><i class="icon-pencil" title="редактировать"></i></a></td>
                    <td><a href="?user_id=521"><i class="icon-remove" title="Удалить"></i></a></td>
                </tr>
                <tr>
                    <td><i class="icon-warning-sign" title="Просрочен"></td>
                    <td>Почтапочтка@почта.пч</td>
                    <td>(495)555-5555</td>
                    <td>Вася Пупкин</td>
                    <td>Компания Васи</td>
                    <td>18.18.18</td>
                    <td>18.18.18 18.18.18</td>
                    <td>Очень очень очень очень длинный запрос</td>
                    <td><a href="?user_id=521"><i class="icon-pencil" title="редактировать"></i></a></td>
                    <td><a href="?user_id=521"><i class="icon-remove" title="Удалить"></i></a></td>
                </tr>
                <tr>
                    <td><i class="icon-ok" title="Подтвержден"></td>
                    <td>Почтапочтка@почта.пч</td>
                    <td>(495)555-5555</td>
                    <td>Вася Пупкин</td>
                    <td>Компания Васи</td>
                    <td>18.18.18</td>
                    <td>18.18.18 18.18.18</td>
                    <td>Очень очень очень очень длинный запрос</td>
                    <td><a href="?user_id=521"><i class="icon-pencil" title="редактировать"></i></a></td>
                    <td><a href="?user_id=521"><i class="icon-remove" title="Удалить"></i></a></td>
                </tr>
            </table>
            </div>
        </div>
        <div class="row tab-pane active" id="demo"> <!--Демо кабинеты -->
            <div class="inner-tab-container">
                <table class="table table-striped centred_icons">
                <tr class="dark_head">
                    <td>Статус</td>
                    <td >Почта</td>
                    <td>Телефон</td>
                    <td>Контактное лицо</td>
                    <td>Компания</td>
                    <td>Дата регистрации</td>
                    <td>Дата окончания</td>
                    <td>Запрос</td>
                    <td>Промо-код</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>';
				$res=$db->query('SELECT a.order_id,a.user_id,a.order_name,a.order_keyword,b.user_active,b.user_email,b.user_name,b.user_ctime,b.user_company,b.user_contact,b.user_promo FROM blog_orders AS a LEFT JOIN users AS b ON a.user_id = b.user_id WHERE b.user_active>0');
				while ($statuser=$db->fetch($res))
				{
					if ($statuser['order_id']>intval($stus[$statuser['user_id']]['order_id']))
					{
						$stus[$statuser['user_id']]['order_id']=$statuser['order_id'];
						$stus[$statuser['user_id']]['user_id']=$statuser['user_id'];
						$stus[$statuser['user_id']]['order_name']=$statuser['order_name'];
						$stus[$statuser['user_id']]['order_keyword']=$statuser['order_keyword'];
						$stus[$statuser['user_id']]['user_active']=$statuser['user_active'];
						$stus[$statuser['user_id']]['user_email']=$statuser['user_email'];
						$stus[$statuser['user_id']]['user_name']=$statuser['user_name'];
						$stus[$statuser['user_id']]['user_ctime']=$statuser['user_ctime'];
						$stus[$statuser['user_id']]['user_company']=$statuser['user_company'];
						$stus[$statuser['user_id']]['user_contact']=$statuser['user_contact'];
						$stus[$statuser['user_id']]['user_promo']=$statuser['user_promo'];
					}
				}
				$res=$db->query('SELECT * FROM users WHERE user_active>0 ORDER BY user_id DESC');
				while ($row=$db->fetch($res))
				{
					if (!isset($stus[$row['user_id']]))
					{
						$stus[$row['user_id']]['order_id']=0;
						$stus[$row['user_id']]['user_id']=$row['user_id'];
						$stus[$row['user_id']]['user_active']=$row['user_active'];
						$stus[$row['user_id']]['user_email']=$row['user_email'];
						$stus[$row['user_id']]['user_name']=$row['user_name'];
						$stus[$row['user_id']]['user_ctime']=$row['user_ctime'];
						$stus[$row['user_id']]['user_company']=$row['user_company'];
						$stus[$row['user_id']]['user_contact']=$row['user_contact'];
						$stus[$row['user_id']]['user_promo']=$row['user_promo'];
					}
				}
				krsort($stus);
				$status=array(1=>'не подтвержден',2=>'подтвержден',3=>'заблокирован');
				$status_ic=array(1=>'time',2=>'ok',3=>'warning-sign');
				foreach ($stus as $key => $item)
				{
					echo '
	                    <tr>
		                    <td><i class="icon-'.$status_ic[$item['user_active']].'" title="'.$status[$item['user_active']].'"></td>
		                    <td>'.(mb_strlen($item['user_email'],'UTF-8')>20?mb_substr($item['user_email'],0,20,'UTF-8'):$item['user_email']).'</td>
		                    <td>'.htmlspecialchars($item['user_contact']).'</td>
		                    <td>'.htmlspecialchars(stripslashes(mb_strlen($item['user_name'],'UTF-8')>15?mb_substr($item['user_name'],0,15,'UTF-8'):$item['user_name'])).'</td>
		                    <td>'.htmlspecialchars(stripslashes(mb_strlen($item['user_company'],'UTF-8')>15?mb_substr($item['user_company'],0,15,'UTF-8'):$item['user_company'])).'</td>
		                    <td>'.($item['user_ctime']==0?'-':date('j.n.Y',$item['user_ctime']-86400*30)).'</td>
		                    <td>'.($item['user_ctime']==0?'-':date('j.n.Y',$item['user_ctime'])).'</td>
		                    <td>'.htmlspecialchars(stripslashes(mb_strlen($item['order_keyword'],'UTF-8')>15?mb_substr($item['order_keyword'],0,15,'UTF-8'):$item['order_keyword'])).'</td>
		                    <td>'.htmlspecialchars(stripslashes(mb_strlen($item['user_promo'],'UTF-8')>10?mb_substr($item['user_promo'],0,10,'UTF-8'):$item['user_promo'])).'</td>
		                    <td><a href="?toPD='.$key.'"><i class="icon-briefcase" title="toPD"></i></a></td>
							<td><a href="?user_id='.$key.'#mod_user"><i class="icon-pencil" title="редактировать"></i></a></td>
		                    <td><a href="?delete_user_id='.$key.'#mod_user" onclick="return confirm(\'Удалить пользователя?\');"><i class="icon-remove" title="Удалить"></i></a></td>
		                </tr>';					
				}

				echo '
            </table>
            </div>
        </div><!--Демо кабинеты -->
                <div class="row tab-pane" id="client_list"> <!--Список клиентов -->
                    <div class="inner-tab-container">
                        <div class="span11"> <!--панель поиска -->
                                <form class="navbar-search pull-right" action="?user_id='.$_GET['user_id'].'#client_list"> 
                                    <input type="text" class="search-query" name="search_user" value="'.$_GET['search_user'].'" placeholder="Поиск"/>
                                </form> <!--панель поиска -->
                        </div>
                        <ul class="nav nav-tabs" id="alf_tabs"> <!--алфавитные вкладки -->
							';
							$qusers=$db->query('SELECT * FROM users '.($_GET['search_user']!=''?'WHERE user_email LIKE \'%'.$_GET['search_user'].'%\'':'').' ORDER BY user_email');
							$i=0;
							while ($users=$db->fetch($qusers))
							{
								$mus[mb_strtolower($users['user_email'][0])]['email'][]=$users['user_email'];
								$mus[mb_strtolower($users['user_email'][0])]['id'][]=$users['user_id'];
								$allus['email'][]=$users['user_email'];
								$allus['id'][]=$users['user_id'];
							}
							echo '<li><a href="#all">Все кабинеты</a></li>';
							foreach ($mus as $key => $item)
							{
								echo '<li><a href="#leter'.$key.'">'.$key.'</a></li>';
							}
							echo '
                            <!--<li><a href="#leterA">A</a></li>
                            <li><a href="#leterB">B</a></li>
                            <li><a href="#leterC">C</a></li>
                            <li><a href="#leterD">D</a></li>
                            <li><a href="#leterE">E</a></li>
                            <li><a href="#leterF">F</a></li>
                            <li><a href="#leterG">G</a></li>
                            <li><a href="#leterH">H</a></li>
                            <li><a href="#leterI">I</a></li>
                            <li><a href="#leterJ">J</a></li>
                            <li><a href="#leterK">K</a></li>
                            <li><a href="#leterL">L</a></li>
                            <li><a href="#leterM">M</a></li>
                            <li><a href="#leterN">N</a></li>
                            <li><a href="#leterO">O</a></li>
                            <li><a href="#leterP">P</a></li>
                            <li><a href="#leterQ">Q</a></li>
                            <li><a href="#leterR">R</a></li>
                            <li><a href="#leterS">S</a></li>
                            <li><a href="#leterT">T</a></li>
                            <li><a href="#leterU">U</a></li>
                            <li><a href="#leterV">V</a></li>
                            <li><a href="#leterW">W</a></li>
                            <li><a href="#leterX">X</a></li>
                            <li><a href="#leterY">Y</a></li>
                            <li><a href="#leterZ">Z</a></li>
                            <li><a href="#leter0">Z</a></li>-->
                        </ul> <!--алфавитные вкладки -->
                        <div class="tab-content"> <!--содержимое вкладок алфавит-->
                            <div class="tab-pane active" id="all">
                                <div class="alfabet_cont">
                                    <button class="btn" id="sort_date">Дата&nbsp;<i class="icon-arrow-'.($_GET['type']=='asc'&&$_GET['sort']=='ctime'?'down':'up').'"></i></button>
                                    <button class="btn" id="sort_alf">По алфавиту&nbsp;<i class="icon-arrow-'.($_GET['type']=='asc'&&$_GET['sort']=='alph'?'down':'up').'"></i></button>
									<table class="table table-striped alfa_table">
									';
									if (($_GET['sort']=='alph') && ($_GET['type']=='desc'))
									{
										array_multisort($allus['email'],SORT_ASC,$allus['id'],SORT_ASC);
									}
									if (($_GET['sort']=='alph') && ($_GET['type']=='asc'))
									{
										array_multisort($allus['email'],SORT_DESC,$allus['id'],SORT_DESC);
									}
									if (($_GET['sort']=='ctime') && ($_GET['type']=='desc'))
									{
										array_multisort($allus['id'],SORT_ASC,$allus['email'],SORT_ASC);
									}
									if (($_GET['sort']=='ctime') && ($_GET['type']=='asc'))
									{
										array_multisort($allus['id'],SORT_DESC,$allus['email'],SORT_DESC);
									}
									foreach ($allus['email'] as $key => $item)
									{
										echo '<tr><td >'.$allus['email'][$key].'</td><td style="width: 50px;"><a href="?user_id='.$allus['id'][$key].'#mod_user"><i class="icon-pencil" title="редактировать"></a></td><td style="width: 50px;"><a href="?delete_user_id='.$allus['id'][$key].'#all" onclick="return confirm(\'Удалить пользователя?\');"><i class="icon-remove" title="Удалить"></i></a></td></td>';
									}
									echo '
									</table>
                                </div>
                            </div>
							';

							foreach ($mus as $key => $item)
							{
								echo '<div class="tab-pane" id="leter'.$key.'">
								<div class="alfabet_cont">
								<h1 id="del_'.$key.'">'.$key.'</h1>
								<table class="table table-striped alfa_table">';
								foreach ($mus[$key]['email'] as $k => $i)
								{
									echo '
									<tr>
                                        <td >'.$mus[$key]['email'][$k].'</td><td style="width: 50px;"><a href="?user_id='.$mus[$key]['id'][$k].'#mod_user"><i class="icon-pencil" title="редактировать"></a></td><td style="width: 50px;"><a href="?delete_user_id='.$mus[$key]['email'][$k].'#del_'.$key.'" onclick="return confirm(\'Удалить пользователя?\');"><i class="icon-remove" title="Удалить"></i></a></td>
                                    </tr>';
								}
								echo '</table>
								</div>
								</div>';
							}
							echo '
                            <!--<div class="tab-pane" id="leterA">
                                <div class="alfabet_cont">
                                        <h1 id="del_a">A</h1>
                                        <table class="table table-striped alfa_table">
                                            <tr>
                                                <td >a.maksimov@adventum.ru</td><td style="width: 50px;"><a href="?user_id=449"><i class="icon-pencil" title="редактировать"></a></td><td style="width: 50px;"><a href="?delete_user_id=449#del_a" onclick="return confirm(\'Удалить пользователя?\');"><i class="icon-remove" title="Удалить"></i></a></td>
                                            </tr>
                                            <tr>
                                                <td>a.panshina@ftc.ru </td><td><a href="?user_id=470"><i class="icon-pencil" title="редактировать"></a></td><td><a href="?delete_user_id=470#del_a" onclick="return confirm(\'Удалить пользователя?\');"><i class="icon-remove" title="Удалить"></i></a></td>
                                            </tr>
                                            <tr>
                                                <td>a.tukai@artox-media.by</b></td><td><a href="?user_id=153"><i class="icon-pencil" title="редактировать"></a></td><td><a href="?delete_user_id=153#del_a" onclick="return confirm(\'Удалить пользователя?\');"><i class="icon-remove" title="Удалить"></i></a></td>
                                            </tr>
                                        </table>    				
                                </div>
                            </div>
                            <div class="tab-pane" id="leterB"><div class="alfabet_cont"></div></div>
                            <div class="tab-pane" id="leterC"><div class="alfabet_cont"></div></div>
                            <div class="tab-pane" id="leterD"><div class="alfabet_cont"></div></div>
                            <div class="tab-pane" id="leterE"></div>
                            <div class="tab-pane" id="leterF"></div>
                            <div class="tab-pane" id="leterG"></div>
                            <div class="tab-pane" id="leterH"></div>
                            <div class="tab-pane" id="leterI"></div>
                            <div class="tab-pane" id="leterJ"></div>
                            <div class="tab-pane" id="leterK"></div>
                            <div class="tab-pane" id="leterL"></div>
                            <div class="tab-pane" id="leterM"></div>
                            <div class="tab-pane" id="leterN"></div>
                            <div class="tab-pane" id="leterO"></div>
                            <div class="tab-pane" id="leterP"></div>
                            <div class="tab-pane" id="leterQ"></div>
                            <div class="tab-pane" id="leterR"></div>
                            <div class="tab-pane" id="leterS"></div>
                            <div class="tab-pane" id="leterT"></div>
                            <div class="tab-pane" id="leterU"></div>
                            <div class="tab-pane" id="leterV"></div>
                            <div class="tab-pane" id="leterW"></div>
                            <div class="tab-pane" id="leterX"></div>
                            <div class="tab-pane" id="leterY"></div>
                            <div class="tab-pane" id="leterZ"></div>
                            <div class="tab-pane" id="leter0"></div>-->
                        </div> <!--содержимое вкладок алфавит-->
                    </div> <!-- inner-tab-container -->
                </div> <!--Список клиентов -->
                ';
				echo '
                <div class="row tab-pane" id="mod_user"> <!--редактирование пользовтелей tab-->
                    <div class="inner-tab-container"> <!--mod_user tab container-->
                                    <div style="margin-bottom: 10px;"><b>Редактирование пользователя: </b><!--[ <a href="?user_id=0">перейти к добавлению пользователя и созданию тарифов</a> ]--></div>
                                    <form style="display: inline;" id="main_formm" method="post" action="?user_id='.$user['user_id'].'#mod_user">
                                    <input type="hidden" name="action" value="edituser">
                                    <input type="hidden" name="user_id" value="'.$user['user_id'].'">
                                    	<table>
                                    	<tr>
                                    	<td width="180px">E-mail <span style="color: red;">*</span> </td><td><input type="text" name="user_email" value="'.$user['user_email'].'"></td>
                                    	</tr>
                                    	<tr>
                                    	<td>Пароль: </td><td><input type="text" name="user_pass" value=""></td>
                                    	</tr>
                                    	<tr>
                                    	<td>Контактное лицо <span style="color: red;">*</span> </td><td><input type="text" name="user_name" value="'.htmlspecialchars(stripslashes($user['user_name'])).'"></td>
                                    	</tr>
                                    	<tr>
                                    	<td>Номер телефона <span style="color: red;">*</span> </td><td><input type="text" name="user_contact" value="'.htmlspecialchars(stripslashes($user['user_contact'])).'"></td>
                                    	</tr>
                                    	<tr>
                                    	<td>Название компании <span style="color: red;">*</span> </td><td><input type="text" name="user_company" value="'.htmlspecialchars(stripslashes($user['user_company'])).'"></td>
                                    	</tr>
                                    	<tr>
                                    	<td>Баланс счета </td><td><input type="text" name="user_money" value="'.$user['user_money'].'"></td>
                                    	</tr>
                                    	<tr>
                                    	<td>Дата регистрации:</td> <td>'.($user['user_ctime']!=0?date('d.m.y H:i:s',$user['user_ctime']):'Неизвестно').'</td>
                                    	</tr>
                                    	<tr>
                                    	<td>Промо-код:</td> <td>'.$user['user_promo'].'</td>
                                    	</tr>
                                    	</table>
                                    <div style="height: 15px;"></div>	
                                    <input class="btn" type="submit" value="Применить">
                                    </form>
									<input class="btn" type="button" value="==>PipeDRIVE" onclick="document.getElementById(\'main_formm\').action=\'?user_id='.$user['user_id'].'&topipe=1#mod_user\'; document.getElementById(\'main_formm\').submit();">
                                    <form style="display: inline;" method="post" action="http://beta.wobot.ru/" target="_blank">
                                    <input type="hidden" name="token" value="'.(md5(mb_strtolower($user['user_email'],'UTF-8').':'.$user['user_pass'])).'">
                                    <input type="hidden" name="user_id" value="'.$user['user_id'].'">
                                    <input class="btn" type="submit" value="Войти в beta">
                                    </form>
                                    <form style="display: inline;" method="post" action="http://production.wobot.ru/" target="_blank">
                                    <input type="hidden" name="token" value="'.(md5(mb_strtolower($user['user_email'],'UTF-8').':'.$user['user_pass'])).'">
                                    <input type="hidden" name="user_id" value="'.$user['user_id'].'">
                                    <input class="btn" type="submit" value="Войти в production">
                                    </form>
                                    <form style="display: inline;" method="post" action="?#demo" onsubmit="return confirm(\'Удалить кабинет?\')&&confirm(\'Точно удалить кабинет?\')&&confirm(\'Ну смари я тебя предупреждал!\')">
                                    <input type="hidden" name="action" value="deleteuser">
                                    <input type="hidden" name="ut_id" value="">
                                    <input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
                                    <input class="btn" type="submit" value="Удалить кабинет">
                                    </form>
                                    <div style="height: 15px;"></div>
                                    <form method="post" action="?user_id='.$user['user_id'].'#mod_user">
                                    <input type="hidden" name="action" value="editactive">
                                    <input type="hidden" name="ut_id" value="">
                                    <input type="hidden" name="user_id" value="'.$user['user_id'].'">
                                    Активность:
                                    <select name="us_active">
                                    <option value="0" '.(($user['user_active']==0)?' selected':'').'>Внутренний</option>
                                    <option value="1" '.(($user['user_active']==1)?' selected':'').'>Не активированный</option>
                                    <option value="2" '.(($user['user_active']==2)?' selected':'').'>Рабочий</option>
                                    <option value="3" '.(($user['user_active']==3)?' selected':'').'>Просроченный</option>
                                    </select>
                                    <input class="btn" type="submit" value="изменить">
                                    </form>
                                    <a href="http://188.120.239.225/tools/groups_tp.php?user_id=61">[ Добавить группы из социальных сетей пользователю ]</a><br>
                                    <div style="height: 15px;"></div>
                                    Добавить тариф пользователю:
                                    	<form style="display: inline;" method="post">
                                        	<input type="hidden" name="action" value="addut">
                                        	<input type="hidden" name="ut_id" value="addut">
                                        	<input type="hidden" name="user_id" value="'.$tar['user_id'].'">
                                        	<select name="tariff_id">';
										foreach ($tariffs as $key => $item)
										{
											echo '<option value="'.$item['tariff_id'].'">'.$item['tariff_name'].'</option>';
										}
										echo '                                        		
                                        	</select> <input class="btn" type="submit" value="добавить">
                                    	</form>                                    
	                                    <div style="height: 15px;"></div>
									';
									$qtar=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as t ON ut.tariff_id=t.tariff_id WHERE ut.user_id='.intval($_GET['user_id']));
									while ($tar=$db->fetch($qtar))
									{
										echo '
											<form style="display: inline;" method="post" action="?user_id='.$user['user_id'].'#mod_user">
		                                    	<input type="hidden" name="action" value="editut">
		                                    	<input type="hidden" name="ut_id" value="'.$tar['ut_id'].'">
		                                    	<input type="hidden" name="user_id" value="'.$tar['user_id'].'">
		                                    	<input type="hidden" name="user_active" value="0">
		                                    	Тариф: <select name="tariff_id">';
											foreach ($tariffs as $key => $item)
											{
												echo '<option value="'.$item['tariff_id'].'" '.($item['tariff_id']==$tar['tariff_id']?'selected':'').'>'.$item['tariff_name'].'</option>';
												$ut_date=$tariff['ut_date'];
												$trfs[$tariff['tariff_id']]=$tariff['tariff_name'];
											}
											echo '
		                                    	</select>
		                                    	Действует до: <input type="text" name="ut_date" value="'.date("d.m.Y",$tar['ut_date']).'">
		                                    	<input class="btn" type="submit" value="изменить">
		                                   	</form>

		                                   	<form style="display: inline;" method="post" action="?user_id='.$user['user_id'].'#mod_user" onsubmit="return confirm(\'Удалить тариф?\');">
		                                    	<input type="hidden" name="action" value="deleteut">
		                                    	<input type="hidden" name="ut_id" value="'.$tar['ut_id'].'">
		                                    	<input class="btn" type="submit" value="Удалить">
		                                   	</form>

		                                     <div style="height: 15px;"></div>
		                                    	<b>Темы тарифа:</b>
		                                    		<table class="table big_table">
		                                    		<tr>
		                                    		<td>Название</td><td>Кеш/Выдача</td><td>Запрос</td><td>Начало</td><td>Конец</td><td>Опции</td><td>Тариф</td><td>Язык</td><td>Действия</td>
		                                    		</tr>';
													$qorder=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_engage,ful_com,order_nastr,order_lang FROM blog_orders WHERE ut_id='.intval($tar['ut_id']).' AND user_id!=0 ORDER BY order_id');
													while ($order=$db->fetch($qorder))
													{
														if ($order['third_sources']!=0)
														{
															$mord[$order['order_id']]['start']=$order['order_start'];
															$mord[$order['order_id']]['end']=$order['order_end']==0?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'];
															$mord[$order['order_id']]['tp']=$order['third_sources'];
															$mord[$order['order_id']]['kw']=$order['order_keyword'];
														}
														echo '
			                                    		<form method="post" action="?user_id='.$_GET['user_id'].'#mod_user" id="refreshform_'.$order['order_id'].'">
			                                    		<input type="hidden" name="action" value="refreshcash">
			                                    		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
			                                    		</form>
			                                    		<form method="post" action="?user_id='.$_GET['user_id'].'#mod_user" id="refreshpost_'.$order['order_id'].'">
			                                    			<input type="hidden" name="action" value="refreshpost">
			                                    			<input type="hidden" name="order_id" value="'.$order['order_id'].'">
			                                    		</form>
			                                    		<form method="post">
			                                    		<input type="hidden" name="action" value="editorder">
			                                    		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
			                                    		<tr>
			                                    		<td><p style="font-size: 12px; display: inline;">#'.$order['order_id'].'</p><input type="text" name="order_name" value="'.$order['order_name'].'" style="width: 15ex; height: 3em; padding: 1em 0 1em 0;"></td>
			                                    		<td>Кеш: <a class="btn" style="width: 86px;" href="#" onclick="document.getElementById(\'refreshform_'.$order['order_id'].'\').submit();">обновить</a>
			                                    		Выдача: '.($order['third_sources']==1?'собирается':'<a class="btn" href="#" onclick="document.getElementById(\'refreshpost_'.$order['order_id'].'\').submit();">пересобрать</a>').'</td>
			                                    		<td><textarea name="order_keyword" cols="30" rows="3">'.$order['order_keyword'].'</textarea></td>
			                                    		<td><input type="text" name="order_start" id="ordertime" value="'.date('d.m.Y',$order['order_start']).'" style="width: 10ex; height: 3em; padding: 1em 0 1em 0;"></td>
			                                    		<td><input type="text" name="order_end" class="ordertime" value="'.date('d.m.Y',$order['order_end']).'" style="width: 10ex; height: 3em; padding: 1em 0 1em 0;"></td>
			                                    		<td>
			                                    		<input type="checkbox" name="ful_com" value="1" title="Полный текст" '.((intval($order['ful_com'])>0)?' checked':'').'><br />
			                                    		<input type="checkbox" name="order_engage" value="1" title="Engagement" '.((intval($order['order_engage'])>0)?' checked':'').'><br />
			                                    		<input type="checkbox" name="order_nastr" value="1" title="Автотональность" '.((intval($order['order_nastr'])>0)?' checked':'').'>
			                                    		</td>
			                                    		<td>
			                                    		<select name="ut_id">
													';
													foreach($uts as $tarff)
													{
														if (intval($tarff['tariff_id'])!=0)
														echo '	<option value="'.$tarff['ut_id'].'" '.(($tarff['tariff_id']==$tar['tariff_id'])?' selected':'').'>'.$tarff['tariff_name'].'</option>
														';
													}
													echo '
			                                    		</select>
			                                    		</td>
			                                    		<td>
			                                    		<select name="ord_lan">
			                                    			<!--<option value="0" >Дефолтный(Русский)</option>-->
			                                    			<option value="1" '.($order['order_lang']==1?'selected':'').'>Иностранный</option>
			                                    			<option value="2" '.($order['order_lang']==2?'selected':'').'>Русский</option>
			                                    			<option value="4" '.($order['order_lang']==4?'selected':'').'>Азербайджан.</option>
			                                    		</select>
			                                            <td>
			                                    		<input class="btn" type="submit" value="применить"></form>

			                                    		<form style="display: inline;" method="post" style="margin-bottom: 0;" action="?user_id='.$_GET['user_id'].'#mod_user">
			                                    		<input type="hidden" name="action" value="blockorder">
			                                    		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
			                                    		<input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
			                                    		<input class="btn" type="submit" value="Заблок. ">
			                                    		</form>

			                                    		<form style="display: inline;" method="post" action="?user_id='.$_GET['user_id'].'#mod_user" onsubmit="return confirm(\'Удалить тему?\')&&confirm(\'Точно удалить тему?\')&&confirm(\'Точно-точно удалить тему?\');">
			                                    		<input type="hidden" name="action" value="deleteorder">
			                                    		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
			                                    		<input class="btn" type="submit" value="Удалить">
			                                    		</form></td>
			                                    		</tr>';
													}
														echo '
		  	                                  </table>
										<table>
										<form method="post">
										<input type="hidden" name="action" value="addorder">
										<input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
										<input type="hidden" name="ut_id" value="'.$tar['ut_id'].'">
										<input type="hidden" name="order_id" value="addorder">
										<tr>
										<td><input type="text" name="order_name" value="" style="width: 20ex; height: 3em; padding: 1em 0 1em 0;"></td>
										<td><textarea name="order_keyword" cols="60" rows="3"></textarea></td>
										<td><input type="text" name="order_start" class="ordertime" value="'.date("d.m.Y").'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
										<td><input type="text" name="order_end" class="ordertime" value="'.date("d.m.Y").'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
										<td>
										<input type="checkbox" name="ful_com" value="1" checked title="Полный текст">
										<input type="checkbox" name="order_engage" value="1" checked title="Engagement">
										<input type="checkbox" name="order_nastr" value="1" title="Автотональность">
										</td>
										<td>
										</td>
										<td><input type="submit" value="добавить"></td>
										</tr>
										</form>
										</table>
										<div style="height: 15px;"></div>	
	                                   	<b>Отключенные темы:</b>
	                                    <div style="height: 15px;"></div>
	                                    	<table class="table table-striped">
	                                    	</table>
	                                    <div style="height: 15px;"></div><table>
										';
										$res2=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_engage,ful_com,order_nastr,order_lang FROM blog_orders WHERE ut_id='.intval($tar['ut_id']).' AND user_id=0 ORDER BY order_id');
										$i=1;
										while($orderblock = $db->fetch($res2))
										{
											if ($orderblock['third_sources']!=0)
											{
												$mord[$orderblock['order_id']]['start']=$orderblock['order_start'];
												$mord[$orderblock['order_id']]['end']=$orderblock['order_end']==0?mktime(0,0,0,date('n'),date('j'),date('Y')):$orderblock['order_end'];
												$mord[$orderblock['order_id']]['tp']=$orderblock['third_sources'];
												$mord[$orderblock['order_id']]['kw']=$orderblock['order_keyword'];
											}
											echo '
											<form method="post">
											<input type="hidden" name="action" value="editorder">
											<input type="hidden" name="order_id" value="'.$orderblock['order_id'].'">
											<tr>
											<td><p style="font-size: 8px; display: inline;">#'.intval($orderblock['order_id']).'</p><input type="text" name="order_name" value="'.htmlspecialchars($orderblock['order_name']).'" style="width: 20ex; height: 3em; padding: 1em 0 1em 0;"></td>
											<td><textarea name="order_keyword" cols="65" rows="3">'.$orderblock['order_keyword'].'</textarea></td>
											<td><input type="text" name="order_start" id="ordertime" value="'.date("d.m.Y",$orderblock['order_start']).'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
											<td><input type="text" name="order_end" class="ordertime" value="'.date("d.m.Y",$orderblock['order_end']).'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
											<td>
											<input type="checkbox" name="ful_com" value="1" title="Полный текст"'.((intval($orderblock['ful_com'])>0)?' checked':'').'>
											<input type="checkbox" name="order_engage" value="1" title="Engagement"'.((intval($orderblock['order_engage'])>0)?' checked':'').'>
											<input type="checkbox" name="order_nastr" value="1" title="Автотональность"'.((intval($orderblock['order_nastr'])>0)?' checked':'').'>
											</td>
											<td>
											</td>
											<td>
											<select name="ord_lan">
												<!--<option value="0" '.($orderblock['order_lang']==0?'selected':'').'>Дефолтный(Русский)</option>-->
												<option value="1" '.($orderblock['order_lang']==1?'selected':'').'>Иностранный</option>
												<option value="2" '.(($orderblock['order_lang']==2)||($orderblock['order_lang']==0)?'selected':'').'>Русский</option>
												<option value="4" '.($orderblock['order_lang']==4?'selected':'').'>Азербайджанский</option>
											</select>
											<input type="submit" value="изменить">
											</form></td>
											<td>
											'.((strlen($_SESSION['fb_158565747504200_access_token'])>0)?'<a href="/project/facebook-manual.php?id='.$orderblock['order_id'].'&token='.$_SESSION['fb_158565747504200_access_token'].'" target="_blank">FB</a>':'').'
											<form style="display: inline;" method="post" action="?user_id='.intval($_GET['user_id']).'" onsubmit="return confirm(\'Удалить тему?\')&&confirm(\'Точно удалить тему?\')&&confirm(\'Точно-точно удалить тему?\');">
											<input type="hidden" name="action" value="deleteorder">
											<input type="hidden" name="order_id" value="'.$orderblock['order_id'].'">
											<input type="submit" value="X">
											</form></td>
											<td>
											<form method="post" style="margin-bottom: 0;" action="?user_id='.intval($_GET['user_id']).'#mod_user">
											<input type="hidden" name="action" value="recoverorder">
											<input type="hidden" name="order_id" value="'.$orderblock['order_id'].'">
											<input type="hidden" name="user_id" value="'.$tar['user_id'].'">
											<input type="submit" value="Восстановить">
											</form>
											</td>
											<td>
											</tr>
											';
										}
										echo '</table>';
									}
									echo '

	                
                                        
                                    <b>Последние заходы:</b>
                                        <div style="height: 15px;"></div>    
                                   	<div style="height: 150px; overflow-y: scroll; width: 60%;">
                                        <table class="table table-striped add_res">';
									$reslog=$db->query('SELECT * FROM blog_log WHERE user_id='.intval($_GET['user_id']).' ORDER BY log_time DESC');
									$i=1;
									while ($log=$db->fetch($reslog))
									{
										echo '
										<tr>
                                            <td>'.$i.'.</td><td>'.date('d.m.y H:i:s',$log['log_time']).'</td>
                                            <td><a href="http://www.maxmind.com/app/locate_demo_ip?ips='.$log['log_ip'].'" target="_blank">'.$log['log_ip'].'</a></td>
                                        </tr>
                                        ';
										$i++;
									}
									echo '
                                        </table>
                                    </div>
                                    Всего заходов: <b>'.($i-1).'</b>
                                    <div style="height: 15px;"></div>
                                    
                                    <!-- в таблице не нужны теги р, нужно как в первых строках просто текст -->
                                    <b>Реал-тайм собранные темы:</b>
                                    <div style="height: 15px;"></div>
                                    <table class="table table-striped">
                                    <tr class="dark_head">
                                    <td>ID</td>
                                    <td>Ключевые слова:</td>
                                    <td>Начало отчета</td>
                                    <td>Конец отчета</td>
                                    <td>Последнее время сбора</td>
                                    </tr>';
									foreach ($mord as $key => $item)
									{
										echo '<tr><td>'.$key.'</td><td>'.$item['kw'].'</td><td>'.date('d.m.Y',$item['start']).'</td><td>'.date('d.m.Y',$item['end']).'</td><td>'.date('d.m.y H:i:s',$item['tp']).'</td></tr>';
									}
									echo '
										</table>
                                    <div style="height: 15px;"></div>
                                    
                                    
                                    <a href="http://188.120.239.225/tools/tp_info.php" target="_blank">[ Статистика реал-тайм сбора ]</a>
                                    <div style="height: 15px;"></div>
                                    <table class="table table-striped" style="width: 70%;">
                                        <tr class="dark_head">
                                            <td>№</td><td>Время</td><td></td><td>Тариф</td></tr>
										';
										$status_bil[-1]='Ошибка платежа';
										$status_bil[0]='Выставлен счет';
										$status_bil[1]='Ошибка платежа';
										$status_bil[2]='Проведен';
										$qbilling=$db->query('SELECT * FROM billing as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE a.user_id='.$_GET['user_id']);
										while ($bil=$db->fetch($qbilling))
										{
											$bil_id++;
											echo '
											<tr>
                                                <td>'.$bil_id.'</td><td>'.date('d.m.y H:i:s',$bil['date']).'</td><td>'.$status_bil[$bil['status']].'</td><td>'.$bil['tariff_name'].'</td>
                                            </tr>';
										}
										echo '
                                    </table>
                                    
                                                        
                    
                        
                    </div> <!--mod_user tab container-->
                </div> <!--редактирование пользовтелей tab-->
                
                
            </div> <!--содержимое вкладок-->
            
              <div class="row" id="debug_textarea" style="display: none;"> <!--окно дебаг-->
                <div class="span12">
                    <div style="margin-left: 50px; margin-top: 20px;">
                        <a id="from_debug">Скрыть</a>
                    </div>
                    <textarea style="width: 90%; height: 150px; margin-left: 20px; margin-top: 10px;">
					';
					print_r($_GET);
					print_r($_POST);
					print_r($_SESSION);
					echo '
                    </textarea>
                </div>
              </div> <!--окно дебаг-->
        </span>
    </div> <!--row tab_row -->    
    </div> <!-- /container -->


  </body>
</html>';

?>