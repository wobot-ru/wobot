<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

error_reporting(0);

$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

auth();
set_log('orders',$_POST);
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
    if ($a['name'] == $b['name']) {
        return 0;
    }
    return ($a['name'] > $b['name']) ? 1 : -1;
}
function startsort($a, $b)
{
    if ($a['start'] == $b['start']) {
        return 0;
    }
    return ($a['start'] < $b['start']) ? 1 : -1;
}
function positivesort($a, $b)
{
    if ($a['proc_positive'] == $b['proc_positive']) {
        return 0;
    }
    return ($a['proc_positive'] < $b['proc_positive']) ? 1 : -1;
}
function negativesort($a, $b)
{
    if ($a['proc_negative'] == $b['proc_negative']) {
        return 0;
    }
    return ($a['proc_negative'] < $b['proc_negative']) ? 1 : -1;
}
function neutralsort($a, $b)
{
    if ($a['proc_neutral'] == $b['proc_neutral']) {
        return 0;
    }
    return ($a['proc_neutral'] < $b['proc_neutral']) ? 1 : -1;
}
function undefinedsort($a, $b)
{
    if ($a['proc_not_process'] == $b['proc_not_process']) {
        return 0;
    }
    return ($a['proc_not_process'] < $b['proc_not_process']) ? 1 : -1;
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
	if ($user['tariff_id']!=3)
	{
		//$res=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,user_id FROM blog_orders WHERE user_id='.intval($user['user_id']).' and ut_id='.intval($rw['ut_id']));
		$res=$db->query('SELECT a.order_id,a.order_name,a.order_keyword,a.order_start,a.order_end,a.order_last,a.third_sources,a.user_id,b.folder_id,b.folder_name,c.subtheme_id,c.subtheme_name,d.user_id as sharing_user_id FROM blog_orders as a LEFT JOIN blog_folders as b ON a.folder_id=b.folder_id LEFT JOIN blog_subthemes as c ON a.order_id=c.order_id LEFT JOIN blog_sharing AS d ON a.order_id = d.order_id WHERE (a.user_id='.intval($user['user_id']).' and a.ut_id='.intval($rw['ut_id']).') or ((a.user_id=0 or a.user_id='.intval($user['user_id']).') and a.ut_id='.intval($user['ut_id']).') ORDER BY order_id DESC');
		//echo 'SELECT a.order_id,a.order_name,a.order_keyword,a.order_start,a.order_end,a.order_last,a.third_sources,a.user_id,b.folder_id,b.folder_name,c.subtheme_id,c.subtheme_name FROM blog_orders as a LEFT JOIN blog_folders as b ON a.folder_id=b.folder_id LEFT JOIN blog_subthemes as c ON a.order_id=c.order_id WHERE (a.user_id='.intval($user['user_id']).' and a.ut_id='.intval($rw['ut_id']).') or ((a.user_id=0 or a.user_id='.intval($user['user_id']).') and a.ut_id='.intval($user['ut_id']).')';
		//die();
	}
	else
	{
		$res=$db->query('SELECT a.order_id,a.order_name,a.order_keyword,a.order_start,a.order_end,a.order_last,a.third_sources,a.user_id,b.folder_id,b.folder_name,c.subtheme_id,c.subtheme_name,d.user_id as sharing_user_id FROM blog_orders as a LEFT JOIN blog_folders as b ON a.folder_id=b.folder_id LEFT JOIN blog_subthemes as c ON a.order_id=c.order_id LEFT JOIN blog_sharing AS d ON a.order_id = d.order_id WHERE (a.user_id='.intval($user['user_id']).' and a.ut_id='.intval($rw['ut_id']).') or ((a.user_id=0 or a.user_id='.intval($user2).') and a.ut_id='.intval($ut2).') ORDER BY order_id DESC');
	}
	while ($row = $db->fetch($res)) 
	{
		if ($out['tarif_exp']==0) continue;
		$row['folder_id']=intval($row['folder_id']);
		if (!isset($moffset_orders[$row['folder_id']][$row['order_id']])) $moffset_orders[$row['folder_id']][$row['order_id']]=count($moffset_orders[$row['folder_id']]);
		if (!isset($moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']])) $moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]=count($moffset_sub_orders[$row['folder_id']][$row['order_id']]);
		$var=$redis->get('orders_'.$row['order_id']);
		$m_dinams=json_decode($var,true);
		//$metrics=json_decode($row['order_metrics'],true);
		//$mas_res=json_decode($row['order_src'],true);
		$res_count=count($mas_res);
		$coll=0;
		foreach ($mas_res as $ind => $item)
		{
			$coll+=$item;
		}
		if (intval($row['folder_id'])!='')
		{
			if (!isset($moffset[$row['folder_id']])) $moffset[$row['folder_id']]=count($moffset);
			if (!isset($massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]))
			{
				$addinfo[$moffset[$row['folder_id']]]['positive']+=intval($m_dinams['nastr']['positive']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['negative']+=intval($m_dinams['nastr']['negative']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['neutral']+=intval($m_dinams['nastr']['neutral']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['undefined']+=intval($m_dinams['nastr']['undefined']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['din_posts']+=$m_dinams['din_posts'];
				$addinfo[$moffset[$row['folder_id']]]['din_value']+=$m_dinams['din_value'];
				$massoc['folders'][$moffset[$row['folder_id']]]['id']=intval($row['folder_id']);
				$massoc['folders'][$moffset[$row['folder_id']]]['name']=$row['folder_name'];
				$massoc['folders'][$moffset[$row['folder_id']]]['src']+=(intval($m_dinams['count_src']));
				$massoc['folders'][$moffset[$row['folder_id']]]['posts']+=(intval($m_dinams['count_post']));
				$massoc['folders'][$moffset[$row['folder_id']]]['value']+=(intval($m_dinams['value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['notes']+=$m_dinams['note_count'];
				$massoc['folders'][$moffset[$row['folder_id']]]['not_process']+=$m_dinams['not_process'];
				$massoc['folders'][$moffset[$row['folder_id']]]['count_orders']++;
				$massoc['folders'][$moffset[$row['folder_id']]]['din_posts']+=(intval($m_dinams['count_post']));
				$massoc['folders'][$moffset[$row['folder_id']]]['din_src']+=(intval($m_dinams['count_src']));
				$massoc['folders'][$moffset[$row['folder_id']]]['din_value']+=(intval($m_dinams['value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_not_process']=20;
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_positive']=10;
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_negative']=50;
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_neutral']=20;
			}
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['name']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['id']=intval($row['order_id']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['posts']=(intval($m_dinams['count_post']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['src']=(intval($m_dinams['count_src']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['value']=(intval($m_dinams['value']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['notes']=$m_dinams['note_count'];
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['not_process']=$m_dinams['not_process'];
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['start']=date('d.m.Y',$row['order_start']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['end']=date('d.m.Y',$row['order_end']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_not_process']=intval($m_dinams['nastr']['undefined']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_positive']=intval($m_dinams['nastr']['positive']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_negative']=intval($m_dinams['nastr']['negative']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_neutral']=intval($m_dinams['nastr']['neutral']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sharing_count']+=(isset($assoc_sharing[$row['order_id']][$row['sharing_user_id']])||($row['sharing_user_id']=='')?0:1);
			$assoc_sharing[$row['order_id']][$row['sharing_user_id']]=1;
			if (intval($row['subtheme_id'])!=0)
			{
				$subvar=$redis->get('suborders_'.$row['subtheme_id']);
				$m_subdinams=json_decode($subvar,true);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['name']=$row['subtheme_name'];
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['keyword']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['id']=intval($row['subtheme_id']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['posts']=(intval($m_subdinams['count_post']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['src']=(intval($m_subdinams['count_src']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['value']=(intval($m_subdinams['value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['start']=date('d.m.Y',$row['order_start']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['end']=date('d.m.Y',$row['order_end']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['notes']=$m_subdinams['note_count'];
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['not_process']=$m_subdinams['not_process'];
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_value']=($m_subdinams['din_value']>999?'+999':($m_subdinams['din_value']>=0?'+'.$m_subdinams['din_value']:$m_subdinams['din_value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_posts']=($m_subdinams['din_posts']>999?'+999':($m_subdinams['din_posts']>=0?'+'.$m_subdinams['din_posts']:$m_subdinams['din_posts']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_not_process']=intval($m_subdinams['nastr']['undefined']*100/$m_subdinams['count_post']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_positive']=intval($m_subdinams['nastr']['positive']*100/$m_subdinams['count_post']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_negative']=intval($m_subdinams['nastr']['negative']*100/$m_subdinams['count_post']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_neutral']=intval($m_subdinams['nastr']['neutral']*100/$m_subdinams['count_post']);
			}
			if ($row['order_last']==0)
			{
				if ($row['third_sources']==0)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='0';
				}
				elseif ($row['third_sources']==1)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='30';
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
				}
				else
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
				}			
			}
			else
			{
				if ($row['third_sources']==1)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
				}
				else
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',$row['order_last']);
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
				}
				if ($row['order_end']<time())
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',$row['order_last']);
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
				}
			}
			if ($row['user_id']==0) $massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
		}
		else
		{
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['name']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['id']=intval($row['order_id']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['posts']=(intval($m_dinams['count_post']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['src']=(intval($m_dinams['count_src']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['value']=(intval($m_dinams['value']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['notes']=$m_dinams['note_count'];
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['not_process']=$m_dinams['not_process'];
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['start']=date('d.m.Y',$row['order_start']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['end']=date('d.m.Y',$row['order_end']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_not_process']=intval($m_dinams['nastr']['undefined']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_positive']=intval($m_dinams['nastr']['positive']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_negative']=intval($m_dinams['nastr']['negative']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_neutral']=intval($m_dinams['nastr']['neutral']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sharing_count']+=(isset($assoc_sharing[$row['order_id']][$row['sharing_user_id']])||($row['sharing_user_id']=='')?0:1);
			$assoc_sharing[$row['order_id']][$row['sharing_user_id']]=1;
			if (intval($row['subtheme_id'])!=0)
			{
				$subvar=$redis->get('suborders_'.$row['subtheme_id']);
				$m_subdinams=json_decode($subvar,true);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['name']=$row['subtheme_name'];
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['keyword']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['id']=intval($row['subtheme_id']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['posts']=(intval($m_subdinams['count_post']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['src']=(intval($m_subdinams['count_src']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['value']=(intval($m_subdinams['value']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['start']=date('d.m.Y',$row['order_start']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['end']=date('d.m.Y',$row['order_end']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['notes']=$m_subdinams['note_count'];
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['not_process']=$m_subdinams['not_process'];
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_value']=($m_subdinams['din_value']>999?'+999':($m_subdinams['din_value']>=0?'+'.$m_subdinams['din_value']:$m_subdinams['din_value']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_posts']=($m_subdinams['din_posts']>999?'+999':($m_subdinams['din_posts']>=0?'+'.$m_subdinams['din_posts']:$m_subdinams['din_posts']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_not_process']=intval($m_subdinams['nastr']['undefined']*100/$m_subdinams['count_post']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_positive']=intval($m_subdinams['nastr']['positive']*100/$m_subdinams['count_post']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_negative']=intval($m_subdinams['nastr']['negative']*100/$m_subdinams['count_post']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_neutral']=intval($m_subdinams['nastr']['neutral']*100/$m_subdinams['count_post']);
			}
			if ($row['order_last']==0)
			{
				if ($row['third_sources']==0)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='0';
				}
				elseif ($row['third_sources']==1)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='30';
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
				}
				else
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
				}			
			}
			else
			{
				if ($row['third_sources']==1)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',time());
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
				}
				else
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',$row['order_last']);
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
				}
				if ($row['order_end']<time())
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=date('d.m.Y',$row['order_last']);
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
				}
			}
			if ($row['user_id']==0) $massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
		}
	}
	$res=$db->query('SELECT b.order_id,b.order_name,b.order_keyword,b.order_start,b.order_end,b.order_last,b.third_sources,b.user_id,a.folder_id,c.folder_name,d.subtheme_id,d.subtheme_name,f.user_email,f.user_id as owner_id,f.user_name,a.sharing_priv FROM blog_sharing as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN blog_folders as c ON a.folder_id=c.folder_id AND a.user_id=c.user_id LEFT JOIN blog_subthemes as d ON a.order_id=d.order_id LEFT JOIN user_tariff as e ON e.ut_id=b.ut_id LEFT JOIN users as f ON e.user_id=f.user_id WHERE a.user_id='.$user['user_id']);
	//echo 'SELECT b.order_id,b.order_name,b.order_keyword,b.order_start,b.order_end,b.order_last,b.third_sources,b.user_id,a.folder_id,c.folder_name,d.subtheme_id,d.subtheme_name,f.user_email,f.user_id as owner_id,a.sharing_priv FROM blog_sharing as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN blog_folders as c ON a.folder_id=c.folder_id AND a.user_id=c.user_id LEFT JOIN blog_subthemes as d ON a.order_id=d.order_id LEFT JOIN user_tariff as e ON e.ut_id=b.ut_id LEFT JOIN users as f ON e.user_id=f.user_id WHERE a.user_id='.$user['user_id'];
	//echo 'SELECT * FROM blog_sharing as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN blog_folders as c ON b.folder_id=c.folder_id AND a.user_id=c.user_id LEFT JOIN blog_subthemes as d ON a.order_id=d.order_id WHERE a.user_id='.$user['user_id'];
	while ($row = $db->fetch($res)) 
	{
		$row['folder_id']=intval($row['folder_id']);
		if (!isset($moffset_orders[$row['folder_id']][$row['order_id']])) $moffset_orders[$row['folder_id']][$row['order_id']]=count($moffset_orders[$row['folder_id']]);
		if (!isset($moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']])) $moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]=count($moffset_sub_orders[$row['folder_id']][$row['order_id']]);
		//print_r($moffset_sub_orders);
		$var=$redis->get('orders_'.$row['order_id']);
		$m_dinams=json_decode($var,true);
		//$metrics=json_decode($row['order_metrics'],true);
		//$mas_res=json_decode($row['order_src'],true);
		$res_count=count($mas_res);
		$coll=0;
		foreach ($mas_res as $ind => $item)
		{
			$coll+=$item;
		}
		if (intval($row['folder_id'])!=0)
		{
			if (!isset($moffset[$row['folder_id']])) $moffset[$row['folder_id']]=count($moffset);
			if (!isset($massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]))
			{
				$addinfo[$moffset[$row['folder_id']]]['positive']+=intval($m_dinams['nastr']['positive']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['negative']+=intval($m_dinams['nastr']['negative']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['neutral']+=intval($m_dinams['nastr']['neutral']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['undefined']+=intval($m_dinams['nastr']['undefined']*100/$m_dinams['count_post']);
				$addinfo[$moffset[$row['folder_id']]]['din_posts']+=$m_dinams['din_posts'];
				$addinfo[$moffset[$row['folder_id']]]['din_value']+=$m_dinams['din_value'];
				$massoc['folders'][$moffset[$row['folder_id']]]['id']=intval($row['folder_id']);
				$massoc['folders'][$moffset[$row['folder_id']]]['name']=$row['folder_name'];
				$massoc['folders'][$moffset[$row['folder_id']]]['src']+=(intval($m_dinams['count_src']));
				$massoc['folders'][$moffset[$row['folder_id']]]['posts']+=(intval($m_dinams['count_post']));
				$massoc['folders'][$moffset[$row['folder_id']]]['value']+=(intval($m_dinams['value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['notes']+=$m_dinams['note_count'];
				$massoc['folders'][$moffset[$row['folder_id']]]['not_process']+=$m_dinams['not_process'];
				$massoc['folders'][$moffset[$row['folder_id']]]['count_orders']++;
				$massoc['folders'][$moffset[$row['folder_id']]]['din_posts']+=(intval($m_dinams['count_post']));
				$massoc['folders'][$moffset[$row['folder_id']]]['din_src']+=(intval($m_dinams['count_src']));
				$massoc['folders'][$moffset[$row['folder_id']]]['din_value']+=(intval($m_dinams['value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_not_process']=20;
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_positive']=10;
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_negative']=50;
				$massoc['folders'][$moffset[$row['folder_id']]]['proc_neutral']=20;
			}
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['name']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['id']=intval($row['order_id']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['posts']=(intval($m_dinams['count_post']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['src']=(intval($m_dinams['count_src']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['value']=(intval($m_dinams['value']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['notes']=$m_dinams['note_count'];
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['not_process']=$m_dinams['not_process'];
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['start']=date('d.m.Y',$row['order_start']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['end']=date('d.m.Y',$row['order_end']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_not_process']=intval($m_dinams['nastr']['undefined']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_positive']=intval($m_dinams['nastr']['positive']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_negative']=intval($m_dinams['nastr']['negative']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_neutral']=intval($m_dinams['nastr']['neutral']*100/$m_dinams['count_post']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['owner_id']=$row['owner_id'];
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['owner_email']=$row['user_email'];
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['owner_fio']=$row['user_name'];
			$priv=explode(",", $row['sharing_priv']);
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sharing_priv']=max($priv);
			if (intval($row['subtheme_id'])!=0)
			{
				$subvar=$redis->get('suborders_'.$row['subtheme_id']);
				$m_subdinams=json_decode($subvar,true);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['name']=$row['subtheme_name'];
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['keyword']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['id']=intval($row['subtheme_id']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['posts']=(intval($m_subdinams['count_post']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['src']=(intval($m_subdinams['count_src']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['value']=(intval($m_subdinams['value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['start']=date('d.m.Y',$row['order_start']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['end']=date('d.m.Y',$row['order_end']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['notes']=$m_subdinams['note_count'];
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['not_process']=$m_subdinams['not_process'];
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_value']=($m_subdinams['din_value']>999?'+999':($m_subdinams['din_value']>=0?'+'.$m_subdinams['din_value']:$m_subdinams['din_value']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_posts']=($m_subdinams['din_posts']>999?'+999':($m_subdinams['din_posts']>=0?'+'.$m_subdinams['din_posts']:$m_subdinams['din_posts']));
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_not_process']=intval($m_subdinams['nastr']['undefined']*100/$m_subdinams['count_post']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_positive']=intval($m_subdinams['nastr']['positive']*100/$m_subdinams['count_post']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_negative']=intval($m_subdinams['nastr']['negative']*100/$m_subdinams['count_post']);
				$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_neutral']=intval($m_subdinams['nastr']['neutral']*100/$m_subdinams['count_post']);
			}
			if ($row['order_last']==0)
			{
				if ($row['third_sources']==0)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='0';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=false;
				}
				elseif ($row['third_sources']==1)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='30';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				else
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}			
			}
			else
			{
				if ($row['third_sources']==1)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				else
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',$row['order_last']);
				}
				if ($row['order_end']<time())
				{
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
					$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',$row['order_last']);
				}
			}
			if ($row['user_id']==0) $massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['folders'][$moffset[$row['folder_id']]]['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
		}
		else
		{
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['name']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['id']=intval($row['order_id']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['posts']=(intval($m_dinams['count_post']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['src']=(intval($m_dinams['count_src']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['value']=(intval($m_dinams['value']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['notes']=$m_dinams['note_count'];
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['not_process']=$m_dinams['not_process'];
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['start']=date('d.m.Y',$row['order_start']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['end']=date('d.m.Y',$row['order_end']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_not_process']=intval($m_dinams['nastr']['undefined']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_positive']=intval($m_dinams['nastr']['positive']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_negative']=intval($m_dinams['nastr']['negative']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['proc_neutral']=intval($m_dinams['nastr']['neutral']*100/$m_dinams['count_post']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['owner_id']=$row['owner_id'];
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['owner_email']=$row['user_email'];
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['owner_fio']=$row['user_name'];
			$priv=explode(",", $row['sharing_priv']);
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sharing_priv']=max($priv);
			if (intval($row['subtheme_id'])!=0)
			{
				$subvar=$redis->get('suborders_'.$row['subtheme_id']);
				$m_subdinams=json_decode($subvar,true);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['name']=$row['subtheme_name'];
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['keyword']=(($row['order_name']=='')?$row['order_keyword']:$row['order_name']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['id']=intval($row['subtheme_id']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['posts']=(intval($m_subdinams['count_post']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['src']=(intval($m_subdinams['count_src']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['value']=(intval($m_subdinams['value']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['start']=date('d.m.Y',$row['order_start']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['end']=date('d.m.Y',$row['order_end']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['notes']=$m_subdinams['note_count'];
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['not_process']=$m_subdinams['not_process'];
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_value']=($m_subdinams['din_value']>999?'+999':($m_subdinams['din_value']>=0?'+'.$m_subdinams['din_value']:$m_subdinams['din_value']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['din_posts']=($m_subdinams['din_posts']>999?'+999':($m_subdinams['din_posts']>=0?'+'.$m_subdinams['din_posts']:$m_subdinams['din_posts']));
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_not_process']=intval($m_subdinams['nastr']['undefined']*100/$m_subdinams['count_post']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_positive']=intval($m_subdinams['nastr']['positive']*100/$m_subdinams['count_post']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_negative']=intval($m_subdinams['nastr']['negative']*100/$m_subdinams['count_post']);
				$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['sub_orders'][$moffset_sub_orders[$row['folder_id']][$row['order_id']][$row['subtheme_id']]]['proc_neutral']=intval($m_subdinams['nastr']['neutral']*100/$m_subdinams['count_post']);
			}
			if ($row['order_last']==0)
			{
				if ($row['third_sources']==0)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='0';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=false;
				}
				elseif ($row['third_sources']==1)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='30';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				else
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}			
			}
			else
			{
				if ($row['third_sources']==1)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='50';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				elseif ($row['third_sources']==2)
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='70';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',time());
				}
				else
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',$row['order_last']);
				}
				if ($row['order_end']<time())
				{
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=true;
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready_perc']='100';
					$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['last_time']=date('d.m.Y',$row['order_last']);
				}
			}
			if ($row['user_id']==0) $massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['ready']=false;
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['graph']='img/graph/'.$row['order_id'].'_main_2.png';
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_posts']=($m_dinams['din_posts']>999?'+999':($m_dinams['din_posts']>=0?'+'.$m_dinams['din_posts']:$m_dinams['din_posts']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_src']=($m_dinams['din_hn']>999?'+999':($m_dinams['din_hn']>=0?'+'.$m_dinams['din_hn']:$m_dinams['din_hn']));
			$massoc['orders'][$moffset_orders[$row['folder_id']][$row['order_id']]]['din_value']=($m_dinams['din_value']>999?'+999':($m_dinams['din_value']>=0?'+'.$m_dinams['din_value']:$m_dinams['din_value']));
		}
	}
	$qfolders=$db->query('SELECT folder_id,folder_name FROM blog_folders WHERE user_id='.$user['user_id']);
	while ($rfolders=$db->fetch($qfolders))
	{
		if (!isset($moffset[$rfolders['folder_id']])) 
		{
			$moffset[$rfolders['folder_id']]=count($moffset);
		}
		else
		{
			continue;
		}
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['id']=intval($rfolders['folder_id']);
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['name']=$rfolders['folder_name'];
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['src']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['posts']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['value']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['notes']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['not_process']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['count_orders']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['din_posts']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['din_src']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['din_value']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['proc_not_process']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['proc_positive']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['proc_negative']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['proc_neutral']=0;
		$massoc['folders'][$moffset[$rfolders['folder_id']]]['orders']=array();
	}
	//print_r($addinfo);
	foreach ($addinfo as $key => $item)
	{
		$massoc['folders'][$key]['din_posts']=$item['din_posts']/$massoc['folders'][$key]['count_orders'];
		$massoc['folders'][$key]['din_src']=0;
		$massoc['folders'][$key]['din_value']=$item['din_value']/$massoc['folders'][$key]['count_orders'];
		$massoc['folders'][$key]['proc_not_process']=intval($item['undefined']*100/$massoc['folders'][$key]['count_orders']);
		$massoc['folders'][$key]['proc_positive']=intval($item['positive']*100/$massoc['folders'][$key]['count_orders']);
		$massoc['folders'][$key]['proc_negative']=intval($item['negative']*100/$massoc['folders'][$key]['count_orders']);
		$massoc['folders'][$key]['proc_neutral']=intval($item['neutral']/$massoc['folders'][$key]['count_orders']);
	}
	$out=$massoc;

	//usort($out['orders'], $_POST['sort']);
	
	$out['av_order']=intval(($rw['tariff_quot']-$i)<0?0:($rw['tariff_quot']-$i));
}

if ($user['tariff_id']==3)
{
	$out['av_order']=9-$i;
	if ($out['av_order']<0) $out['av_order']=0;
}

/*foreach ($out['orders'] as $i => $item)
{
	$out['orders'][intval($row['folder_id'])][$i]['value']=formatint(intval($out['orders'][intval($row['folder_id'])][$i]['value']));
	$out['orders'][intval($row['folder_id'])][$i]['start']=date('d.m.Y',$out['orders'][intval($row['folder_id'])][$i]['start']);
	$out['orders'][intval($row['folder_id'])][$i]['posts']=formatint(intval($out['orders'][intval($row['folder_id'])][$i]['posts']));
}*/




if (($out['tarif_exp']==0)||($user['user_active']==3))
{
	$out['tarif_exp']='Аккаунт заблокирован';
	//$out['orders']=array();
}
if ($user['tariff_id']==4)
{
	$out['av_order']=0;
}
//$out['tarif_exp']=((30-((mktime(0,0,0,date('n',time()),date('d',time()),date('Y',time()))-$rw['ut_date'])/86400))<0?0:(30-((mktime(0,0,0,date('n',time()),date('d',time()),date('Y',time()))-$rw['ut_date'])/86400)));
//echo mktime(0,0,0,date('n'),date('j'),date('Y')).' '.$user['ut_date'].' '.(mktime(0,0,0,date('n'),date('j'),date('Y'))-$user['ut_date']).' '.intval((mktime(0,0,0,date('n'),date('j'),date('Y'))-$user['ut_date'])/86400);
//die();
$out['user_exp']=(intval(($user['ut_date']-mktime(0,0,0,date('n'),date('j'),date('Y')))/86400)<0?0:intval(($user['ut_date']-mktime(0,0,0,date('n'),date('j'),date('Y')))/86400));
//$out['user_tarif']=$rw['tariff_name'];
//$out['tarif_id']=$rw['tariff_id'];
//$out['user_money']=intval($user['user_money']);
//$out['user_email']=$user['user_email'];
//$out['user_priv']=$user['user_priv'];
$out['user_tarif']=$rw['tariff_name'];
$out['tarif_id']=$user['tariff_id'];
$out['user_money']=intval($user['user_money']);
$out['user_email']=$user['user_email'];
$out['user_priv']=$user['user_priv'];
$out['user_exp']=(intval((mktime(0,0,0,date('j'),date('n'),date('Y'))-$user['ut_date'])/86400)<0?0:intval((mktime(0,0,0,date('j'),date('n'),date('Y'))-$user['ut_date'])/86400));

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
elseif ($_POST['sort']=='positive')
{
	usort($out['orders'], 'positivesort');
}
elseif ($_POST['sort']=='negative')
{
	usort($out['orders'], 'negativesort');
}
elseif ($_POST['sort']=='neutral')
{
	usort($out['orders'], 'neutralsort');
}
elseif ($_POST['sort']=='undefined')
{
	usort($out['orders'], 'undefinedsort');
}
else
{
	usort($out['orders'], 'idsort');
}

//$out['tarif_exp']=5;
//echo json_encode($massoc);
//die();
// echo json_encode($assoc_sharing);
// die();
echo json_encode($out);
?>