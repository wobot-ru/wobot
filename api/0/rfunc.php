<?
// error_reporting(-1);
function get_order_cash($row,$start,$end)
{
	global $redis,$memcache,$wobot,$morphy;
	$_GET['order_id']=$row['order_id'];
	$_GET['start']=$start;
	$_GET['end']=$end;
	$out['order_prev']="";
	$out['order_next']="";
	$out['graph']=array();
	$out['promotions']=array();
	$out['speakers']=array();
	$out['city']=array();
	//$out['city']=array();
	//$out['city'][0]['count']=0;

	/*if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
	{
		if ($row['order_beta']!='')
		{
			$m_dinams=json_decode($row['order_beta'],true);
		}
		else
		{
			//$m = new Memcached();
			//$m->addServer('localhost', 11211);
			//$m->setOption(Memcached::OPT_COMPRESSION, false);
			//$m_dinams=json_decode($m->get(('order_'.$row['order_id'])),true);
			$memcache = memcache_connect('localhost', 11211);
			$m_dinams = json_decode(memcache_get($memcache, 'order_'.$row['order_id']),true);
		}
	}*/
	$out['order_name']=($row['order_name']=='')?$row['order_keyword']:$row['order_name'];

	if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
	{
		//echo 123;
		unset($out['speakers']);
		unset($out['promotions']);
		unset($out['sources']);
		unset($out['city']);
		unset($out['value_mdin']);
		unset($out['eng_mdin']);
		unset($out['words']);
		for($t=strtotime($_GET['start']);$t<=strtotime($_GET['end']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			$var=$redis->get('order_'.$row['order_id'].'_'.$t);
			$m_dinams=json_decode($var,true);
			$out['graph'][$t]=intval($m_dinams['count_post']);
			$count_post_per+=$m_dinams['count_post'];
			$count_positive+=$m_dinams['nastr'][1];
			$count_negative+=$m_dinams['nastr'][-1];
			$count_neutral+=$m_dinams['nastr'][0];
			foreach ($m_dinams['src'] as $key => $item)
			{
				$msrc[$key]+=$item;
			}
			foreach ($m_dinams['words'] as $key => $item)
			{
				$mwrds[$key]+=$item;
			}
			foreach ($m_dinams['geo'] as $key => $item)
			{
				$mas_geo_per[$key]+=$item;
			}
			foreach ($m_dinams['sp_pr'] as $key => $item)
			{
				//echo $key.'<br>';
				foreach ($item as $k => $i)
				{
					if ($i['nick']=='') continue;
					if (!isset($yet_prms[$key]))
					{
						$mvalue[$k]+=$i['readers'];
						$mprm['count'][]=$i['count'];
						$mprm['readers'][]=$i['readers'];
						$mprm['nick'][]=(trim($i['nick'])==''?(trim($i['login'])==''?$key:$i['login']):$i['nick']);
						$mprm['id'][]=$key;
						$count_value_per+=$i['readers'];
					}
					$yet_prms[$key]+=$i['count'];
				}
			}
			foreach ($m_dinams['eng_time'] as $key => $item)
			{
				if ($key!='')
				{
					if ($key=='livejournal.com') 
					{
						$key='Комментарии Livejournal';
						$outper['eng_mdint'][$key]+=$item;
					}
					if (($key=='vkontakte.ru') || ($key=='vk.com')) 
					{
						$key='"Мне нравится" Вконтакте';
						$outper['eng_mdint'][$key]+=$item;
					}
					//if () $key='Нравится Вконтакте';
					if ($key=='facebook.com') 
					{
						$key='Лайки Facebook';
						$outper['eng_mdint'][$key]+=$item;
					}
					if ($key=='twitter.com') 
					{
						$key='Ретвиты Twitter';
						$outper['eng_mdint'][$key]+=$item;
					}
				}
			}
		}
		//print_r($yet_prms);
		array_multisort($mprm['readers'],SORT_DESC,$mprm['count'],SORT_DESC,$mprm['nick'],SORT_DESC,$mprm['id'],SORT_DESC);
		arsort($msrc);
		arsort($mwrds);
		arsort($mas_geo_per);
		arsort($outper['eng_mdint']);
		arsort($mvalue);
		$i=0;
		foreach ($mvalue as $key => $item)
		{
			if ($item==0) continue;
			$out['value_mdin'][$i]['name']=$key;
			$out['value_mdin'][$i]['count']=$item;
			$i++;
		}
		$indks=0;
		foreach ($outper['eng_mdint'] as $key => $item)
		{
			if ($item!=0)
			{
				$out['eng_mdin'][$indks]['name']=$key;
				$out['eng_mdin'][$indks]['count']=$item;
				$count_eng_per+=$item;
				$indks++;
			}
		}
		//echo $count_eng_per;
		//print_r($mprm);
		foreach ($mprm['count'] as $key => $item)
		{
			if ($key==30) break;
			$out['promotions'][$key]['count_posts']=intval($yet_prms[$mprm['id'][$key]]);
			$out['promotions'][$key]['nick']=(mb_strlen($mprm['nick'][$key],'UTF-8')>12?mb_substr($mprm['nick'][$key],0,12,'UTF-8').'..':$mprm['nick'][$key]);
			$out['promotions'][$key]['id']=$mprm['id'][$key];
			$out['promotions'][$key]['count']=intval($mprm['readers'][$key]);
		}
		$i=0;
		foreach ($msrc as $key => $item)
		{
			$out['sources'][$i]['name']=$key;
			$out['sources'][$i]['count']=$item;
			$i++;
		}
		$count_src_per=count($out['sources']);
		$i=0;
		foreach ($mwrds as $key => $item)
		{
			if ($i==50) break;
			// $outmas[$k]['word']=($newword[0]!=null?mb_strtolower($newword[0],'UTF-8'):$out[$key]['word']);			
			$out['words2'][$i]['word']=$key;
			$out['words2'][$i]['count']=$item;
			$i++;
		}
		$i=0;
		//print_r($mas_geo_per);
		foreach ($mas_geo_per as $key => $item)
		{
			if ($key!='')
			{
				$out['city'][$i]['name']=(($key=='')?'Не определено':$key);
				$out['city'][$i]['count']=$item;
				$cd=$wobot['destn2'][$key];
				$mcd=explode(' ',$cd);
				$out['city'][$i]['x']=intval($mcd[0]*100)/100;
				$out['city'][$i]['y']=intval($mcd[1]*100)/100;
				$i++;
			}
		}
		$count_uniq_per=count($mprm['count']);
	}
	$count_word=0;
	foreach ($out['words2'] as $key => $item)
	{
		if ($count_word==30) break;
		$newword=$morphy->lemmatize(mb_strtoupper($item['word'],'UTF-8'), phpMorphy:: NORMAL);
		if (isset($yet_words[$newword[0]])) continue;
		$yet_words[$newword[0]]=1;
		$out['words'][$count_word]['word']=($newword[0]!=null?mb_strtolower($newword[0],'UTF-8'):$item['word']);
		$out['words'][$count_word]['count']=$item['count'];
		$count_word++;
	}
	unset($out['words2']);
	$out['graphtype']='day';
	if ((($row['order_end']-$row['order_start'])>86400*23) && ($_GET['start']=='null') && ($_GET['end']=='null'))
	{
		//echo 123;
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_week($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='week';
	}
	if ((($row['order_end']-$row['order_start'])>86400*167) && ($_GET['start']=='null') && ($_GET['end']=='null'))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_mon($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='month';
	}
	if ((($row['order_end']-$row['order_start'])>86400*730) && ($_GET['start']=='null') && ($_GET['end']=='null'))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_quar($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='quarter';
	}
	if ((($row['order_end']-$row['order_start'])>86400*1825) && ($_GET['start']=='null') && ($_GET['end']=='null'))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_halfy($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='halfyear';
	}
	//print_r($_GET);
	//echo strtotime($_GET['end'])-strtotime($_GET['start']);
	if ((((strtotime($_GET['end'])-strtotime($_GET['start']))>86400*23) && (strtotime($_GET['start'])!=0)))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_week($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		//print_r($mgr);
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='week';
	}
	if ((((strtotime($_GET['end'])-strtotime($_GET['start']))>86400*167) && (strtotime($_GET['start'])!=0)))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_mon($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='month';
	}
	if ((((strtotime($_GET['end'])-strtotime($_GET['start']))>86400*730) && (strtotime($_GET['start'])!=0)))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_quar($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='quarter';
	}
	if ((((strtotime($_GET['end'])-strtotime($_GET['start']))>86400*1825) && (strtotime($_GET['start'])!=0)))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas_halfy($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
		$out['graphtype']='halfyear';
	}
	if (((strtotime($_GET['start'])==strtotime($_GET['end'])) && (strtotime($_GET['start'])!=0)) || ($row['order_start']==$row['order_end']))
	{
		//echo 'gg';
		unset($out['graph']);
		$out['graph']=array();
		$mgr=get_mas($_GET['order_id'],strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start']));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][strtotime($_GET['start'])+($key)*3600]=$item;
			//$count_post_per+=$item;
		}
		$out['graphtype']='hour';
	}

	//$out['graph']='img/graph/'.$row['order_id'].'.png';
	if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
	{
		$out['posts']=formatint($count_post_per);
		$out['src']=formatint(intval($count_src_per));
		$out['value']=formatint(intval($count_value_per));
		$out['uniq']=formatint(intval($count_uniq_per));
		$out['engage']=formatint(intval($count_eng_per));		
	}
	else
	{
		$out['posts']=formatint($coll);
		$out['src']=formatint(intval($res_count));
		$out['value']=formatint(intval($metrics['value']));
		$out['uniq']=formatint(intval($metrics['speakers']['uniq']));
		$out['engage']=formatint(intval($metrics['engagement']));
	}
	if (intval($count_eng_per)>0)
	{
		$out['engage']=1;
	}
	else
	{
		$out['engage']=0;
	}
	//echo $count_eng_per;
	$out['start']=date('d.m.Y',$row['order_start']);
	$out['end']=date('d.m.Y',($row['order_end']==0)?$row['order_last']:($row['order_end']>time()?time():$row['order_end']));
	//$out['start']=date('d.m.Y',time());
	//$out['end']=date('d.m.Y',time());
	$out['posts_dyn']=intval($metrics['d_post']>0?formatint(abs($metrics['d_post'])):'-'.formatint(abs($metrics['d_post'])));
	$out['src_dyn']=intval($metrics['d_src']>0?formatint(abs($metrics['d_src'])):'-'.formatint(abs($metrics['d_src'])));//$metrics['d_src'];
	$out['value_dyn']=intval($metrics['d_aud']>0?formatint(abs($metrics['d_aud'])):'-'.formatint(abs($metrics['d_aud'])));//$metrics['d_aud'];
	$out['engage_dyn']=intval($metrics['d_eng']>0?formatint(abs($metrics['d_eng'])):'-'.formatint(abs($metrics['d_eng'])));//$metrics['d_eng'];
	$out['uniq_dyn']=intval($metrics['d_uniq']>0?formatint(abs($metrics['d_uniq'])):'-'.formatint(abs($metrics['d_uniq'])));//$metrics['d_uniq'];
	$out['cash_update']=date('d.m.Y H:i:s',$row['cash_update']);
	$out['nsi']=intval(($count_positive+$count_neutral-$count_negative)*100/$count_post_per)/100;
	$i++;
	if (!isset($out['speakers']))
	{
		$out['speakers']=array();
	}
	if (!isset($out['promotions']))
	{
		$out['promotions']=array();
	}
	if (!isset($out['sources']))
	{
		$out['sources']=array();
	}
	if (!isset($out['city']))
	{
		$out['city']=array();
	}
	if (!isset($out['value_mdin']))
	{
		$out['value_mdin']=array();
	}
	if (!isset($out['eng_mdin']))
	{
		$out['eng_mdin']=array();
	}
	if (!isset($out['words']))
	{
		$out['words']=array();
	}
	//print_r($out);
	//$memcache->set('order_'.$row['order_id'].'_'.strtotime($start).'_'.strtotime($end), json_encode($out), MEMCACHE_COMPRESSED, 86400);
	//$var=$redis->get('order_caches');
	//$m_dinams=json_decode($var,true);
	//$m_dinams[$row['order_id'].'_'.strtotime($start).'_'.strtotime($end)]=time()+86400;
	//$redis->set('order_caches', json_encode($m_dinams));
	return $out;
}

function get_filters_cash($row,$start,$end)
{
	global $redis,$memcache,$db,$wobot,$morphy;
	$_GET['order_id']=$row['order_id'];
	$_GET['start']=$start;
	$_GET['end']=$end;
	$out['params']['full_com']=intval($row['ful_com']);
	$out['params']['tags']=array();
	$pres_inf=$db->query('SELECT * FROM blog_preset WHERE order_id='.intval($_GET['order_id']));
	while ($pres=$db->fetch($pres_inf))
	{
		$mas['params']['presets'][]=$pres['name'];
	}
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_GET['order_id']));
	while ($tag=$db->fetch($tags_info))
	{
		//$out['params']['tags'][$tag['tag_tag']]=$tag['tag_name'];
		$out['params']['tags'][$tag['tag_tag']]=str_replace('.', '', mb_substr($tag['tag_name'],0,23,'UTF-8'));
	}
	$out['params']['words']=array();
	$out['params']['speakers']=array();
	$out['params']['promotions']=array();
	$out['params']['city']=array();
	$out['params']['city_tree']=array();
	$out['params']['promotions']=array();
	$out['params']['source_tree']=array();
	$out['params']['sources']=array();
	for($t=strtotime($_GET['start']);$t<=strtotime($_GET['end']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		$var=$redis->get('order_'.$row['order_id'].'_'.$t);
		$m_dinams=json_decode($var,true);
		foreach ($m_dinams['geo'] as $key => $item)
		{
			//$out['params']['city'][$key]['name']=(($key=='')?'Не определено':$key);
			//$out['params']['city'][$key]['count']+=$item;
			if (($wobot['destn3'][$key]!='') && ($wobot['destn3'][$key]!=' '))
			{
				$mcity[(($key=='')?'Не определено':$key)]+=$item;
				if (isset($m[$key]))
				{
					if (count($out['params']['city_tree'][$wobot['destn3'][$key]])<70)
					$out['params']['city_tree'][$wobot['destn3'][$key]][$m[$key]][$key]+=$item;
				}
				else
				{
					if (count($out['params']['city_tree'][$wobot['destn3'][$key]])<70)
					$out['params']['city_tree'][$wobot['destn3'][$key]][$key]+=$item;
				}
			}
		}
		foreach ($m_dinams['src'] as $key => $item)
		{
			$out['params']['source_tree'][mb_substr($key,0,1,"UTF-8")][$key]+=$item;
		}
		/*foreach ($m_dinams['words'] as $key => $item)
		{
			if ((trim($key)!='') && (mb_strlen($key,'UTF-8')>3))
			{
				$m_wrds[preg_replace('/[^а-яА-Яa-zA-ZёЁ]/isu','',$key)]['count']+=$item;
				$m_wrds[preg_replace('/[^а-яА-Яa-zA-ZёЁ]/isu','',$key)]['word']=$key;
			}
		}*/
		foreach ($m_dinams['words'] as $key => $item)
		{
			$mwrds[$key]+=$item;
		}
		foreach ($m_dinams['sp_pr'] as $key => $item)
		{
			//echo $key.'<br>';
			foreach ($item as $k => $i)
			{
				if ($i['nick']=='') continue;
				if (!isset($yet_prms[$key]))
				{
					$mprm['count'][]=$i['count'];
					$mprm['readers'][]=$i['readers'];
					$mprm['nick'][]=$i['nick'];
					$mprm['id'][]=$key;
					$mprm['hn'][]=$k;
				}
				$yet_prms[$key]+=$i['count'];
			}
		}
	}
	arsort($mwrds);
	arsort($mcity);
	ksort($out['params']['source_tree']);
	ksort($out['params']['city_tree']);
	// print_r($out['params']['source_tree']);
	// die();
	array_multisort($mprm['readers'],SORT_DESC,$mprm['count'],SORT_DESC,$mprm['nick'],SORT_DESC,$mprm['id'],SORT_DESC,$mprm['hn'],SORT_DESC);
	$p=0;
	foreach ($mcity as $key => $item)
	{
		$out['params']['city'][$p]['name']=$key;
		$out['params']['city'][$p]['count']=$item;
		$p++;
	}
	foreach ($mprm['count'] as $key => $item)
	{
		if ($key==30) break;
		$out['params']['promotions'][$key]['count']=intval($yet_prms[$mprm['id'][$key]]);
		$out['params']['promotions'][$key]['nick']=$mprm['nick'][$key];
		$out['params']['promotions'][$key]['id']=$mprm['id'][$key];
		$out['params']['promotions'][$key]['readers']=intval($mprm['readers'][$key]);
		$out['params']['promotions'][$key]['link']=$mprm['hn'][$key];
	}
	usort($m_wrds, 'wordssort');
	/*$k=0;
	foreach ($m_wrds as $key => $item)
	{
		if ($k>30) break;
		$out['params']['words'][$k]['word']=preg_replace('/[^а-яА-Яa-zA-Z]/isu','',$item['word']);
		$out['params']['words'][$k]['count']=$item['count'];
		$k++;
	}*/
	// $i=0;
	// foreach ($mwrds as $key => $item)
	// {
	// 	if ($i==30) break;
	// 	$out['params']['words'][$i]['word']=$key;
	// 	$out['params']['words'][$i]['count']=$item;
	// 	$i++;
	// }
	$i=0;
	foreach ($mwrds as $key => $item)
	{
		if ($i==50) break;
		$newword=$morphy->lemmatize(mb_strtoupper($key,'UTF-8'), phpMorphy:: NORMAL);
		$m_words_dump[($newword[0]!=null?mb_strtolower($newword[0],'UTF-8'):$key)]+=$item;
		$i++;
	}
	$i=0;
	foreach ($m_words_dump as $key => $item)
	{
		if ($i==30) break;
		$out['params']['words'][$i]['word']=$key;
		$out['params']['words'][$i]['count']=$item;
		$i++;
	}
	//print_r($out);
	//echo 'filters_'.$row['order_id'].'_'.strtotime($start).'_'.strtotime($end);
	//$memcache->set('filters_'.$row['order_id'].'_'.strtotime($start).'_'.strtotime($end), json_encode($out), MEMCACHE_COMPRESSED, 86400);
	return $out;
}

function wordssort($a, $b)
{
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] < $b['count']) ? 1 : -1;
}

