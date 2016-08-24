<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/new/com/porter.php');

//error_reporting(0);

date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
//print_r($_SESSION);
$word_stem=new Lingua_Stem_Ru();
//$msg=$word_stem->stem_word('бдбд');
//echo $msg;
//die();
//print_r($_POST);
//$_POST=$_GET;
auth();

//echo $loged;
//if (!$loged) die();
if ((!$loged) && ($user['tariff_id']==3)) die();
set_log('comment',$_POST);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//session_destroy();
//print_r($_SESSION);
if ($user['tariff_id']==3)
{
	$user['user_id']=61;
}
if ($_POST['perpage']=='null')
{
	$_POST['perpage']=10;
}

{
	foreach ($_POST as $key => $item)
	{
		//echo substr($key, 0, 4).' ';
		if ((substr($key, 0, 4)=='res_'))
		{
			$resorrr[]=str_replace("_",".",substr($key,4));
		}
        if (($key=='location') && ($item!='')) $loc=explode(',', $item);
        if (($key=='cou') && ($item!=''))
        {
            $mcou=explode(',', $item);
            foreach ($mcou as $kmcou => $imcou)
            {
                foreach ($wobot['destn3'] as $kdest => $idest)
                {
                    if ($imcou==$idest) $loc[]=$kdest;
                }
            }
        }
        if (($key=='res') && ($item!='')) $resorrr=explode(',', $item);
        if (($key=='shres') && ($item!='')) $short_resorrr=explode(',', $item);
		if ((substr($key, 0, 4)=='loc_'))
		{
			if (isset($wobot['destn2'][str_replace('_',' ',substr($key, 4))]))
			{
				$loc[]=str_replace('_',' ',substr($key,4));
			}
			if (substr($key, 4)=='не_определено')
			{
				$loc[]='na';
			}
		}
		if ((substr($key, 0, 4)=='tag_'))
		{
			//$tags[]=str_replace("_",".",substr($key,5));
			$tags[]=intval(substr($key,4));
			//echo $key;
		}
		if ((substr($key, 0, 5)=='word_'))
		{
			$addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
			$word[]=str_replace("_",".",substr($key,5));
		}
		if ((substr($key, 0, 3)=='mw_'))
		{
			$addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
			$word[]=str_replace("_",".",substr($key,3));
		}
		if ((substr($key, 0, 4)=='mew_'))
		{
			$addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
			$eword[]=str_replace("_",".",substr($key,4));
		}
		if ((substr($key, 0, 6)=='speak_') && (substr($key,7,11)!='link'))
		{
			$speak[str_replace("_",".",substr($key,6))]=$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
			$speakid[str_replace("_",".",substr($key,6))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
		if ((substr($key, 0, 5)=='prom_') && (substr($key,6,10)!='link'))
		{
			$prom[str_replace("_",".",substr($key,5))]=$_POST['prom_link_'.str_replace("_",".",substr($key,5))];
			$speakid[str_replace("_",".",substr($key,5))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
	}
	//print_r($word);
	//print_r($loc);
	$order_info=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	$order=$db->fetch($order_info);
	$orderkw=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$order['order_keyword']);
	$mkw=explode(' ',$orderkw);
	foreach ($mkw as $item)
	{
		if (mb_strlen($word_stem->stem_word($item),'UTF-8')>=3)
		{
			$yet[$word_stem->stem_word($item)]=1;
		}
	}
	$metrics=json_decode($order['order_metrics']);
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']));
	while ($tg=$db->fetch($tags_info))
	{
		$d_tags[$tg['tag_tag']]=$tg['tag_name'];
		$d_astags[$tg['tag_name']]=$tg['tag_tag'];
	}
	$qw='SELECT post_id FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id '.$addjoin.' WHERE order_id='.$_POST['order_id'].' AND post_time>='.strtotime($_POST['stime']).' AND post_time<'.(mktime(0,0,0,date('n',strtotime($_POST['etime'])),date('j',strtotime($_POST['etime']))+1,date('Y',strtotime($_POST['etime'])))).' ';
	if ($_POST['positive']=='true')
	{
		$mton[]=1;
		//$qw.='AND (p.post_nastr=1';
	}
	if ($_POST['negative']=='true')
	{
		$mton[]=-1;		
		//$qw.=' OR p.post_nastr=-1';
	}
	if ($_POST['neutral']=='true')
	{
		$mton[]=0;		
		//$qw.=' OR p.post_nastr=0)';
	}
	if ($_POST['undefined']=='true')
	{
		$mton[]=2;		
		//$qw.=' OR p.post_nastr=0)';
	}
	//print_r($mton);
	$or='';
	if (count($mton)!=0)
	{
		$qw.='AND (';
		foreach ($mton as $item)
		{
			$qw.=$or.'p.post_nastr='.$item;
			$or=' or ';
		}
		$qw.=')';
	}
	if (strlen($wh1)) $wh.='('.$wh1.')';
	$or='';
    if (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($_POST['hosts']=='selected'))
    {
        $or='';
        $wh1=' AND (';
        foreach ($resorrr as $item)
        {
            $wh1.=$or.' p.post_host=\''.$item.'\'';
            $or=' OR ';
        } 
        foreach ($short_resorrr as $item)
        {
            if (trim($item)=='') continue;
            $wh1.=$or.' p.post_host LIKE \''.$item.'%\'';
            $or=' OR ';
        } 
        $wh1.=')';
        $qw.=$wh1;
    }
    elseif (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($_POST['hosts']=='except'))
    {
        $or='';
        $wh1=' AND (';
        foreach ($resorrr as $item)
        {
            $wh1.=$or.' p.post_host!=\''.$item.'\'';
            $or=' AND ';
        } 
        foreach ($short_resorrr as $item)
        {
            if (trim($item)=='') continue;
            $wh1.=$or.' p.post_host NOT LIKE \''.$item.'%\'';
            $or=' AND ';
        } 
        $wh1.=')';
        $qw.=$wh1;
    }
	if (count($loc)!=0)
	{
		$or='';
		$wh1=' AND (';
		foreach ($loc as $item)
		{
			if ($item=='na')
			{
				$wh1.=$or.' b.blog_location=\'\'';
				$or=' OR ';
			}
			else
			{
				if (isset($wobot['destn2'][$item]))
				{
					$wh1.=$or.' b.blog_location=\''.$wobot['destn2'][$item].'\'';
					$or=' OR ';
				}
			}
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	$or='';
	if (count($tags)!=0)
	{
		if ($_POST['tags']!='all')
		{
			$wh1.=' AND (';
			foreach ($tags as $item)
			{	if ($_POST['tags']=='selected')
				{
					$wh1.=$or.'(FIND_IN_SET(\''.$item.'\',post_tag)>0)';
					$or=' OR ';
				}
				else
				if ($_POST['tags']=='except')
				{
					$wh1.=$or.'(FIND_IN_SET(\''.$item.'\',post_tag)=0)';
					$or=' AND ';
				}
				//if ($item!='без_тегов')
				//{
					//$wh.=$or.'(FIND_IN_SET(\''.$d_astags[$item].'\',post_tag)>0)';
				//}
				//else
				/*{
					$wh.=$or.'(post_tag = \'\')';
					$or=' OR ';
				}*/
			}
			$wh1.=')';
			$qw.=$wh1;
		}
	}
	switch ($_POST['post_type']) {
	    case 'fav':
	        $qw.=' AND (p.post_fav=1)';
	        break;
	    case 'nospam':
        	$qw.=' AND (p.post_spam!=1)';
	        break;
	    case 'spam':
        	$qw.=' AND (p.post_spam=1)';
	        break;
	}
	/*$or='';
	if (count($speak)!=0)
	{
		$qw.=' AND (';
		foreach ($speak as $key => $item)
		{
			if ($_POST['Speakers']=='selected')
			{
				$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
				$or=' OR ';
			}
			else
			if ($_POST['Speakers']=='except')
			{
				$qw.=$or.'(b.blog_nick!=\''.$key.'\' AND b.blog_link!=\''.$item.'\')';
				$or=' AND ';
			}
		}
		$qw.=')';
	}*/
	$or='';
	if ((count($speakid)!=0) && ($_POST['Promotions']!='all'))
	{
		$qw.=' AND (';
		foreach ($speakid as $key => $item)
		{
			if ($key!=0)
			{
				if ($_POST['Promotions']=='selected')
				{
					//$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
					$qw.=$or.'(b.blog_id=\''.$key.'\')';
					$or=' OR ';
				}
				else
				if ($_POST['Promotions']=='except')
				{
					$qw.=$or.'(IFNULL(b.blog_id,0)!=\''.$key.'\')';
					$or=' AND ';
				}
			}
		}
		$qw.=')';
	}
	$or='';
	if (count($word)!=0)
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			$qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
			$qw.=' OR (LOWER(f.ful_com_post) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR f.ful_com_post LIKE "'.$item.'%")';
			$or=' OR ';
		}
		$qw.=')';
	}
	$or='';
	if (count($eword)!=0)
	{
		$qw.=' AND (';
		foreach ($eword as $key => $item)
		{
			$qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND p.post_content NOT LIKE "'.$item.'%")';
			$qw.=' AND (LOWER(f.ful_com_post) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND f.ful_com_post NOT LIKE "'.$item.'%")';
			$or=' AND ';
		}
		$qw.=')';
	}
	if ($_POST['gender']=='м')
	{
		$qw.=' AND b.blog_gender=2';
	}
	else
	if ($_POST['gender']=='ж')
	{
		$qw.=' AND b.blog_gender=1';
	}
	if ($_POST['age_min']!=null)
	{
		$qw.=' b.blog_age>'.intval($_POST['age_min']);
	}
	if ($_POST['age_max']!=null)
	{
		$qw.=' b.blog_age<'.intval($_POST['age_max']);
	}
	if ((count($word)!=0) && ($_POST['words']!='all'))
	{
		/*switch ($_POST['sort']) {
		    case 'date':
		        $qw.=' ORDER BY p.post_time DESC';
		        break;
		    case 'audience':
		        $qw.=' ORDER BY b.blog_readers DESC';
		        break;
		    case 'eng':
		        $qw.=' ORDER BY p.post_engage DESC';
		        break;
		    default:
		       $qw.=' ORDER BY p.post_time DESC';
		}*/
		switch ($_POST['sort']) {
		    case 'date':
		        $qw.=' ORDER BY p.post_time DESC';
		        break;
		    case 'audience':
		        $qw.=' ORDER BY b.blog_readers DESC';
		        break;
		    case 'eng':
		        $qw.=' ORDER BY p.post_engage DESC';
		        break;
		    default:
		       $qw.=' ORDER BY p.post_time DESC';
		}
	}
	else
	{
		switch ($_POST['sort']) {
		    case 'date':
		        $qw.=' ORDER BY p.post_time DESC';
		        break;
		    case 'audience':
		        $qw.=' ORDER BY b.blog_readers DESC';
		        break;
		    case 'eng':
		        $qw.=' ORDER BY p.post_engage DESC';
		        break;
		    default:
		       $qw.=' ORDER BY p.post_time DESC';
		}
	}
	//$qw.=' ORDER BY p.post_time DESC LIMIT 10';
	//print_r($tags);
	//echo $qw;
	$posts=$db->query($qw);
	$outmas['count']=$db->num_rows($posts);
	echo json_encode($outmas);
}

?>