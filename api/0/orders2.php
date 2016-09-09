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
//print_r($_COOKIE);
if (!$loged)
{
	if ($user['tariff_id']==3)
	{
		$rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id='.intval($user['user_id']));
		$rw = $db->fetch($rs);
		$out['user_id']=$user['user_id'];
		$out['user_tarif']=$rw['tariff_name'];
		$out['tarif_id']=$rw['tariff_id'];
		$out['user_money']=intval($user['user_money']);
		$out['user_email']=$user['user_email'];
		$out['user_priv']=$user['user_priv'];
		$out['user_exp']='Аккаунт заблокирован';
		$out['is_addorder']=0;
		if (($count_left[$user['tariff_id']]-intval((time()-mktime(0,0,0,date('n',$user['ut_date']),date('d',$user['ut_date']),date('Y',$user['ut_date']))) / 86400))<0)
		{
			$out['tarif_exp']='Аккаунт заблокирован';
		}
		else
		{
			$out['tarif_exp']='Аккаунт заблокирован';
		}
		$out['orders']=array();
		echo json_encode($out);
		die();
	}
}
$out['orders']=array();
//sleep(3);
//echo 'gg';
//$user['user_id']=61;//$_COOKIE['user_id'];
//$rw['ut_id']=61;//$_COOKIE['user_id'];
//auth();
//if (!$loged) die();
$i=0;
$out['user_id']=$user['user_id'];

//print_r($user);
if ($user['tariff_id']!=3)
{
	$rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id='.intval($user['user_id']));
}
else
{
	$rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id=61');
	$user2=intval($user['user_id']);
	$user['user_id']=61;
	
	//echo 'userid '.$user2['user_id'];
	$rs2=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id='.intval($user2));
	$rw2 = $db->fetch($rs2);
	$ut2 = $rw2['ut_id'];
	//echo $ut2.'123';
}
$count_left[2]=30;
$count_left[3]=14;
$count_left[4]=30;

if ($user['tariff_id']!=1)
{
	if (((time()-$user['ut_date'])/86400)<30)
	{
		$out['is_addorder']=1;
	}
	else
	{
		$out['is_addorder']=0;
	}
}
else
{
	$out['is_addorder']=1;
}


function idsort($a, $b)
{
    if ($a['id'] == $b['id']) {
        return 0;
    }
    return ($a['id'] < $b['id']) ? 1 : -1;
}
function valuesort($a, $b)
{
    if ($a['value'] == $b['value']) {
        return 0;
    }
    return ($a['value'] < $b['value']) ? 1 : -1;
}
function postssort($a, $b)
{
    if ($a['posts'] == $b['posts']) {
        return 0;
    }
    return ($a['posts'] < $b['posts']) ? 1 : -1;
}
function kwsort($a, $b)
{
    if ($a['keyword'] == $b['keyword']) {
        return 0;
    }
    return ($a['keyword'] > $b['keyword']) ? 1 : -1;
}
function startsort($a, $b)
{
    if ($a['start'] == $b['start']) {
        return 0;
    }
    return ($a['start'] < $b['start']) ? 1 : -1;
}

