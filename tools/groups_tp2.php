<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

function validateURL($url)
{
$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-fа-яА-Я\d]{2,2})+(:([\d\w]|%[a-fA-fа-яА-Я\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-fа-яА-Я\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-fа-яА-Я\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-fа-яА-Я\d]{2,2})*)?$/';
return preg_match($pattern, $url);
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
	elseif ($_POST['act']=='add')
	{
		$groups=explode(',',$_POST['groups']);
		//print_r($groups);
		foreach ($groups as $item)
		{
			//echo urldecode($item).' ';
			//if (validateURL(urldecode($item)))
			{
				//echo 1;
				$hn=parse_url($item);
				$hn=$hn['host'];
				//print_r($hn);
				if ($hn=='vk.com' || $hn=='vkontakte.ru')
				{
					//$qw_tp=$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$_POST['order_id'].','',\'vk\')');
					$out['id'][0]='';
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
						$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'vk\')');
					}
					
				}
				elseif ($hn=='www.facebook.com')
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
						$regex='/\/(?<id>[a-zA-Zа-яА-Я0-9\-ёЁ]+)$\/?$/isu';
						preg_match_all($regex,trim($item),$out);
						//echo $out['id'][0].' ';
					}
					if ($out['id'][0]!='')
					{
						$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$_POST['order_id'].',\''.$out['id'][0].'\',\'fb\')');
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
	<div style="border: 1px solid black; padding: 10px;">
	<form method="POST" id="addform">
	<input type="hidden" name="act" value="add">
	<b>Добавление новых групп:</b>
	<br>Отчет:
	<select name="order_id">
	';
	foreach ($orids as $item)
	{
		echo '<option value="'.$item.'">'.$item.'</option>';
	}
	echo '
	</select><br>
	Список ссылок на группы(пример ссылок <b><br>http://www.facebook.com/RostelecomTatarstan,<br>'.urldecode('http://www.facebook.com/pages/%D0%9C%D0%B0%D0%BA%D1%80%D0%BE%D1%80%D0%B5%D0%B3%D0%B8%D0%BE%D0%BD%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9-%D1%84%D0%B8%D0%BB%D0%B8%D0%B0%D0%BB-%D0%A1%D0%B5%D0%B2%D0%B5%D1%80%D0%BE-%D0%97%D0%B0%D0%BF%D0%B0%D0%B4-%D0%9E%D0%90%D0%9E-%D0%A0%D0%BE%D1%81%D1%82%D0%B5%D0%BB%D0%B5%D0%BA%D0%BE%D0%BC/382135805146443').',<br>http://www.facebook.com/groups/383816194985813/</b>):<br>
	<textarea cols="170" rows="10" name="groups"></textarea>
	<br>
	<input type="submit" value="Добавить">
	</form>
	</div>
	<br>
	<table border="1"><tr><td>id отчета</td><td>id группы</td><td>время сбора</td><td>Ресурс группы</td></tr>';
	$qw_tp=$db->query('SELECT a.tp_id,a.order_id,a.tp_last,a.gr_id,a.tp_type FROM blog_tp as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_GET['user_id']));
	while ($tp=$db->fetch($qw_tp))
	{
		if ($tp['tp_type']=='fb')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://facebook.com/'.$tp['gr_id'].'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td></tr>';
		}
		elseif ($tp['tp_type']=='vk')
		{
			echo '<tr><td>'.$tp['order_id'].'</td><td><a href="http://vk.com/'.(preg_match('/[\d]/siu',$tp['gr_id'][0])?'club'.$tp['gr_id']:$tp['gr_id']).'" target="_blank">'.$tp['gr_id'].'</a></td><td>'.date('d.m.y H:i:s',$tp['tp_last']).'</td><td>'.$tp['tp_type'].'</td><td><a href="#" onclick="document.getElementById(\'idtp\').value='.$tp['tp_id'].'; document.getElementById(\'submform\').submit();">X</a></td></tr>';
		}
		//echo $tp['order_id'].' '.$tp['tp_last'].' '.$tp['gr_id'].' '.$tp['tp_type'].'<br>';
	}
	echo '</table><a href="http://bmstu.wobot.ru/new?user_id='.$_GET['user_id'].'">назад</a>';
}

?>