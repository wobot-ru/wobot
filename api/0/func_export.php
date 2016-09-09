<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');


function get_query()
{
	global $_GET,$_POST,$wobot,$db,$user,$order,$word,$morphy;
	
        //echo '///'.$_POST['etime'].'///';
	//print_r($order);
	$_POST['stime']=$_POST['start'];
	$_POST['etime']=$_POST['end'];
	if ($user['tariff_id']==3)
	{
		if ($order['order_end']>time())
		{
			$_POST['stime']=date('j.n.Y',mktime(0,0,0,date('n'),date('j'),date('Y'))-86400*14);
		}
		else
		{
			$_POST['stime']=date('j.n.Y',$order['order_end']-86400*14);//date('n.j.Y',mktime(0,0,0,date('n',$order['order_end']),date('j',$order['order_end'])-14,date('Y',$order['order_end'])));
		}
		$_POST['etime']=date('j.n.Y',$order['order_end']);
		if (strtotime($_POST['stime'])<$order['order_start'])
		{
			$_POST['stime']=date('j.n.Y',$order['order_start']);
		}
		//echo $order['order_end'];
	}
	if ($order['order_end']>time())
	{
		if ((strtotime($_POST['etime'])>time()) || (strtotime($_POST['etime'])==0))
		{
			$_POST['etime']=date('j.n.Y',mktime(0,0,0,date('n',time()),date('j',time()),date('Y',time())));
			//echo '!!!'.$_POST['etime'].'!!!';
		}
	}
	if ($user['tariff_id']!=3)
	{
		if (strtotime($_POST['stime'])==0)
		{
			$_POST['stime']=date('j.n.Y',mktime(0,0,0,date('n',$order['order_start']),date('j',$order['order_start']),date('Y',$order['order_start'])));
		}
		if (strtotime($_POST['etime'])==0)
		{
			$_POST['etime']=date('j.n.Y',mktime(0,0,0,date('n',$order['order_end']),date('j',$order['order_end']),date('Y',$order['order_end'])));
		}
	}
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
					if ($imcou==$idest)	$loc[]=$kdest;
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
			$tags[]=str_replace("_",".",substr($key,4));
		}
		if ((substr($key, 0, 6)=='speak_') && (substr($key,7,11)!='link'))
		{
			$speak[str_replace("_",".",substr($key,6))]=$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
			$speakid[str_replace("_",".",substr($key,6))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
		if ((substr($key, 0, 5)=='prom_') && (substr($key,6,10)!='link'))
		{
			$prom[str_replace("_",".",substr($key,5))]=$_POST['prom_link_'.str_replace("_",".",substr($key,5))];
			$promid[str_replace("_",".",substr($key,5))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
		if ((mb_substr($key, 0, 5,'UTF-8')=='word_'))
		{
			$lem_words=$morphy->getAllFormsWithGramInfo(mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8'), true);
			// $fp = fopen('logquery.txt', 'a');
			// fwrite($fp, mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8').' '.json_encode($lem_words)."\n");
			// fclose($fp);
			foreach ($lem_words[0]['forms'] as $item_lem_words)
			{
				$word[]=mb_strtolower($item_lem_words,'UTF-8');
			}
			$word[]=mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8');
		}
	}
	// print_r($tags);
	$order_info=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	$order=$db->fetch($order_info);
	$metrics=json_decode($order['order_metrics'],true);
	$settings=json_decode($order['order_settings'],true);
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE user_id='.intval($_POST['user_id']));
	while ($tg=$db->fetch($tags_info))
	{
		$d_tags[$tg['tag_tag']]=$tg['tag_name'];
		$d_astags[$tg['tag_name']]=$tg['tag_tag'];
	}
	$qw='SELECT * FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id  LEFT JOIN blog_full_com as f ON p.post_id=f.ful_com_post_id WHERE '.(intval($settings['remove_spam'])==0?'':' p.post_spam!=1 AND ').' order_id='.$_POST['order_id'].((intval(strtotime($_POST['stime']))==0)?'':' AND post_time>='.strtotime($_POST['stime'])).((intval(strtotime($_POST['etime']))==0)?'':' AND post_time<'.(strtotime($_POST['etime'])+86400)).' ';
	// if ($_POST['positive']=='true')
	// {
	// 	$qw.='AND (p.post_nastr=1';
	// }
	// if ($_POST['negative']=='true')
	// {
	// 	$qw.=' OR p.post_nastr=-1';
	// }
	// if ($_POST['neutral']=='true')
	// {
	// 	$qw.=' OR p.post_nastr=0)';
	// }
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
	// if (count($resorrr)!=0)
	// {
	// 	$or='';
	// 	$wh1=' AND (';
	// 	foreach ($resorrr as $item)
	// 	{
	// 		$wh1.=$or.' p.post_host=\''.$item.'\'';
	// 		$or=' OR ';
	// 	} 
	// 	$wh1.=')';
	// 	$qw.=$wh1;
	// }
	if (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($_POST['hosts']=='selected'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			if (trim($item)=='') continue;
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
			if (trim($item)=='') continue;
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
	if ((count($loc)!=0) && ($_POST['locations']=='selected'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($loc as $item)
		{
			if (trim($item)=='') continue;
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
	elseif ((count($loc)!=0) && ($_POST['locations']=='except'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($loc as $item)
		{
			if (trim($item)=='') continue;
			if ($item=='na')
			{
				$wh1.=$or.' b.blog_location!=\'\'';
				$or=' AND ';
			}
			else
			{
				if (isset($wobot['destn2'][$item]))
				{
					$wh1.=$or.' b.blog_location!=\''.$wobot['destn2'][$item].'\'';
					$or=' AND ';
				}
			}
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	$or='';
	// if (count($tags)!=0)
	// {
	// 	$wh1.=' AND (';
	// 	foreach ($tags as $item)
	// 	{
	// 		if ($item!='без_тегов')
	// 		{
	// 			$wh.=$or.'(FIND_IN_SET(\''.$d_astags[$item].'\',post_tag)>0)';
	// 			$or=' OR ';
	// 		}
	// 		else
	// 		{
	// 			$wh.=$or.'(post_tag = \'\')';
	// 			$or=' OR ';
	// 		}
	// 	}
	// 	$wh1.=')';
	// 	$qw.=$wh1;
	// }
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
	if ($_POST['post_type']=='null')
	{
		if ($settings['remove_spam']==1) $_POST['post_type']='nospam';
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
	}
	$or='';
	if (count($promid)!=0)
	{
		$qw.=' AND (';
		foreach ($promid as $key => $item)
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
				$qw.=$or.'(b.blog_id!=\''.$key.'\')';
				$or=' AND ';
			}
		}
		$qw.=')';
	}
	$or='';
	/*if (count($word)!=0)
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			if ($_POST['words']=='selected')
			{
				//$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
				$qw.=$or.'(UPPER(a.post_content) LIKE \'%'.$item.'%\')';
				$or=' OR ';
			}
			else
			if ($_POST['words']=='except')
			{
				$qw.=$or.'(UPPER(a.post_content) NOT LIKE \'%'.$item.'%\')';
				$or=' AND ';
			}
		}
		$qw.=')';
	}*/
	$or='';
	if (count($word)!=0)
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			if ($_POST['words']=='selected')
			{
				$qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
				$qw.=' OR (LOWER(f.ful_com_post) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR f.ful_com_post LIKE "'.$item.'%")';
				$or=' OR ';
			}
			elseif ($_POST['words']='except')
			{
				$qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND p.post_content NOT LIKE "'.$item.'%" AND p.post_content NOT LIKE "%'.$item.'")';
				$qw.=' AND (LOWER(f.ful_com_post) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND f.ful_com_post NOT LIKE "'.$item.'%" AND f.ful_com_post NOT LIKE "%'.$item.'")';
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
	$qw.=' ORDER BY p.post_time DESC';
	//echo $qw;
	$fp = fopen('/var/www/api/0/logquery.txt', 'a');
	fwrite($fp, $qw);
	fclose($fp);
	return $qw;
}

function multi_attach_mail($to, $files, $sendermail){
    // email fields: to, from, subject, and so on
    $from = "Files attach <".$sendermail.">"; 
    $subject = date("d.M H:i")." F=".count($files); 
    $message = date("Y.m.d H:i:s")."\n".count($files)." attachments";
    $headers = "From: $from";
 
    // boundary 
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
 
    // headers for attachment 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
 
    // multipart boundary 
    $message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
 
    // preparing attachments
    //for($i=0;$i<count($files);$i++){
        //if(is_file($files[$i]))
		{
            $message .= "--{$mime_boundary}\n";
            /*$fp =    @fopen($files[$i],"rb");
        $data =    @fread($fp,filesize($files[$i]));
                    @fclose($fp);
            $data = chunk_split(base64_encode($data));*/
			$data=$files;
            $message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n" . 
            "Content-Description: ".basename($files[$i])."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }
        //}
    $message .= "--{$mime_boundary}--";
    $returnpath = "-f" . $sendermail;
    $ok = @mail($to, $subject, $message, $headers, $returnpath); 
    if($ok){ return $i; } else { return 0; }
    }


?>
