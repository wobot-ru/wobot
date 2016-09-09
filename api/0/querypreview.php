<?
//require_once('/var/www/daemon/com/config.php');
//require_once('/var/www/daemon/com/func.php');
//require_once('/var/www/daemon/com/db.php');
//require_once('/var/www/daemon/bot/kernel.php');

//Ник
//Иконку сервиса
//фильтрация по языку

//print_r($_POST);



class users
{
    // property declaration
    public $link;
	public $blog_id;
	public $hn;
	public $nick;

    // method declaration
	public function get_hn()
	{
		$hn=parse_url($this->link);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		$this->hn=$hn;
		return $hn;
	}

	public function get_twitter()
	{
		global $db;
		$rgx='/twitter\.com\/(?<id_acc>.*?)\//is';
		preg_match_all($rgx,$this->link,$acc_id);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		//$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'twitter.com\' LIMIT 1');
		//if (mysql_num_rows($chbb)==0)
		//{
		//	$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'twitter\.com\',\''.$acc_id['id_acc'][0].'\')');
		//	$bb1['blog_id']=$db->insert_id();
		//}
		//else
		//{
		//	$bb1=$db->fetch($chbb);
		//}
		//$this->blog_id=$bb1['blog_id'];
		//echo $this->blog_id;
		return $this->blog_id;
	}

