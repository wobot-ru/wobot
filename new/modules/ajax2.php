<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');
//$fpizdec = fopen('debug.txt', 'a');
//fwrite($fpizdec,$_POST['order_id']." ".$_POST['fav']."\n");
auth();
if (!$loged) die();

if (intval($_POST['order_id'])!=0)
{
	if ($_POST['tag']!='')
	{
		$mton=explode(' ',$_POST['tag']);
		//echo $mton[1];
		switch ($mton[1]) {
		    case 'goodton':
		    	$t=1;
		        break;
		    case 'neuton':
		    	$t=0;
		        break;
		    case 'badton':
		    	$t=-1;
		        break;
		    default;
		        $t=0;
		    break;
		}
		//echo $t;
		if (strpos($_POST['tag'],'bright')==false)
		{
			$t=0;
		}	
		//echo 'UPDATE blog_post SET post_nastr='.$t.' WHERE post_id='.intval($_POST['post_id']);
		$db->query('UPDATE blog_post SET post_nastr='.$t.' WHERE post_id='.intval($_POST['post_id']));
		//print_r($_POST);
	}
	if ($_POST['ton']!='')
	{
		//print_r($_POST);
		if (strpos($_POST['ton'],'bright')==false)
		{
			$t=0;
		}
		else
		{
			$t=1;
		}
		//echo 'UPDATE blog_post SET post_fav='.$t.' WHERE post_id='.intval($_POST['post_id']);
		$db->query('UPDATE blog_post SET post_fav='.$t.' WHERE post_id='.intval($_POST['post_id']));
	}
	if ($_POST['spam']!='')
	{
		//print_r($_POST);
		if ($_POST['idt']=='undefined')
		{
			$chh=explode('|',$_POST['spam']);
			if ($chh[2]=='true')
			{
				$db->query('UPDATE blog_post SET post_spam=1 WHERE post_host=\''.$_POST['hn'].'\' AND order_id='.intval($_POST['order_id']));
			}
			if ($chh[1]=='true')
			{
				if (intval($_POST['atr'])!=0)
				{
					$db->query('UPDATE blog_post SET post_spam=1 WHERE blog_id='.intval($_POST['atr']).' AND order_id='.intval($_POST['order_id']));
					echo 'reload';
				}
			}
			if ($chh[0]=='true')
			{
				$db->query('UPDATE blog_post SET post_spam=1 WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']));
				echo 'reload';
			}
		}
		else
		{
			if (mb_substr($_POST['idt'],0,5,'UTF-8')=='spamm')
			{
				$regex='/\_(?<id>[0-9]*)/is';
				preg_match_all($regex,$_POST['idt'],$out);
				$db->query('UPDATE blog_post SET post_spam=0 WHERE post_id='.intval($out['id'][0]).' AND order_id='.intval($_POST['order_id']));
			}
		}
	}
	if (isset($_POST['taghtml']))
	{
		$mtt=json_decode(urldecode($_POST['tagsall']),true);
		print_r($mtt);
		echo $_POST['taghtml'].' ';
		echo $_POST['post_id'].' '.$_POST['order_id'].' ';
		$tt=explode(', ',$_POST['taghtml']);
		$zap='';
		foreach ($tt as $item)
		{
			$strtag.=$zap.$mtt[$item]['id'];
			$zap=',';
		}
		echo $strtag;
		echo 'UPDATE blog_post SET post_tag=\''.$strtag.'\' WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']);
		$db->query('UPDATE blog_post SET post_tag=\''.$strtag.'\' WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']));
	}
	if (isset($_POST['nameaddtag']))
	{
		$query='SELECT * FROM blog_tag WHERE user_id='.$_POST['user_id'];
		//echo $query;
		$mtt=array('1','2','3','4','5','6','7','8','9','10');
		$respost=$db->query($query);
		while ($rpp=$db->fetch($respost))
		{
			$mtt2[]=$rpp['tag_tag'];
		}
		print_r($mtt2);
		foreach ($mtt as $item)
		{
			if (!in_array($item,$mtt2))
			{
				$mtt3[]=$item;
			}
		}
		$query='INSERT INTO blog_tag (user_id,tag_name,tag_tag) VALUES ('.$_POST['user_id'].',\''.$_POST['nameaddtag'].'\','.$mtt3[0].')';
		$respost=$db->query($query);
	}
	if (isset($_POST['namedeltag']))
	{
		$mtt=json_decode(urldecode($_POST['tagsall']),true);
		$mast=explode(' ',$_POST['namedeltag']);
		foreach ($mast as $item)
		{
			if (($item!=' ') && ($item!=''))
			{
				$num[]=$item;
			}
		}
		//print_r($num);
		//print_r($mtt);
		foreach ($num as $item)
		{
			$ids[]=$mtt[$item]['id'];
		}
		print_r($ids);
		$res=$db->query('SELECT * FROM blog_orders WHERE user_id='.$_POST['user_id']);
		while ($row = $db->fetch($res)) 
		{
			$udids.='order_id='.$row['order_id'].' OR ';
		}
		$udids=substr($udids,0,strlen($udids)-4);
		foreach ($ids as $ttg)
		{
			$db->query('UPDATE blog_post SET post_tag=REPLACE(post_tag, \''.$ttg.'\', \'\') WHERE '.$udids);
			//echo 'UPDATE blog_post SET post_tag=REPLACE(post_tag, \''.$ttg.'\', \'\') WHERE '.$udids;
			$del_text.='tag_tag = '.intval($ttg).' OR ';
		}
		$del_text=substr($del_text,0,strlen($del_text)-4);
		$query='DELETE FROM blog_tag WHERE user_id='.intval($_POST['user_id']).' AND ('.$del_text.')';
		$db->query($query);
		//echo $query;
	}
	if (isset($_POST['nameedittag']))
	{
		//print_r($_POST);
		$mtt=json_decode(urldecode($_POST['tagsall']),true);
		$edt=explode('|',$_POST['nameedittag']);
		foreach ($edt as $item)
		{
			$regex='/(?<tag>.*?)tag\_(?<id>\d+)/is';
			preg_match_all($regex,$item,$out);
			//print_r($out);
			
			if (($out['tag'][0]!='') && ($out['id'][0]!=''))
			{
				$medt[$out['tag'][0]]=$out['id'][0];
			}
		}
		//print_r($medt);
		foreach ($medt as $key => $item)
		{
			if (($key!=' ') && ($key!=''))
			{
				$key=mb_substr($key,0,mb_strlen($key,'UTF-8')-1,'UTF-8');
				//echo $mtt[$key]['name'].' '.$key.'|';
				if (!isset($mtt[$key]['id']))
				{
					//echo 'UPDATE blog_tag SET tag_name=\''.$key.'\' WHERE user_id='.intval($_POST['user_id']).' AND tag_tag='.intval($item);
					$db->query('UPDATE blog_tag SET tag_name=\''.$key.'\' WHERE user_id='.intval($_POST['user_id']).' AND tag_tag='.intval($item));
				}
			}
		}
	}
	if (isset($_POST['ful_post']))
	{
		//print_r($_POST);
		//echo 'SELECT * FROM blog_full_com WHERE ful_com_post_id='.$_POST['post_id'].' AND ful_com_order_id='.$_POST['order_id'];
		$kword=urldecode($_POST['keyword']);
		//echo $kword;
		$kword=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$kword);
		$kword=preg_replace('/[ ]+/isu',' ',$kword);
		$kword=explode(' ',$kword);
		$ressec=$db->query('SELECT * FROM blog_full_com WHERE ful_com_post_id='.$_POST['post_id'].' AND ful_com_order_id='.$_POST['order_id']);
		while($blog=$db->fetch($ressec))
		{
			$text=preg_replace('/<iframe.*?>/','',preg_replace('/\s/is',' ',$blog['ful_com_post']));
			/*foreach ($kword as $item)
			{
				if (($item!='') && ($item!=' '))
				{
					$regex1='/\s(?<str>.{1,100}'.mb_strtolower($item,"UTF-8").'.{1,200})[\.\s]/isu';
					$regex='/\.(?<str>.*?'.mb_strtolower($item,"UTF-8").'.*?)\./isu';
					//echo $regex;
					preg_match_all($regex,$text,$out);
					preg_match_all($regex1,$text,$out1);
					//echo $out['str'][0].'<br>';
					//print_r($out);
					foreach ($out['str'] as $kk)
					{
						$outmm[]=$kk;
					}
					foreach ($out1['str'] as $kk)
					{
						$outmm1[]=$kk;
					}
				}
			}
			foreach ($outmm as $itt)
			{
				if ($itt!='')
				{
					$tf=$itt;
					break;
				}
			}
			foreach ($outmm1 as $itt)
			{
				if ($itt!='')
				{
					$tf1=$itt;
					break;
				}
			}
			//$text=$outmm[0].'<br><br><br><br><br><br><br>'.$outmm1[0];
			//$text=$tf.'<br><br><br><br><br><br><br>'.$tf1;
			if ($tf>$tf1)
			{
				$text=$tf;
			}
			else
			{
				$text=$tf1;
			}
			if ($text=='')
			{
				$text=urldecode($_GET['cont']);
			}*/
			foreach ($kword as $item)
			{
				if (($item!='') && ($item!=' ') && (mb_strlen($item)>2))
				{
					$text=preg_replace('/('.mb_strtolower(urldecode($item),"UTF-8").')/isu', '<span class="keyword bold jrcRounded" style="position: relative; ">$1</span>', $text);
				}
			}
		}
		echo $text;
	}
	if (isset($_POST['st']) && isset($_POST['et']))
	{
		//echo 'gg';
		$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
		$t1=strtotime($_POST['st']);
		$d1=date('d',$t1);
		$m1=date('m',$t1);
		$y1=date('Y',$t1);
		$t2=strtotime($_POST['et']);
		$d2=date('d',$t2);
		$m2=date('m',$t2);
		$y2=date('Y',$t2);
		//echo $d1.' '.$m1.' '.$y1.' '.$d2.' '.$m2.' '.$y2;
		while($blog=$db->fetch($ressec))
		{
			$gr=json_decode($blog['order_graph'],true);
			//print_r($gr['all']);
			foreach ($gr['all'] as $key => $year)
			{
				foreach ($year as $key1 => $mon)
				{
					foreach ($mon as $key2 => $day)
					{
						foreach ($day as $key3 => $item)
						{
							//echo $key1.' '.$key2.' '.$key3;
							//if ((($key1>=$y1) && ($key1<=$y2)) && (($key2>=$m1) && ($key2<=$m2)) && (($key3>=$d1) && ($key3<=$d2)))
							{
								$mt[$key1][$key2][$key3]+=$item;
							}
						}
					}
				}
			}
		}
		//echo json_encode($mt);
		/*foreach ($mt as $key => $item)
		{
			foreach ($item as $key1 => $item1)
			{
				foreach ($item1 as $key2 => $item2)
				{
					$mt2[]=$item2;
				}
			}
		}*/
		for($t=$t1;$t<=$t2;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			$mt2[] = intval($mt[date('Y',$t)][date('n',$t)][date('j',$t)]);
		}
		echo preg_replace('/\s/isu','',json_encode($mt2));
	}
}
?>