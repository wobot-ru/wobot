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
//print_r($_GET);

if ($_GET['monitor']=='1')
{
	if ((intval($_POST['user_id'])==61)||(intval($_POST['user_id'])>125))
	$resres=$db->query("INSERT INTO blog_res (user_id, res_h, res_w) VALUES (".intval($_POST['user_id']).",".intval($_POST['height']).",".intval($_POST['width']).")");
}

if (($_GET['export']!='') && ($_GET['order_id']!=''))
{
	$resajaxq=$db->query("SELECT * from blog_export WHERE order_id=".$_GET['order_id']." AND export_time>".(time()-2));
	//echo "SELECT * from blog_export WHERE order_id=".$_GET['order_id']." AND export_time>".(time()-10);
	if (mysql_num_rows($resajaxq)!=0)
	{
		$orderajaxq = $db->fetch($resajaxq);	
		$id=$orderajaxq['export_id'];
		$data=$orderajaxq['export_arg'];
		$data=json_decode(urldecode($data),true);
		$mas=explode(',',$_GET['arg']);
		foreach ($mas as $item)
		{
			$item=explode(' ',$item);
			$data[$item[1]]=$item[0];
		}
		//print_r($mas);
		if (count($mas)==250)
		{
			$resajaxq=$db->query("UPDATE blog_export SET export_arg='".urlencode(json_encode($data))."',export_time=".time()." WHERE order_id=".$_GET['order_id']." AND export_time>".(time()-2));
		}
		else
		{
			$resajaxq=$db->query("UPDATE blog_export SET export_arg='".urlencode(json_encode($data))."',export_time=".time().",name_file='".$_GET['order_id']."_".date('h:i:s d.m.Y')."' WHERE order_id=".$_GET['order_id']." AND export_time>".(time()-2));
		}
	}
	else
	{
		$mas=explode(',',urldecode($_GET['arg']));
		foreach ($mas as $item)
		{
			$item=explode(' ',$item);
			$data[$item[1]]=$item[0];
		}
		if (count($mas)==250)
		{
			$resajaxq=$db->query("INSERT INTO blog_export (order_id,export_arg,export_time) VALUES (".$_GET['order_id'].",'".urlencode(json_encode($data))."',".time().")");
		}
		else
		{
			$resajaxq=$db->query("INSERT INTO blog_export (order_id,export_arg,name_file,export_time) VALUES (".$_GET['order_id'].",'".urlencode(json_encode($data))."','".$_GET['order_id']."_".date('h:i:s d.m.Y')."',".time().")");
		}
	}
	//print_r($_GET['arg']);
}
if (($_POST['order_id']!='') && ($_POST['start_tt']!=''))
{
	//echo strtotime($_POST['start_tt']);
	$outm=get_mas($_POST['order_id'],strtotime($_POST['start_tt']));
	//$outm['time'][0]=0;
	$outm['timem']=strtotime($_POST['start_tt']).'000';
	$outm['timee']=intval(strtotime($_POST['start_tt']).'000')+86400000;
	echo json_encode($outm,true);
}
if (($_GET['plink']!='') && ($_GET['kword']!=''))
{
	//$resajax=$db->query("SELECT * from users WHERE user_id=".$_GET['userid']." LIMIT 1");
	//$orderajax = $db->fetch($resajax);
	//$count=$orderajax['user_ajax_limit'];
	//if ($count<100)
	{
		//$ktext=getkeyword(urldecode(urldecode($_GET['kword'])),urldecode(urldecode($_GET['plink'])));
		//header("Location: ".urldecode($_GET['plink']));
		echo urldecode($_GET['plink']);
		$limerr=0;
		//$resajax=$db->query("UPDATE users SET user_ajax_limit=user_ajax_limit+1 WHERE user_id=".$_GET['userid']);
	}
	/*else
	{
		$limerr=1;
	}
	if ((($ktext)!='') && ($limerr==0))
	{
		echo '<base href="http://'.parse_url(urldecode(urldecode($_GET['plink'])),PHP_URL_HOST).'/">'.$ktext;
	}
	elseif ((($ktext)=='') && ($limerr==1))
	{
		header("Location: ".urldecode($_GET['plink']));
	}
	else
	{
		echo '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
		<title>WOBOT &copy; Research - Страница не доступна</title>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		
		<style>
		a:link {color:#3e3f43; text-decoration: underline; padding: 0; margin: 0; }
		a:visited {color:#3e3f43; text-decoration: underline; }
		a:hover {color:#3e3f43; text-decoration: none; }
		a:active {color:#83b226; text-decoration: none; }
		
		</style>
		</head>
		
		<body style="min-width: 1000px; max-width: 1000px;margin: 0px;padding: 0px; font-family: tahoma, helvetica, verdana; background: 		#ffffff url(\'/img/bg.jpg\') no-repeat center;">
		<center>
		<table align="center" valign="center" height="100%">
		<td>
		<img src="/img/wobot_logo.png" alt="wobot">
		<h1>Извините выбранный вами сервис недоступен</h1>
		<p><a href="'.urldecode(urldecode($_GET['plink'])).'">'.urldecode(urldecode($_GET['plink'])).'</a></p>
		</td>
		</table>
		</center>
		</body>
		</html>';
		
	}*/
}
if (($_GET['order_id']!='') && ($_GET['ch']==1))
{
	$user['user_id']=$_GET['user_id'];
	$order_id=$_GET['order_id'];
	$res=$db->query("SELECT * from blog_orders WHERE order_id=".$order_id." and user_id=".intval($user['user_id'])." LIMIT 1");
	if (mysql_num_rows($res)==0) die();
	$order = $db->fetch($res);
	$order['order_start']=intval($order['order_start']);
	$order['order_end']=((intval($order['order_end'])==0)||(intval($order['order_end'])>mktime(0,0,0,date('m'),date('d')-1,date('Y'))))?(mktime(0,0,0,date('m'),date('d'),date('Y'))):intval($order['order_end']+60*60*24);
	$res_tag=$db->query("SELECT * from blog_tag WHERE user_id=".intval($user['user_id']));
	while ($order_tag = $db->fetch($res_tag))
	{
		$mtags[$order_tag['tag_tag']]=$order_tag['tag_name'];
	}
	$data=$order['order_metrics'];
	$metrics=json_decode($data,true);
	$data=$order['order_src'];
	$sources=json_decode($data, true);
	$k=0;
	$src_count=count($sources);
	$other=-1;
	foreach ($sources as $i => $source)
	{
			$other+=$source;
	}
	$c_posts=$other-$metrics['location'][''];

	$count_cou=0;
	$kk=0;
	arsort($metrics['location_cou']);
	//print_r($metrics['location_cou']);
	foreach ($metrics['location_cou'] as $key => $item)
	{
		$kk++;
		if ($kk>4)
		{
			$count_cou+=$item;
		}
	}
	$order_id=$_GET['order_id'];
	//echo $_POST['mtags'];
	//echo json_encode($mtags);
	//echo $order_id;
	//print_r($_GET);
	//echo json_decode($_GET,true);
	echo '			<b><u><a href="#" id="masterbtn">Мастер отчетов</a></u></b>
				<div class=\'filterbox-contents-show\'>
				<form action="/new/comment" method="post" id="filternameform" target="_blank">
					<input type="hidden" name="order_id" value="'.$order_id.'">
					<input type="hidden" id="nname" name="snick" value="">
					<input type="hidden" id="nword" name="sword" value="">
				</form>
				<form action="/new/comment" method="post" id="filterform" '.(($_GET['com2']==1)?'':'target="_blank"').'>
				<input type="hidden" name="tag_links" value="'.urlencode(json_encode($mtags)).'">
				<input type="hidden" name="order_id" value="'.$order_id.'">
			<table>
				<tr>
					<td width="120"><img src="/img/post.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Возможно выбрать упоминания без спама и рекламы.<br>Для добавления сообщения в избранное нужно перейти в меню просмотра.">Сообщения</font></font></td>
					<td style="font-size: 11px;">
						<table cellspacing="0" cellpadding="0">
							<tr>
							<td><label for="izb" tabindex="1"></label><input id="izb" type="radio" name="showmode" value="showfav"/> избранные&nbsp;</td>
							<td><label for="spm" tabindex="1"></label><input id="spm" type="radio" name="showmode" value="notspam"/> без спама&nbsp;</td>
							<td><label for="ospm" tabindex="1"></label><input id="ospm" type="radio" name="showmode" value="onlyspam"/> только спам&nbsp;</td>
							<td><label for="ol" tabindex="1"></label><input id="ol" type="radio" name="showmode" value="showall" checked="true"/> все&nbsp;</td><br>
							</tr>
						</table>
						<table cellspacing="0" cellpadding="0">
							<tr>
							<td><label for="dub" tabindex="1"></label><input id="dub" type="checkbox" name="unrep" checked="checked" class="rescheck"/> фильтровать дубли&nbsp;</td>
							<td><select width="70px" name="os" class="styled">
								<option value="all" SELECTED>Все категории</option>
								<option value="1">Не важно</option>
								<option value="2">Средне</option>
								<option value="3">Важно</option>
								<option value="4">Очень важно</option>
							</select></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><img src="/img/time.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите временной период, за который вы хотите исследовать упоминания или используйте стандартные временные периоды.">Время</font></div></td>
					<td style="font-size: 11px;">с <input name="ntime" id="ntime" value="'.date('d.m.Y',$order['order_start']).'" type="text"> по <input name="etime" id="etime" value="'.((intval($order['order_end']>time())||(intval($order['order_end'])==0))?date('d.m.Y',mktime(0,0,0,date('n'),date('j')-1,date('Y'))):date('d.m.Y',$order['order_end']-60*60*24)).'" type="text"> <b>или</b> <a href="#" onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">день</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-691200000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">неделя</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-2678400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">месяц</a></td>
				</tr>
				<tr>
					<td><img src="/img/nastr.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите эмоциональную окраску мнений.">Тональность</font></div></td>
					<td style="font-size: 11px;">
					<table cellspacing="0" cellpadding="0">
						<tr>
						<td><label for="neu" tabindex="1"></label><input id="neu" type="checkbox" checked="checked" name="neutral" class="rescheck"/> нейтральные&nbsp;</td>
						<td><label for="pol" tabindex="1"></label><input id="pol" type="checkbox" checked="checked" name="positive" class="rescheck"/> положительные&nbsp;</td>
						<td><label for="otr" tabindex="1"></label><input id="otr" type="checkbox" checked="checked" name="negative" class="rescheck"/> отрицательные&nbsp;</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>';
				if (count($mtags)==0)
				{
					$deltt='';
				}
				else
				{
					$deltt='<a href="#" class="vtip" title="Удалить тег" onclick="loadmodal1(\'/deletetagsmain?orid='.intval($user['user_id']).'&oorid='.$order_id.'\',604,'.(200+count($mtags)*30).');return false;"><img src="/img/Button-Close-icon.png"></a>';
				}
				if (count($mtags)>8)
				{
					$addtt='';
				}
				else
				{
					$addtt='<a href="#" class="vtip" title="Добавить тег" onclick="loadmodal1(\'/addtagsmain?orid='.$order_id.'&tags='.urlencode(json_encode($mtags)).'\',604,250);return false;"><img src="/img/Button-Add-icon.png"></a>&nbsp;<a href="#" class="vtip" title="Редактировать тег" onclick="loadmodal1(\'/edittagsmain?orid='.intval($user['user_id']).'&tags='.urlencode(json_encode($mtags)).'\',604,'.(200+count($mtags)*30).');return false;"><img src="/img/edit-icon.png"></a>';
				}
				if ($_GET['com2']==1)
				{
					$addtt='';
					$deltt='';
				}
				$html_out.='
					<td><img src="/img/tag-icon.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите теги, которые вы хотели бы отобразить в выдаче.">Теги</font></div>&nbsp;'.$addtt.'&nbsp;'.$deltt.'</td>
					<td style="font-size: 11px;"><table cellspacing="0" cellpadding="0"><tr>';
					foreach ($mtags as $key => $item)
					{
						$ti++;
						$html_out.='<td width="120"><label for="tgp'.$key.'" tabindex="1"></label><input id="tgp'.$key.'" type="checkbox" checked="checked" name="tg'.$key.'" value="'.$key.'"> '.$item.'&nbsp;</td>';
						if ($ti % 4 == 0)
						{
							$html_out.='</tr><tr>';
						}
					}
					$html_out.='<td width="120"><label for="tgpn" tabindex="1"></label><input id="tgpn" type="checkbox" checked="checked" name="tgn" value="na"> Без тегов&nbsp;</td>';
					$html_out.='</tr></table></td>
				</tr>
				<tr>
					<td valign="top"><img src="/img/sources.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите ресурсы, по которым хотите посмотреть выдачу.">Ресурсы</font></div></td>
					<td style="font-size: 11px;">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<label for="resch11" tabindex="1" onclick="if($(\'#resch11\').is(\':checked\')) {checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.u_ssoc\');} else { uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.u_ssoc\');}">социальные сети&nbsp;</label><input type="checkbox" name="markt" checked="checked" class="rescheck2" id="resch11" value="true" onclick=" if($(\'#resch11\').is(\':checked\')) { checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.u_ssoc\');} else {uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.u_ssoc\');} " value="социальные сети"/>
								</td>
								<td>
									<label for="resch12" tabindex="1" onclick="if($(\'#resch12\').is(\':checked\')) {uncheckPrettyCb(\'.novres\'); uncheckPrettyCb(\'.u_novres\');} else { checkPrettyCb(\'.novres\'); checkPrettyCb(\'.u_novres\');}">новостные ресурсы&nbsp;</label><input type="checkbox" name="markt1" checked="checked" class="rescheck2" id="resch12" value="true" onclick="uecheckAllPC(\'.novres\'); uacheckPrettyCb(\'.u_novres\'); return false;"/>
								</td>
								<td>
									<label for="resch13" tabindex="1" onclick="if($(\'#resch13\').is(\':checked\')) {uncheckPrettyCb(\'.microb\'); uncheckPrettyCb(\'.u_microb\');} else { checkPrettyCb(\'.microb\'); checkPrettyCb(\'.u_microb\');}">микроблоги&nbsp;</label><input type="checkbox" name="markt2" checked="checked" class="rescheck2" id="resch13" value="true" onclick="uecheckAllPC(\'.microb\'); uacheckPrettyCb(\'.u_microb\'); return false;"/>
								</td>
								<td>
									<label for="resch14" tabindex="1" onclick="if($(\'#resch14\').is(\':checked\')) {uncheckPrettyCb(\'.forabl\'); uncheckPrettyCb(\'.u_forabl\');} else { checkPrettyCb(\'.forabl\'); checkPrettyCb(\'.u_forabl\');}">форумы и блоги</label><input type="checkbox" name="markt3" checked="checked" class="rescheck2" id="resch14" value="true" onclick="uecheckAllPC(\'.forabl\'); uacheckPrettyCb(\'.u_forabl\'); return false;"/>
								</td>
							</tr>
						</table>
						';

					if ($order_id!=0)
					{
						$sourcesgr=$sources;
						unset($sources);
						//$fn = "/var/www/data/blog/".intval($order_id).".src";
						//$h = fopen($fn, "r");
						//$data = fread($h, filesize($fn));
						$data=$order['order_src'];
						//print_r($sourcesgr);
						$sources=json_decode($data, true);

						//$data=$order['order_src'];
						//$sources=json_decode($data, true);

						//$iii=0;
						arsort($metrics['location_cou']);
						$sources2=array_slice($sources, 10, -1);
						uksort($sources2,"strcoll");
						//print_r($sources2);
						$sources=array_slice($sources, 0, 10);
						/*foreach($sources2 as $src2)
						{
							$sources[]=$src2;
						}*/
						$sources=array_merge($sources, $sources2);


						$other=0;
						$k=0;
						$html_out.='<table cellspacing="0" cellpadding="0"><tr>';
						//$sources = array_multisort($sources,);
						//ksort($sources);
						foreach ($sources as $i => $source)
						{
							$k++;
							if ($k<10)
							{
								if (($i=="facebook.com") || ($i=="vkontakte.ru"))
								{
									$html_out.='<td><label for="ssoc'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								else
								if ($i=="mail.ru")
								{
									$html_out.='<td><label for="novres'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								else
								if (($i=="twitter.com") || ($i=="rutvit.ru"))
								{
									$html_out.='<td><label for="microb'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								else
								{
									$html_out.='<td><label for="forabl'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								if (($k%4)==0) $html_out.= '</tr><tr>';
								$socialall+=$source;
							}
							else
							{
								if ($k==10)
								{
									$html_out1.='<div id="othr" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: '.(($_GET['com2']==1)?'360':'500').'px; left: '.(($_GET['com2']==1)?'400':'520').'px; background-color: #FFFFFF;"><!--<input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="rescheck1" id="resch1" value="true"/> '.$i.' ('.$source.')&nbsp;--><table><tr>';
									if (($i=="facebook.com") || ($i=="vkontakte.ru"))
									{
										$html_out1.='<td><label for="ssoc'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if ($i=="mail.ru")
									{
										$html_out1.='<td><label for="novres'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if (($i=="twitter.com") || ($i=="rutvit.ru"))
									{
										$html_out1.='<td><label for="microb'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									{
										$html_out1.='<td><label for="forabl'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									$socialall+=$source;
								}
								else
								{
									if (($i=="facebook.com") || ($i=="vkontakte.ru"))
									{
										$html_out1.='<tr><td><label for="ssoc'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if ($i=="mail.ru")
									{
										$html_out1.='<tr><td><label for="novres'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if (($i=="twitter.com") || ($i=="rutvit.ru"))
									{
										$html_out1.='<tr><td><label for="microb'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									{
										$html_out1.='<tr><td><label for="forabl'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									$socialall+=$source;
								}
								$other+=$source;
							}
						}
						if ($k>10) $html_out1.='</table></div>';
						$socialother=$other;//<input type="checkbox" checked="checked" name="res_other" class="rescheck" value="true" />
						$html_out.='<td><label for="op" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}"></label><input type="checkbox" class="rescheck" id="op" value="true" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}" checked="checked"/><a id="lothr" class="vtip" title="Ресурсов: '.(count($sources)-9).'" href="#" onclick="$(\'#othr\').toggle(); return false;">другие</a> ('.$other.')&nbsp;</td>
		';
						$html_out.='</tr></table>';
						$html_out.=$html_out1;
						fclose($h);
					}		
					$html_out.='<div id="lothr1" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 495px; left: 380px; background-color: #FFFFFF;"><table>';		
					$ii=0;
					arsort($metrics['location']);
					foreach ($metrics['location'] as $k => $item)
					{
						if ($k!='')
						{
							$ii++;
							$html_out.='<tr><td><label for="loc'.$ii.'"></label><input type="checkbox" class="cou_'.$wobot['destn3'][$k].'" style="margin: 2px;" name="loc_'.$k.'" checked="checked" id="loc'.$ii.'" value="true"/> '.$k.' ('.$item.')&nbsp;<br></td></tr>';
						}
					}
					$html_out.='<tr><td><label for="locothr"></label><input type="checkbox" style="margin: 2px;" class="cou_na" name="loc_othr" checked="checked" id="locothr"/> Неопределено ('.$metrics['location'][''].')&nbsp;<br></td></tr>';
	$html_out.='</table></div><table style="margin-top: 5px;" cellspacing="0" cellpadding="0"><tr>';
					$ii=0;
					$chall.=' checkPrettyCb(\'.cc_cou_na\');';
					$unchall.=' uncheckPrettyCb(\'.cc_cou_na\');';
					foreach ($metrics['location_cou'] as $kk => $itt)
					{
						$chall.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
						$unchall.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
						if ($kk!='')
						{
							$ii++;
							if ($ii<4)
							{
								if (($ii % 4) == 0)
								{
									$html_out.='</tr><tr>';
								}
								$html_out.='<td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" checked="checked" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td>';
							}
							elseif ($ii==4)
							{
								$html_out.='<td><div id="lothr5" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 550px; left: 670px; background-color: #FFFFFF;"><table cellspacing="0" cellpadding="0"><tr><td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" checked="checked" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td></tr>';		
								$challoth.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
								$unchalloth.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
							}
							elseif ($ii>4)
							{
								$html_out.='<tr><td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" checked="checked" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td></tr>';
								$challoth.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
								$unchalloth.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
							}
						}
					}
					if (count($metrics['location_cou'])>4)
					{
						$html_out.='</table></div></td><td><label for="op1" onclick="if($(\'#op1\').is(\':checked\')) {'.$challoth.'} else { '.$unchalloth.'}"></label><input type="checkbox" class="rescheck" id="op1" value="true" onclick="if($(\'#op1\').is(\':checked\')) {'.$challoth.'} else {'.$unchalloth.'}" checked="checked"/><a href="#" onclick="$(\'#lothr5\').toggle(); return false;">Другие</a>&nbsp;('.$count_cou.')</td>';
					}
					$html_out.='<td><label for="loc_countna" onclick="if(!$(\'#loc_countna\').is(\':checked\')) {checkPrettyCb(\'.cou_na\');} else { uncheckPrettyCb(\'.cou_na\');}"></label><input type="checkbox" style="margin: 2px;" checked="checked" name="loc_othr1" class="cc_cou_na" id="loc_countna"/>Неопределено ('.$metrics['location_cou'][''].')</td></tr></table><a href="#" id="locopen">Города</a><br>';
		$html_out.=				'
	<!--<br><br><input type="checkbox" name="markt" checked="checked" class="rescheck2" id="resch11" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'ssoc\') { if ($(\'#resch11\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>социальные сети&nbsp;<input type="checkbox" name="markt1" checked="checked" class="rescheck2" id="resch12" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'novres\') { if ($(\'#resch12\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>новостные ресурсы&nbsp;<input type="checkbox" name="markt2" checked="checked" class="rescheck2" id="resch13" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'microb\') { if ($(\'#resch13\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>микроблоги&nbsp;<input type="checkbox" name="markt3" checked="checked" class="rescheck2" id="resch14" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'forabl\') { if ($(\'#resch14\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>форумы и блоги--><a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/new/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&unch=1'.(($_GET['com2']==1)?'&com2=1':'').'\',function(){ afterunch(); afterunch1();}); /*uncheckPrettyCb(\'.rescheck2\'); uncheckPrettyCb(\'.rescheck\'); uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.novres\'); uncheckPrettyCb(\'.microb\'); uncheckPrettyCb(\'.forabl\'); uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\'); '.$unchall.'*/ return false;">снять все</a> <a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/new/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&ch=1'.(($_GET['com2']==1)?'&com2=1':'').'\',function(){ afterunch(); afterunch1();}); checkPrettyCb(\'.rescheck2\'); checkPrettyCb(\'.rescheck\'); checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.novres\'); checkPrettyCb(\'.microb\'); checkPrettyCb(\'.forabl\'); checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\'); '.$chall.' return false;">отметить все</a><a href="#" style="font-size: 12px;" onclick="$(\'input\').each(function(){ if($(this).attr(\'id\')==(\'blogs\')) {$(this).attr(\'checked\',\'checked\');}});"></a><br>Экспорт в формате: <select name="format" class="styled"><option value="excel">Excel</option><option value="word">Word</option><option value="openoffice">OpenOffice</option></select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right"><img src="/img/show.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\''.$config['html_root'].'comment2\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Список мнений по заданным критериям появится в новой вкладке.">Показать</font></a> <img src="/img/pdf.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\'/export\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Сохранить отчет в формате Word.">Экспорт</font></a></td>
				</tr>
				</table>
				</form>
				</div>';
				echo $html_out;
}
if (($_GET['order_id']!='') && ($_GET['unch']==1))
{
	$user['user_id']=$_GET['user_id'];
	$order_id=$_GET['order_id'];
	$res=$db->query("SELECT * from blog_orders WHERE order_id=".$order_id." and user_id=".intval($user['user_id'])." LIMIT 1");
	if (mysql_num_rows($res)==0) die();
	$order = $db->fetch($res);
	$order['order_start']=intval($order['order_start']);
	$order['order_end']=((intval($order['order_end'])==0)||(intval($order['order_end'])>mktime(0,0,0,date('m'),date('d')-1,date('Y'))))?(mktime(0,0,0,date('m'),date('d'),date('Y'))):intval($order['order_end']+60*60*24);
	$res_tag=$db->query("SELECT * from blog_tag WHERE user_id=".intval($user['user_id']));
	while ($order_tag = $db->fetch($res_tag))
	{
		$mtags[$order_tag['tag_tag']]=$order_tag['tag_name'];
	}
	$data=$order['order_metrics'];
	$metrics=json_decode($data,true);
	$data=$order['order_src'];
	$sources=json_decode($data, true);
	$k=0;
	$src_count=count($sources);
	$other=-1;
	foreach ($sources as $i => $source)
	{
			$other+=$source;
	}
	$c_posts=$other-$metrics['location'][''];

	$count_cou=0;
	$kk=0;
	arsort($metrics['location_cou']);
	//print_r($metrics['location_cou']);
	foreach ($metrics['location_cou'] as $key => $item)
	{
		$kk++;
		if ($kk>4)
		{
			$count_cou+=$item;
		}
	}
	$order_id=$_GET['order_id'];
	//echo $_POST['mtags'];
	//echo json_encode($mtags);
	//echo $order_id;
	//print_r($_GET);
	//echo json_decode($_GET,true);
	echo '			<b><u><a href="#" id="masterbtn">Мастер отчетов</a></u></b>
				<div class=\'filterbox-contents-show\'>
				<form action="/comment" method="post" id="filternameform" target="_blank">
					<input type="hidden" name="order_id" value="'.$order_id.'">
					<input type="hidden" id="nname" name="snick" value="">
					<input type="hidden" id="nword" name="sword" value="">
				</form>
				<form action="/comment" method="post" id="filterform" '.(($_GET['com2']==1)?'':'target="_blank"').'>
				<input type="hidden" name="tag_links" value="'.urlencode(json_encode($mtags)).'">
				<input type="hidden" name="order_id" value="'.$order_id.'">
			<table>
				<tr>
					<td width="120"><img src="/img/post.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Возможно выбрать упоминания без спама и рекламы.<br>Для добавления сообщения в избранное нужно перейти в меню просмотра.">Сообщения</font></font></td>
					<td style="font-size: 11px;">
						<table cellspacing="0" cellpadding="0">
							<tr>
							<td><label for="izb" tabindex="1"></label><input id="izb" type="radio" name="showmode" value="showfav"/> избранные&nbsp;</td>
							<td><label for="spm" tabindex="1"></label><input id="spm" type="radio" name="showmode" value="notspam"/> без спама&nbsp;</td>
							<td><label for="ospm" tabindex="1"></label><input id="ospm" type="radio" name="showmode" value="onlyspam"/> только спам&nbsp;</td>
							<td><label for="ol" tabindex="1"></label><input id="ol" type="radio" name="showmode" value="showall" checked="true"/> все&nbsp;</td><br>
							</tr>
						</table>
						<table cellspacing="0" cellpadding="0">
							<tr>
							<td><label for="dub" tabindex="1"></label><input id="dub" type="checkbox" name="unrep" class="rescheck"/> фильтровать дубли&nbsp;</td>
							<td><select width="70px" name="os" class="styled">
								<option value="all" SELECTED>Все категории</option>
								<option value="1">Не важно</option>
								<option value="2">Средне</option>
								<option value="3">Важно</option>
								<option value="4">Очень важно</option>
							</select></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><img src="/img/time.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите временной период, за который вы хотите исследовать упоминания или используйте стандартные временные периоды.">Время</font></div></td>
					<td style="font-size: 11px;">с <input name="ntime" id="ntime" value="'.date('d.m.Y',$order['order_start']).'" type="text"> по <input name="etime" id="etime" value="'.((intval($order['order_end']>time())||(intval($order['order_end'])==0))?date('d.m.Y',mktime(0,0,0,date('n'),date('j')-1,date('Y'))):date('d.m.Y',$order['order_end']-60*60*24)).'" type="text"> <b>или</b> <a href="#" onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">день</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-691200000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">неделя</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-2678400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">месяц</a></td>
				</tr>
				<tr>
					<td><img src="/img/nastr.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите эмоциональную окраску мнений.">Тональность</font></div></td>
					<td style="font-size: 11px;">
					<table cellspacing="0" cellpadding="0">
						<tr>
						<td><label for="neu" tabindex="1"></label><input id="neu" type="checkbox" name="neutral" class="rescheck"/> нейтральные&nbsp;</td>
						<td><label for="pol" tabindex="1"></label><input id="pol" type="checkbox" name="positive" class="rescheck"/> положительные&nbsp;</td>
						<td><label for="otr" tabindex="1"></label><input id="otr" type="checkbox" name="negative" class="rescheck"/> отрицательные&nbsp;</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>';
				if (count($mtags)==0)
				{
					$deltt='';
				}
				else
				{
					$deltt='<a href="#" class="vtip" title="Удалить тег" onclick="loadmodal1(\'/deletetagsmain?orid='.intval($user['user_id']).'&oorid='.$order_id.'\',604,'.(200+count($mtags)*30).');return false;"><img src="/img/Button-Close-icon.png"></a>';
				}
				if (count($mtags)>8)
				{
					$addtt='';
				}
				else
				{
					$addtt='<a href="#" class="vtip" title="Добавить тег" onclick="loadmodal1(\'/addtagsmain?orid='.$order_id.'&tags='.urlencode(json_encode($mtags)).'\',604,250);return false;"><img src="/img/Button-Add-icon.png"></a>&nbsp;<a href="#" class="vtip" title="Редактировать тег" onclick="loadmodal1(\'/edittagsmain?orid='.intval($user['user_id']).'&tags='.urlencode(json_encode($mtags)).'\',604,'.(200+count($mtags)*30).');return false;"><img src="/img/edit-icon.png"></a>';
				}
				if ($_GET['com2']==1)
				{
					$addtt='';
					$deltt='';
				}
				$html_out.='
					<td><img src="/img/tag-icon.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите теги, которые вы хотели бы отобразить в выдаче.">Теги</font></div>&nbsp;'.$addtt.'&nbsp;'.$deltt.'</td>
					<td style="font-size: 11px;"><table cellspacing="0" cellpadding="0"><tr>';
					foreach ($mtags as $key => $item)
					{
						$ti++;
						$html_out.='<td width="120"><label for="tgp'.$key.'" tabindex="1"></label><input id="tgp'.$key.'" type="checkbox" name="tg'.$key.'" value="'.$key.'"> '.$item.'&nbsp;</td>';
						if ($ti % 4 == 0)
						{
							$html_out.='</tr><tr>';
						}
					}
					$html_out.='<td width="120"><label for="tgpn" tabindex="1"></label><input id="tgpn" type="checkbox" name="tgn" value="na"> Без тегов&nbsp;</td>';
					$html_out.='</tr></table></td>
				</tr>
				<tr>
					<td valign="top"><img src="/img/sources.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите ресурсы, по которым хотите посмотреть выдачу.">Ресурсы</font></div></td>
					<td style="font-size: 11px;">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<label for="resch11" tabindex="1" onclick="if($(\'#resch11\').is(\':checked\')) {checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.u_ssoc\');} else { uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.u_ssoc\');}">социальные сети&nbsp;</label><input type="checkbox" name="markt" class="rescheck2" id="resch11" value="true" onclick=" if($(\'#resch11\').is(\':checked\')) { checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.u_ssoc\');} else {uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.u_ssoc\');} " value="социальные сети"/>
								</td>
								<td>
									<label for="resch12" tabindex="1" onclick="if($(\'#resch12\').is(\':checked\')) {uncheckPrettyCb(\'.novres\'); uncheckPrettyCb(\'.u_novres\');} else { checkPrettyCb(\'.novres\'); checkPrettyCb(\'.u_novres\');}">новостные ресурсы&nbsp;</label><input type="checkbox" name="markt1" class="rescheck2" id="resch12" value="true" onclick="uecheckAllPC(\'.novres\'); uacheckPrettyCb(\'.u_novres\'); return false;"/>
								</td>
								<td>
									<label for="resch13" tabindex="1" onclick="if($(\'#resch13\').is(\':checked\')) {uncheckPrettyCb(\'.microb\'); uncheckPrettyCb(\'.u_microb\');} else { checkPrettyCb(\'.microb\'); checkPrettyCb(\'.u_microb\');}">микроблоги&nbsp;</label><input type="checkbox" name="markt2" class="rescheck2" id="resch13" value="true" onclick="uecheckAllPC(\'.microb\'); uacheckPrettyCb(\'.u_microb\'); return false;"/>
								</td>
								<td>
									<label for="resch14" tabindex="1" onclick="if($(\'#resch14\').is(\':checked\')) {uncheckPrettyCb(\'.forabl\'); uncheckPrettyCb(\'.u_forabl\');} else { checkPrettyCb(\'.forabl\'); checkPrettyCb(\'.u_forabl\');}">форумы и блоги</label><input type="checkbox" name="markt3" class="rescheck2" id="resch14" value="true" onclick="uecheckAllPC(\'.forabl\'); uacheckPrettyCb(\'.u_forabl\'); return false;"/>
								</td>
							</tr>
						</table>
						';

					if ($order_id!=0)
					{
						$sourcesgr=$sources;
						unset($sources);
						//$fn = "/var/www/data/blog/".intval($order_id).".src";
						//$h = fopen($fn, "r");
						//$data = fread($h, filesize($fn));
						$data=$order['order_src'];
						//print_r($sourcesgr);
						$sources=json_decode($data, true);

						//$data=$order['order_src'];
						//$sources=json_decode($data, true);

						//$iii=0;
						arsort($metrics['location_cou']);
						$sources2=array_slice($sources, 10, -1);
						uksort($sources2,"strcoll");
						//print_r($sources2);
						$sources=array_slice($sources, 0, 10);
						/*foreach($sources2 as $src2)
						{
							$sources[]=$src2;
						}*/
						$sources=array_merge($sources, $sources2);


						$other=0;
						$k=0;
						$html_out.='<table cellspacing="0" cellpadding="0"><tr>';
						//$sources = array_multisort($sources,);
						//ksort($sources);
						foreach ($sources as $i => $source)
						{
							$k++;
							if ($k<10)
							{
								if (($i=="facebook.com") || ($i=="vkontakte.ru"))
								{
									$html_out.='<td><label for="ssoc'.$k.'"></label><input type="checkbox" name="res_'.$i.'" class="ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								else
								if ($i=="mail.ru")
								{
									$html_out.='<td><label for="novres'.$k.'"></label><input type="checkbox" name="res_'.$i.'" class="novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								else
								if (($i=="twitter.com") || ($i=="rutvit.ru"))
								{
									$html_out.='<td><label for="microb'.$k.'"></label><input type="checkbox" name="res_'.$i.'" class="microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								else
								{
									$html_out.='<td><label for="forabl'.$k.'"></label><input type="checkbox" name="res_'.$i.'" class="forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
								}
								if (($k%4)==0) $html_out.= '</tr><tr>';
								$socialall+=$source;
							}
							else
							{
								if ($k==10)
								{
									$html_out1.='<div id="othr" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: '.(($_GET['com2']==1)?'360':'500').'px; left: '.(($_GET['com2']==1)?'400':'520').'px; background-color: #FFFFFF;"><!--<input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="rescheck1" id="resch1" value="true"/> '.$i.' ('.$source.')&nbsp;--><table><tr>';
									if (($i=="facebook.com") || ($i=="vkontakte.ru"))
									{
										$html_out1.='<td><label for="ssoc'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if ($i=="mail.ru")
									{
										$html_out1.='<td><label for="novres'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if (($i=="twitter.com") || ($i=="rutvit.ru"))
									{
										$html_out1.='<td><label for="microb'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									{
										$html_out1.='<td><label for="forabl'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									$socialall+=$source;
								}
								else
								{
									if (($i=="facebook.com") || ($i=="vkontakte.ru"))
									{
										$html_out1.='<tr><td><label for="ssoc'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if ($i=="mail.ru")
									{
										$html_out1.='<tr><td><label for="novres'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									if (($i=="twitter.com") || ($i=="rutvit.ru"))
									{
										$html_out1.='<tr><td><label for="microb'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									else
									{
										$html_out1.='<tr><td><label for="forabl'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" class="u_forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
									}
									$socialall+=$source;
								}
								$other+=$source;
							}
						}
						if ($k>10) $html_out1.='</table></div>';
						$socialother=$other;//<input type="checkbox" checked="checked" name="res_other" class="rescheck" value="true" />
						$html_out.='<td><label for="op" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}"></label><input type="checkbox" class="rescheck" id="op" value="true" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}" /><a id="lothr" class="vtip" title="Ресурсов: '.(count($sources)-9).'" href="#" onclick="$(\'#othr\').toggle(); return false;">другие</a> ('.$other.')&nbsp;</td>
		';
						$html_out.='</tr></table>';
						$html_out.=$html_out1;
						fclose($h);
					}		
					$html_out.='<div id="lothr1" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 495px; left: 380px; background-color: #FFFFFF;"><table>';		
					$ii=0;
					arsort($metrics['location']);
					foreach ($metrics['location'] as $k => $item)
					{
						if ($k!='')
						{
							$ii++;
							$html_out.='<tr><td><label for="loc'.$ii.'"></label><input type="checkbox" class="cou_'.$wobot['destn3'][$k].'" style="margin: 2px;" name="loc_'.$k.'" id="loc'.$ii.'" value="true"/> '.$k.' ('.$item.')&nbsp;<br></td></tr>';
						}
					}
					$html_out.='<tr><td><label for="locothr"></label><input type="checkbox" style="margin: 2px;" class="cou_na" name="loc_othr" id="locothr"/> Неопределено ('.$metrics['location'][''].')&nbsp;<br></td></tr>';
	$html_out.='</table></div><table style="margin-top: 5px;" cellspacing="0" cellpadding="0"><tr>';
					$ii=0;
					$chall.=' checkPrettyCb(\'.cc_cou_na\');';
					$unchall.=' uncheckPrettyCb(\'.cc_cou_na\');';
					foreach ($metrics['location_cou'] as $kk => $itt)
					{
						$chall.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
						$unchall.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
						if ($kk!='')
						{
							$ii++;
							if ($ii<4)
							{
								if (($ii % 4) == 0)
								{
									$html_out.='</tr><tr>';
								}
								$html_out.='<td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td>';
							}
							elseif ($ii==4)
							{
								$html_out.='<td><div id="lothr5" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 550px; left: 670px; background-color: #FFFFFF;"><table cellspacing="0" cellpadding="0"><tr><td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td></tr>';		
								$challoth.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
								$unchalloth.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
							}
							elseif ($ii>4)
							{
								$html_out.='<tr><td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td></tr>';
								$challoth.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
								$unchalloth.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
							}
						}
					}
					if (count($metrics['location_cou'])>4)
					{
						$html_out.='</table></div></td><td><label for="op1" onclick="if($(\'#op1\').is(\':checked\')) {'.$challoth.'} else { '.$unchalloth.'}"></label><input type="checkbox" class="rescheck" id="op1" value="true" onclick="if($(\'#op1\').is(\':checked\')) {'.$challoth.'} else {'.$unchalloth.'}" /><a href="#" onclick="$(\'#lothr5\').toggle(); return false;">Другие</a>&nbsp;('.$count_cou.')</td>';
					}
					$html_out.='<td><label for="loc_countna" onclick="if(!$(\'#loc_countna\').is(\':checked\')) {checkPrettyCb(\'.cou_na\');} else { uncheckPrettyCb(\'.cou_na\');}"></label><input type="checkbox" style="margin: 2px;" name="loc_othr1" class="cc_cou_na"  id="loc_countna"/>Неопределено ('.$metrics['location_cou'][''].')</td></tr></table><a href="#" id="locopen">Города</a><br>';
		$html_out.=				'
	<!--<br><br><input type="checkbox" name="markt" checked="checked" class="rescheck2" id="resch11" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'ssoc\') { if ($(\'#resch11\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>социальные сети&nbsp;<input type="checkbox" name="markt1" checked="checked" class="rescheck2" id="resch12" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'novres\') { if ($(\'#resch12\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>новостные ресурсы&nbsp;<input type="checkbox" name="markt2" checked="checked" class="rescheck2" id="resch13" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'microb\') { if ($(\'#resch13\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>микроблоги&nbsp;<input type="checkbox" name="markt3" checked="checked" class="rescheck2" id="resch14" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'forabl\') { if ($(\'#resch14\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>форумы и блоги--><a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/new/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&unch=1'.(($_GET['com2']==1)?'&com2=1':'').'\',function(){ afterunch(); afterunch1();}); /*uncheckPrettyCb(\'.rescheck2\'); uncheckPrettyCb(\'.rescheck\'); uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.novres\'); uncheckPrettyCb(\'.microb\'); uncheckPrettyCb(\'.forabl\'); uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\'); '.$unchall.'*/ return false;">снять все</a> <a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/new/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&ch=1'.(($_GET['com2']==1)?'&com2=1':'').'\',function(){ afterunch(); afterunch1();}); checkPrettyCb(\'.rescheck2\'); checkPrettyCb(\'.rescheck\'); checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.novres\'); checkPrettyCb(\'.microb\'); checkPrettyCb(\'.forabl\'); checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\'); '.$chall.' Custom.init; return false;">отметить все</a><a href="#" style="font-size: 12px;" onclick="$(\'input\').each(function(){ if($(this).attr(\'id\')==(\'blogs\')) {$(this).attr(\'checked\',\'checked\');}});"></a><br>Экспорт в формате: <select name="format" class="styled"><option value="excel">Excel</option><option value="word">Word</option><option value="openoffice">OpenOffice</option></select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right"><img src="/img/show.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\''.$config['html_root'].'comment2\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Список мнений по заданным критериям появится в новой вкладке.">Показать</font></a> <img src="/img/pdf.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\'/export\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Сохранить отчет в формате Word.">Экспорт</font></a></td>
				</tr>
				</table>
				</form>
				</div>';
				echo $html_out;
}

if (($_POST['plink']!='') && ($_POST['phrase']!=''))
{
	echo GetFullPost($_POST['plink'],$_POST['phrase']);
}
if ((intval($_POST['order_id'])!=0) && ($_POST['typep']!=''))
{
   echo $_POST['typep'];//."UPDATE blog_post SET post_type=".intval($_POST['typep'])." WHERE order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['link'])."') LIMIT 1";
   //$db->query("UPDATE blog_post SET post_type=".intval($_POST['typep'])." WHERE order_id=".intval($_POST['order_id'])." AND (post_link='".addslashes($_POST['link'])."' OR post_link='".addslashes($_POST['link']."\n")."')");
	$resfav2=$db->query("UPDATE blog_post SET post_type=".intval($_POST['typep'])." WHERE order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['link'])."') LIMIT 1");

}
if (intval($_POST['order_id'])!=0)
{
			$res=$db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1"); // проверка order_id
		if (mysql_num_rows($res)==0) die();

		if (intval($_POST['post_id'])!=0) {
			//UPDATE blog_post SET post_tag = 'User,Client' WHERE post_id = 1;
			$restag=$db->query("UPDATE blog_post SET post_tag = '".addslashes($_POST['tag'])."' WHERE post_id=".intval($_POST['post_id'])." LIMIT 1");
			echo $_POST['tag'];
		}
			//---------------------------------SPAM ADD------------------------------------
			/*if ($_POST['spam']!='')
			{
				$resspam1=$db->query("SELECT * from blog_spam WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".$_POST['spam']."' LIMIT 1");
				$orderspam1 = $db->fetch($resspam1);
				if (intval($orderspam1['spam_id'])==0)
				{
				$resspam2=$db->query("INSERT INTO  `blog_spam` (`spam_link` ,`order_id`) VALUES ('".$_POST['spam']."' , '".$_POST['order_id']."');");
				}
				$_POST['spam']='';
			}*/
			//-----------------------------------------------------------------------------

			 //---------------------------------SPAM ADD------------------------------------
			if ($_POST['spam']!='')
			{
				$resspam1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_id='".intval($_POST['spam'])."' LIMIT 1");
				$orderspam1 = $db->fetch($resspam1);
					$resspam2=$db->query("UPDATE `blog_post` set post_spam=".(intval($orderspam1['post_spam'])==1?0:1)." WHERE  order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['spam'])."') LIMIT 1");
				if (intval($orderspam1['post_spam'])==0) echo "spamm2";
				else echo "spamm";
				
				$_POST['spam']='';
			}
			//-----------------------------------------------------------------------------		

			//---------------------------------FAV ADD------------------------------------
			if ($_POST['fav']!='')
			{
				$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['fav'])."') LIMIT 1");
				$orderfav1 = $db->fetch($resfav1);
				//if (intval($orderfav1['fav_id'])==0)
				//{
					$resfav2=$db->query("UPDATE `blog_post` set post_fav=".(intval($orderfav1['post_fav'])==1?0:1)." WHERE order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['fav'])."') LIMIT 1");
					//fwrite($fpizdec,"fav");
					//echo "fav";
				//}
				if (intval($orderfav1['post_fav'])==0) echo "fav";
				else echo "fav2";
				$_POST['fav']='';
				//fwrite($fpizdec,"fav2");
			}


			if ($_POST['positive']!='')
			{
				//$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['positive'])."' LIMIT 1");
				//$orderfav1 = $db->fetch($resfav1);
				$resfav2=$db->query("UPDATE blog_post SET post_nastr=1 WHERE order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['positive'])."') LIMIT 1");
				echo "green";
			}
			if ($_POST['negative']!='')
			{
				//$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['negative'])."' LIMIT 1");
				//$orderfav1 = $db->fetch($resfav1);
				$resfav2=$db->query("UPDATE blog_post SET post_nastr=-1 WHERE order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['negative'])."') LIMIT 1");
				echo "red";
			}
			if ($_POST['neutral']!='')
			{
				//$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['neutral'])."' LIMIT 1");
				//$orderfav1 = $db->fetch($resfav1);
				$resfav2=$db->query("UPDATE blog_post SET post_nastr=0 WHERE order_id='".intval($_POST['order_id'])."' AND (post_id='".intval($_POST['neutral'])."') LIMIT 1");
				echo "black";
			}
			if (($_POST['id']!='') && ($_POST['tags']!=''))
			{
				//print_r($_POST);
				//echo $_POST['tags'].' '.$_POST['value'];
				//$mtags=json_decode(urldecode($_POST['tagsall']),true);
				//print_r($mtags);
				$masre=explode('|',urldecode($_POST['retags']));
				foreach($masre as $item)
				{
					if ($item!='')
					{
						$regex='/\_\d+\_(?<data>.*)/is';
						preg_match_all($regex,$item,$out);
						//print_r($out);
						$mtags[]=$out['data'][0];
					}
				}
				//print_r($mtags);
				$tagl=json_decode(urldecode($_POST['taglinks']),true);
				//print_r($tagl);
				foreach ($tagl as $key => $item)
				{
					if (in_array($item,$mtags))
					{
						$updtags.=$key.',';
					}
				}
				$updtags=substr($updtags, 0, strlen($updtags)-1);
				$resfav2=$db->query("UPDATE  `wobot`.`blog_post` SET  `post_tag` =  '".$updtags."' WHERE  `blog_post`.`post_id` =".$_POST['id'].";");
				//echo "UPDATE  `wobot`.`blog_post` SET  `post_tag` =  '".$updtags."' WHERE  `blog_post`.`post_id` =".$_POST['id'].";";
				//echo "UPDATE  `wobot`.`blog_post` SET  `post_tag` =  '".$updtags."' WHERE  `blog_post`.`post_id` =".$_POST['id'].";";
				//UPDATE  `wobot`.`blog_post` SET  `post_tag` =  'Client,User' WHERE  `blog_post`.`post_id` =774598;
			}
			//echo 123;
			//-----------------------------------------------------------------------------		
}
//fclose($fpizdec);
?>