while ($rw = $db->fetch($rs)) 
{
	/*if (($count_left[$user['tariff_id']]-intval((time()-mktime(0,0,0,date('n',$user['ut_date']),date('d',$user['ut_date']),date('Y',$user['ut_date']))) / 86400))<0)
	{
		$out['tarif_exp']=0;
	}
	else
	{
		$out['tarif_exp']=$count_left[$user['tariff_id']]-intval((time()-mktime(0,0,0,date('n',$user['ut_date']),date('d',$user['ut_date']),date('Y',$user['ut_date']))) / 86400);
	}*/
	//echo mktime(0,0,0,date('n',$rw['ut_date']),date('j',$rw['ut_date']),date('Y',$rw['ut_date'])).' '.$rw['ut_date'];
	if (intval((mktime(0,0,0,date('n',$user['ut_date']),date('j',$user['ut_date']),date('Y',$user['ut_date']))-time()) / 86400)<0)
	{
		$out['tarif_exp']=0;
	}
	else
	{
		$out['tarif_exp']=intval((mktime(0,0,0,date('n',$user['ut_date']),date('j',$user['ut_date']),date('Y',$user['ut_date']))-time()) / 86400)+1;
	}
	//$out['tarif_exp']=((30-((mktime(0,0,0,date('n',time()),date('d',time()),date('Y',time()))-$rw['ut_date'])/86400))<0?0:(30-((mktime(0,0,0,date('n',time()),date('d',time()),date('Y',time()))-$rw['ut_date'])/86400)));
	$out['user_tarif']=$rw['tariff_name'];
	$out['tarif_id']=$rw['tariff_id'];
	$out['user_money']=intval($user['user_money']);
	$out['user_email']=$user['user_email'];
	$out['user_priv']=$user['user_priv'];
	$out['user_exp']=(intval((mktime(0,0,0,date('j'),date('n'),date('Y'))-$user['ut_date'])/86400)<0?0:intval((mktime(0,0,0,date('j'),date('n'),date('Y'))-$user['ut_date'])/86400));
	
	if ($user['tariff_id']!=3)
	{
		$res=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_metrics,order_src,user_id FROM blog_orders WHERE user_id='.intval($user['user_id']).' and ut_id='.intval($rw['ut_id']));
	}
	else
	{
		$res=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_metrics,order_src,user_id FROM blog_orders WHERE (user_id='.intval($user['user_id']).' and ut_id='.intval($rw['ut_id']).') or ((user_id=0 or user_id='.intval($user2).') and ut_id='.intval($ut2).')');
	}
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
		$out['user_money']=intval($user['user_money']);
		$out['user_email']=$user['user_email'];
		$out['user_priv']=$user['user_priv'];
		$out['user_exp']=(intval((mktime(0,0,0,date('j'),date('n'),date('Y'))-$user['ut_date'])/86400)<0?0:intval((mktime(0,0,0,date('j'),date('n'),date('Y'))-$user['ut_date'])/86400));
		$out['orders'][$i]['keyword']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
		$out['orders'][$i]['id']=intval($row['order_id']);
		//$out['orders'][$i]['posts']=formatint(intval($coll));
		$out['orders'][$i]['id']=intval($row['order_id']);
		$out['orders'][$i]['posts']=intval($coll);
		$out['orders'][$i]['src']=formatint(intval($res_count));
		//$out['orders'][$i]['value']=formatint(intval($metrics['value']));
		$out['orders'][$i]['value']=intval($metrics['value']);
		//$out['orders'][$i]['start']=date('d.m.Y',$row['order_start']);
		$out['orders'][$i]['start']=$row['order_start'];
		$out['orders'][$i]['end']=date('d.m.Y',$row['order_end']);
		if ($row['order_last']>time()) $row['order_last']=time();
		$out['orders'][$i]['ready']=$row['third_sources']<=1?(($row['order_last']==0)?false:date('d.m.Y',$row['order_last'])):date('d.m.Y',$row['third_sources']);
		if ($row['user_id']==0) $out['orders'][$i]['ready']=false;
		$out['orders'][$i]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
		//$out['orders'][$i]['din_posts']=($metrics['d_post']>0)?'+'.(intval($metrics['d_post']/$coll*100)).'%':(intval($metrics['d_post']/$coll*100)).'%';
		$out['orders'][$i]['din_posts']=($metrics['d_post']>0)?'+'.((intval($metrics['d_post']/$coll*100)>999?999:intval($metrics['d_post']/$coll*100))).'%':((intval($metrics['d_post']/$coll*100)>999?999:intval($metrics['d_post']/$coll*100))).'%';
		$out['orders'][$i]['din_src']=($metrics['d_src']>0)?'+'.((intval($metrics['d_src']/$res_count*100)>999?999:intval($metrics['d_src']/$res_count*100))).'%':((intval($metrics['d_src']/$res_count*100)>999?999:intval($metrics['d_src']/$res_count*100))).'%';
		$out['orders'][$i]['div_value']=($metrics['d_aud']>0)?'+'.((intval($metrics['d_aud']/$metrics['value']*100)>999?999:intval($metrics['d_aud']/$metrics['value']*100))).'%':((intval($metrics['d_aud']/$metrics['value']*100)>999?999:intval($metrics['d_aud']/$metrics['value']*100))).'%';
		$out['orders'][$i]['din_posts']=intval($metrics['d_post']).'%';
		if (intval($coll)==0)
		{
			//$out['orders'][$i]['ready']=false;
		}
		//$out['orders'][$i]['div_value']=($metrics['d_aud']>0)?'+'.(intval($metrics['d_aud']/$metrics['value']*100)).'%':(intval($metrics['d_aud']/$metrics['value']*100)).'%';
		
		$i++;
	}
	if ($_POST['sort']=='default')
	{
		usort($out['orders'], 'startsort');
	}
	elseif ($_POST['sort']=='time')
	{
		usort($out['orders'], 'idsort');
	}
	elseif ($_POST['sort']=='post')
	{
		usort($out['orders'], 'postssort');
	}
	elseif ($_POST['sort']=='value')
	{
		usort($out['orders'], 'valuesort');
	}
	elseif ($_POST['sort']=='alpha')
	{
		usort($out['orders'], 'kwsort');
	}
	else
	{
		usort($out['orders'], 'valuesort');
	}
	//usort($out['orders'], $_POST['sort']);
	
	$out['av_order']=intval(($rw['tariff_quot']-$i)<0?0:($rw['tariff_quot']-$i));
}

if ($user['tariff_id']==3)
{
	$out['av_order']=9-$i;
	if ($out['av_order']<0) $out['av_order']=0;
}

foreach ($out['orders'] as $i => $item)
{
	$out['orders'][$i]['value']=formatint(intval($out['orders'][$i]['value']));
	$out['orders'][$i]['start']=date('d.m.Y',$out['orders'][$i]['start']);
	$out['orders'][$i]['posts']=formatint(intval($out['orders'][$i]['posts']));
}
if (($out['tarif_exp']==0)||($user['user_active']==3))
{
	$out['tarif_exp']='Аккаунт заблокирован';
	$out['orders']=array();
}
if ($user['tariff_id']==4)
{
	$out['av_order']=0;
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
//$out['tarif_exp']=5;
echo json_encode($out);
?>