function get_authors($params)
{
	global $wobot,$db;
	$_POST=$params;
	foreach ($_POST as $key => $item)
	{
		//echo substr($key, 0, 4).' ';
		if ((substr($key, 0, 4)=='res_'))
		{
			$resorrr[]=str_replace("_",".",substr($key,4));
		}
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
			$word[]=str_replace("_",".",substr($key,5));
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
	$qw.=' AND p.post_time>='.strtotime($_POST['stime']).' AND p.post_time<'.(strtotime($_POST['etime'])+86400).' ';
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
	if (count($resorrr)!=0)
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			$wh1.=$or.' p.post_host=\''.$item.'\'';
			$or=' OR ';
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
	if ((count($word)!=0) && ($_POST['words']!='all'))
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			if ($_POST['words']=='selected')
			{
				//$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
				$qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
				$or=' OR ';
			}
			else
			if ($_POST['words']=='except')
			{
				$qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
				$or=' AND ';
			}
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
	$srt='b.blog_readers';
	if ($_POST['sort']=='eng')
	{
		$srt='eng';
	}
	elseif ($_POST['sort']=='audience')
	{
		$srt='b.blog_readers';
	}
	elseif ($_POST['sort']=='date')
	{
		$srt='p.post_time';
	}
	if ($_POST['search']!='')
	{
		$qw.=' AND (b.blog_nick LIKE \''.preg_replace('/[а-яa-zё\s0-9]/isu','',$_POST['search']).'%\' OR b.blog_login LIKE \''.preg_replace('/[а-яa-zё\s0-9]/isu','',$_POST['search']).'%\')';
	}
	$q='SELECT COUNT( p.blog_id ) AS cnt, SUM( p.post_engage ) AS eng, b.blog_nick, b.blog_readers, b.blog_login, b.blog_id, b.blog_link, b.blog_ico, p.post_time FROM  `blog_post` AS p LEFT JOIN robot_blogs2 AS b ON p.blog_id = b.blog_id WHERE p.order_id ='.$params['order_id'].' AND b.blog_id !=0 AND b.blog_nick != \'\' '.$qw.' '.($_POST['blocking']=='true'?'AND p.post_spam=1':'AND p.post_spam!=1').' GROUP BY p.blog_id ORDER BY '.$srt.' DESC LIMIT 300';
	//echo $q;
	$qauthors=$db->query($q);
	$i=0;
	while ($author=$db->fetch($qauthors))
	{
		$outmas[$i]['nick']=($author['blog_nick']==''?$author['blog_login']:$author['blog_nick']);
		$outmas[$i]['link']=$author['blog_link'];
		$outmas[$i]['blog_id']=$author['blog_id'];
		$outmas[$i]['posts']=$author['cnt'];
		$outmas[$i]['value']=$author['blog_readers'];
		$outmas[$i]['engage']=$author['eng'];
		$outmas[$i]['ico']=$author['blog_ico'];
		$outmas[$i]['auth_link']=get_author_link($author);
		$tonalmas[$author['blog_id']]=$i;
		$ids.=$zap.$author['blog_id'];
		$zap=',';
		$i++;
	}
	$q='SELECT b.blog_id, p.post_nastr FROM  `blog_post` AS p LEFT JOIN robot_blogs2 AS b ON p.blog_id = b.blog_id WHERE p.order_id ='.$params['order_id'].' AND b.blog_id !=0 AND b.blog_nick != \'\' '.$qw.' AND p.blog_id IN ('.$ids.') '.($_POST['blocking']=='true'?'AND p.post_spam=1':'AND p.post_spam!=1').'';
	$qtonal=$db->query($q);
	while ($tonal=$db->fetch($qtonal))
	{
		$ton[$tonal['blog_id']][$tonal['post_nastr']]++;
	}
	foreach ($ton as $key => $item)
	{
		$outmas[$tonalmas[$key]]['proc_positive']=intval($item[1]*100/$outmas[$tonalmas[$key]]['posts']);
		$outmas[$tonalmas[$key]]['proc_negative']=intval($item[-1]*100/$outmas[$tonalmas[$key]]['posts']);
		$outmas[$tonalmas[$key]]['proc_neutral']=intval($item[0]*100/$outmas[$tonalmas[$key]]['posts']);
		$outmas[$tonalmas[$key]]['proc_undefined']=intval($item[2]*100/$outmas[$tonalmas[$key]]['posts']);
	}
	//print_r($outmas);
	return $outmas;
}

function getwordsbyfilter($params)
{
	global $db,$user,$stem_word,$morphy;
	$_POST=$params;
	foreach ($_POST as $key => $item)
	{
		//echo substr($key, 0, 4).' ';
		if ((substr($key, 0, 4)=='res_'))
		{
			$resorrr[]=str_replace("_",".",substr($key,4));
		}
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
			$word[]=str_replace("_",".",substr($key,5));
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
	$qw.=' AND p.post_time>='.strtotime($_POST['stime']).' AND p.post_time<'.(strtotime($_POST['etime'])+86400);
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
	if (count($resorrr)!=0)
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			$wh1.=$or.' p.post_host=\''.$item.'\'';
			$or=' OR ';
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
	if ((count($word)!=0) && ($_POST['words']!='all'))
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			if ($_POST['words']=='selected')
			{
				//$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
				$qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
				$or=' OR ';
			}
			else
			if ($_POST['words']=='except')
			{
				$qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
				$or=' AND ';
			}
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
	if (($_POST['search']!='') && ($_POST['response_type']=='word'))
	{
		$_POST['search']=preg_replace('/[^а-яa-zё0-9]/isu','',$_POST['search']);
		$qw.=' AND (LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$_POST['search'].'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$_POST['search'].'%")';
	}
	$q='SELECT '.($_POST['response_type']=='tag'?'post_tag':'LOWER(post_content) as post_content').',post_nastr FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id WHERE p.order_id='.$_POST['order_id'].$qw;
	// echo $q;
	$qpost=$db->query($q);
	if ($_POST['response_type']=='word')
	{
		while ($post=$db->fetch($qpost))
		{
			// $words=getWords($post['post_content']);	
			$words=str_word_count($post['post_content'],1,'абвгдеёжзиклмнопрстуфхцчшщъчьэюя');
			foreach ($words as $item)
			{
				$outmas[$item]++;
				$outmasnastr[$item][$post['post_nastr']]++;
			}
		}
		arsort($outmas);
		// die();
		$k=0;
		$nword=array("здесь"=>1,"всем"=>1,"кто"=>1,"что"=>1,"сколько"=>1,"какой"=>1,"каков"=>1,"чей"=>1,"который"=>1,"столько"=>1,"этот"=>1,"тот"=>1,"такой"=>1,"таков"=>1,"весь"=>1,"всякий"=>1,"сам"=>1,"самый"=>1,"каждый"=>1,"любой"=>1,"другой"=>1,"иной"=>1,"никто"=>1,"ничто"=>1,"некого"=>1,"нечего"=>1,"нисколько"=>1,"никакой"=>1,"ничей"=>1,"onclick"=>1,"onclick="=>1,"return"=>1,"style="=>1,"to=http%"=>1,"fwww"=>1,"href="=>1,"factions%"=>1,"onmouseout="=>1,"textdecoration="=>1,"this"=>1,"style"=>1,"html"=>1,"cursor"=>1,"style"=>1,"pointer"=>1,"onmouseover="=>1,"underline"=>1,"none"=>1,"style="=>1,"http"=>1,"span"=>1,"link"=>1,"href"=>1,"class"=>1,"return"=>1,"type"=>1,"com"=>1,"twitter"=>1,"value"=>1,"bit"=>1,"amp"=>1,"xhref"=>1,"https"=>1,"www"=>1,"nbsp"=>1);
		foreach ($outmas as $key => $item)
		{
			if ($k>350) break;
			if (isset($nword[$key])) continue;
			if (mb_strlen($key,'UTF-8')<3) continue;
			if ($k==0) $first_count=$item;
			if (($_POST['search']!='') && (!preg_match('/^'.addslashes($_POST['search']).'.*$/isu', $key))) continue;
			// $word_stem=new Lingua_Stem_Ru();
			$sword=$stem_word->stem_word($key);
			$out[$sword]['positive']+=$outmasnastr[$key][1];
			$out[$sword]['negative']+=$outmasnastr[$key][-1];
			$out[$sword]['neutral']+=$outmasnastr[$key][0];
			$out[$sword]['undefined']+=$outmasnastr[$key][2];
			$out[$sword]['count']+=$item;
			$out[$sword]['word']=mb_strtolower($key,'UTF-8');
			$out[$sword]['proc']=(intval($item*100/$first_count)/100>$out[$sword]['proc']?intval($item*100/$first_count)/100:$out[$sword]['proc']);
			$k++;
		}
		$k=0;
		unset($outmas);
		foreach ($out as $key => $item)
		{
			if ($k>=300) break;
			$outmas[$k]['positive']=$out[$key]['positive'];
			$outmas[$k]['negative']=$out[$key]['negative'];
			$outmas[$k]['neutral']=$out[$key]['neutral'];
			$outmas[$k]['undefined']=$out[$key]['undefined'];
			$outmas[$k]['count']=$out[$key]['count'];
			$newword=$morphy->lemmatize(mb_strtoupper($out[$key]['word'],'UTF-8'), phpMorphy:: NORMAL);
			// $outmas[$k]['result']=($morphy-> getLastPredictionType() == phpMorphy::PREDICT_BY_NONE);
			$outmas[$k]['word']=($newword[0]!=null?mb_strtolower($newword[0],'UTF-8'):$out[$key]['word']);
			// $outmas[$k]['word2']=$out[$key]['word'];
			$outmas[$k]['proc']=($out[$key]['proc']!=null?$out[$key]['proc']:0);
			$k++;
		}
		usort($outmas, "cmp");
		return $outmas;
	}
	elseif ($_POST['response_type']=='tag')
	{
		$qtag=$db->query('SELECT tag_tag,tag_name FROM blog_tag WHERE order_id='.$_POST['order_id'].' AND user_id='.$user['user_id']);
		while ($tag=$db->fetch($qtag))
		{
			$mtag[$tag['tag_tag']]=$tag['tag_name'];
		}
		//print_r($mtag);
		while ($post=$db->fetch($qpost))
		{
			$mpt=explode(',',$post['post_tag']);
			foreach ($mpt as $it)
			{
				if (trim($it)=='') continue;
				if (!isset($mtag[$it])) continue;
				$outmas[$mtag[$it]]++;
				$outmasnastr[$mtag[$it]][$post['post_nastr']]++;
			}
		}
		arsort($outmas);
		//print_r($outmas);
		$k=0;
		foreach ($outmas as $key => $item)
		{
			if (($_POST['search']!='') && (!preg_match('/^'.addslashes($_POST['search']).'.*$/isu', $key))) continue;
			if ($k==0) $first_count=$item;
			$out[$k]['positive']=intval($outmasnastr[$key][1]);
			$out[$k]['negative']=intval($outmasnastr[$key][-1]);
			$out[$k]['neutral']=intval($outmasnastr[$key][0]);
			$out[$k]['undefined']=intval($outmasnastr[$key][2]);
			$out[$k]['count']=$item;
			$out[$k]['tag']=$key;
			$out[$k]['proc']=intval($item*100/$first_count)/100;
			$k++;
		}
	}
	usort($out, "cmp");
	//print_r($out);
	//arsort($out);
	return $out;
}

function cmp($a, $b)
{
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] > $b['count']) ? -1 : 1;
}

