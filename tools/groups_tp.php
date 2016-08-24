<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

$_POST['filter']=($_POST['filter']=='on'?1:0);

function validateURL($url)
{
$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-fа-яА-Я\d]{2,2})+(:([\d\w]|%[a-fA-fа-яА-Я\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-fа-яА-Я\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-fа-яА-Я\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-fа-яА-Я\d]{2,2})*)?$/';
return preg_match($pattern, $url);
}

if ($_GET['filter']!='')
{
	$db->query('UPDATE blog_tp SET tp_filter=1 WHERE order_id='.intval($_GET['order_id']).' AND tp_id='.$_GET['filter']);
}
if ($_GET['nofilter']!='')
{
	$db->query('UPDATE blog_tp SET tp_filter=0 WHERE order_id='.intval($_GET['order_id']).' AND tp_id='.$_GET['nofilter']);
}

if (isset($_GET['user_id']))
{
	$qw_tp=$db->query('SELECT b.order_id FROM blog_orders as b LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_GET['user_id']).' GROUP BY order_id');
	while ($orid=$db->fetch($qw_tp))
	{
		$orids[]=$orid['order_id'];
	}
	//print_r($_GET);
	//print_r($_POST);
	if ($_POST['act']=='del')
	{
		$qw_tp=$db->query('DELETE FROM blog_tp WHERE tp_id='.$_POST['id']);
		//echo 'DELETE FROM blog_tp WHERE tp_id='.$_POST['id'];
	}
	elseif ($_POST['act']=='refreash')
	{
		// echo 'UPDATE blog_orders SET third_sources=2 WHERE order_id='.$_POST['order_id'];
		$db->query('UPDATE blog_orders SET third_sources=2 WHERE order_id='.$_POST['order_id']);
	}
	elseif ($_GET['tp_id']!='')
	{
		file_get_contents('http://188.120.239.225/tools/3rd_cs.php?tp_id='.$_GET['tp_id']);
	}
	elseif ($_POST['act']=='add')
	{
		$groups=explode(',',$_POST['groups']);
		//print_r($groups);
		foreach ($groups as $item)
		{
			$item=trim($item);
			//echo urldecode($item).' ';
			//if (validateURL(urldecode($item)))
			{
				//echo 1;
				$hn=parse_url($item);
			    $hn=$hn['host'];
			    $ahn=explode('.',$hn);
			    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
				$hh = $ahn[count($ahn)-2];
				// print_r($hn);
				if ($hn=='vk.com' || $hn=='vkontakte.ru')
				{
					//$qw_tp=$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$_POST['order_id'].','',\'vk\')');
					$out['id'][0]='';
					if (preg_match('/vk\.com\/board\d+/isu',$item))
					{
						$regex='/board(?<id>\d+)/isu';
						preg_match_all($regex, trim($item), $out);
						if ($out['id'][0]!='')
						{
							$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'vk_board\' AND order_id='.$_POST['order_id']);
							if (mysql_num_rows($iss)==0)
							{
								$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'vk_board\','.intval($_POST['filter']).')');
							}
							continue;
						}
					}
					if (preg_match('/vk\.com\/videos[\-]?\d+/isu',$item))
					{
						$regex='/videos(?<id>[\-]?\d+)/isu';
						preg_match_all($regex, trim($item), $out);
						if ($out['id'][0]!='')
						{
							$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'vk_video\' AND order_id='.$_POST['order_id']);
							if (mysql_num_rows($iss)==0)
							{
								$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'vk_video\','.intval($_POST['filter']).')');
							}
							continue;
						}
					}
					if (preg_match('/vk\.com\/club\/?/isu',$item))
					{
						//echo 1;
						$regex='/\/club(?<id>\d+)\/?$/isu';
						preg_match_all($regex,trim($item),$out);
						//echo $out['id'][0].' ';
					}
					elseif (preg_match('/vk\.com\/public\/?/isu',$item))
					{
						//echo 2;
						$regex='/\/public(?<id>\d+)\/?$/isu';
						preg_match_all($regex,trim($item),$out);
						//echo $out['id'][0].' ';
					}
					else
					{
						//echo 3;
						$regex='/vk\.com\/(?<id>[\da-zA-Zа-яА-ЯёЁ\.\_]+)\/?$/isu';
						preg_match_all($regex,trim($item),$out);
						//echo $out['id'][0].' ';
					}
					if ($out['id'][0]!='')
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'vk\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'vk\','.intval($_POST['filter']).')');
						}
					}
					
				}
				elseif ($hn=='facebook.com')
				{
					//echo 123;
					$out['id'][0]='';
					if (preg_match('/\/pages\/?/isu',$item))
					{
						//echo 1;
						$regex='/\/(?<id>\d+)\/?$/isu';
						preg_match_all($regex,trim($item),$out);
						//echo $out['id'][0].' ';
					}
					elseif (preg_match('/\/groups\/?/isu',$item))
					{
						//echo 2;
						$regex='/\/(?<id>\d+)\/?$/isu';
						preg_match_all($regex,trim($item),$out);
						//echo $out['id'][0].' ';
					}
					else
					{
						//echo 3;
						$regex='/\/(?<id>[a-zA-Zа-яА-Я0-9\-ёЁ\.]+)$\/?$/isu';
						preg_match_all($regex,trim($item),$out);
						//echo $out['id'][0].' ';
					}
					if ($out['id'][0]!='')
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'fb\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'fb\','.intval($_POST['filter']).')');
						}
					}
				}
				elseif ($hn=='yandex.ru')
				{
					$regex='/http\:\/\/market\.yandex\.ru\/shop\-opinions\.xml\?shop\_id\=(?<id>\d+)$/isu';
					preg_match_all($regex,trim($item),$out);
					if ($out['id'][0]=='')
					{
						$regex='/http\:\/\/market\.yandex\.ru\/shop\/(?<id>\d+)\/reviews/isu';
						preg_match_all($regex,trim($item),$out);
					}
					if ($out['id'][0]=='')
					{
						$regex='/http\:\/\/market\.yandex\.ru\/model\.xml\?modelid=(?<modelid>\d+)\&hid=(?<hid>\d+)/isu';
						preg_match_all($regex,trim($item),$out);
					}
					// print_r($out);
					if (($out['id'][0]!='')||($out['modelid'][0]!=''))
					{
						if ($out['id'][0]!='') $out['id'][0]=json_encode(array('shop_id'=>$out['id'][0]));
						elseif ($out['modelid'][0]!='') $out['id'][0]=json_encode(array('modelid'=>$out['modelid'][0],'hid'=>$out['hid'][0]));
						//echo 'INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'ya_market\')<br>';
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'ya_market\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'ya_market\','.intval($_POST['filter']).')');
						}
					}
				}
				elseif ($hn=='mail.ru')
				{
					$regex='/http\:\/\/torg\.mail\.ru\/review\/shops\/(?<id>.*?)\//isu';
					preg_match_all($regex,trim($item),$out);
					if ($out['id'][0]!='')
					{
						//echo 'INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'torg_mail\')<br>';
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'torg_mail\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'torg_mail\','.intval($_POST['filter']).')');
						}
					}
				}
				elseif ($hn=='banki.ru')
				{
					$regex='/\?PAGE_NAME=read\&FID=(?<fid>\d+)\&TID=(?<tid>\d+)/isu';
					preg_match_all($regex, trim($item), $out);
					if (($out['tid'][0]!='') && ($out['fid'][0]!=''))
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['fid'][0].'_'.$out['tid'][0].'\' AND tp_type=\'banki_forum\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['fid'][0].'_'.$out['tid'][0].'\',\'banki_forum\','.intval($_POST['filter']).')');
						}
					}
					$regex='/\/friends\/group\/(?<fid>[^\/]*?)\/forum\/(?<tid>\d+)\//isu';
					preg_match_all($regex, trim($item), $out);
					if (($out['tid'][0]!='') && ($out['fid'][0]!=''))
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['fid'][0].'/'.$out['tid'][0].'\' AND tp_type=\'banki_friends\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['fid'][0].'/'.$out['tid'][0].'\',\'banki_friends\','.intval($_POST['filter']).')');
						}
					}
					$regex='/\/services\/questions\-answers\/\?id=(?<tid>\d+)/isu';
					preg_match_all($regex, trim($item), $out);
					if ($out['tid'][0]!='')
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['tid'][0].'\' AND tp_type=\'banki_question\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['tid'][0].'\',\'banki_question\','.intval($_POST['filter']).')');
						}
					}
					$regex='/\/services\/responses\/bank\/(?<idresp>.*?)\//isu';
					preg_match_all($regex, trim($item), $out);
					if ($out['idresp'][0]!='')
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['idresp'][0].'\' AND tp_type=\'banki_responses\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['idresp'][0].'\',\'banki_responses\','.intval($_POST['filter']).')');
						}
					}
				}
				elseif ($hn=='google.com')
				{
					$regex='/plus.google.com\/(?<id>[^\/]*)/isu';
					preg_match_all($regex, trim($item), $out);
					if ($out['id'][0]!='')
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'gp\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'gp\','.intval($_POST['filter']).')');
						}
					}
				}
				elseif ($hn=='twitter.com')
				{
					$regex='/twitter\.com\/(?<id>[^\/]*)/isu';
					preg_match_all($regex, trim($item), $out);
					if ($out['id'][0]!='')
					{
						$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'twitter\' AND order_id='.$_POST['order_id']);
						if (mysql_num_rows($iss)==0)
						{
							$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'twitter\','.intval($_POST['filter']).')');
						}
					}
				}
			}
		}
		$accounts=explode(',',$_POST['accounts']);
		foreach ($accounts as $item)
		{
			$item=trim($item);
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			//echo $hn;
			//print_r($hn);
			if ($hn=='vk.com' || $hn=='vkontakte.ru')
			{
				$out['id'][0]='';
				if (preg_match('/vk\.com\/id\/?/isu',$item))
				{
					//echo 1;
					$regex='/\/id(?<id>\d+)\/?$/isu';
					preg_match_all($regex,trim($item),$out);
					//echo $out['id'][0].' ';
				}
				else
				{
					//echo 3;
					$regex='/vk\.com\/(?<id>[\da-zA-Zа-яА-ЯёЁ\.\_]+)\/?$/isu';
					preg_match_all($regex,trim($item),$out);
					//echo $out['id'][0].' ';
				}
				if ($out['id'][0]!='')
				{
					$iss=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$out['id'][0].'\' AND tp_type=\'vk_acc\' AND order_id='.$_POST['order_id']);
					if (mysql_num_rows($iss)==0)
					{
						$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'vk_acc\','.intval($_POST['filter']).')');
					}
				}
			}
		}
	}
	echo '<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<form method="POST" id="submform">
	<input type="hidden" name="act" value="del">
	<input type="hidden" name="id" value="0" id="idtp">
	</form>
	<form id="refreshform" method="POST">
	<input type="hidden" name="act" value="refreash">
	<input type="hidden" name="order_id" value="" id="refr_id">
	</form>
	<div style="border: 1px solid black; padding: 10px;">
	<form method="POST" id="addform">
	<input type="hidden" name="act" value="add">
	<b>Добавление новых групп:</b>
	<br>Отчет:
	<select name="order_id" id="sel_id">
	';
	foreach ($orids as $item)
	{
		echo '<option value="'.$item.'">'.$item.'</option>';
	}
	echo '
	</select><a href="#" onclick="document.getElementById(\'refr_id\').value=document.getElementById(\'sel_id\').value; document.getElementById(\'refreshform\').submit();">Пересобрать</a><br>
	Список ссылок на группы(пример ссылок <b><br>http://www.facebook.com/RostelecomTatarstan,<br>'.urldecode('http://www.facebook.com/pages/%D0%9C%D0%B0%D0%BA%D1%80%D0%BE%D1%80%D0%B5%D0%B3%D0%B8%D0%BE%D0%BD%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9-%D1%84%D0%B8%D0%BB%D0%B8%D0%B0%D0%BB-%D0%A1%D0%B5%D0%B2%D0%B5%D1%80%D0%BE-%D0%97%D0%B0%D0%BF%D0%B0%D0%B4-%D0%9E%D0%90%D0%9E-%D0%A0%D0%BE%D1%81%D1%82%D0%B5%D0%BB%D0%B5%D0%BA%D0%BE%D0%BC/382135805146443').',<br>http://www.facebook.com/groups/383816194985813/,<br>http://torg.mail.ru/review/shops/euromaxx-ru-cid3740/,<br>http://market.yandex.ru/shop-opinions.xml?shop_id=89991<br>http://vk.com/footbal_mem<br>http://vk.com/club263450<br>http://www.banki.ru/forum/?PAGE_NAME=read&FID=13&TID=123228<br>http://www.banki.ru/friends/group/tcs-bank/forum/92445/<br>http://www.banki.ru/services/questions-answers/?id=2836732#tags</b>):<br>
	<hr>
	<b>Группы:</b>
	<textarea cols="170" rows="10" name="groups"></textarea>
	<b>Аккаунты</b>
	<textarea cols="170" rows="10" name="accounts"></textarea>
	<br>
	Фильрация по ключевому слову: <input type="checkbox" name="filter">
	<input type="submit" value="Добавить">
	</form>
	</div>
	<br>
	<table border="1"><tr><td>id отчета</td><td>id группы</td><td>время сбора</td><td>Ресурс группы</td></tr>';
	$qw_tp=$db->query('SELECT a.tp_id,a.order_id,a.tp_last,a.gr_id,a.tp_type,a.tp_filter,b.third_sources FROM blog_tp as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_GET['user_id']));
	while ($tp=$db->fetch($qw_tp))
	{
		if ($tp['tp_last']==0) $harv='<td><a href="?user_id='.$_GET['user_id'].'&tp_id='.$tp['tp_id'].'">собрать</a></td>';
		else $harv='<td>собрано</td>';
		if ($tp['tp_type']=='fb')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://facebook.com/'.$tp['gr_id'].'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='vk')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://vk.com/'.(preg_match('/[\d]/siu',$tp['gr_id'][0])?'club'.$tp['gr_id']:$tp['gr_id']).'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='vk_acc')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://vk.com/'.(preg_match('/[\d]/siu',$tp['gr_id'][0])?'id'.$tp['gr_id']:$tp['gr_id']).'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='vk_board')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://vk.com/'.(preg_match('/[\d]/siu',$tp['gr_id'][0])?'board'.$tp['gr_id']:$tp['gr_id']).'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='vk_video')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://vk.com/videos'.$tp['gr_id'].'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='ya_market')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td>'.$tp['gr_id'].'</td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='torg_mail')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://torg.mail.ru/review/shops/'.$tp['gr_id'].'/" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='banki_forum')
		{
			$mid=explode('_', $tp['gr_id']);
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://www.banki.ru/forum/?PAGE_NAME=read&FID='.$mid[0].'&TID='.$mid[1].'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='banki_friends')
		{
			$mid=explode('/', $tp['gr_id']);
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://www.banki.ru/friends/group/'.$mid[0].'/forum/'.$mid[1].'/" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='banki_question')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://www.banki.ru/services/questions-answers/?id='.$tp['gr_id'].'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='banki_responses')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://www.banki.ru/services/responses/bank/'.$tp['gr_id'].'/" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='gp')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="https://plus.google.com/'.$tp['gr_id'].'/posts" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		elseif ($tp['tp_type']=='twitter')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="https://twitter.com/'.$tp['gr_id'].'/" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td>'.($tp['tp_filter']==1?'с фильтрацией':'без фильтрации').'</td><td><a href="?filter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">с фильтрацией</a> <a href="?nofilter='.$tp['tp_id'].'&order_id='.$tp['order_id'].'&user_id='.$_GET['user_id'].'">без фильтрации</a></td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td>'.$harv.'</tr>';
		}
		//echo $tp['order_id'].' '.$tp['tp_last'].' '.$tp['gr_id'].' '.$tp['tp_type'].'<br>';
	}
	echo '</table><a href="http://bmstu.wobot.ru/new?user_id='.$_GET['user_id'].'">назад</a>';
}

?>
