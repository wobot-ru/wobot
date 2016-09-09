<?

date_default_timezone_set ( 'Europe/Moscow' );

/*$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
error_reporting(0);*/

function get_linear_data2($order_id,$start,$end,$step,$ytype)
{
	global $redis;
	$mode=($order_id[0]=='s'?'sub':'');
	if ($order_id[0]=='s') $order_id=substr($order_id, 1);
	$real_start=$start;
	$start+=((($end-$start)/86400) % $step)*86400+$step*86400;
	//echo $start.' '.$end;
	for($globalt=$start-($step*86400);$globalt<=$end;$globalt=mktime(0,0,0,date("n",$globalt),date("j",$globalt)+$step,date("Y",$globalt)))
	{
		for ($t=$globalt-86400*($step-1);$t<=$globalt;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			if ($t<$real_start) continue;
			//echo $t.' '.date('r',$t)."\n";
			$var=$redis->get($mode.'order_'.$order_id.'_'.$t);
			$m_dinams=json_decode($var,true);
			//print_r($m_dinams);
			if ($ytype=='post_count') $out[$globalt]+=$m_dinams['count_post'];			
			if ($ytype=='author_count') $out[$globalt]+=count($m_dinams['value2']);			
			if ($ytype=='value') 
			{
				$value=0;
				foreach ($m_dinams['value2'] as $k => $i)
				{
					foreach ($i as $key => $item)
					{
						$value+=$item;
					}
				}
				$out[$globalt]+=$value;			
			}
			if ($ytype=='engage')
			{
				$out[$globalt]+=$m_dinams['eng'];
			}
			if ($ytype=='retweet')
			{
				$out[$globalt]+=$m_dinams['retweet'];
			}
			if ($ytype=='likes')
			{
				$out[$globalt]+=$m_dinams['likes'];
			}
			if ($ytype=='comment')
			{
				$out[$globalt]+=$m_dinams['comment'];
			}
		}
	}
	return $out;
}

