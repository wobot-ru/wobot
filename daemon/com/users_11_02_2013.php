<?
/*require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

$db = new database();
$db->connect();*/

class users
{
    // property declaration
    public $link;
	public $blog_id;
	public $hn;
	public $age;
	public $location;
	public $gender;

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
		$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'twitter.com\' LIMIT 1');
		if (mysql_num_rows($chbb)==0)
		{
			$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'twitter\.com\',\''.$acc_id['id_acc'][0].'\')');
			$bb1['blog_id']=$db->insert_id();
		}
		else
		{
			$bb1=$db->fetch($chbb);
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
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
		$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'livejournal.com\' LIMIT 1');
		if (mysql_num_rows($chbb)==0)
		{
			$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'livejournal\.com\',\''.$acc_id['id_acc'][0].'\')');
			$bb1['blog_id']=$db->insert_id();
		}
		else
		{
			$bb1=$db->fetch($chbb);
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
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
		if (intval($acc_id['id_acc'][0])!=0)
		{
			$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'vkontakte.ru\' LIMIT 1');
			if (mysql_num_rows($chbb)==0)
			{
				$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'vkontakte\.ru\',\''.$acc_id['id_acc'][0].'\')');
				$bb1['blog_id']=$db->insert_id();
			}
			else
			{
				$bb1=$db->fetch($chbb);
			}
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}
	
	public function get_facebook()
	{
		global $db;
		$rgx='/\&id\=(?<id_acc>\d+)$/is';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'facebook.com\' LIMIT 1');
		if (mysql_num_rows($chbb)==0)
		{
			$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'facebook\.com\',\''.$acc_id['id_acc'][0].'\')');
			$bb1['blog_id']=$db->insert_id();
		}
		else
		{
			$bb1=$db->fetch($chbb);
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_mail()
	{
		global $db;
		$rgx='/http\:\/\/blogs\.mail\.ru\/(?<type_acc>[^\/]*?)\/(?<id_acc>[^\/]*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['type_acc'][0]!='community')
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
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_liveinternet()
	{
		global $db;
		$rgx='/http\:\/\/www\.liveinternet\.ru\/users\/(?<id_acc>[^\/]*?)\//is';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_ya()
	{
		global $db;
		$rgx='/http\:\/\/(?<id_acc>.*?)\.ya\.ru\//is';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if (($acc_id['id_acc'][0]!='') && ($acc_id['id_acc'][0]!='clubs'))
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
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}
	
	public function get_yandex()
	{
		global $db;
		$rgx='/http\:\/\/fotki\.yandex\.ru\/users\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		}
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_rutwit()
	{
		global $db;
		$rgx='/http\:\/\/rut[wv]it\.ru\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_babyblog()
	{
		global $db;
		$rgx='/http\:\/\/www\.babyblog\.ru\/user\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_blog()
	{
		global $db;
		$rgx='/http\:\/\/(?<id_acc>.*?)\.blog\.ru\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_foursquare()
	{
		global $db;
		$rgx='/foursquare\.com\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_kp()
	{
		global $db;
		$rgx='/blog\.kp\.ru\/users\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_aif()
	{
		global $db;
		$rgx='/blog\.aif\.ru\/users\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}


	public function get_ff()
	{
		global $db;
		$rgx='/friendfeed\.com\/(?<id_acc>.*?)\//isu';
		preg_match_all($rgx,$this->link,$acc_id);
		//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
		$bb1['blog_id']=0;
		if ($acc_id['id_acc'][0]!='')
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
		$this->blog_id=$bb1['blog_id'];
		$this->age=$bb1['blog_age'];
		$this->location=$bb1['blog_location'];
		$this->gender=$bb1['blog_gender'];
		$this->last_update=$bb1['blog_last_update'];
		return $this->blog_id;
	}

	public function get_url($url,$all_info=0)
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
		    case 'vkontakte.ru':
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
		if ($all_info==0)
		{
			return intval($this->blog_id);
		}
		elseif ($all_info==1)
		{
			$outmas['age']=$this->age;
			$outmas['blog_id']=$this->blog_id;
			$outmas['loc']=$this->location;
			$outmas['gender']=$this->gender;
			$outmas['last_update']=$this->last_update;
			return $outmas;
		}
	}

    public function get_blogid() {
		return $this->blog_id;
    }
}

//$user=new users();
//print_r($user->get_url('http://vk.com/photo-32788766_290200943?list=b3dd48157d650a1c66&og=1'));
//echo $user->get_url('https://foursquare.com/joeytribbiani20/checkin/502a2c40e4b0e46579a68138?ref=tw&s=lL10utx38TrApHzWYs_KCzCteiw');
//echo $user->get_blogid();

?>