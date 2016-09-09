<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

auth();
//if (!$loged) die();

if ($_POST['sum']>0)
{
	//$rs=$db->query('SELECT * FROM billing WHERE user_id='.intval($user['user_id']).' and `status`=0 and date>'.(time()-1800).' ORDER BY bill_id DESC LIMIT 1');
	//$inv_id=$db->fetch($rs);
//$rsdel=$db->query('DELETE FROM billing WHERE `status`=0 and date<'.(time()-86400*2));
	//$inv_id=$db->fetch($rs);
	//if (intval($inv_id['bill_id'])>0)
	//{
	//	$rs=$db->query('UPDATE billing SET date='.time().', money=0, tariff_id='.intval($_POST['tariff_id']).', months='.intval($_POST['months']).' WHERE bill_id='.intval($inv_id['bill_id']));
	//	$outmas['bill_id']=$inv_id['bill_id'];
	//}
	//else
	//{
		$rs=$db->query('INSERT INTO billing (user_id, money, date, `status`, tariff_id, months) values ('.intval($user['user_id']).', 0, '.time().', 0, '.intval($_POST['tariff_id']).', '.intval($_POST['months']).')');
		$inv_id=$db->insert_id();
		$outmas['bill_id']=intval($inv_id);
	//}
	//echo 'Wobot:'.intval($_POST['sum']).':'.$outmas['bill_id'].':r1o2m3a4:Shp_item='.intval($user['user_id']);
	$crc  = md5('Wobot:'.intval($_POST['sum']).':'.$outmas['bill_id'].':r1o2m3a4:Shp_item='.intval($user['user_id']));
	$outmas['sign']=$crc;

	$bill_myragon=md5('939'.intval($inv_id));
	$outmas['bill_myragon']=$bill_myragon;
}
else
{
	$inf=$db->query('SELECT * FROM billing WHERE user_id='.intval($user['user_id']).' and `status`!=0 ORDER BY date DESC');
	$kk=0;
	while ($src=$db->fetch($inf))
	{
		$outmas['bill'][$kk]['date']=date('d.m.Y',$src['date']);
		$outmas['bill'][$kk]['money']=intval($src['money']);
		$outmas['bill'][$kk]['type']=(intval($src['money'])>=0)?1:2;
		$outmas['bill'][$kk]['status']=($src['status']==-1)?0:($src['status']==3?3:1);

		$kk++;
	}
}

echo json_encode($outmas);
?>