function get_author_link($post)
{
	if ($post['blog_link']=='vkontakte.ru')
	{
		if ($post['blog_login'][0]=='-')
		{
			$link='http://vk.com/club'.substr($post['blog_login'],1);
		}
		else
		{
			$link='http://vk.com/id'.$post['blog_login'];
		}
	}
	elseif ($post['blog_link']=='facebook.com')
	{
		$link='http://facebook.com/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='twitter.com')
	{
		$link='http://twitter.com/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='livejournal.com')
	{
		$link='http://'.$post['blog_login'].'.livejournal.com';			
	}
	elseif (preg_match('/mail\.ru/isu',$post['blog_link']))
	{
		$link='http://blogs.'.$post['blog_link'].'/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='liveinternet.ru')
	{
		$link='http://www.liveinternet.ru/users/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='ya.ru')
	{
		$link='http://'.$post['blog_login'].'.ya.ru';			
	}
	elseif ($post['blog_link']=='yandex.ru')
	{
		$link='http://'.$post['blog_login'].'.ya.ru';			
	}
	elseif ($post['blog_link']=='rutwit.ru')
	{
		$link='http://rutwit.ru/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='rutvit.ru')
	{
		$link='http://rutwit.ru/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='babyblog.ru')
	{
		$link='http://www.babyblog.ru/user/info/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='blog.ru')
	{
		$link='http://'.$post['blog_login'].'.blog.ru/profile';			
	}
	elseif ($post['blog_link']=='foursquare.com')
	{
		$link='https://ru.foursquare.com/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='kp.ru')
	{
		$link='http://blog.kp.ru/users/'.$post['blog_login'].'/profile/';			
	}
	elseif ($post['blog_link']=='aif.ru')
	{
		$link='http://blog.aif.ru/users/'.$post['blog_login'].'/profile';			
	}
	elseif ($post['blog_link']=='friendfeed.com')
	{
		$link='http://friendfeed.com/'.$post['blog_login'];			
	}
	elseif ($post['blog_link']=='plus.google.com')
	{
		$link='https://plus.google.com/'.$post['blog_login'].'/about';			
	}
	return $link;
}

/*function promosort($a, $b)
{
    if ($a['readers'] == $b['readers']) {
        return 0;
    }
    return ($a['readers'] < $b['readers']) ? 1 : -1;
}*/

function get_statistics($query)
{
	global $db,$_GET,$row,$wobot;
	$nword=array("для"=>1,"здесь"=>1,"всем"=>1,"кто"=>1,"что"=>1,"сколько"=>1,"какой"=>1,"каков"=>1,"чей"=>1,"который"=>1,"столько"=>1,"этот"=>1,"тот"=>1,"такой"=>1,"таков"=>1,"весь"=>1,"всякий"=>1,"сам"=>1,"самый"=>1,"каждый"=>1,"любой"=>1,"другой"=>1,"иной"=>1,"никто"=>1,"ничто"=>1,"некого"=>1,"нечего"=>1,"нисколько"=>1,"никакой"=>1,"ничей"=>1,"onclick"=>1,"onclick="=>1,"return"=>1,"style="=>1,"to=http%"=>1,"fwww"=>1,"href="=>1,"factions%"=>1,"onmouseout="=>1,"textdecoration="=>1,"this"=>1,"style"=>1,"html"=>1,"cursor"=>1,"style"=>1,"pointer"=>1,"onmouseover="=>1,"underline"=>1,"none"=>1,"style="=>1,"http"=>1,"span"=>1,"link"=>1,"href"=>1,"class"=>1,"return"=>1,"type"=>1,"com"=>1,"twitter"=>1,"value"=>1,"bit"=>1,"amp"=>1,"xhref"=>1,"https"=>1,"www"=>1,"nbsp"=>1);
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \.\'\"]+/isu','',mb_strtolower($row['order_keyword'],'UTF-8'));
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\-\'\’\.]/isu','  ',$keyword);
	$keyword=explode('  ',$keyword);
	foreach ($keyword as $item)
	{
		$assoc_kw[$item]=1;
	}
	$out['order_name']=$row['order_name'];
	$out['order_name']=$row['order_name'];
	$out['start']=$_GET['start'];
	$out['end']=$_GET['end'];
	$out['cash_update']=$row['cash_update'];
	$start=strtotime($_GET['start']);
	$end=strtotime($_GET['end']);
	$graphtype='day';
	if ($end==$start) $graphtype='hour';
	if (($end-$start)/86400>23) $graphtype='week';
	if (($end-$start)/86400>167) $graphtype='month';
	if (($end-$start)/86400>730) $graphtype='quarter';
	if (($end-$start)/86400>1825) $graphtype='halfyear';
	if ($graphtype=='hour')
	{
		for ($i=$start;$i<=$end+86400;$i+=3600)
		{
			$out['graph'][$i]=0;
		}
	}
	$qpost=$db->query($query);
	while ($post=$db->fetch($qpost))
	{
		$count_post_per++;
		if ($post['post_nastr']==1) $count_positive++;
		elseif ($post['post_nastr']==0) $count_neutral++;
		elseif ($post['post_nastr']==-1) $count_negative++;
		if ($graphtype=='hour') $out['graph'][mktime(date('H',$post['post_time']),0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']))]++;
		else $mtime[mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']))]++;
		$msrc[$post['post_host']]++;
		$mword=getWords_orders($post['ful_com_post']!=''?$post['ful_com_post']:$post['post_content']);
		foreach ($mword as $item)
		{
			if (mb_strlen($item,'UTF-8')<3) continue;
			$mwrds[$item]++;
		}
		$geo[$wobot['destn1'][$post['blog_location']]]++;
		$mpromo[$post['blog_id']]['count_posts']++;
		$mpromo[$post['blog_id']]['count']=$post['blog_readers'];
		$mpromo[$post['blog_id']]['id']=$post['blog_id'];
		$mpromo[$post['blog_id']]['nick']=($post['blog_nick']!=''?$post['blog_nick']:$post['blog_login']);
		if ($post['post_engage']!=0) $mengage[$post['post_host']]+=$post['post_engage'];
		if (intval($post['blog_readers'])!=0 && !isset($yet_blog_id[$post['blog_id']])) 
		{
			$mvalue[$post['post_host']]+=$post['blog_readers'];
			$yet_blog_id[$post['blog_id']]=1;
		}
	}

	arsort($msrc);
	$i=0;
	foreach ($msrc as $key => $item)
	{
		$out['sources'][$i]['name']=$key;
		$out['sources'][$i]['count']=$item;
		$i++;
	}
	if (count($out['sources'])==0) $out['sources']=array();
	$count_src_per=count($out['sources']);

	arsort($mwrds);
	// die(json_encode($mwrds));
	$i=0;
	foreach ($mwrds as $key => $item)
	{
		if ($i==50) break;
		if (isset($nword[$key]) || isset($assoc_kw[$key])) continue;
		// $outmas[$k]['word']=($newword[0]!=null?mb_strtolower($newword[0],'UTF-8'):$out[$key]['word']);			
		$out['words'][$i]['word']=$key;
		$out['words'][$i]['count']=$item;
		$i++;
	}
	if (count($out['words2'])==0) $out['words2']=array();

	arsort($geo);
	$i=0;
	//print_r($mas_geo_per);
	foreach ($geo as $key => $item)
	{
		if ($key!='')
		{
			$out['city'][$i]['name']=(($key=='')?'Не определено':$key);
			$out['city'][$i]['count']=$item;
			$cd=$wobot['destn2'][$key];
			$mcd=explode(' ',$cd);
			$out['city'][$i]['x']=intval($mcd[0]*100)/100;
			$out['city'][$i]['y']=intval($mcd[1]*100)/100;
			$i++;
		}
	}
	if (count($out['city'])==0) $out['city']=array();
	arsort($mengage);
	$i=0;
	foreach ($mengage as $key => $item)
	{
		if ($item!=0)
		{
			$out['eng_mdin'][$i]['name']=$key;
			$out['eng_mdin'][$i]['count']=$item;
			$count_eng_per+=$item;
			$i++;
		}
	}
	if (count($out['eng_mdin'])==0) $out['eng_mdin']=array();

	arsort($mvalue);
	$i=0;
	foreach ($mvalue as $key => $item)
	{
		if ($item==0) continue;
		$out['value_mdin'][$i]['name']=$key;
		$out['value_mdin'][$i]['count']=$item;
		$count_value_per+=$item;
		$i++;
	}
	if (count($out['value_mdin'])==0) $out['value_mdin']=array();

	//echo $count_eng_per;
	//print_r($mprm);
	usort($mpromo, 'promosort');
	// die(json_encode($mpromo));
	$i=0;
	foreach ($mpromo as $key => $item)
	{
		if ($i==30) break;
		$out['promotions'][$i]['count_posts']=$item['count_posts'];
		$out['promotions'][$i]['nick']=(mb_strlen($item['nick'],'UTF-8')>12?mb_substr($item['nick'],0,12,'UTF-8').'..':$item['nick']);
		$out['promotions'][$i]['id']=$item['id'];
		$out['promotions'][$i]['count']=$item['count'];
		$i++;
	}
	if (count($out['promotions'])==0) $out['promotions']=array();

	$count_uniq_per=count($mpromo);
	$out['posts']=formatint($count_post_per);
	$out['src']=formatint(intval($count_src_per));
	$out['value']=formatint(intval($count_value_per));
	$out['uniq']=formatint(intval($count_uniq_per));
	$out['engage']=formatint(intval($count_eng_per));		
	$out['nsi']=intval(($count_neutral+$count_positive-$count_negative)*100/$count_post_per)/100;
	$out['graphtype']=$graphtype;

	// die(json_encode($out['graph']));
	if ($graphtype!='hour') $out['graph']=get_graph_data($mtime,$graphtype);
	if (count($out['graph'])==0) $out['graph']=array();
	return $out;
}

function get_graph_data($mtime,$type)
{
	global $_GET;
	krsort($mtime);
	// print_r($mtime);
	switch($type) 
	{
	    case 'day': $split=1; break;
	    case 'week': $split=7; break;
	    case 'month': $split=30; break;
	    case 'quarter': $split=90; break;
	    case 'halfyear': $split=180; break;
	    default: $split=1; break;
	}
	$split_time=strtotime($_GET['end']);
	for ($time=strtotime($_GET['end']);$time>strtotime($_GET['start']);$time-=$split*86400)
	{
		$out[$time]=0;
	}
	foreach ($mtime as $key => $item)
	{
		// echo $split_time-$key.' '.(($split_time-$key)/($split*86400)).' '.(intval(($split_time-$key)/($split*86400))+1)*86400*$split.'|';
		// if ($split_time=='') $split_time=$key;
		if ($split_time-$key>=$split*86400) $split_time-=intval(($split_time-$key)/($split*86400))*86400*$split;
		$out[$split_time]+=$item;
	}
	// die(json_encode($out));
	return $out;
}

function is_nulled_filer()
{
	global $_POST;
	// if ($_POST['page']!=0) return false;
	// if ($_POST['sort']!='null') return false;
	if ($_POST['positive']!='true') return false;
	if ($_POST['negative']!='true') return false;
	if ($_POST['neutral']!='true') return false;
	if ($_POST['post_type']!='null') return false;
	// if ($_POST['md5']!='') return false;
	// if ($_POST['perpage']!='null') return false;
	// if ($_POST['Promotions']!='selected') return false;
	// if ($_POST['words']!='selected') return false;
	// if ($_POST['tags']!='selected') return false;
	if ($_POST['location']!='') return false;
	if ($_POST['cou']!='') return false;
	// if ($_POST['locations']!='selected') return false;
	if ($_POST['res']!='') return false;
	if ($_POST['shres']!='') return false;
	// if ($_POST['hosts']!='selected') return false;
	foreach ($_POST as $key => $item)
	{
		if (preg_match('/^word\_.*$/ius', $key)) return false;
		if (preg_match('/^tag\_.*$/ius', $key)) return false;
		if (preg_match('/^prom\_.*$/ius', $key)) return false;
	}
	// echo 1;
	return true;
}

function getWords_orders($text)
{
	//$text=preg_replace('/\s+/isu',' ',$text);
	//$text=preg_replace('/([^а-яА-Яa-zA-ZёЁ])/isu',' ',$text);
	$words=preg_split('/([^а-яa-zё0-9]+)/isu', mb_strtolower($text,'UTF-8'), null, PREG_SPLIT_NO_EMPTY);
	// $words=str_word_count($text,1,'абвгдеёжзиклмнопрстуфхцчшщъчьэюя');
	return $words;
}
// get_statistics('');

?>