function get_linear_data($order_id,$start,$end,$step,$ytype,$xtype)
{
	global $wobot,$db;
	//echo $step.' '.$ytype.' '.$xtype;
	// $step=1/24;
	// $ytype='comment';
	// $xtype='post_host';
	$m_split['post_host']='post_host';
	$m_split['blog_location']='blog_location';
	$m_split['blog_gender']='blog_gender';
	$m_ytype['post_count']='post_id';
	$m_ytype['author_count']='b.blog_id';
	$m_ytype['value']='blog_readers,b.blog_id';
	$m_ytype['engage']='post_engage';
	$m_ytype['retweet']='post_advengage';
	$m_ytype['comment']='post_advengage';
	$m_ytype['likes']='post_advengage';
	if ($order_id[0]=='s') 
	{
		$suborder_id=substr($order_id, 1);
		$qsubord=$db->query('SELECT order_id,subtheme_settings FROM blog_subthemes WHERE subtheme_id='.$suborder_id.' LIMIT 1');
		$suborder=$db->fetch($qsubord);
		$order_id=$suborder['order_id'];
		$addquery=get_subquery(json_decode($suborder['subtheme_settings'],true));
	}
	$qw='SELECT '.($m_split[$xtype]!=''?$m_split[$xtype]:'post_host').','.($m_ytype[$ytype]!=''?$m_ytype[$ytype]:'post_id').',p.post_time FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id WHERE order_id='.$order_id.' AND p.post_time>='.$start.' AND p.post_time<'.$end.' '.$addquery.' ORDER BY post_time DESC';
	//echo $qw;
	$qdata=$db->query($qw);
	while ($data=$db->fetch($qdata))
	{
		switch ($xtype)
		{
			case 'post_host':
				$index=$data['post_host'];
				break;
			case 'blog_location':
				$index=$wobot['destn1'][$data['blog_location']];
				break;
			case 'blog_gender':
				$index=$data['blog_gender'];
				break;
			case 'time':
				$index=mktime(0,0,0,date('n',$data['post_time']),date('j',$data['post_time']),date('Y',$data['post_time']));
				break;
		    default:
				$index=mktime(0,0,0,date('n',$data['post_time']),date('j',$data['post_time']),date('Y',$data['post_time']));
		}
		switch ($ytype)
		{
			case 'post_count':
				$value=1;
				break;
			case 'author_count':
				$value=(isset($yet_auth[$data['blog_id']])?0:1);
		    	$yet_auth[$data['blog_id']]=1;
		        break;
			case 'value':
		    	$value=(isset($yet_auth_count[mktime(0,0,0,date('n',$data['post_time']),date('j',$data['post_time']),date('Y',$data['post_time']))][$data['blog_id']])?0:$data['blog_readers']);
		    	$yet_auth_count[mktime(0,0,0,date('n',$data['post_time']),date('j',$data['post_time']),date('Y',$data['post_time']))][$data['blog_id']]=1;
		        break;
			case 'engage':
		    	$value=$data['post_engage'];
		        break;
			case 'retweet':
				$eng=json_decode($data['post_advengage'],true);
		    	$value=$eng['retweet'];
		        break;
			case 'likes':
				$eng=json_decode($data['post_advengage'],true);
		    	$value=$eng['likes'];
		        break;
			case 'comments':
				$eng=json_decode($data['post_advengage'],true);
		    	$value=$eng['comment'];
		        break;
		    default:
				$value=1;
		}
		if (intval($value)==0) continue;
		//echo $index.' '.$value.' '.($index==0).'|';
		if ($index=='') continue;
		if (($xtype=='blog_gender') && ($index==0)) continue;
		//echo date('r',($end-intval(($end-$data['post_time'])/(86400*$step))*$step*86400)).' ';
		if (($xtype!='time') && ($xtype!='')) $out[$index]+=$value;
		else $out[($end-(intval(($end-$data['post_time'])/(86400*$step))-1)*$step*86400)]+=$value;
	}
	if (($xtype=='time') || ($xtype==''))
	{
		for ($t=$start-($step*86400);$t<=$end;$t+=$step*86400)
		{
			if (!isset($out[$t])) $out[$t]=0;
		}
	}
	if (($xtype!='') && ($xtype!='post_time')) 
	{
		arsort($out);
		$i=0;
		foreach ($out as $key => $item)
		{
			$i++;
			if ($i>10) break;
			$outmas[$key]=$item;
		}
		return $outmas;
	}
	else 
	{
		for ($t=$end;$t>$start;$t-=$step*86400)
		{
			//if (!isset($out[$t])) $out[$t]=0;
		}
		krsort($out);
	}
	return $out;
}

function get_stack_data2($order_id,$start,$end,$step,$ytype,$split)
{
	global $redis;
	$mode=($order_id[0]=='s'?'sub':'');
	if ($order_id[0]=='s') $order_id=substr($order_id, 1);
	$real_start=$start;
	$start+=((($end-$start)/86400) % $step)*86400+$step*86400;
	//echo $start.' '.$end;
	for($globalt=$start-($step*86400);$globalt<=$end;$globalt=mktime(0,0,0,date("n",$globalt),date("j",$globalt)+$step,date("Y",$globalt)))
	{
		for ($t=$globalt-86400*($step-1);$t<=$globalt;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			if ($t<$real_start) continue;
			$var=$redis->get($mode.'order_'.$order_id.'_'.$t);
			$m_dinams=json_decode($var,true);
			if ($ytype=='engage')
			{
				foreach ($m_dinams['eng_time'] as $key => $item)
				{
					$out[$globalt][$key]+=$item;
					$allsrc[$key]=1;
				}
			}
		}
	}	
	foreach ($out as $key => $item)
	{
		foreach ($item as $k => $i)
		{
			foreach ($allsrc as $kk => $ii)
			{
				if (!isset($out[$key][$kk])) $out[$key][$kk]=0;
			}
		}
	}
	return $out;
}

