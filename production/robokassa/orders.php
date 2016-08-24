<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();
auth();
//echo $loged;
if (!$loged) die();
//echo 'gg';
//$user['user_id']=61;//$_COOKIE['user_id'];
//$rw['ut_id']=61;//$_COOKIE['user_id'];
//auth();
//if (!$loged) die();
$i=0;
//print_r($user);
$rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id='.intval($user['user_id']));
while ($rw = $db->fetch($rs)) 
{
	$res=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($user['user_id']).' and ut_id='.$rw['ut_id']);
	while ($row = $db->fetch($res)) 
	{
		$metrics=json_decode($row['order_metrics'],true);
		$mas_res=json_decode($row['order_src'],true);
		$res_count=count($mas_res);
		$coll=0;
		foreach ($mas_res as $ind => $item)
		{
			$coll+=$item;
		}
		$out['user_tarif']=$rw['tariff_name'];
		$out['tarif_id']=$rw['tariff_id'];
		$out['user_money']=$user['user_money'];
		$out['user_email']=$user['user_email'];
		$out['user_priv']=$user['user_priv'];
		$out['orders'][$i]['keyword']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
		$out['orders'][$i]['id']=intval($row['order_id']);
		$out['orders'][$i]['posts']=$coll;
		$out['orders'][$i]['src']=$res_count;
		$out['orders'][$i]['value']=$metrics['value'];
		$out['orders'][$i]['start']=date('d.m.Y',$row['order_start']);
		$out['orders'][$i]['end']=date('d.m.Y',$row['order_end']);
		$out['orders'][$i]['ready']=(($row['order_last']==0)?false:date('d.m.Y',$row['order_last']));
		$out['orders'][$i]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
		$out['orders'][$i]['din_posts']=($metrics['d_post']>0)?'+'.(intval($metrics['d_post']/$coll*100)).'%':(intval($metrics['d_post']/$coll*100)).'%';
		$out['orders'][$i]['din_src']=($metrics['d_src']>0)?'+'.(intval($metrics['d_src']/$res_count*100)).'%':(intval($metrics['d_src']/$res_count*100)).'%';
		$out['orders'][$i]['div_value']=($metrics['d_aud']>0)?'+'.(intval($metrics['d_aud']/$metrics['value']*100)).'%':(intval($metrics['d_aud']/$metrics['value']*100)).'%';
		$i++;
	}
}
//print_r($metrics);
/*$out['user_tarif']='Демо';
$out['user_email']='lala@wobot.ru';
$out['user_priv']=0;
$out['consultant']='+7 (929) 3333 333';

$out['orders'][0]['keyword']='hse';
$out['orders'][1]['keyword']='htc';
$out['orders'][2]['keyword']='wobot';
$out['orders'][3]['keyword']='youscan';
$out['orders'][4]['keyword']='Евросеть';

$out['orders'][0]['id']=135;
$out['orders'][1]['id']=142;
$out['orders'][2]['id']=150;
$out['orders'][3]['id']=152;
$out['orders'][4]['id']=189;

$out['orders'][0]['posts']=7730;
$out['orders'][1]['posts']=5098;
$out['orders'][2]['posts']=23211;
$out['orders'][3]['posts']=976;
$out['orders'][4]['posts']=0;

$out['orders'][0]['din_posts']='+360';
$out['orders'][1]['din_posts']='+78';
$out['orders'][2]['din_posts']='+21';
$out['orders'][3]['din_posts']='+109';
$out['orders'][4]['din_posts']='-29';

$out['orders'][0]['src']=526;
$out['orders'][1]['src']=282;
$out['orders'][2]['src']=3342;
$out['orders'][3]['src']=86;
$out['orders'][4]['src']=0;

$out['orders'][0]['din_src']='-89';
$out['orders'][1]['din_src']='+21';
$out['orders'][2]['din_src']='-96';
$out['orders'][3]['din_src']='+12';
$out['orders'][4]['din_src']='-90';

$out['orders'][0]['value']=693102;
$out['orders'][1]['value']=277181;
$out['orders'][2]['value']=398505;
$out['orders'][3]['value']=197026;
$out['orders'][4]['value']=0;

$out['orders'][0]['div_value']='+35';
$out['orders'][1]['div_value']='-90';
$out['orders'][2]['div_value']='+78';
$out['orders'][3]['div_value']='-92';
$out['orders'][4]['div_value']='+67';

$out['orders'][0]['ready']='10.01.2012';
$out['orders'][1]['ready']='10.01.2012';
$out['orders'][2]['ready']='10.01.2012';
$out['orders'][3]['ready']='10.01.2012';
$out['orders'][4]['ready']=false;

$out['orders'][0]['start']='15.08.2010';
$out['orders'][1]['start']='15.11.2010';
$out['orders'][2]['start']='15.07.2011';
$out['orders'][3]['start']='28.01.2011';
$out['orders'][4]['start']='15.03.2011';

$out['orders'][0]['end']='15.09.2010';
$out['orders'][1]['end']='04.02.2011';
$out['orders'][2]['end']='23.09.2011';
$out['orders'][3]['end']='15.03.2011';
$out['orders'][4]['end']='15.04.2011';

$out['orders'][0]['graph']='http://lala.ru/img/135.png';
$out['orders'][1]['graph']='http://lala.ru/img/142.png';
$out['orders'][2]['graph']='http://lala.ru/img/150.png';
$out['orders'][3]['graph']='http://lala.ru/img/152.png';
$out['orders'][4]['graph']='http://lala.ru/img/189.png';
*/
echo json_encode($out);
?>