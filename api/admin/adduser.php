<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

if ((trim($_POST['user_email'])!='') && (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($_POST['user_email']))))
{
	if (($_POST['user_email']!='')&&($_POST['user_pass']!='')&&($_POST['user_name']!='')&&($_POST['user_contact']!='')&&($_POST['user_company']!=''))
	{
		$yet=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($_POST['user_email']).'\' LIMIT 1');
		if (mysql_num_rows($yet)==0)
		{
			if (in_array(intval($_POST['tariff_id']),array(12,13,14))) $user_setting['user_reaction']=1;
			else $user_setting=array();
			$db->query('INSERT INTO users (user_email, user_pass, user_name, user_contact, user_company, user_money, user_ctime,user_active,user_settings) values ("'.addslashes($_POST['user_email']).'","'.md5($_POST['user_pass']).'","'.addslashes($_POST['user_name']).'","'.addslashes($_POST['user_contact']).'","'.addslashes($_POST['user_company']).'","'.intval($_POST['user_money']).'", "'.time().'", "'.intval($_POST['user_active']).'","'.addslashes(json_encode($user_setting)).'")');
			$adduser_id=$db->insert_id();
			if($_POST['tariff_id']!="null" && trim($_POST['user_to_date'])!=''){
				$db->query('INSERT INTO user_tariff (user_id, tariff_id, ut_date) values ("'.$adduser_id.'","'.intval($_POST['tariff_id']).'","'.strtotime($_POST['user_to_date']).'")');
			}
			$outmas['user_id']=$adduser_id;
			//echo 'Пользователь добавлен<br>';
			/*echo "Tariff ".$_POST['tariff_id']." ".trim($_POST['user_to_date']);
			if($_POST['tariff_id']!="null" && trim($_POST['user_to_date'])!=''){
				echo "Got tariff date".strtotime($_POST['user_to_date']);
			}*/
		}
		else
		{
			//echo 'Такой пользователь уже существует<br>';
			$already=1;
			$outmas['already']=1;
		}
	}
	else
	{
		//echo 'Введены не все поля<br>';
	}
}

die(json_encode($outmas));

?>