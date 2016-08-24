<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');
require_once('/var/www/com/checker.php');
require_once('/var/www/daemon/fsearch3/ch.php');

$db = new database();
$db->connect();

if ((trim($_POST['order_name'])!='')&&($_POST['order_start']!='')&&($_POST['order_end']!='')&&(intval($_POST['ut_id'])!=0)&&(trim($_POST['order_keyword'])!='')&&(check_query(trim($_POST['order_keyword']))==1))
{
	if (strtotime($_POST['order_start'])<=strtotime($_POST['order_end']))
	{
		if($_POST['order_nastr']==1){
				$order_nastr=1;
			}
			else{
				$order_nastr=0;
			}
		//echo  "!!!!!".(strtotime($_POST['order_start'])<=strtotime($_POST['order_end']));
		$db->query('INSERT INTO blog_orders (order_date,user_id, ut_id, order_id, order_name, order_keyword, order_start, order_end, third_sources, ful_com, order_engage, similar_text, order_nastr, youtube_last, order_lang,order_settings) values ("'.time().'","'.intval($_POST['user_id']).'", "'.intval($_POST['ut_id']).'", "'.intval($_POST['order_id']).'", "'.addslashes($_POST['order_name']).'", "'.addslashes($_POST['order_keyword']).'", "'.strtotime($_POST['order_start']).'", "'.strtotime($_POST['order_end']).'", 1, "'.intval($_POST['ful_com']).'", "'.intval($_POST['order_engage']).'", "1", "'.intval($order_nastr).'", "'.intval($_POST['youtube_last']).'", 2, \''.addslashes(json_encode($settings)).'\')');
		//echo 'INSERT INTO blog_orders (order_date,user_id, ut_id, order_id, order_name, order_keyword, order_start, order_end, third_sources, ful_com, order_engage, order_fb_rt, order_nastr, youtube_last, order_lang) values ("'.time().'","'.intval($_POST['user_id']).'", "'.intval($_POST['ut_id']).'", "'.intval($_POST['order_id']).'", "'.addslashes($_POST['order_name']).'", "'.addslashes($_POST['order_keyword']).'", "'.strtotime($_POST['order_start']).'", "'.strtotime($_POST['order_end']).'", 1, "'.intval($_POST['ful_com']).'", "'.intval($_POST['order_engage']).'", "'.intval($_POST['order_fb_rt']).'", "'.intval($_POST['order_nastr']).'", "'.intval($_POST['youtube_last']).'", 2)';
		$insert_new_id=$db->insert_id();
		if (strlen($_POST['order_keyword'])<80)
		{
			$novoteka_query=preg_replace('/(\&+)/isu',' $1 ',$_POST['order_keyword']);
			$novoteka_query=preg_replace('/(\|)/isu',' $1 ',$novoteka_query);
			$novoteka_query=preg_replace('/(\~+)/isu',' ~ ',$novoteka_query);
			$novoteka_query=preg_replace('/(\/\s*\(?[\+\-]\d+\s*[\+\-]\d+\)?|\/[\-\+]?\d+)/isu', ' & ', $novoteka_query);
			$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$insert_new_id.',\''.addslashes($novoteka_query).'\',\'novoteka_news\')');
			$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$insert_new_id.',\''.addslashes($novoteka_query).'\',\'google_news\')');
		}

		$nostop = val_not($_POST['order_keyword'],'');
		preg_match_all('/#(?<tag>[A-Za-z_0-9А-Яа-я]*)/isu', $nostop['kw'], $outtag);
		if(count($outtag['tag'])>0){
			$arr = $outtag['tag'];
			foreach ($arr as $key => $value) {
				$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$insert_new_id.',\''.addslashes($value).'\',\'tag_instagram\',\'1\')');
			}
		}

		die(json_encode(array('order_id'=>$insert_new_id)));
		// parseUrl('http://188.120.239.225/tools/charge.php?order_id='.$db->insert_id());
	}
	//echo 'INSERT INTO blog_orders (order_date,user_id, ut_id, order_id, order_name, order_keyword, order_start, order_end, third_sources, ful_com, order_engage, order_fb_rt, order_nastr, youtube_last, order_lang) values ("'.time().'","'.intval($_POST['user_id']).'", "'.intval($_POST['ut_id']).'", "'.intval($_POST['order_id']).'", "'.addslashes($_POST['order_name']).'", "'.addslashes($_POST['order_keyword']).'", "'.strtotime($_POST['order_start']).'", "'.strtotime($_POST['order_end']).'", 1, "'.intval($_POST['ful_com']).'", "'.intval($_POST['order_engage']).'", "'.intval($_POST['order_fb_rt']).'", "'.intval($_POST['order_nastr']).'", "'.intval($_POST['youtube_last']).'", 2)';
	//echo 'Тариф добавлен<br>';
}
else
{
	//echo 'Создание темы '.$_POST['order_name'].' '.$_POST['order_start'].' '.$_POST['order_end'].''.$_POST['ut_id'].' '.$_POST['order_keyword'].'<br>';
	if (check_query(trim($_POST['order_keyword']))!=1)
	{
		die(json_encode(array('status'=>'fail')));
	}
}

?>