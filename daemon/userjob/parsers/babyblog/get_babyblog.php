<?
// require_once('../../com/config.php');
// require_once('../../com/func.php');
// require_once('../../com/db.php');
// require_once('../../bot/kernel.php');
// require_once('../../com/tmhOAuth.php');
// require_once('../../com/vkapi.class.php');

// $db = new database();
// $db->connect();
date_default_timezone_set ( 'Europe/Moscow' );
$fem=array('виргиния','аврора','агата','агафья','агнесса','агния','ада','аза','алевтина','александра','алина','алиса','алла','альбина','альфреа','анастасия','ангелина','анжелика','анжела','анита','анна','антонина','анфиса','арина','астра','беата','белла','берта','бета','богдана','борислава','бронислава','валентина','валерия','ванда','варвара','василиса','венера','вера','вероника','веста','виктория','виолетта','виталина','виталия','галина','гелена','гелла','генриетта','гертруда','глафира','глория','дайна','данута','дарья','джульета','диана','дина','доля','доминика','ева','евгения','евдокия','екатерина','елена','елизавета','жанна','зинаида','зоя','иветта','изабелла','изольда','инара','инга','инесса','инна','ирина','ирма','ия','камилла','капитолина','карина','каролина','катарина','кира','клавдия','клара','кристина','ксения','кузьма','козьма','лада','лайма','лариса','леся','лиана','лидия','лилия','лилиана','лина','лиана','лола','лолла','лолита','людмила','майя','маргарита','марианна','марина','мария','марта','марфа','милена','мирдза','мирра','муза','надежда','наталия','неонила','нила','никита','нина','нонна','нора','оксана','олеся','ольга','полина','прасковья','раиса','регина','рина','рената','римма','роза','розалия','роксана','руслана','руфина','савва','сарра','сара','светлана','серафима','софия','станислава','стелла','стефания','сусанна','сюзанна','таира','таисия','тамара','тамила','томила','татьяна','ульяна','фаина','фекла','флора','фома','франсуаза','фрида','христина','эдита','элеонора','эллина','эвелина','элина','эльвира','эльза','эмма','эмилия','эрика','юлия','юнона','ядвига','яна','ярослава');
$mail=array('абрам','авраам','ибрагил','аввакум','аввакуум','август','августин','аверьян','аверкий','агафон','адам','адольф','адриан','азарий','аким','алан','александр','алексей','альберт','альфред','анатолий','андрей','антон','аполлон','аристарх','аркадий','арнольд','арсений','арсен','артем','артур','архип','аскольд','афанасий','бенедикт','богдан','болеслав','борис','бронислав','вадим','валентин','валерий','вальтер','варлаам','варлам','василий','велор','велорий','венедикт','вениамин','виктор','вилен','вилли','виссарион','виталий','витольд','владимир','владислав','владлен','влас','власий','вольдемар','всеволод','вячеслав','гавриил','гарри','гаянэ','геннадий','георгий','герасим','герман','глеб','гордей','гордей','гордий','григорий','гурий','давид','даниил','демьян','денис','дмитрий','дональд','донат','евгений','евдоким','егор','елисей','емельян','ермак','ермолай','ефим','ефрем','захар','зиновий','иван','игнат','игнатий','игорь','илларион','илья','иннокентий','иосиф','осип','ипполит','ираклий','исаак','исак','казимир','карл','касьян','кассиан','ким','кирилл','клемент','клим','климент','кондрат','кондратий','константин','корней','корнелий','лавр','лаврентий','лазарь','лев','леонард','леонид','леонтий','лука','лукьян','любовь','любомир','людвиг','май','макар','максим','максимилиан','марк','мартин','мартын','матвей','мечеслав','мечислав','мирон','мирослав','митрофан','михаил','модест','моисей','мстислав','назарий','натан','наум','нелли','никифор','николай','нинель','нисон','овидий','олег','орест','осип','оскар','остап','павел','панкрат','панкратий','пантелей','пантелеймон','парамон','петр','платон','прохор','роберт','родион','роман','ростислав','рубен','рудольф','руслан','савелий','самуил','святослав','севастьян','семен','сергей','соломон','станислав','степан','тарас','терентий','тимофей','тимур','тихон','трофим','устин','юстин','юстиниан','федор','феликс','филипп','фрол','флор','харитон','эдуард','эльдар','эрик','эрнест','юлий','юлиан','юрий','яков','ян','ярослав','алекс');
$arrgen['w']=1;
$arrgen['m']=2;

function get_babyblog($nick) 
{
	global $db,$fem,$mail,$arrgen;
	do
	{
		$cont=parseUrlproxy('http://www.babyblog.ru/user/info/'.$nick);
		if ($cont=='')
		{
			$attmp++;
			echo "\n".'continue...'."\n";
		}
	}
	while (($cont=='') && ($attmp<3));
	// echo $cont;
	//$cont=parseUrl('http://www.babyblog.ru/user/info/'.$nick);
	$regex='/<h2><i class="icon icon-clock" title="[^\"]*?"><\/i><span><a href="[^\"]*?">(?<name>.*?)<\/a><\/span><\/h2>/isu';
	preg_match_all($regex,$cont,$out);
	$outmas['name']=$out['name'][0];
	$outmas['nick']=$nick;
	$regex='/<h3>.*?(?<bdate>\d\d\d\d)\sгод[а-я]?\,/isu';
	preg_match_all($regex,$cont,$out);
	if (intval($out['bdate'][0])!=0)
	{
		$outmas['age']=date('Y')-$out['bdate'][0];
	}
	else
	{
		$outmas['age']=0;
	}
	$outmas['gender']=1;
	$regex='/<span class="journal__header_bage_text">(?<loc>[^\<\>]*?)<br><i>[а-я]*?<\/i><\/span>/isu';
	preg_match_all($regex,$cont,$out);
	$loc=$out['loc'][0];
	if ($loc!='')
	{
		$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$loc."'");
		if (mysql_num_rows($rru)==0)
		{
			$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($loc).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
			$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
			preg_match_all($regextw,$content_tw_loc,$out_tw);
			$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$loc.'\',\''.$out_tw['loc_tw'][0].'\')');
		}
		else
		{
			$rru1=$db->fetch($rru);
			$out_tw['loc_tw'][0]=$rru1['loc_coord'];
		}
	}
	if ($out_tw['loc_tw'][0]!='')
	{
		$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
		//echo $key;
		preg_match_all($regex,$out_tw['loc_tw'][0],$out);
		$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
	}
	$outmas['loc']=$twl;
	$regex='/more\_friends\((?<id_fol>\d+)\,\d+\)\;/isu';
	preg_match_all($regex,$cont,$out);
	//print_r($out);
	//sleep(1);
	$c_cont=$cont;
	$regex='/<a href="\/user\/info\/friends\/.*?">(?<fol>\d+)\sдруз[а-я]*?<\/a>/isu';
	preg_match_all($regex, $cont, $out);
	$outmas['fol']=intval($out['fol'][0]);
	$regex='/<img rel="myAvatarSrc" alt="" src="(?<ico>[^\"]*?)">/isu';
	preg_match_all($regex, $cont, $out);
	//print_r($out);
	$outmas['ico']=$out['ico'][0];
	// print_r($outmas);
	return $outmas;
}
// $cont=parseUrl('http://188.120.239.225/getlist.php');
// $mproxy=json_decode($cont,true);
// get_babyblog('ksuxa1979');
?>