<?
 //require_once('/var/www/com/config.php');
require_once('com/func.php');
 //require_once('/var/www/com/db.php');
require_once('bot/kernel.php');
require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');

$api_id = 2124816; // Insert here id of your application
$secret_key = 'f98VkwX1Cc64xSj76vP4'; // Insert here secret key of your application
// $m_access_token[0]='e468e23290e16f5231d6c1bef354b451db6d98041a98131ca29d28a637693c84b42efd6c14943dea3f812';
// $m_access_token[1]='2af49b2681d87de5f2bae8949d4979d3957c281fb44098ae7ee2c085d815c30925d851be02b0270d4e2a7';
// $m_access_token[2]='5f04c371670c397e2c5f9115da38e3a2cd3c69e69c66e96af357b4dde670544ea32143a0302413688d82a';
// $m_access_token[3]='31485842ebb32f95488f1d838cfcfa7072fb31c5aee1875e1eed2a77ca4f70db6daee6ee4b199a57134ad';
 //$db = new database();
// $db->connect();
date_default_timezone_set ( 'Europe/Moscow' );

$filename = "get_vk_cities.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);
$cities=unserialize($contents);
$fem=array('виргиния','аврора','агата','агафья','агнесса','агния','ада','аза','алевтина','александра','алина','алиса','алла','альбина','альфреа','анастасия','ангелина','анжелика','анжела','анита','анна','антонина','анфиса','арина','астра','беата','белла','берта','бета','богдана','борислава','бронислава','валентина','валерия','ванда','варвара','василиса','венера','вера','вероника','веста','виктория','виолетта','виталина','виталия','галина','гелена','гелла','генриетта','гертруда','глафира','глория','дайна','данута','дарья','джульета','диана','дина','доля','доминика','ева','евгения','евдокия','екатерина','елена','елизавета','жанна','зинаида','зоя','иветта','изабелла','изольда','инара','инга','инесса','инна','ирина','ирма','ия','камилла','капитолина','карина','каролина','катарина','кира','клавдия','клара','кристина','ксения','кузьма','козьма','лада','лайма','лариса','леся','лиана','лидия','лилия','лилиана','лина','лиана','лола','лолла','лолита','людмила','майя','маргарита','марианна','марина','мария','марта','марфа','милена','мирдза','мирра','муза','надежда','наталия','неонила','нила','никита','нина','нонна','нора','оксана','олеся','ольга','полина','прасковья','раиса','регина','рина','рената','римма','роза','розалия','роксана','руслана','руфина','савва','сарра','сара','светлана','серафима','софия','станислава','стелла','стефания','сусанна','сюзанна','таира','таисия','тамара','тамила','томила','татьяна','ульяна','фаина','фекла','флора','фома','франсуаза','фрида','христина','эдита','элеонора','эллина','эвелина','элина','эльвира','эльза','эмма','эмилия','эрика','юлия','юнона','ядвига','яна','ярослава');
$male=array('абрам','авраам','ибрагил','аввакум','аввакуум','август','августин','аверьян','аверкий','агафон','адам','адольф','адриан','азарий','аким','алан','александр','алексей','альберт','альфред','анатолий','андрей','антон','аполлон','аристарх','аркадий','арнольд','арсений','арсен','артем','артур','архип','аскольд','афанасий','бенедикт','богдан','болеслав','борис','бронислав','вадим','валентин','валерий','вальтер','варлаам','варлам','василий','велор','велорий','венедикт','вениамин','виктор','вилен','вилли','виссарион','виталий','витольд','владимир','владислав','владлен','влас','власий','вольдемар','всеволод','вячеслав','гавриил','гарри','гаянэ','геннадий','георгий','герасим','герман','глеб','гордей','гордей','гордий','григорий','гурий','давид','даниил','демьян','денис','дмитрий','дональд','донат','евгений','евдоким','егор','елисей','емельян','ермак','ермолай','ефим','ефрем','захар','зиновий','иван','игнат','игнатий','игорь','илларион','илья','иннокентий','иосиф','осип','ипполит','ираклий','исаак','исак','казимир','карл','касьян','кассиан','ким','кирилл','клемент','клим','климент','кондрат','кондратий','константин','корней','корнелий','лавр','лаврентий','лазарь','лев','леонард','леонид','леонтий','лука','лукьян','любовь','любомир','людвиг','май','макар','максим','максимилиан','марк','мартин','мартын','матвей','мечеслав','мечислав','мирон','мирослав','митрофан','михаил','модест','моисей','мстислав','назарий','натан','наум','нелли','никифор','николай','нинель','нисон','овидий','олег','орест','осип','оскар','остап','павел','панкрат','панкратий','пантелей','пантелеймон','парамон','петр','платон','прохор','роберт','родион','роман','ростислав','рубен','рудольф','руслан','савелий','самуил','святослав','севастьян','семен','сергей','соломон','станислав','степан','тарас','терентий','тимофей','тимур','тихон','трофим','устин','юстин','юстиниан','федор','феликс','филипп','фрол','флор','харитон','эдуард','эльдар','эрик','эрнест','юлий','юлиан','юрий','яков','ян','ярослав');