function get_stack_data($order_id,$start,$end,$step,$ytype,$split,$xtype)
{
	global $db,$wobot;
	//echo $step.' '.$xtype.' '.$ytype.' '.$split;
	// $step=30;
	// $ytype='value';
	// $xtype='post_time';
	// $split='blog_location';
	$m_split['post_host']='post_host';
	$m_split['blog_location']='blog_location';
	$m_split['blog_gender']='blog_gender';
	$m_ytype['post_count']='post_id';
	$m_ytype['author_count']='b.blog_id';
	$m_ytype['value']='blog_readers,b.blog_id';
	$m_ytype['engage']='post_engage';
	$m_ytype['retweet']='post_advengage';
	$m_ytype['comment']='post_advengage';
	$m_ytype['likes']='post_advengage';
	if ($order_id[0]=='s') 
	{
		$suborder_id=substr($order_id, 1);
		$qsubord=$db->query('SELECT order_id,subtheme_settings FROM blog_subthemes WHERE subtheme_id='.$suborder_id.' LIMIT 1');
		$suborder=$db->fetch($qsubord);
		$order_id=$suborder['order_id'];
		$addquery=get_subquery(json_decode($suborder['subtheme_settings'],true));
	}
	$q='SELECT '.($m_split[$xtype]!=''?$m_split[$xtype]:'post_host').','.($m_split[$split]!=''?$m_split[$split]:'post_host').','.($m_ytype[$ytype]!=''?$m_ytype[$ytype]:'post_id').',p.post_time FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id WHERE order_id='.$order_id.' AND p.post_time>='.$start.' AND p.post_time<'.($end+86400).' '.$addquery.' ORDER BY post_time DESC';
	// echo $q;
	// die();
	$qdata=$db->query($q);
	while ($data=$db->fetch($qdata))
	{
		switch ($split) 
		{
		    case 'post_host':
		        $index=$data['post_host'];
		        break;
		    case 'blog_location':
		        $index=$wobot['destn1'][$data['blog_location']];
		        break;
		    case 'blog_gender':
		        $index=$data['blog_gender'];
		        break;
		    default:
				$index=$data['post_host'];
		}
		switch ($xtype)
		{
			case 'post_host':
				$mainindex=$data['post_host'];
				break;
			case 'blog_location':
				$mainindex=$wobot['destn1'][$data['blog_location']];
				break;
			case 'blog_gender':
				$mainindex=$data['blog_gender'];
				break;
			case 'time':
				$mainindex=mktime(0,0,0,date('n',$data['post_time']),date('j',$data['post_time']),date('Y',$data['post_time']));
				break;
		    default:
				$mainindex=mktime(0,0,0,date('n',$data['post_time']),date('j',$data['post_time']),date('Y',$data['post_time']));
		}
		switch ($ytype) 
		{
		    case 'post_count':
		        $value=1;
		        break;
		    case 'author_count':
		    	$value=(isset($yet_auth[$data['blog_id']])?0:1);
		    	$yet_auth[$data['blog_id']]=1;
		        break;
		    case 'value':
		    	$value=(isset($yet_auth[$data['blog_id']])?0:$data['blog_readers']);
		    	$yet_auth[$data['blog_id']]=1;
		        break;
		    case 'engage':
		        $value=$data['post_engage'];
		        break;
		    case 'retweet':
		    	$post_advengage=json_decode($data['post_advengage'],true);
		        $value=$post_advengage['retweet'];
		        break;
		    case 'comment':
		    	$post_advengage=json_decode($data['post_advengage'],true);
		        $value=$post_advengage['comment'];
		        break;
		    case 'likes':
		    	$post_advengage=json_decode($data['post_advengage'],true);
		        $value=$post_advengage['likes'];
		        break;
		    default:
		        $value=1;
		        break;
		}
		if ($index=='') continue;
		if ($mainindex=='') continue;
		if ($value==0) continue;
		if (($xtype=='blog_gender') && ($mainindex==0)) continue;
		//echo date('r',($end-intval(($end-$data['post_time'])/(86400*$step))*$step*86400)).'|';
		if (($xtype!='time') && ($xtype!='')) $out[$mainindex][$index]+=$value;
		else $out[($end-intval(($end-$data['post_time'])/(86400*$step))*$step*86400)][$index]+=$value;
		//$out[$end-intval(($end-$data['post_time'])/($step*86400))*86400*$step][$index]+=$value;
		$indexes[$index]+=$value;
	}
	arsort($indexes);
	//die();
	// echo json_encode($indexes);
	// die();
	foreach ($out as $k => $i)
	{
		foreach ($i as $kk => $ii)
		{
			$j=0;
			foreach ($indexes as $key => $item)
			{
				if ($j<5)
				{
					$outmas[$k][$key]=intval($out[$k][$key]);
				}
				else
				{
					$outmas[$k]['другие']+=intval($out[$k][$key]);
				}
				$j++;
			}
		}
	}
	//$start+=((($end-$start)/86400) % $step)*86400;
	if (($xtype=='time') || ($xtype==''))
	{
		for ($t=$end;$t>=$start;$t-=$step*86400)
		{
			if (!isset($outmas[$t]))
			{
				$i=0;
				foreach ($indexes as $key => $item)
				{
					if ($i<5)
					{
						//echo date('r',$t).' ';
						$outmas[$t][$key]=0;
					}
					$i++;
				}
				$outmas[$t]['другие']=0;
			}
		}
	}
	//echo json_encode($outmas);
	//echo $q;
	return $outmas;
}

