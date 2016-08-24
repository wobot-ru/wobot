<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');
//out_summ=4983.000000&OutSum=4983.000000&inv_id=532&InvId=532&crc=191AAA2DC6B6773E68B9E2A795E66DE3&SignatureValue=191AAA2DC6B6773E68B9E2A795E66DE3&PaymentMethod=EMoney&IncSum=4983.000000&IncCurrLabel=YandexMerchantOceanR&Shp_item=2304
$db = new database();
$db->connect();

$fl=@fopen("payment.log","a+") or
          die("error");
fputs($fl,'result: '.json_encode($_REQUEST)."\n\n");
fclose($fl);

//print_r($user);

// регистрационная информация (пароль #2)
// registration info (password #2)
$mrh_pass2 = "r1o2m3a4a5";

//установка текущего времени
//current date
$tm=getdate(time()+9*3600);
$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

// проверка корректности подписи
// check signature
if ($my_crc !=$crc)
{
  echo "bad sign\n";
  exit();
}

// признак успешно проведенной операции
// success
echo "OK\n";

// запись в файл информации о прведенной операции
// save order info to file
$f=@fopen("order.txt","a+") or
          die("error");
fputs($f,"order_num :$inv_id; Item: $shp_item;Summ :$out_summ;Date :$date\n");
fclose($f);



//TODO проверить, что оплата еще не проводилась


//$rs=$db->query('UPDATE billing SET date='.time().', status=1, money='.$out_summ.' WHERE bill_id='.intval($inv_id));
//$rs2=$db->query('UPDATE users SET user_money=user_money+'.intval($out_summ).' WHERE user_id='.intval($shp_item));

$rs=$db->query('UPDATE billing SET date='.time().', status=2, money="'.$out_summ.'" WHERE bill_id='.intval($inv_id));

$res=$db->query('SELECT * FROM billing WHERE bill_id='.intval($inv_id).' LIMIT 1');
$bill=$db->fetch($res);

$res2=$db->query('SELECT * FROM users WHERE user_id='.intval($bill['user_id']).' LIMIT 1');
//echo 'SELECT * FROM users WHERE user_id='.intval($bill['user_id']).' LIMIT 1';
$user=$db->fetch($res2);

$res3=$db->query('SELECT * FROM user_tariff WHERE user_id='.intval($bill['user_id']).' LIMIT 1');
$ut=$db->fetch($res3);

if (($ut['tariff_id']==3)||($ut['ut_date']==0))
{//С демо на платный
	$res=$db->query('INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')');
	//echo 'INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')';
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n')+$bill['months'],date('j'),date('Y')).', tariff_id='.$bill['tariff_id'].' WHERE ut_id='.$ut['ut_id']);
	//echo $bill['months']."\n";
	//echo 'UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n')+$bill['months'],date('j'),date('Y')).', tariff_id='.$bill['tariff_id'].' WHERE ut_id='.$ut['ut_id'];
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE users set user_active=2 WHERE user_id='.$user['user_id']);
	//echo 'UPDATE users set user_active=2 WHERE user_id='.$user['user_id'];
	//echo "\n";
	$doit=$db->fetch($res);
	$qtar=$db->query('SELECT * FROM blog_tariff WHERE tariff_id='.$ut['tariff_id']);
	$tar=$db->fetch($qtar);
	$headers  = "From: noreply@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('diana@wobot-research.com','Команда Wobot'.(($ut['tariff_id']==3)?' // Оплата!':''),"Пользователь ".$user['user_email']." Оплатил кабинет! . <br>Сумма: ".(intval($bill['money'])*(-1))."<br>Количество месяцев: ".$bill['months']."<br> Тариф: ".$tar['tariff_name'],$headers);

	$headers  = "From: noreply@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('r@wobot.co','Команда Wobot'.(($ut['tariff_id']==3)?' // Оплата!':''),"Пользователь ".$user['user_email']." Оплатил кабинет! . <br>Сумма: ".(intval($bill['money'])*(-1))."<br>Количество месяцев: ".$bill['months']."<br> Тариф: ".$tar['tariff_name'],$headers);

}