function get_vk($nick)
{
	$agemonthe=array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	global $m_access_token,$db,$cities,$_SERVER;
	//sleep(1);
	if (intval($nick)>0)
	{
		do
		{
			usleep(200000);
			$info_users=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($nick).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$m_access_token[$_SERVER['argv'][1]]);
			$inf=json_decode($info_users,true);
			if ($inf['error']['error_code']==5) 
			{
				$headers  = "From: alert@wobot.ru\r\n"; 
				$headers .= "Bcc: alert@wobot.ru\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
				// mail('zmei123@yandex.ru','Пизда токену вконтакте!!!','убит демон userjob_vk',$headers);
				// mail('r@wobot.co','Пизда токену вконтакте!!!','убит демон userjob_vk',$headers);
				sleep(5);
				//die();
			}
		}
		while (count($inf['error'])!=0);
		$outmas['gender']=$inf['response'][0]['sex'];
		$outmas['name']=$inf['response'][0]['first_name'].' '.$inf['response'][0]['last_name'];
		if($outmas['gender']==0){
			$mm=mb_strtolower($inf['response'][0]['first_name'],'UTF-8');
			$c=0;
			foreach ($mm as $item)
			{
				if ($c!=1)
				{
					if (in_array($item,$fem))
					{
						$mm='1';
						$c=1;
					}
					elseif (in_array($item,$male))
					{	
						$mm='2';
						$c=1;
					}
					else
					{
						$mm='0';
					}
				}
			}
			$outmas['gender']=$mm;
		}
		$outmas['nick']=$nick;
		$regex='/(?<d>\d\d?)\.(?<m>\d\d?)\.(?<y>\d\d\d\d)/is';
		//echo $inf['bdate'];
		preg_match_all($regex,$inf['response'][0]['bdate'],$out);
		if ((intval($out['d'][0])!=0) && (intval($out['m'][0])!=0) && (intval($out['y'][0])!=0)) $outmas['age']=intval((time()-mktime(0,0,0,$out['m'][0],$out['d'][0],$out['y'][0]))/(86400*365));
		else $outmas['age']=0;
		$outmas['fol']=intval($inf['response'][0]['counters']['followers'])+intval($inf['response'][0]['counters']['friends']);
		if ($cities[$inf['response'][0]['city']]!='')
		{
			$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$cities[$inf['response'][0]['city']]."'");
			if (mysql_num_rows($rru)==0)
			{
			$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($cities[$inf['response'][0]['city']]).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
				$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
				preg_match_all($regextw,$content_tw_loc,$out_tw);
				$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$cities[$inf['response'][0]['city']].'\',\''.$out_tw['loc_tw'][0].'\')');
			}
			else
			{
				$rru1=$db->fetch($rru);
				$out_tw['loc_tw'][0]=$rru1['loc_coord'];
			}
		}
		$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
		preg_match_all($regex,$out_tw['loc_tw'][0],$out);
		if (($out['ch'][0]!='') && ($out['d'][0]!='') && ($out['ch'][1]!='') && ($out['d'][1]!=''))
		{
			$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
		}
		$outmas['loc']=$twl;
		$outmas['ico']=$inf['response'][0]['photo'];
	}
	else
	{
		$info_users=parseUrl('https://api.vkontakte.ru/method/groups.getById?gid='.abs(intval($nick)).'&fields=city,members_count&access_token='.$m_access_token[$_SERVER['argv'][1]]);
		$inf=json_decode($info_users,true);
		$outmas['gender']=0;
		$outmas['name']=($inf['response'][0]['name']!=''?$inf['response'][0]['name']:$inf['response'][0]['screen_name']);
		$outmas['nick']=$nick;
		$outmas['age']=0;
		if ($cities[$inf['response'][0]['city']]!='')
		{
			$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$cities[$inf['response'][0]['city']]."'");
			if (mysql_num_rows($rru)==0)
			{
			$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($cities[$inf['response'][0]['city']]).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
				$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
				preg_match_all($regextw,$content_tw_loc,$out_tw);
				$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$cities[$inf['response'][0]['city']].'\',\''.$out_tw['loc_tw'][0].'\')');
			}
			else
			{
				$rru1=$db->fetch($rru);
				$out_tw['loc_tw'][0]=$rru1['loc_coord'];
			}
		}
		$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
		preg_match_all($regex,$out_tw['loc_tw'][0],$out);
		if (($out['ch'][0]!='') && ($out['d'][0]!='') && ($out['ch'][1]!='') && ($out['d'][1]!=''))
		{
			$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
		}
		$outmas['loc']=$twl;
		//$info_users1=parseUrl('https://api.vkontakte.ru/method/groups.getMembers?gid='.abs(intval($nick)).'&access_token='.$access_token);
		//$inf1=json_decode($info_users1,true);
		$outmas['fol']=$inf['response'][0]['members_count'];
		$outmas['ico']=$inf['response'][0]['photo'];
	}
	//print_r($outmas);
	return $outmas;
}
//get_vk('16662688');

?>