function get_pie_data($order_id,$start,$end,$metric,$split)
{
	global $redis;
	$mode=($order_id[0]=='s'?'sub':'');
	if ($order_id[0]=='s') $order_id=mb_substr($order_id, 1, 'UTF-8');
	for ($t=$start;$t<=$end;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		$var=$redis->get($mode.'order_'.$order_id.'_'.$t);
		$m_dinams=json_decode($var,true);
		if ($split=='resources')
		{
			if ($metric=='posts')
			{
				foreach ($m_dinams['src'] as $key => $item)
				{
					$out[$key]+=$item;
				}
			}
		}
	}
	arsort($out);
	$i=0;
	foreach ($out as $key => $item)
	{
		if ($i>8) 
		{
			$sum+=$item;
			continue;
		}
		$outmas[$key]=$item;
		$i++;
	}
	$outmas['другие']=$sum;
	return $outmas;
}

function get_metric($order_id,$start,$end)
{
	global $redis;
	$mode=($order_id[0]=='s'?'sub':'');
	if ($order_id[0]=='s') $order_id=mb_substr($order_id, 1, 'UTF-8');
	for ($t=$start;$t<=$end;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		$var=$redis->get($mode.'order_'.$order_id.'_'.$t);
		$m_dinams=json_decode($var,true);
		$out['count_posts']+=$m_dinams['count_post'];
		foreach ($m_dinams['src'] as $key => $item)
		{
			if (!isset($yetsrc[$key])) $out['count_src']++;
			$yetsrc[$key]=1;
		}
		foreach ($m_dinams['sp_pr'] as $key => $item)
		{
			foreach ($item as $k => $i)
			{
				if ($i['nick']=='') continue;
				if (!isset($yet_prms[$key]))
				{
					$out['value']+=$i['readers'];
				}
				$yet_prms[$key]=1;
			}
		}
		if ($type='eng')
		{
			foreach ($m_dinams['eng_time'] as $key => $item)
			{
				$out['eng']+=$item;
			}
		}
	}
	$out['uniq']=count($yet_prms);
	return $out;
}
//print_r(get_metric(712,mktime(0,0,0,10,1,2012),mktime(0,0,0,11,1,2012)));
//print_r(get_pie_data(712,mktime(0,0,0,10,1,2012),mktime(0,0,0,11,1,2012),'posts','resources'));
//print_r(get_stack_data(712,mktime(0,0,0,10,1,2012),mktime(0,0,0,11,1,2012),7,'engage','post_host'));
//print_r(get_linear_data(712,mktime(0,0,0,10,1,2012),mktime(0,0,0,11,1,2012),7,'author_count'));

?>