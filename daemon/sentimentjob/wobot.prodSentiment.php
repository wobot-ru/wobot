<?

require_once('/var/www/daemon/com/porter.php');
require_once('/var/www/daemon/com/infix.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$order_delta=$_SERVER['argv'][1];
$debug_mode=$_SERVER['argv'][2];
$fp = fopen('/var/www/pids/sj'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
echo $order_delta;

$db=new database();
$db->connect();

error_reporting(0);

function get_tone($post, $t_type, $t_object)
{

	// if($t_type=='' || $post=='') return 0;

	$question_words = '"как"|"где"|"зачем"|"почему"|"куда"';
	/*
		t_type:
		0 - commom
		1 - banks
		2 - sells
	*/
//choose tone array

		//echo "Tone_type= ".$t_type." eq ".($t_type==1)."\n";

    if($t_type==1){
    	//echo "bank tone".$t_type."\n";
    	$positive_array=array('(ХОРОШИЙ~(не|нет))', '(ДЕЛИКАТНЫЙ~(не|нет))',	'(ДОПОЛНИТЕЛЬНЫЙ~(не|нет))',	'(ВЫГОДНЫЙ~(не|нет))',	'(ПРОФЕССИОНАЛЬНЫЙ~(не|нет))',	'(ТОЛКОВЫЙ~(не|нет))',	'(ЭФФЕКТНЫЙ~(не|нет))',	'(НЕОБХОДИМЫЙ~(не|нет|документ))',	'(СТАБИЛЬНЫЙ~(не|нет))',	'(СОВРЕМЕННЫЙ~(не|нет))',	'(БЕСПЛАТНЫЙ~(не|нет))',	'(БЕЗУПРЕЧНЫЙ~(не|нет))',	'(БЛАГОТВОРИТЕЛЬНЫЙ~(не|нет))',	'(УСПЕШНЫЙ~(не|нет))',	'(ПРИВЛЕКАТЕЛЬНЫЙ~(не|нет))',	'(БАЛЛОВЫЙ~(не|нет))',	'(ОТЛИЧНЫЙ~(не|нет))',
 '(ЗАРЕГИСТРИРОВАТЬ~(не|нет|без|кабинет|сайт))', '(ВЫПЛАТИТЬ~(не|нет|без|долг))', '(ПРИСОЕДИНЯТЬСЯ~(не|нет|без))', '(СОХРАНИТЬ~(не|нет|без))', '(РЕКОМЕНДОВАТЬ~(не|нет|без))', '(ВОЗМЕЩАТЬ~(не|нет|без))', '(СОТРУДНИЧАТЬ~(не|нет|без))', '(РАЗВИВАТЬ~(не|нет|без))', '(ВЫПЛАЧИВАТЬ~(не|нет|без))', '(ПОДДЕРЖАТЬ~(не|нет|без))', '(ВЫПОЛНИТЬ~(не|нет|без))', '(ПОДПИСАТЬ~(не|нет|без))', '(ХВАЛЯТЬ~(не|нет|без))', '(ПРИНЯТЬ~(не|нет|без))', '(ПОЗВОЛЯТЬ~(не|нет|без))',
 '(ГАРАНТИЯ~(не|нет|без))',  '(ОГРАНИЧЕНИЕ&(без|не|нет|опровергнуть|опровергли))',
 '(санкции&не&затронуть)', '(санкции&помочь)', '(санкции&не&эффективный)', '(надежный~(не|нет|без))', '(выплаты&вырос~кредит)', '(рост&выплат~кредит)', '(доброе&дело)', '(благотворительность~(не|нет|без))', '(давно&пользоваться~но)', '(выгодно~(не|нет|без|где))', '(вырости /3 прибыль~снизить~расход)', '(рост /3 прибыль~снизить~расход)', '(возглавить&рейтинг)', '(высокий&рейтинг~(не|нет|без))', '(дружелюбный~(не|нет|без))', '(спасать~(не|нет|без))', '(лучший&банк~(не|нет))', '(предоставлять&защита~(не|нет))',
 '(мошенники&(задержать|поймать)~(не|нет|без))'); 

		$negative_array=array('(НЕЗАКОННЫЙ)', '(ЛОЖЕВОЙ)',	'(ХОРОШИЙ&(не|нет|без))',	'(ОПЕРАТИВНЫЙ&(не|нет|без))',	'(ЧИСТЫЙ&(не|нет|без))',	'(ГОНДОНСКИЙ)',	'(ПЛОХОЙ~(не|нет|без))',	'(УЁБИЩНЫЙ)',	'(ГОТОВЫЙ&(не|нет|без))',	'(НЕНАДЕЖНЫЙ)',	'(ТОСКЛИВЫЙ~(не|нет|без))',	'(СМЕШНОЙ&(не|нет|без))',	'(УДОБНЫЙ /-2 (не|нет|без))',	'(ДОРОГОСТОЯЩИЙ~(не|нет|без))',	'(ОТРИЦАТЕЛЬНЫЙ~(не|нет|без))',	'(БЕСПРОБЛЕМНЫЙ&(не|нет|без))',	'(НЕВИДАННЫЙ)',	'(ШИКАРНЫЙ&(не|нет|без))',	'(ОПРЯТНЫЙ&(не|нет|без))',	'(ПОДСТАВНОЙ~(не|нет|без))',	'(ПОНЯТНЫЙ&(не|нет|без))',	'(ВКЛЮЧЕННЫЙ&(не|нет|без))',	'(САМОСТОЯТЕЛЬНЫЙ&(не|нет|без))',	'(СОВРЕМЕННЫЙ&(не|нет|без))',	'(ПОРЯДОЧНЫЙ&(не|нет|без))',	'(ПРИЛИЧНЫЙ&(не|нет|без))',	'(ЗЛОЙ~(не|нет|без))',	'(НАСТОЯЩИЙ&(не|нет|без))',	'(ДОЛГОСРОЧНЫЙ&(не|нет|без))',	'(ВЕДУЩИЙ&(не|нет|без))',	'(ПРОТИВОПОЛОЖНЫЙ~(не|нет|без))',
 '(ЗАРЕГИСТРИРОВАТЬ&(не|нет|без)~(кабинет|сайт))', '(ДОБАВИТЬ&(не|нет|без))', '(ПОЛЬЗОВАТЬСЯ&(не|нет|без))', '(УВЕРИТЬ&(не|нет|без))', '(ЗАРЕГИСТРИРОВАТЬСЯ&(не|нет|без))', '(СОХРАНИТЬ&(не|нет|без))', '(ПРИСОЕДИНЯТЬСЯ&(не|нет|без))', '(УДАЛИТЬ~(не|нет|без))', '(ПОЛУЧАТЬ&(не|нет|без))', '(АТАКОВАТЬ~(не|нет|без))', '(РАБОТАТЬ&(не|нет|без))', '(РАСПРОСТРАНЯТЬСЯ&(не|нет|без))', '(ОТКАЗАТЬСЯ~(не|нет|без))', '(ВЗЯТЬ&(не|нет|без))', '(СКАЗАТЬ&(не|нет|без))', '(ОБМАНУТЬ~(не|нет|без))', '(ТОЛПИТЬСЯ~(не|нет|без))', '(ЖДАТЬ&(не|нет|без))', '(НАЙТИ&(не|нет|без))', '(ВЫДАТЬ&(не|нет|без))', '(СООБЩИТЬ&(не|нет|без))', '(ОСТАВИТЬ&(не|нет|без))', '(ПОЛУЧИТЬ&(не|нет|без))', '(ПРОДЕМОНСТРИРОВАТЬ&(не|нет|без))', '(РАЗУБЕДИТЬ~(не|нет|без))', '(НАПИСАТЬ&(не|нет|без))', '(СНИМАТЬ&(не|нет|без))', '(РУХНУТЬ~(не|нет|без))', '(СОБИРАТЬСЯ&(не|нет|без))', '(НЕНАВИДЕТЬ)', '(РАССМОТРЕТЬ&(не|нет|без))', '(ПЛАТИТЬ&(не|нет|без))', '(ОПЛАТИТЬ&(не|нет|без))', '(ДЕЛАТЬ&(не|нет|без))', '(ПРЕДЛАГАТЬ&(не|нет))', '(СДЕЛАТЬ&(не|нет|без))', '(УКРАСТЬ~(не|нет|без))', '(ОТКАЗЫВАТЬСЯ~(не|нет|без))', '(КУПИТЬ&(не|нет|без))', '(СВЕРНУТЬ~(не|нет|без))', '(ПЫТАТЬСЯ&(не|нет|без))', '(ЯВЛЯТЬСЯ&(не|нет|без))', '(ПРИСОСЕДИТЬСЯ&(не|нет|без))', '(ПОКУПАТЬ&(не|нет|без))', '(СЧИТАТЬ&(не|нет|без))', '(РАЗВЕРНУТЬ&(не|нет|без))', '(ВПАРИТЬ~(не|нет|без))', '(НАМЕРИТЬ&(не|нет|без))', '(КРЕДИТОВАТЬ&(не|нет|без))', '(ДАТЬ&(не|нет|без))', '(ПРОВЕРИТЬ&(не|нет|без))', '(ОБЪЯСНЯТЬ&(не|нет|без))', '(МЫТЬ&(не|нет|без))', '(ЗАПИСЫВАТЬ&(не|нет|без))', '(ПРОВОДИТЬСЯ&(не|нет|без))', '(НАЗНАЧИТЬ&(не|нет|без))', '(ПРОДОЛЖАТЬ&(не|нет|без))', '(ПРИТРОНУТЬСЯ&(не|нет|без))', '(ВЫИГРАТЬ&(не|нет|без))', '(СКАКАНУТЬ~(не|нет|без))', '(ПОЗВОЛЯТЬ&(не|нет|без))', '(ОТПИСАТЬСЯ~(не|нет|без))', '(КАКАТЬ&(не|нет|без))', '(ВКЛЮЧИТЬ&(не|нет|без))', '(ПРЕКРАТИТЬ&(не|нет|без))', '(ВСУЧИТЬ~(не|нет|без))', '(СОТРУДНИЧАТЬ&(не|нет|без))', '(МЕНЯТЬ&(не|нет|без))', '(СПРОСИТЬ&(не|нет|без))',
 '(ДОРОГОЙ~~(не|нет|без))', '(ОГРАНИЧЕНИЕ~(без|не|нет|опровергнуть|опровергли))',
 '(рехнулся)', '(выпиздить)', '(пиздошить)', '(пиздуй)', '(пиздюрить)', '(пиздюхать)', '(попиздили)', '(припиздить)', '(пропиздить)', '(упиздить)', '(хуй)', '(нехуй)', '(хуем)', '(хуёв)', '(хуёвина)', '(хуёвничать)', '(хуями)', '(хули)', '(хуяк)', '(хуеватенький)', '(хуевато)', '(хуета)', '(хуетень)', '(хуёво)', '(хуёвый)', '(хуё-моё)', '(хуйня)', '(сука)', '(ебало)', '(ебло)', '(ебаный)', '(сучара)', '(пидор)', '(пидорас)', '(пиздец)', '(падла)', '(мудак)', '(мудило)', '(хуйило)', '(мудофил)', '(мудень)', '(долбаеб)', '(дурак)', '(дебил)', '(дибил)', '(долбоеб)', '(пиздец)', '(пизда)', '(ебать)', '(дерьмо~(не|нет|без))', '(гавно~(не|нет|без))', '(какашка)', '(дерьмише)', '(дерьмище)', '(говнище)', '(говнише)', '(гавнише)', '(гавнище)', '(говнюк)', '(гавнюк)', '(уебки)', '(уебок)', '(недоноски)', '(недоносок)', '(ублюдки)', '(удлюдок)', '(тварь)', '(наплевать~(не|нет|без))', '(плевать~(не|нет|без))',  '(гавно~(не|нет|без))',  '(говно~(не|нет|без))',  '(жопа~(не|нет|без))',  '(заебать)',  '(отбить~(не|нет|без))',  '(профонация|профанация)',
 '(кидалово|кидалаво)', '(накрутить~(не|нет|без))', '(заломить~(не|нет|без))', '(хрен)', '(охренеть)', '(нахер)', '(на хер)', '(нахрен)', '(хреновый)', '(херовый)', '(на хрен)', '(охереть)', '(хер)', '(бля)', '(блядь)', '(бещеный)', '(бой)', '(драка~(не|нет|без))', '(дибильный~(не|нет|без))', '(дебильный~(не|нет|без))', '(мошенники~(не|нет|без|задержать|поймать))', '(вор~(не|нет|без))', '(обман~(не|нет|без))', '(обманывать~(не|нет|без))', '(коцать~(не|нет|без))', '(покоцаный~(не|нет|без))', '(покоцанный~(не|нет|без))', '(негативный~(не|нет|без))', '(негатив~(не|нет|без))', '(херота)', '(хренота)', '(помойка~(не|нет|без))', '(бесить~(не|нет|без))', '(наеб)', '(наёб)', '(вырвать~(не|нет|без))', '(рвать~(не|нет|без))',  '(пострадать /-2 (не|нет))',  '(могила)',  '(пофиг)',  '(нафиг)',  '(кошмар~(не|нет|без))',  '(ужас~(не|нет|без))', '(бардак~(не|нет|без))',
 '(ошибка~(не|нет|без))',
 '(ужесточить&санкции~(не|нет|))', '(конфискация~(не|нет|без))', '((не|нет|без)&может&добиться)', '(акции&обвал)', '(санкции&затронуть~(не|нет|без))', '(применить&санкции~(не|нет|без))', '(лишить&финансирование~(не|нет|без))', '(черный&список~(не|нет|без))', '(заморозка&счет~(не|нет|без))', '((зарплата|премия)&управление&вырасти)', '(лучше&(не|нет|без)&обращаться)', '(потерять&совесть~(не|нет|без))', '(ввети&санкции~(не|нет|без))', '(кровососы)', '(грабить~(не|нет|без))', '((рост|поднять)&ставок&ипотека)', '((рост|поднять)&процент&ипотека)', '((рост|поднять)&ставок&кредит)', '((рост|поднять)&процент&кредит)', '(заморозить~(не|нет|без))', '(запрешать&покупать)', '(убытки~(не|нет|без))', '((не|нет|без)&помогать)', '(обман~(не|нет|без))', '(разорить~(не|нет|без))', '(большой&комиссия~(не|нет|без))', '(быдло~(не|нет|без))', '(жаловаться~(не|нет|без))', '(жалоба~(не|нет|без))', '(содрать~(не|нет|без))', '(олень|алень)', '(лоботомия)', '(война~(не|нет|без))',
 '(отключить~(не|нет|без))','(ВЫПЛАТИТЬ&ДОЛГ&(ТРЕБУЕТ|ТРЕБОВАТЬ))');

    } else if ($t_type==2) {
    	$positive_array = array('(ДОВОЛЬНЫЙ~(не|нет))','(ХОРОШИЙ~(не|нет|без))','(ОТЛИЧНЫЙ~(не|нет|без))','(ДЕШЁВЫЙ~(не|нет|без))','(ВЕЖЛИВЫЙ~(не|нет|без))','(УДОБНЫЙ~(не|нет|без))','(ПРИЯТНЫЙ~(не|нет|без))','(БЫСТРЫЙ~(не|нет|без))','(СУПЕР~(не|нет|без))','(ОГРОМНЫЙ&ВЫБОР)','(КАЧЕСТВЕННЫЙ~(не|нет|без))','(ГОТОВЫЙ~(не|нет|без))','(ВЫГОДНЫЙ~(не|нет|без))','(КРУПНЫЙ~(не|нет|без))','(ВЫСОКИЙ~(не|нет|без|цена))','(ДОСТОЙНЫЙ~(не|нет|без))','(БЕСПЛАТНЫЙ~(не|нет|без))','(ДОБРОЖЕЛАТЕЛЬНЫЙ~(не|нет|без))','(ВНИМАТЕЛЬНЫЙ~(не|нет|без))','(ПРИВЕТЛИВЫЙ~(не|нет|без))','(ЕДИНСТВЕННЫЙ~(не|нет|без))','(ГАРАНТИЙНЫЙ~(не|нет|без))','(РАЗЛИЧНЫЙ~(не|нет|без))','(НЕОБХОДИМЫЙ~(не|нет|без))','(ЗАМЕЧАТЕЛЬНЫЙ~(не|нет|без))','(ДЕШЕВЫЙ~(не|нет|без))','(ПОЛОЖИТЕЛЬНЫЙ~(не|нет|без))','(ГРАМОТНЫЙ~(не|нет|без))','(БАЛЛОВЫЙ~(не|нет|без))','(ОПЕРАТИВНЫЙ~(не|нет|без))','(ПРЕКРАСНЫЙ~(не|нет|без))','(КЛАССНЫЙ~(не|нет|без))','(ПРИЕМЛЕМЫЙ~(не|нет|без))','(ЛЮБИМЫЙ~(не|нет|без))','(ДОСТУПНЫЙ~(не|нет|без))','(ПЛОХОЙ&(не|нет|без))','(КРАСИВЫЙ~(не|нет|без))','(НЕДОРОГОЙ~(не|нет|без))','(УДАЧНЫЙ~(не|нет|без))','(ОТЗЫВЧИВЫЙ~(не|нет|без))','(АДЕКВАТНЫЙ~(не|нет|без))','(ИНТЕРЕСНЫЙ~(не|нет|без))','(СЧАСТЛИВЫЙ~(не|нет|без))','(ПРОФЕССИОНАЛЬНЫЙ~(не|нет|без))','(НЕПЛОХОЙ~(не|нет|без))',
'(ПОНРАВИТЬСЯ~(не|нет|без))','(РЕКОМЕНДОВАТЬ~(не|нет|без))','(НРАВИТЬСЯ~(не|нет|без))','(СОВЕТОВАТЬ~(не|нет|без))','(РАДОВАТЬ~(не|нет|без))','(ПОРАДОВАТЬ~(не|нет|без))','(ПОЛУЧИТЬСЯ~(не|нет|без))','(ЛЮБИТЬ~(не|нет|без))','(СЭКОНОМИТЬ~(не|нет|без))','(ОБСЛУЖИТЬ~(не|нет|без))','(ПОДАРИТЬ~(не|нет|без))','(ВЫИГРАТЬ~(не|нет|без))',
'(СПАСИБО~(не|нет|без))','(БЫСТРО~(не|нет|без))','(УДОБНО~(не|нет|без))','(КАЧЕСТВО~(не|нет|без))','(ПРОБЛЕМА~(не|нет|без))','(ПРИЯТНО~(не|нет|без))','(ХОРОШО~(не|нет|без))','(МОЛОДЕЦ~(не|нет|без))','(ОТЛИЧНО~(не|нет|без))','(ВОВРЕМЯ~(не|нет|без))','(ПОМОЧЬ~(не|нет|без))');
    	$negative_array = array('(УЖАСНЫЙ)', '(НУЖНЫЙ&(не|нет|без))', '(НОВЫЙ&(не|нет|без))', '(ДОЛЖНЫЙ&(не|нет|без))', '(ХОРОШИЙ&(не|нет|без))', '(ДЕШЁВЫЙ&(не|нет|без))', '(ПОЛНЫЙ&(не|нет|без))', '(КОНЕЧНЫЙ&(не|нет|без))', '(ГАРАНТИЙНЫЙ&(не|нет|без))', '(ДАЛЁКИЙ&(не|нет|без))', '(ПЛОХОЙ)', '(ГОТОВЫЙ&(не|нет|без))', '(НОРМАЛЬНЫЙ&(не|нет|без))', '(ЦЕЛЫЙ&(не|нет|без))', '(УКАЗАННЫЙ&(не|нет|без))', '(РАННИЙ&(не|нет|без))', '(РАЗНЫЙ&(не|нет|без))', '(МАЛЕНЬКИЙ&(не|нет|без))', '(БЕСПЛАТНЫЙ&(не|нет|без))', '(ДОВОЛЬНЫЙ&(не|нет|без))', '(ОТВРАТИТЕЛЬНЫЙ~(не|нет|без))', '(НЕПРИЯТНЫЙ~(не|нет|без))', '(ПАЛЕВЫЙ~(не|нет|без))', '(БЫСТРЫЙ&(не|нет))', '(ПЛАТНЫЙ~(не|нет|без))', '(БИТЫЙ~(не|нет|без))', '(СКОРЫЙ&(не|нет|без))', '(ИНТЕРЕСНЫЙ&(не|нет|без))', '(ПРИЯТНЫЙ&(не|нет|без))', '(ТУПОЙ~(не|нет|без))', '(НЕДОВОЛЬНЫЙ)', '(ДОБРЫЙ&(не|нет|без))', '(ПОШЛЫЙ~(не|нет|без))', '(ОТЛИЧНЫЙ&(не|нет|без))', '(СУПЕР&(не|нет|без))', '(УДОБНЫЙ&(не|нет|без))', '(УВАЖАЕМЫЙ&(не|нет|без))', '(РУССКИЙ&(не|нет|без))', '(НЕКАЧЕСТВЕННЫЙ)', '(ВЕЖЛИВЫЙ&(не|нет|без))', '(ГРУБЫЙ~(не|нет|без))', '(ВНИМАТЕЛЬНЫЙ&(не|нет|без))', '(ВЫСОКИЙ&(не|нет|без))', '(ДЕШЕВЫЙ&(не|нет|без))', '(НАСТОЯЩИЙ&(не|нет|без))', '(НЕРАБОТАЮЩИЙ)', '(БРАКОВАННЫЙ~(не|нет|без))', '(КУПИТЬ&(не|нет|без))', '(СКАЗАТЬ&(не|нет|без))', '(РАБОТАТЬ&(не|нет|без))', '(ХОТЕТЬ&(не|нет|без))', '(ЖДАТЬ&(не|нет|без))', '(СДЕЛАТЬ&(не|нет|без))', '(ПОЛУЧИТЬ&(не|нет|без))', '(ПРОВЕРИТЬ&(не|нет|без))', '(ПОЗВОНИТЬ&(не|нет|без))', '(ОПЛАТИТЬ&(не|нет|без))', '(ВЕРНУТЬ&(не|нет|без))', '(НАПИСАТЬ&(не|нет|без))', '(ПРИВЕЗТИ&(не|нет|без))', '(ЗВОНИТЬ&(не|нет|без))', '(ОТВЕТИТЬ&(не|нет|без))', '(ОТКАЗАТЬСЯ~(не|нет|без))', '(СМОЧЬ&(не|нет|без))', '(ПРОВЕРЯТЬ&(не|нет|без))', '(НАЙТИ&(не|нет|без))', '(ОШИБАТЬСЯ~(не|нет|без))', '(ДОСТАВЛЯТЬ&(не|нет|без))', '(ОФОРМЛЯТЬ&(не|нет|без))', '(ПРЕДЛАГАТЬ&(не|нет|без))', '(СОВЕТОВАТЬ&(не|нет|без))', '(ПОДОЙТИ&(не|нет|без))', '(ПОМЕНЯТЬ&(не|нет|без))', '(ХАПАТЬ&(не|нет|без))', '(ВОЗВРАТ&(не|нет|без))', '(ОЧЕРЕДЬ&(не|нет|без))', '(БОЛЕЕ&(не|нет|без))', '(БРАК~(не|нет|без))', '(рехнулся)', '(выпиздить)', '(пиздошить)', '(пиздуй)', '(пиздюрить)', '(пиздюхать)', '(попиздили)', '(припиздить)', '(пропиздить)', '(упиздить)', '(хуй)', '(нехуй)', '(хуем)', '(хуёв)', '(хуёвина)', '(хуёвничать)', '(хуями)', '(хули)', '(хуяк)', '(хуеватенький)', '(хуевато)', '(хуета)', '(хуетень)', '(хуёво)', '(хуёвый)', '(хуё-моё)', '(хуйня)', '(сука)', '(ебало)', '(ебло)', '(ебаный)', '(сучара)', '(пидор)', '(пидорас)', '(пиздец)', '(падла)', '(мудак)', '(мудило)', '(хуйило)', '(мудофил)', '(мудень)', '(долбаеб)', '(дурак)', '(дебил)', '(дибил)', '(долбоеб)', '(пиздец)', '(пизда)', '(ебать)', '(дерьмо)', '(гавно)', '(какашка)', '(дерьмише)', '(дерьмище)', '(говнище)', '(говнише)', '(гавнише)', '(гавнище)', '(говнюк)', '(гавнюк)', '(уебки)', '(уебок)', '(недоноски)', '(недоносок)', '(ублюдки)', '(удлюдок)', '(тварь)', '(гавно)', '(говно)', '(жопа)', '(заебать)', '(отбить)', '(профонация|профанация)', '(кидалово|кидалаво)', '(накрутить)', '(заломить)', '(хрен)', '(охренеть)', '(нахер)', '(на хер)', '(нахрен)', '(хреновый)', '(херовый)', '(на хрен)', '(охереть)', '(хер)', '(бля)', '(блядь)', '(бещеный)', '(бой)', '(драка)', '(дибильный)', '(дебильный)', '(мошеники)', '(вор)', '(коцать)', '(покоцаный)', '(покоцанный)', '(негативный)', '(негатив)', '(херота)', '(хренота)', '(бесить~(не|нет|без))', '(наеб)', '(наёб)', '(вырвать~(не|нет|без))', '(рвать)', '(пострадать~(не|нет|без))', '(могила)', '(пофиг)', '(нафиг)', '(кошмар)', '(ужас)', '(бардак)', '(ошибка~(не|нет|без))', '(наплевать~(не|нет|без))', '(плевать~(не|нет|без))', '(мошенники~(не|нет|без|задержать|поймать))', '(обман~(не|нет|без))', '(обманывать~(не|нет|без))', '(мошенники~(не|нет|без|задержать|поймать))');
    } else {
    	$positive_array = array('(ХОРОШИЙ~(не|нет|без))', '(НРАВИТЬСЯ~(не|нет|без))', '(ВЫГОДНЫЙ~(не|нет|без))', '(ПРОФЕССИОНАЛЬНЫЙ~(не|нет|без))', '(НЕОБХОДИМЫЙ)', '(БЕСПЛАТНЫЙ~(не|нет|без))', '(ОТЛИЧНЫЙ~(не|нет|без))', '(РЕКОМЕНДОВАТЬ~(не|нет|без))', '(ЭФФЕКТНЫЙ~(не|нет|без))', '(БЛАГОТВОРИТЕЛЬНЫЙ~(не|нет|без))', '(ПРИВЛЕКАТЕЛЬНЫЙ~(не|нет|без))', '(УСПЕШНЫЙ~(не|нет|без))', '(ХВАЛЯТЬ~(не|нет|без))', '(ШИКАРНЫЙ~(не|нет|без))', '(БЛАГОДАРНЫЙ~(не|нет|без))', '(ЗАМЕЧАТЕЛЬНЫЙ~(не|нет|без))', '(СПАСИБО~(не|нет))', '(мошенники&(задержать|поймать)~(не|нет|без))');
    	$negative_array = array('(ХОРОШИЙ&(не|нет|без))', '(ПЛОХОЙ~(не|нет|без))', '(УДОБНЫЙ&(не|нет|без))', '(РАБОТАТЬ&(не|нет|без))', '(ОТКАЗАТЬСЯ~(не|нет|без))', '(НАЙТИ&(не|нет|без))', '(ОПЛАТИТЬ&(не|нет|без))', '(ПРЕДЛАГАТЬ&(не|нет|без))', '(СДЕЛАТЬ&(не|нет|без))', '(КУПИТЬ&(не|нет|без))', '(ПРОВЕРИТЬ&(не|нет|без))', '(НЕЗАКОННЫЙ)', '(ЛОЖЕВОЙ~(не|нет|без))', '(ГОНДОНСКИЙ)', '(УЁБИЩНЫЙ)', '(НЕНАДЕЖНЫЙ)', '(ТОСКЛИВЫЙ~(не|нет|без))', '(ЗЛОЙ~(не|нет|без))', '(ОБМАНУТЬ~(не|нет|без))', '(УКРАСТЬ~(не|нет|без))', '(ВСУЧИТЬ~(не|нет|без))', '(рехнулся)', '(выпиздить)', '(пиздошить)', '(пиздуй)', '(пиздюрить)', '(пиздюхать)', '(попиздили)', '(припиздить)', '(пропиздить)', '(упиздить)', '(хуй)', '(нехуй)', '(хуем)', '(хуёв)', '(хуёвина)', '(хуёвничать)', '(хуями)', '(хули)', '(хуяк)', '(хуеватенький)', '(хуевато)', '(хуета)', '(хуетень)', '(хуёво)', '(хуёвый)', '(хуё-моё)', '(хуйня)', '(сука)', '(ебало)', '(ебло)', '(ебаный)', '(сучара)', '(пидор)', '(пидорас)', '(пиздец)', '(падла)', '(мудак)', '(мудило)', '(хуйило)', '(мудофил)', '(мудень)', '(долбаеб)', '(дурак)', '(дебил)', '(дибил)', '(долбоеб)', '(пиздец)', '(пизда)', '(ебать)', '(дерьмо)', '(гавно)', '(какашка)', '(дерьмише)', '(дерьмище)', '(говнище)', '(говнише)', '(гавнише)', '(гавнище)', '(говнюк)', '(гавнюк)', '(уебки)', '(уебок)', '(недоноски)', '(недоносок)', '(ублюдки)', '(удлюдок)', '(тварь)', '(гавно)', '(говно)', '(жопа)', '(заебать)', '(отбить)', '(профонация|профанация)', '(кидалово|кидалаво)', '(накрутить)', '(заломить)', '(хрен)', '(охренеть)', '(нахер)', '(на хер)', '(нахрен)', '(хреновый)', '(херовый)', '(на хрен)', '(охереть)', '(хер)', '(бля)', '(блядь)', '(бещеный)', '(бой)', '(драка)', '(дибильный)', '(дебильный)', '(мошеники)', '(вор)', '(коцать)', '(покоцаный)', '(покоцанный)', '(негативный)', '(негатив)', '(херота)', '(хренота)', '(бесить~(не|нет|без))', '(наеб)', '(наёб)', '(вырвать~(не|нет|без))', '(рвать)', '(пострадать~(не|нет|без))', '(могила)', '(пофиг)', '(нафиг)', '(кошмар)', '(ужас)', '(бардак)', '(ошибка~(не|нет|без))', '(наплевать~(не|нет|без))', '(плевать~(не|нет|без))', '(мошенники~(не|нет|без|задержать|поймать))', '(обман~(не|нет|без))', '(обманывать~(не|нет|без))', '(мошенники~(не|нет|без|задержать|поймать))');
    }
// apply object and stop words
    if($t_object==''){
    	foreach ($positive_array as $key => $value) {
    		$positive_array[$key] = $value.'~('.$question_words.')';
	    }

	    foreach ($negative_array as $key => $value) {
	    	$negative_array[$key] = $value.'~('.$question_words.')';
	    }
    } else {
    	//echo "bank object ".$t_object."\n";
    	foreach ($positive_array as $key => $value) {
    		$positive_array[$key] = '('.$t_object.')&'.$value.'~('.$question_words.')';
	    }

	    foreach ($negative_array as $key => $value) {
	    	$negative_array[$key] = '('.$t_object.')&'.$value.'~('.$question_words.')';
	    }
    }
    
//make pos/neg query
	$positive_query='('.implode('|', $positive_array).')';
	$negative_query='('.implode('|', $negative_array).')';
	//check tone
	$positive_tone=check_post($post,$positive_query);
	$negative_tone=check_post($post,$negative_query);
	if ($positive_tone==0 && $negative_tone==0) return 0;
	elseif ($positive_tone==0 && $negative_tone==1) return -1;
	elseif ($positive_tone==1 && $negative_tone==0) return 1;
	else return 0;
}

while (1)
{
    if (!$db->ping()) {
        echo "MYSQL disconnected, reconnect after 10 sec...\n";
        sleep(10);
        $db->connect();
        die();
    }
	$ressec = $db->query('SELECT * FROM blog_orders WHERE order_nastr!=0 AND MOD (order_id, '.$_SERVER['argv'][2].') = '.$_SERVER['argv'][1].' AND (order_end>'.time().' OR order_nastr=1) ORDER BY order_id DESC');
	// $ressec = $db->query('SELECT * FROM blog_orders WHERE order_id=6914');

	while ($order=$db->fetch($ressec))
	{
		print_r($order);

		$settings = json_decode($order['order_settings'],true);
		$type_nastr = intval($settings['tone_type']);
		echo "\n".'type_nastr='.$type_nastr."\n";
		$object_nastr = $settings['tone_object'];
		//$object_nastr = str_replace(",", "|", $object_nastr);

		if ($order['order_nastr']==1)
		{
			$start=$order['order_start'];
			if ($order['order_end']<time()) $end=$order['order_end'];
			else $end=mktime(0,0,0,date('n'),date('j')+1,date('Y'));
		}
		else
		{
			$start=mktime(0,0,0,date('n'),date('j'),date('Y'));
			$end=mktime(0,0,0,date('n'),date('j')+1,date('Y'));
		}
		if ($order['order_nastr']>1000000000) 
		{
			$qlast_post=$db->query('SELECT post_id FROM blog_post WHERE post_time>'.$order['order_nastr'].' ORDER BY post_id ASC LIMIT 1');
			$dblast_post=$db->fetch($qlast_post);
			$last_post_id=$dblast_post['post_id'];//$order['order_nastr'];
		}
		else $last_post_id=$order['order_nastr'];
		$sql = 'SELECT post_id, post_content, post_host, post_nastr
		         FROM blog_post 
		         WHERE order_id='.$order['order_id'].' AND post_nastr=0
		         AND post_id>='.$last_post_id.'
		         ORDER BY post_id ASC
		         ';

		echo $sql . "\n";
		// die();
		$res = $db->query($sql);
		while ($post=$db->fetch($res))
		{
			echo $post['post_content']."\n\n";
			$tone=get_tone($post['post_content'], $type_nastr, $object_nastr);
			// echo $tone."\n";
			if ($tone!=0)
			{
				$db->query('UPDATE blog_post SET post_nastr='.$tone.' WHERE post_id='.$post['post_id'].' AND post_nastr=0');
				echo $post['post_content']."\n";
				echo "tone = ".$tone."\n";
			}
			$last_post_id=$post['post_id'];
		}
		$db->query('UPDATE blog_orders SET order_nastr='.$last_post_id.' WHERE order_id='.$order['order_id']);
		echo 'next...'."\n";
		sleep(1);
	}
	sleep(1800);
}

?>