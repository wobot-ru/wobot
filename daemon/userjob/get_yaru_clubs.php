<?
/*require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');*/
//require_once('bot/kernel.php');
/*require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');*/

date_default_timezone_set ( 'Europe/Moscow' );
$fem=array('виргиния','аврора','агата','агафья','агнесса','агния','ада','аза','алевтина','александра','алина','алиса','алла','альбина','альфреа','анастасия','ангелина','анжелика','анжела','анита','анна','антонина','анфиса','арина','астра','беата','белла','берта','бета','богдана','борислава','бронислава','валентина','валерия','ванда','варвара','василиса','венера','вера','вероника','веста','виктория','виолетта','виталина','виталия','галина','гелена','гелла','генриетта','гертруда','глафира','глория','дайна','данута','дарья','джульета','диана','дина','доля','доминика','ева','евгения','евдокия','екатерина','елена','елизавета','жанна','зинаида','зоя','иветта','изабелла','изольда','инара','инга','инесса','инна','ирина','ирма','ия','камилла','капитолина','карина','каролина','катарина','кира','клавдия','клара','кристина','ксения','кузьма','козьма','лада','лайма','лариса','леся','лиана','лидия','лилия','лилиана','лина','лиана','лола','лолла','лолита','людмила','майя','маргарита','марианна','марина','мария','марта','марфа','милена','мирдза','мирра','муза','надежда','наталия','неонила','нила','никита','нина','нонна','нора','оксана','олеся','ольга','полина','прасковья','раиса','регина','рина','рената','римма','роза','розалия','роксана','руслана','руфина','савва','сарра','сара','светлана','серафима','софия','станислава','стелла','стефания','сусанна','сюзанна','таира','таисия','тамара','тамила','томила','татьяна','ульяна','фаина','фекла','флора','фома','франсуаза','фрида','христина','эдита','элеонора','эллина','эвелина','элина','эльвира','эльза','эмма','эмилия','эрика','юлия','юнона','ядвига','яна','ярослава');
$mail=array('абрам','авраам','ибрагил','аввакум','аввакуум','август','августин','аверьян','аверкий','агафон','адам','адольф','адриан','азарий','аким','алан','александр','алексей','альберт','альфред','анатолий','андрей','антон','аполлон','аристарх','аркадий','арнольд','арсений','арсен','артем','артур','архип','аскольд','афанасий','бенедикт','богдан','болеслав','борис','бронислав','вадим','валентин','валерий','вальтер','варлаам','варлам','василий','велор','велорий','венедикт','вениамин','виктор','вилен','вилли','виссарион','виталий','витольд','владимир','владислав','владлен','влас','власий','вольдемар','всеволод','вячеслав','гавриил','гарри','гаянэ','геннадий','георгий','герасим','герман','глеб','гордей','гордей','гордий','григорий','гурий','давид','даниил','демьян','денис','дмитрий','дональд','донат','евгений','евдоким','егор','елисей','емельян','ермак','ермолай','ефим','ефрем','захар','зиновий','иван','игнат','игнатий','игорь','илларион','илья','иннокентий','иосиф','осип','ипполит','ираклий','исаак','исак','казимир','карл','касьян','кассиан','ким','кирилл','клемент','клим','климент','кондрат','кондратий','константин','корней','корнелий','лавр','лаврентий','лазарь','лев','леонард','леонид','леонтий','лука','лукьян','любовь','любомир','людвиг','май','макар','максим','максимилиан','марк','мартин','мартын','матвей','мечеслав','мечислав','мирон','мирослав','митрофан','михаил','модест','моисей','мстислав','назарий','натан','наум','нелли','никифор','николай','нинель','нисон','овидий','олег','орест','осип','оскар','остап','павел','панкрат','панкратий','пантелей','пантелеймон','парамон','петр','платон','прохор','роберт','родион','роман','ростислав','рубен','рудольф','руслан','савелий','самуил','святослав','севастьян','семен','сергей','соломон','станислав','степан','тарас','терентий','тимофей','тимур','тихон','трофим','устин','юстин','юстиниан','федор','феликс','филипп','фрол','флор','харитон','эдуард','эльдар','эрик','эрнест','юлий','юлиан','юрий','яков','ян','ярослав','алекс');

function get_yaruclubs($url)
{
	$cont=parseUrl($url);
	$regex='/<b class="b-user"><a href="http\:\/\/(?<nick>.*?)\.ya\.ru\/" class="b-user__link"><b class="b-user__first-letter">(?<name>.*?)<\/a><\/b>/isu';
	preg_match_all($regex,$cont,$out);
	$outmas['nick']=$out['nick'][0];
	$outmas['name']=strip_tags($out['name'][0]);
	//print_r($outmas);
	sleep(1);
	return $outmas;
}

//get_yaruclubs('http://clubs.ya.ru/4611686018427415435/replies.xml?item_no=1217');
?>