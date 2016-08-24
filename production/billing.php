<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Биллинг WOBOT &beta;</title>
    <meta content="" name="description" />
    <meta content="Wobot" name="keywords" />
    <meta content="Wobot media" name="author" />
    <meta content="all" name="robots" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="http://beta.wobot.ru/css/main.css">
   	<link rel="stylesheet" type="text/css" href="http://beta.wobot.ru/css/msglist.css">
</head>
<body style="background-color: #ECECEC;">
<div id="MM" style="opacity: 1; height: 1154px; width: 600px;">
            <div id="mm-items" style="display: table-row;">
            	<div class="mm-header" style="overflow: hidden;white-space: nowrap; width: auto;">
                	<div class="inline el1" style="width: 70px;">Дата</div>
					<div class="inline el1" style="width: 100px; text-align: right;">Сумма (руб.)</div>
					<div class="inline el1" style="width: 200px;">Назначение</div>
					<div class="inline el1" style="width: 70px;">Статус</div>
                </div>
<?
$rs2=$db->query('SELECT * FROM billing WHERE user_id='.intval($_GET['user_id']).' and `status`!=0');
while($bill = $db->fetch($rs2))
{
	echo '
	<div class="mm-header mm-item mm-itm-closed" style="overflow: hidden;white-space: nowrap; width: auto;">
    	<div class="el1 h inline" style="width: 70px;"><span class="text" style="font-weight: normal;">'.date('d.m.Y',$bill['date']).'</span></div>
		<div class="el1 h inline" style="width: 100px; text-align: right;"><span class="text">'.$bill['money'].'</span></div>
		<div class="el1 h inline" style="width: 200px;"><span class="text" style="font-weight: normal;">'.(($bill['money']>=0)?'Пополнение счета кабинета':'Оплата тарифа').'</span></div>
		<div class="el1 h inline"><span class="text" style="font-weight: normal;">'.((intval($bill['status'])==-1)?'Не успешно':'Успешно').'</span></div>
    </div>';
}
?>
			</div>
<?


// сумма заказа
// sum of order
$out_summ = "29250";

// номер заказа
// number of order

//$rs=$db->query('INSERT INTO billing (user_id, money, date, `status`) values ('.intval($_GET['user_id']).', '.$out_summ.', '.time().', 0)');
//$inv_id=$db->insert_id();
$inv_id=248;
// 2.
// Оплата заданной суммы с выбором валюты на сайте ROBOKASSA
// Payment of the set sum with a choice of currency on site ROBOKASSA

// регистрационная информация (логин, пароль #1)
// registration info (login, password #1)
$mrh_login = "Wobot";
$mrh_pass1 = "r1o2m3a4";

// описание заказа
// order description
$inv_desc = "ROBOKASSA Пополнение счета кабинета";

// тип товара
// code of goods
$shp_item = 61;//intval($_GET['user_id']);

// предлагаемая валюта платежа
// default payment e-currency
$in_curr = "PCR"; 

// язык
// language
$culture = "ru";

// формирование подписи
// generate signature
$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");  

// форма оплаты товара
// payment form
print "<div style=\"text-align: center; width: 500px; padding: 10px;\">".
      "<form action='https://merchant.roboxchange.com/Index.aspx' method=POST target='_blank'>".
      "<input type=hidden name=MrchLogin value=$mrh_login>".
      "<input type=hidden name=OutSum value=$out_summ>".
      "<input type=hidden name=InvId value=$inv_id>".
      "<input type=hidden name=Desc value='$inv_desc'>".
      "<input type=hidden name=SignatureValue value=$crc>".
      "<input type=hidden name=Shp_item value='$shp_item'>".
      "<input type=hidden name=IncCurrLabel value=$in_curr>".
      "<input type=hidden name=Culture value=$culture>".
      "<input type=submit value='Пополнить баланс'>".
      "</form>
</div>";
?>
	</body>
	</html>
