<?

// require_once('/var/www/daemon/userjob/bot/kernel.php');
// require_once('/var/www/daemon/userjob/com/db.php');
// require_once('/var/www/daemon/userjob/com/config.php');
// require_once('porter.php');

// $db=new database();
// $db->connect();

// $word=new Lingua_Stem_Ru();
// $msg=$word->stem_word('юскан');

function replace_link($text)
{
	$regex='@\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))@';
	preg_match_all($regex, $text, $out);
	foreach ($out[0] as $key => $item)
	{
		$item_temp=$item;
		$item=preg_replace('/([\.])/isu','***point***',$item);
		$item=preg_replace('/([\?])/isu','***quest***',$item);
		//echo $item."\n";
		//echo '/'.preg_replace('/([^а-яёa-z0-9])/isu','\\\\$1',$item_temp).'/isu'."\n";
		$text=preg_replace('/'.preg_replace('/([^а-яёa-z0-9])/isu','\\\\$1',$item_temp).'/isu',$item,$text,1);
		// echo $text."\n\n\n";
	}
	return $text;
}

// echo replace_link($text);

function get_sentence($text)
{
	// echo $text."\n";
	$text=replace_link($text);
	$text=preg_replace('/(\s[a-z]+)(\.)(ru|com|net|az|en|gov|org|info|ру)([\s\,])/isu', '$1***point***$3$4', $text);
	$text=preg_replace('/([^а-яёa-z])([а-яёa-z])(\.)([а-яёa-z])(\.)/isu','$1$2***point***$4***point***',$text);
	$text=preg_replace('/([^а-яёa-z])([а-яёa-z])(\.)/isu','$1$2***point***',$text);
	$text=preg_replace('/(«[^«]*?)\!([^«]*?»)/isu','$1***voskl***$2',$text);
	$text=preg_replace('/(«[^«]*?)\.([^«]*?»)/isu','$1***point***$2',$text);
	$text=preg_replace('/(«[^«]*?)\?([^«]*?»)/isu','$1***point***$2',$text);
	$text=preg_replace('/(\!|\?)([^\s])/isu', '$1 $2',$text);
	// echo $text."\n";
	//$text=preg_replace('/(\s[^\@\:\/]+)(\.)([^r][^u]|[^c][^o][^m]|[^n][^e][^t])/isu', '$1$2 $3',$text);
	$text=preg_replace('/([\s\-\:\,])([а-яёa-z]+)(\.)([а-яёa-z]+)([\s\-\:\,])/isu','$1$2$3 $4$5',$text);
	// echo $text;
	$result = preg_split('/(?<=[.?!;])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
	foreach ($result as $key => $item)
	{
		$item=trim(preg_replace('/\*\*\*point\*\*\*/isu','.',$item));
		$item=trim(preg_replace('/\*\*\*voskl\*\*\*/isu','!',$item));
		$item=trim(preg_replace('/\*\*\*quest\*\*\*/isu','?',$item));
		$outmas[]=$item;
		// $outmas[]=trim(preg_replace('/\*\*\*quest\*\*\*/isu','?',$item));
	}
	return $outmas;
}

function get_sentence2($text)
{
	// $text=preg_replace('/([\!\?\.])/isu',' $1 ',$text);
	$re='/(?<=[.!?]|[.!?][\'"])\s+/';
	$msent=preg_split($re,$text,-1,PREG_SPLIT_NO_EMPTY);
	foreach ($msent as $item)
	{
		if (mb_strlen(trim($item),'UTF-8')<5) continue;
		$outmsent[]=$item;
	}
	// print_r($outmsent);
	return $outmsent;
}

function get_needed_sentence($mtext,$query)
{
	global $word_stem,$post,$order;
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$query);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	$mkeyword=explode('  ',$keyword);
	// print_r($mtext);
	foreach ($mtext as $it_mtext)
	{
		foreach ($mkeyword as $item)
		{
			if (trim($item)=='') continue;
			if (preg_match('/[^а-яёa-z0-9]'.addslashes($word_stem->stem_word($item)).'/isu',' '.$it_mtext.' '))
			{
				return $it_mtext;
			}
		}
	}
}

// print_r(get_sentence('Дневник кота. (Один день из жизни)Утро.Эта дура встала,Волосенки почесала,Сонно в ванную ползет-Там ее подарок ждет.Не в горшке, а как обычноНа пол я наделал лично.Пусть позлится, убирая-С добрым утром, дорогая!Подождал, покуда ЭтаПоползет из туалета.Я - под ноги. Oп, споткнулась!Получилось! Навернулась!Вышла завтракать старушка,Наливает кофе в кружку,Дикий мяв - и все дела-Получилось! Разлила!Ладно, можно отдохнуть,Пару строк в дневник черкнуть,Запишу, себе не льстя:Утро прожито не зря.День.Душевно отоспался,Только спакостить собрался.И вот тут, блин, как назло,Мне конкретно не свезло.Видел, шмотки надевала,Рыло все размалевала,Думал, что куда-то прется,Хрен поймешь, когда вернется,А она меня схватила,К ветеринару потащила,Тот мне, гад, вкатил укол-Срок прививки подошел.Ничего, за муки этиАдекватно я ответил:Мне уколы портят шкурку,Ей же - кожаную куртку.Время даром не терял-По дороге куртку дралИ штаны ее из кожиТак уделал - не дай боже!Впредь запомнит, может быть:Не хрен, блин, меня лечить!Несколько позже.На кровати рвал игрушку-Черепашную подушку.Так увлекся делом этим,Что хозяйку не заметил.По башке огреб не слабо-Что за гадостная баба!Случай к мести не искал-Тут же под кровать нассал.Но, блин, снова облажался-В руки сразу к ней попался,Как последнего дебилаРожей в луже отвозила.Как отбился - сам не знаю!Так теперь мочой воняюБудто я - ночная ваза,Младший братец унитаза.Мыть меня, наверно, будет:Может к вечеру забудет?..Защемился в тихом месте-Сочиняю планы мести.С максимально честной рожейЯ обои драл в прихожей -У меня инстинкт -и точка!(типа, нету когтеточки)Отдохнуть она решила,Пазлы, дура, разложила.Что ж, я ей возможность дамСобирать их по углам.Вечер.Эта меня мыла (вот зараза, не забыла!)Что за гадство, не пойму,Кто я ей -тупой Муму?За мытье ей отомстил:Пару чашек я разбил.Слушал, как она визжалаНа душе полегче стало.В довершение к разоруЯ содрал на кухне штору.Долго прыгал, но достал:Получилось! Оборвал!Ближе к ночи.Эта крем на рыло мажет,Значит, скоро спать заляжет,Свет пока горит, как раз,Подведу дневной баланс.В целом день прошел нормально,Перевес за мной реально:Счет в сегодняшнем турниреВ мою пользу семь - четыре.Я вполне доволен счетом,Отдых честно заработан.Всё, ложиться можно спать,Завтра будет день опять:PSДа, еще, пожалуй, можноНочью поорать истошно,Пару раз ее поднять -Не фиг, блин, спокойно спать!'));
// die();

// $qpost=$db->query('SELECT * FROM blog_full_com as a LEFT JOIN blog_orders as b ON a.ful_com_order_id=b.order_id ORDER BY ful_com_id DESC LIMIT 100');
// while ($post=$db->fetch($qpost))
// {
// 	echo "\n\n========================\n\n".$post['ful_com_post']."\n\n";
// 	$mtext=get_sentence($post['ful_com_post']);
// 	print_r($mtext);
// 	echo get_needed_sentence($mtext,$post['order_keyword']);
// }
?>