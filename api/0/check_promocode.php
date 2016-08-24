<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

//$_POST=$_GET;
auth();
if (!$loged) die();

$db=new database();
$db->connect();

if ($_POST['code']!='')
{
	$qrt=$db->query('SELECT * FROM user_promo WHERE code_value=\''.addslashes(preg_replace('/[^а-яa-z0-9ё]/isu','',$_POST['code'])).'\' LIMIT 1');//профильтровать только буквы и цифры
	$code=$db->fetch($qrt);
	if (intval($code['code_id'])==0)
	{
		$outmas['status']=2;//промокод не существует
		echo json_encode($outmas);
		die();
	}
	if ($code['code_exp']<time())
	{
		$outmas['status']=3;//промокод истек
		echo json_encode($outmas);
		die();
	}

	if ($user['tariff_id']==10) $billing=2;
	else $billing=1;
	if ($code['code_billing']!=$billing)
	{
		$outmas['status']=4;//промокод расчитан под другую схему биллинга
		echo json_encode($outmas);
		die();
	}

	if ($code['code_type']==1) //многоразовый промокод
	{
		$qrt2=$db->query('SELECT * FROM user_promo_activation WHERE code_id='.intval($code['code_id']).' and user_id='.intval($user['user_id']).' LIMIT 1');//профильтровать только буквы и цифры
		$activation=$db->fetch($qrt2);
		if (intval($activation['activation_id'])>0)
		{
			$outmas['status']=5;//промокод уже использован
			echo json_encode($outmas);
			die();
		}
		//Активация промокода
		$db->query('INSERT INTO user_promo_activation (code_id,user_id) values ('.intval($code['code_id']).','.intval($user['user_id']).')');
		$db->query('INSERT INTO billing (user_id,money,date,status,tariff_id) VALUES ('.$user['user_id'].',0,'.time().',3,'.$user['tariff_id'].')');
		//echo 'UPDATE user_tariff SET ut_date=ut_date+'.($code['code_billing']==1?$code['code_count']*86400:$code['code_count']).' WHERE ut_id='.$user['ut_id'];
		$db->query('UPDATE user_tariff SET ut_date=ut_date+'.($code['code_billing']==1?$code['code_count']*86400:$code['code_count']).' WHERE ut_id='.$user['ut_id']);
		$qbalance=$db->query('SELECT * FROM user_tariff WHERE ut_id='.$user['ut_id']);
		$balance=$db->fetch($qbalance);
		$outmas['balance']=($code['code_billing']==1?intval((time()-$balance['ut_date'])/86400):$balance['ut_date']);
		$outmas['status']='ok';
		echo json_encode($outmas);
		die();
	}
	elseif ($code['code_type']==2) //одноразовый промокод
	{
		$qrt2=$db->query('SELECT * FROM user_promo_activation WHERE code_id='.intval($code['code_id']).' LIMIT 1');//профильтровать только буквы и цифры
		$activation=$db->fetch($qrt2);
		if (intval($activation['activation_id'])>0)
		{
			$outmas['status']=5;//промокод уже использован
			echo json_encode($outmas);
			die();
		}
		//Активация промокода
		$db->query('INSERT INTO user_promo_activation (code_id,user_id) values ('.intval($code['code_id']).','.intval($user['user_id']).')');
		$db->query('INSERT INTO billing (user_id,money,date,status,tariff_id) VALUES ('.$user['user_id'].',0,'.time().',3,'.$user['tariff_id'].')');
		//echo 'UPDATE user_tariff SET ut_date=ut_date+'.($code['code_billing']==1?$code['code_count']*86400:$code['code_count']).' WHERE ut_id='.$user['ut_id'];
		$db->query('UPDATE user_tariff SET ut_date=ut_date+'.($code['code_billing']==1?$code['code_count']*86400:$code['code_count']).' WHERE ut_id='.$user['ut_id']);
		$qbalance=$db->query('SELECT * FROM user_tariff WHERE ut_id='.$user['ut_id']);
		$balance=$db->fetch($qbalance);
		$outmas['balance']=($code['code_billing']==1?intval((time()-$balance['ut_date'])/86400):$balance['ut_date']);
		$outmas['status']='ok';
		echo json_encode($outmas);
		die();
	}
	else
	{
		$outmas['status']=6;//промокод аннулирован
		echo json_encode($outmas);
		die();
	}

}
else
{
	$outmas['status']=1;//не передан промокод
	echo json_encode($outmas);
	die();
}

?>