	public function get_livejournal()
	{
		global $db;
		$rgx='/\/\/(?<id_acc>.*?)\.livejournal/is';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'livejournal.com\' LIMIT 1');
		if (mysql_num_rows($chbb)==0)
		{
			$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'livejournal\.com\',\''.$acc_id['id_acc'][0].'\')');
			$bb1['blog_id']=$db->insert_id();
		}
		else
		{
			$bb1=$db->fetch($chbb);
		}*/
		$this->blog_id=$bb1['blog_id'];
		return $this->blog_id;
	}

	public function get_vk()
	{
		global $db;
		$rgx='/wall(?<id_acc>\d+)\_/is';
		preg_match_all($rgx,$this->link,$acc_id);
		if (intval($acc_id['id_acc'][0])==0)
		{
			$rgx1='/id(?<id_acc>\d+)\?/is';
			preg_match_all($rgx1,$this->link,$acc_id);
		}
		if (intval($acc_id['id_acc'][0])==0)
		{
			$rgx='/wall(?<id_acc>\-\d+)\_/is';
			preg_match_all($rgx,$this->link,$acc_id);
		}
		if (intval($acc_id['id_acc'][0])==0)
		{
			$rgx='/video(?<id_acc>\d+)\_/is';
			preg_match_all($rgx,$this->link,$acc_id);
		}
		if (intval($acc_id['id_acc'][0])==0)
		{
			$rgx='/note(?<id_acc>\d+)\_/is';
			preg_match_all($rgx,$this->link,$acc_id);
		}
		if (intval($acc_id['id_acc'][0])==0)
		{
			$rgx='/photo(?<id_acc>\d+)\_/is';
			preg_match_all($rgx,$this->link,$acc_id);
		}
		if (intval($acc_id['id_acc'][0])==0)
		{
			$rgx='/photo(?<id_acc>\-\d+)\_/is';
			preg_match_all($rgx,$this->link,$acc_id);
		}
		//$mas_inf_vk=get_vk($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		if (intval($acc_id['id_acc'][0])!=0)
		{
			/*$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'vkontakte.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'vkontakte\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}*/
		}
		$this->blog_id=$bb1['blog_id'];
		return $this->blog_id;
	}
	
	public function get_facebook()
	{
		global $db;
		$rgx='/\&id\=(?<id_acc>\d+)$/is';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'facebook.com\' LIMIT 1');
		if (mysql_num_rows($chbb)==0)
		{
			$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'facebook\.com\',\''.$acc_id['id_acc'][0].'\')');
			$bb1['blog_id']=$db->insert_id();
		}
		else
		{
			$bb1=$db->fetch($chbb);
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}

	public function get_mail()
	{
		global $db;
		$rgx='/http\:\/\/blogs\.mail\.ru\/(?<type_acc>[^\/]*?)\/(?<id_acc>[^\/]*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['type_acc'][0]!='community')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'mail.ru/'.$acc_id['type_acc'][0].'\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'mail\.ru/'.$acc_id['type_acc'][0].'\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}*/
		$this->blog_id=$bb1['blog_id'];
		return $this->blog_id;
	}

	public function get_liveinternet()
	{
		global $db;
		$rgx='/http\:\/\/www\.liveinternet\.ru\/users\/(?<id_acc>[^\/]*?)\//is';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'liveinternet.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'liveinternet\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}*/
		$this->blog_id=$bb1['blog_id'];
		return $this->blog_id;
	}

	public function get_ya()
	{
		global $db;
		$rgx='/http\:\/\/(?<id_acc>.*?)\.ya\.ru\//is';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if (($acc_id['id_acc'][0]!='') && ($acc_id['id_acc'][0]!='clubs'))
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'ya.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'ya\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}*/
		$this->blog_id=$bb1['blog_id'];
		return $this->blog_id;
	}
	
	public function get_yandex()
	{
		global $db;
		$rgx='/http\:\/\/fotki\.yandex\.ru\/users\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'ya.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'ya\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}*/
		$this->blog_id=$bb1['blog_id'];
		return $this->blog_id;
	}

	public function get_rutwit()
	{
		global $db;
		$rgx='/http\:\/\/rut[wv]it\.ru\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'rutwit.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'rutwit\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}

	public function get_babyblog()
	{
		global $db;
		$rgx='/http\:\/\/www\.babyblog\.ru\/user\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'babyblog.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'babyblog\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}

	public function get_blog()
	{
		global $db;
		$rgx='/http\:\/\/(?<id_acc>.*?)\.blog\.ru\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'blog.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'blog\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}

	public function get_foursquare()
	{
		global $db;
		$rgx='/foursquare\.com\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'foursquare.com\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'foursquare\.com\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}

	public function get_kp()
	{
		global $db;
		$rgx='/blog\.kp\.ru\/users\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'kp.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'kp\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}

	public function get_aif()
	{
		global $db;
		$rgx='/blog\.aif\.ru\/users\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'aif.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'aif\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}


	public function get_ff()
	{
		global $db;
		$rgx='/friendfeed\.com\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$this->nick=$acc_id['id_acc'][0];
		/*if ($acc_id['id_acc'][0]!='')
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'friendfeed.com\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'friendfeed.com\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];*/
		return $this->blog_id;
	}

	public function get_url($url)
	{
		$this->link=$url;
		$this->blog_id=0;
		$this->get_hn();
		switch ($this->hn) {
		    case 'twitter.com':
				$this->get_twitter();
				break;
		    case 'livejournal.com':
		        $this->get_livejournal();
		        break;
		    case 'vk.com':
		        $this->get_vk();
		        break;
		    case 'facebook.com':
		        $this->get_facebook();
		        break;
		    case 'mail.ru':
		        $this->get_mail();
		        break;
		    case 'liveinternet.ru':
		        $this->get_liveinternet();
		        break;
		    case 'ya.ru':
		        $this->get_ya();
		        break;
		    case 'yandex.ru':
		        $this->get_yandex();
		        break;
		    case 'rutwit.ru':
		        $this->get_rutwit();
		        break;
		    case 'rutvit.ru':
		        $this->get_rutwit();
		        break;
		    case 'babyblog.ru':
		        $this->get_babyblog();
		        break;
		    case 'blog.ru':
		        $this->get_blog();
		        break;
		    case 'foursquare.com':
		        $this->get_foursquare();
		        break;
		    case 'kp.ru':
		        $this->get_kp();
		        break;
		    case 'aif.ru':
		        $this->get_aif();
		        break;
		    case 'friendfeed.com':
		        $this->get_ff();
		        break;
		}
		return intval($this->nick);
	}

    public function get_blogid() {
		return $this->blog_id;
    }
}

//$user=new users();
//echo $user->get_url('http://vk.com/photo-32788766_290200943?list=b3dd48157d650a1c66&og=1');
//echo $user->get_url('https://foursquare.com/joeytribbiani20/checkin/502a2c40e4b0e46579a68138?ref=tw&s=lL10utx38TrApHzWYs_KCzCteiw');
//echo $user->get_blogid();


require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' ); // локальное время на сервере

$db = new database();
$db->connect();
//$_GET['mkw']='путин & медведев';
//$_POST=$_GET;

$memcache_obj = new Memcache;

$memcache_obj->connect('localhost', 11211);
$out1=intval($memcache_obj->get('preview_'.$_POST['uid']));
if ($out1>3)
{
	$out['status']='fail';
	$errors[]=3;
	$out['errors']=$errors;
	echo json_encode($out);
	die();
}
else
{
	$out1++;
	$memcache_obj->set('preview_'.$_POST['uid'], $out1,0,30);
}

$_POST['data']=json_decode($_POST['data'],true);

foreach($_POST['data'] as $data)
{
	if (isset($data['order_date']))
	{
		$_POST['order_start']=strtotime($data['order_date']['order_start']);
		$_POST['order_end']=strtotime($data['order_date']['order_end']);
	}
	if (isset($data['order_name']))
	{
		//echo 'fuck';
		$_POST['order_name']=$data['order_name'];
	}
	elseif (isset($data['mnw'])||isset($data['mw'])||isset($data['mew']))
	{
		$_POST['mnw']=implode(",", $data['mnw']);
		$_POST['mw']=implode(",", $data['mw']);
		$_POST['mew']=implode(",", $data['mew']);
	}
	elseif (isset($data['mkw']))
	{
		//$_POST['mkw']=$data['mkw'][0];//implode(",", $data['mkw']);
		$_POST['mkw']=implode(",", $data['mkw']);
	}
	elseif (isset($data['res_type']))
	{
		$_POST['res_type']=$data['res_type'];
		$_POST['res']=implode(",", $data['data']);
		if ($data['data']=='all')
		{
			$_POST['res_type']='all';
			$_POST['res']=null;
		}
	}
	elseif (isset($data['author_type']))
	{
		$_POST['author_type']=$data['author_type'];
		$_POST['authors']=implode(",", $data['data']);
		if ($data['data']=='all')
		{
			$_POST['author_type']='all';
			$_POST['authors']=null;
		}
	}
	elseif (isset($data['random_age']))
	{
		$_POST['random_age']=$data['random_age'];
		if ($data['random_age']!=0)
		{
			$_POST['from_age']=$data['random_age']['from_age'];
			$_POST['to_age']=$data['random_age']['to_age'];
		}
	}
	elseif (isset($data['gender']))
	{
		$_POST['gender']=$data['gender'];
	}
	elseif (isset($data['location']))
	{
		if (isset($data['loc_type'])) $_POST['loc_type']=$data['loc_type'];
		else $_POST['loc_type']='only';
		$_POST['loc']=implode(",", $data['location']);
	}
	elseif (isset($data['auto_nastr']))
	{
		$_POST['auto_nastr']=$data['auto_nastr'];
	}
}


function parseURLproxyPreview( $url,$proxy )
{

  //$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
  //$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";
  //$uagent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.152011";
  $muagents[]='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1309.0 Safari/537.17';
  $muagents[]='Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.15 (KHTML, like Gecko) Chrome/24.0.1295.0 Safari/537.15';
  $muagents[]='Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.14 (KHTML, like Gecko) Chrome/24.0.1292.0 Safari/537.14';
  $muagents[]='Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
  $muagents[]='Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
  $muagents[]='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
  $muagents[]='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
  $uagent=$muagents[rand(0,count($muagents)-1)];
  $ch = curl_init( $url );
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
  curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
  curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
  curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // таймаут соединения
  curl_setopt($ch, CURLOPT_TIMEOUT, 1);        // таймаут ответа
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
  if ($proxy!='')
  {
	  //echo 'proxy='.$proxy;
	  curl_setopt($ch, CURLOPT_PROXY, $proxy);
  }
	else
	{
		return $nnl;
	}
  curl_setopt ($ch, CURLOPT_COOKIE, 'adult_concepts=1' ); 

  //curl_setopt($ch, CURLOPT_COOKIEJAR, "z://coo.txt");
  //curl_setopt($ch, CURLOPT_COOKIEFILE,"z://coo.txt");

  $content = curl_exec( $ch );
  $err     = curl_errno( $ch );
  $errmsg  = curl_error( $ch );
  $header  = curl_getinfo( $ch );
  curl_close( $ch );

  /*$header['errno']   = $err;
  $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $header;*/
  return $content;
}

function chech_yandex_content_preview($cont)
{
	return intval(preg_match('/\<rss xmlns\:yablogs\=\"urn\:yandex\-blogs\" xmlns\:wfw\=\"http\:\/\/wellformedweb\.org\/CommentAPI\/" version\=\"2\.0\">/isu',$cont));
}

function getpost_yandex_preview($text,$params,$ts,$te,$geo,$proxys)
{
	//echo $text;
	if ($geo=='az')
	{
		$geotxt='&geo='.urlencode('Азербайджан');
		//echo $geotxt;
	}
	/*	if ($geo=='ru')
	{
		$geotxt='&geo='.urlencode('Россия');
		//echo $geotxt;
	}*/
	$i_proxy=0;
	//print_r($mproxy);
	do
	{
		//echo '/';
		$cc=count($out['time']);
		do
		{
			$cont=parseURL('http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.$params);
			//echo '.';
			if (chech_yandex_content_preview($cont)==0)
			{
				//echo '*';
				$i_proxy++;
			}
			//echo '!'.$cont.'!';
		}
		while ((chech_yandex_content_preview($cont)==0) && ($i_proxy<count($proxys)));
		// echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.$params;
		$r1='/<yablogs\:count>(?<count>.*?)<\/yablogs\:count>/is';
		preg_match_all($r1,$cont,$ot1);
		$count_posts=intval($ot1['count'][0]);
		$outmas['count_posts']=$count_posts;
		$mas=simplexml_load_string($cont);
		$json = json_encode($mas);
		$mas= json_decode($json,true);
		//print_r($mas);
		//echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt."\n";
		//$regex='/<item>.*?(<author>(?<author>.*?)<\/author>.*?)?<pubDate>(?<time>.*?)<\/pubDate>.*?<link>(?<link>.*?)<\/link>.*?(<wfw\:commentRss>(?<comm>.*?)<\/wfw\:commentRss>.*?)?<description>(?<content>.*?)<\/description>.*?<\/item>/is';
		//preg_match_all($regex,$cont,$out);
		//print_r($out);
		foreach ($mas['channel']['item'] as $key => $item)
		{
			//echo strtotime($item).' '.$out['link'][$key].' '.strip_tags(str_replace('\n','',html_entity_decode($out['content'][$key],ENT_QUOTES,'UTF-8')))."\n";
			if (in_array(str_replace('\n','',$item['link']),$outmas['link']))
			{
				$c++;
			}
			else
			{
				$hn=parse_url(str_replace('\n','',$item['link']));
		    	$hn=$hn['host'];
		    	$ahn=explode('.',$hn);
		    	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
				$hh = $ahn[count($ahn)-2];
				if ($hn=='twitter.com')
				{
					$item['title']=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$item['title']);
					$item['description']=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$item['description']);
				}
				$outmas['author'][]=$item['author'];
				$outmas['comm'][]='';//$out['comm'][$key];
				$outmas['time'][]=strtotime($item['pubDate']);
				$outmas['link'][]=str_replace('\n','',$item['link']);
				$outmas['content'][]=strip_tags(preg_replace('/\s+/is',' ',html_entity_decode(trim($item['description'])!=''?$item['description']:(trim($item['title'])==''?$item['link']:$item['title']),ENT_QUOTES,'UTF-8')));
				$outmas['flag'][]='ya';
			}
		}
		return $outmas;
		//echo count($out['time']);
		$i++;
		//sleep(1);
		//echo intval($c).'!!!';
		//print_r($outmas);
		//echo count($mas['channel']['item']);
	}
	while ((intval($c)<100) && (count($mas['channel']['item'])>50));//От зацикливаний!!!
	//print_r($outmas);
	echo "\n";
	return $outmas;
}

//print_r($_REQUEST);
//print_r($_POST);
//print_r($_POST['mnw']);
//echo $_POST['mnw'];
$_POST=$_GET;
if ($_POST['mkw']=='')
{
	if ($_POST['mnw']!='')
	{
		//$mnw=explode(',',$_POST['mnw']);
		//$mnw=json_decode($_POST['mnw'], true);
		//print_r($mnw);
		$mnw=$_POST['mnw'];
		//print_r($mnw);
		foreach ($mnw as $nw)
		{
			//echo '!'.$nw.'!';
			if (preg_match('/\"/is',trim($nw)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($nw))) && (trim($nw)!=''))
				{
					$strnw.=$or.trim($nw);
					$or=' && ';
				}
				else
				{
					$errors[]=12;
					//$mas['status']=22;
					//echo json_encode($mas);	
					//die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+$/isu',trim($nw))) && (trim($nw)!=''))
				{
					$strnw.=$or.trim($nw);
					$or=' && ';
				}
				else
				{
					$errors[]=12;
					//$mas['status']=22;
					//echo json_encode($mas);	
					//die();
				}
			}
		}
	}
	if ($_POST['mw']!='')
	{
		//$mw=explode(',',$_POST['mw']);
		//$mw=json_decode($_POST['mw'],true);
		$mw=$_POST['mw'];
		foreach ($mw as $w)
		{
			if (preg_match('/\"/is',trim($w)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($w))) && (trim($w)!=''))
				{
					$strw.=$and.trim($w);
					$and=' | ';
				}
				else
				{
					//echo $w;
					$errors[]=11;
					//$mas['status']=21;
					//echo json_encode($mas);	
					//die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+$/isu',trim($w))) && (trim($w)!=''))
				{
					$strw.=$and.trim($w);
					$and=' | ';
				}
				else
				{
					//echo $w;
					$errors[]=11;
					//$mas['status']=21;
					//echo json_encode($mas);	
					//die();
				}
			}
		}
	}
	if ($_POST['mew']!='')
	{
		//$mew=explode(',',$_POST['mew']);
		//$mew=json_decode($_POST['mew'],true);
		$mew=$_POST['mew'];
		foreach ($mew as $ew)
		{
			if (preg_match('/\"/is',trim($ew)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($ew))) && (trim($ew)!=''))
				{
					$ex=' ~~ ';
					$strew.=$ex.trim($ew);
				}
				else
				{
					$errors[]=13;
					//$mas['status']=23;
					//echo json_encode($mas);	
					//die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+$/isu',trim($ew))) && (trim($ew)!=''))
				{
					$ex=' ~~ ';
					$strew.=$ex.trim($ew);
				}
				else
				{
					$errors[]=13;
					//$mas['status']=23;
					//echo json_encode($mas);	
					//die();
				}
			}
		}
	}
	$qwry=(($strw!='')?'('.$strw.')':'').$strnw.$strew;
}
else
{
	$mcount_open=explode('(', $_POST['mkw']);
	$mcount_close=explode(')', $_POST['mkw']);
	if (count($mcount_close)!=count($mcount_open))
	{
		$errors[]=2;
		//$outmas['status']=6;
		//echo json_encode($outmas);
		//die();
	}
	$qwry=$_POST['mkw'];
}

if (($_POST['from_age']!='')&&($_POST['to_age']!=''))
{
	$params.='&age_from='.intval($_POST['from_age']).'&age_to='.intval($_POST['to_age']);
}
if ($_POST['loc']!='')
{
	$ml=explode(',', $_POST['loc']);
	$params.='&loc='.$ml[0];
}
if ($_POST['authors']!='')
{
	$ma=explode(',', $_POST['authors']);
	$params.='&author='.$ma[0];
}
if ($_POST['gender']!='')
{
	$params.='&gender='.($_POST['gender']==1?'female':'male');
}
if ($_POST['res']!='')
{
	$ms=explode(',', $_POST['res']);
	$params.='&server='.$ms[0];
}
//echo $qwry;
if (count($errors)==0)
{
	$cont_proxys=parseUrl('http://188.120.239.225/getlist.php');
	$proxys=json_decode($cont_proxys,true);
	$pst=getpost_yandex_preview($qwry,$params,mktime(0,0,0,date('n'),date('j')-7,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')),'ru',$proxys);
	$i=0;
	foreach ($pst['link'] as $key => $item)
	{
		$hn=parse_url(str_replace('\n','',$item));
    	$hn=$hn['host'];
    	$ahn=explode('.',$hn);
    	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		$out['preview'][$i]['id']=rand(1,9322218);
		$out['preview'][$i]['post']=$pst['content'][$key];
		$out['preview'][$i]['title']=$pst['content'][$key];
		$out['preview'][$i]['time']=date('h:i:s d.n.Y',$pst['time'][$key]);
		$out['preview'][$i]['url']=$pst['link'][$key];
		$out['preview'][$i]['auth_url']=$pst['author'][$key];
		$out['preview'][$i]['host']=$hh;
		$out['preview'][$i]['img_url']='';
		$out['preview'][$i]['host_name']=$hn;
		$user=new users();
		$user->get_url($pst['author'][$key]);
		$out['preview'][$i]['nick']=$user->nick;
		//echo $user->nick;
		$out['preview'][$i]['nastr']=0;
		$out['preview'][$i]['spam']=0;
		$out['preview'][$i]['eng']=0;
		$out['preview'][$i]['fav']=0;
		$out['preview'][$i]['foll']=0;
		$out['preview'][$i]['geo']=null;
		$out['preview'][$i]['geo_c']=null;
		$out['preview'][$i]['tags']=array();
		$out['preview'][$i]['age']=0;
		$i++;
	}
	$out['count']=$pst['count_posts'];
}
else
{
	$out['status']='fail';
	$out['errors']=$errors;
	echo json_encode($out);
	die();
}
$out['status']='ok';
echo json_encode($out);
die();
?>