//Продление без смены тарифа
//Обновляем дату завершения срока действия = старая дата завершения + 30 дней * кол-во месяцев
elseif (($ut['tariff_id']!=3)&&($ut['tariff_id']==$bill['tariff_id']))
{
	if ($ut['ut_date']<time()) $ut['ut_date']=time();
	$res=$db->query('INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')');
	//echo 'INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')';
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n',$ut['ut_date'])+$bill['months'],date('j',$ut['ut_date']),date('Y',$ut['ut_date'])).' WHERE ut_id='.$ut['ut_id']);
	//echo $bill['months']."\n";
	//echo 'UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n')+$bill['months'],date('j'),date('Y')).', tariff_id='.$bill['tariff_id'].' WHERE ut_id='.$ut['ut_id'];
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE users set user_active=2 WHERE user_id='.$user['user_id']);
	//echo 'UPDATE users set user_active=2 WHERE user_id='.$user['user_id'];
	//echo "\n";
	$doit=$db->fetch($res);
	$qtar=$db->query('SELECT * FROM blog_tariff WHERE tariff_id='.$ut['tariff_id']);
	$tar=$db->fetch($qtar);
	$headers  = "From: noreply@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('diana@wobot-research.com','Команда Wobot'.(($ut['tariff_id']==3)?' // Продление!':''),"Пользователь ".$user['user_email']." продлил кабинет без смены тарифа! . <br>Сумма: ".(intval($bill['money'])*(-1))."<br>Количество месяцев: ".$bill['months']."<br> Тариф: ".$tar['tariff_name'],$headers);

	$headers  = "From: noreply@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('r@wobot.co','Команда Wobot'.(($ut['tariff_id']==3)?' // Продление!':''),"Пользователь ".$user['user_email']." продлил кабинет без смены тарифа! . <br>Сумма: ".(intval($bill['money'])*(-1))."<br>Количество месяцев: ".$bill['months']."<br> Тариф: ".$tar['tariff_name'],$headers);

}
//Замена с продлением
//Обновляем дату завершения срока действия = текущая дата + 30 дней * кол-во месяцев
//Обновляем тариф
elseif (($ut['tariff_id']!=3)&&($ut['tariff_id']!=$bill['tariff_id']))
{
	$res=$db->query('INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')');
	//echo 'INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')';
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n')+$bill['months'],date('j'),date('Y')).', tariff_id='.$bill['tariff_id'].' WHERE ut_id='.$ut['ut_id']);
	//echo $bill['months']."\n";
	//echo 'UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n')+$bill['months'],date('j'),date('Y')).', tariff_id='.$bill['tariff_id'].' WHERE ut_id='.$ut['ut_id'];
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE users set user_active=2 WHERE user_id='.$user['user_id']);
	//echo 'UPDATE users set user_active=2 WHERE user_id='.$user['user_id'];
	//echo "\n";
	$doit=$db->fetch($res);
	$qtar=$db->query('SELECT * FROM blog_tariff WHERE tariff_id='.$ut['tariff_id']);
	$tar=$db->fetch($qtar);
	$headers  = "From: noreply@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('diana@wobot-research.com','Команда Wobot'.(($ut['tariff_id']==3)?' // Продление!':''),"Пользователь ".$user['user_email']." продлил кабинет со сменой тарифа! . <br>Сумма: ".(intval($bill['money'])*(-1))."<br>Количество месяцев: ".$bill['months']."<br> Тариф: ".$tar['tariff_name'],$headers);

	$headers  = "From: noreply@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('r@wobot.co','Команда Wobot'.(($ut['tariff_id']==3)?' // Продление!':''),"Пользователь ".$user['user_email']." продлил кабинет со сменой тарифа! . <br>Сумма: ".(intval($bill['money'])*(-1))."<br>Количество месяцев: ".$bill['months']."<br> Тариф: ".$tar['tariff_name'],$headers);

}

?>


