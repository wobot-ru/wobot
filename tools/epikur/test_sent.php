<?

require_once('splitter.php');

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

echo '<form method="POST">
<pre>
Текст:           <input type="text" name="text" style="width: 1000px; font-size: 32px;"><br>
</pre>
<input type="submit" value="проверить">
</form>';

if ($_POST['text']!='')
{
	$sents=split_sentences($_POST['text']);
	echo '<table border="1">';
	foreach ($sents as $item)
	{
		echo '<tr><td>'.$item.'</td></tr>';
	}
	echo '</table>';
}

$tests=array(
	'Приветствую! я создал сайт ya.ru, который позволяет пользоваться яндексом проще, явный конкурент http://go.mail.ru',
	'13 апреля в 16.35 мы приглашаем вас на практический семинар «Как правильно выбрать клинику для ЭКО?». На ваши вопросы ответит Президент группы компаний «Свитчайлд», член комитета по этике и праву Российской ассоциации репродукции человека,член координационного Совета Министерства Здравоохранения РФ Сергей Владимирович Лебедев.',
	'И вот, однажды, близкие люди разойдутся в разные стороны и больше никогда не сойдутся вместе, они расщепятся на сотые, тысячные, станут погрешностью, затеряются, как третьи или четвертые цифры после запятой, которыми можно пренебречь в расчетах...(с) Сергей Лебедев',
	'Сергей Лебедев: «Воздух несколько очистится с этой смертью» http://ruskline.ru/news_rl/2013/03/25/sergej_lebedev_vozduh_neskolko_ochistitsya_s_etoj_smertyu/ … с помощью @ruskline',
	'ЛДПР не повторит ошибок Березовского В Москве прошел 26-й съезд ЛДПР. В своем докладе, который по традиции должен был стать стержнем мероприятия, бессменный вождь партии Владимир Жириновский разоблачал масонов, большевиков, происки США и «болотную» оппозицию. Официально основным вопросом съезда были выборы председателя, высшего совета и центральной контрольно-ревизионной комиссии ЛДПР. Сенсаций изначально не ожидалось. Альтернативы руководящему партией уже 23 года Владимиру Жириновскому соратники пока не видят. В бюллетенях была представлена всего одна фамилия. «Я уверен, что он будет выбран единогласно», – спрогнозировал в беседе с корреспондентом «БалтИнфо» председатель высшего совета ЛДПР и сын лидера Игорь Лебедев. Впрочем, Владимир Жириновский в ходе форума, по данным СМИ, не исключил, что по итогам следующего съезда в 2017 году у партии может появиться новый председатель. Сам отец-основатель при таком раскладе будет довольствоваться должностью руководителя фракции или вице-спикера в Госдуме. Однако в представленный на официальном сайте ЛДПР отчет эти слова лидера не вошли. Но и в 2013 году все оказалось не столь однозначно. Несмотря на отсутствие альтернативы, вопреки прогнозу Лебедева, 13 делегатов проголосовали против кандидатуры Жириновского. Это примерно 1,5% от общего числа участников съезда. Остальные, разумеется, были «за». Состав высшего совета партии был увеличен с 6 до 11 человек, а центральной ревизионной комиссии – с 3 до 6. «Много достойных людей уже сейчас принимают активное участие в жизни партии, и поэтому мы расширяем состав», – объяснил Игорь Лебедев. В свою очередь заместитель координатора ЛДПР по Санкт-Петербургу Сергей Тихомиров в беседе с корреспондентом «БалтИнфо» подчеркнул, что «никаких новых веяний» среди его однопартийцев нет. «Мы остаемся на тех же позициях, что и раньше», – подчеркнул политик. На съезде вспоминали 20-летие «первой сокрушительной победы» ЛДПР на парламентских выборах в 1993 году, когда эта партия набрала относительное большинство голосов по партийным спискам, что вызвало настоящую панику среди либералов и возгласы об «одуревшей России». Как отмечается в итоговой резолюции, в партии намерены продолжить «курс на омоложение», усилить работу с избирателями и «бороться за чистоту» в политике и экономике. «Коммунисты с их догмами провалились, ельцинские дискредитировали себя, Кремль не смог реализовать свои политические проекты. Только у ЛДПР есть всё необходимое для того, чтобы сделать Россию процветающей страной», – заключили либерал-демократы в своем документе. «ЛДПР – это явление русской политической мысли», – пояснил в своем докладе на съезде Владимир Жириновский. Слово «русский» по традиции звучало в его речи очень часто. Как водится, крепко досталось американцам, которые «поняли», что «войной русских не взять, водкой не взять, наркотиками и коррупцией не взять». Вместо этого они, по мнению политика, готовят идеологическую диверсию посредством «неправительственных организаций», которых насоздавали «тысячи по всей стране». «В США действует огромный идеологический аппарат. Они истратили на обработку наших мозгов 23 миллиарда долларов», – подсчитал Жириновский. Он уверен, что вся эта деятельность имеет глубокие исторические корни: масоны, потом большевики, потом те, кто разрушал СССР, а последним звеном цепи стала «болотная оппозиция». В отличие от них, по словам Владимира Жириновского, либерал-демократы всегда были патриотами и никогда не сделают ничего, что повредит российскому государству. «В последние годы (существования СССР, - прим. «БалтИнфо»), с 1987-го по 1991-й, мы хотели уменьшения роли КПСС, чтобы она снизошла до одной из нескольких партий. Но мы не бегали по улицам и не кричали «долой КПСС», «долой Советский Союз», «долой КГБ». Это наше отличие от всех партий», – заявил лидер ЛДПР. А в 1917 году, по его словам, ЛДПР была бы «единственной политической партией, которая не выступила бы против царя». «Все депутаты выступили, расстреляли царя, что получили – кромешный ад, красный террор», – возмутился Жириновский. По его мнению, сегодня Россию пытаются поссорить с Китаем. Вспомнил Жириновский и Кипр: «Сегодня на Кипре уже не советские большевики конфискуют деньги и драгоценности. Брюссельская власть просто забирает... И снова конфискация русских денег! Первую русскую революцию делали за деньги. Болотная – это за деньги. Все звенья одной цепи – бить по русскому государству. Куда ездил Дима Гудков? В Вашингтон. С кем там встречался? С самыми ярыми антирусскими организациями... Идет великая провокация – оккупируют нашу страну». Был помянут и умерший на днях бывший «олигарх» Борис Березовский: «Это был ярчайший индивидуалист. Он повторил судьбу КПСС: та тоже не видела смысла жизни и разбежалась в августе 1991-го. ЛДПР таких ошибок не совершает». Олег Саломатин http://www.baltinfo.ru/2013/03/25/LDPR-ne-povtorit-os..',
	'Шойгу напомнил: он не гинеколог, а министр обороны Глава МО РФ Сергей Шойгу во время визита в Севастополь проинспектировал строительство детского сада и школы для детей из семей российских моряков После традиционного возложения цветов на площади Нахимова к Вечному огню, когда министр переходил через площадь к Графской пристани, его приветствовали пророссийские активисты с триколорами. Они дружно скандировали: «Забери нас!», «Россия, мы с тобой!» и «Отстаивайте Севастополь!». На Графской пристани состоялась встреча московского гостя с министром обороны Украины Лебедевым, после которой пути разошлись: россияне на катерах отправились на флагман Черноморского флота крейсер «Москва», а украинский министр — на «Гетман Сагайдачный». Один из главных пунктов программы визита — инспекция строящегося для российских военных микрорайона в бухте Казачья. Строительство было практически сорвано украинским подрядчиком. Как доложил представитель «Спецстроя России», сейчас фундаменты, заложенные украинским подрядчиком в Севастополе, проходят экспертизу. В зависимости от ее результатов готовые конструкции придется либо укреплять, либо разбирать. Новый подрядчик — украино-турецкое ООО «Юрт Индастри» из Донецка — должен будет выполнить все намеченные объемы за 9 месяцев вместо 14. Всего планируется сдать более 3 тысяч квартир. Председатель севастопольской госадминистрации Владимир Яцуба заверил, что детский сад на 280 мест и школу на 600 мест, которые город строит для микрорайона за российские деньги, сдадут в срок. Ходом строительства Сергей Шойгу остался явно недоволен. Покидая стройплощадку, он кинул председателю севастопольского горсовета Юрию Дойникову фразу: «Ты меня за гинеколога не держи. Я не министр юстиции, я министр обороны».',
	'Опыт-это когда на смену вопросам что?где?когда?как? и почему? приходит единственный вопрос-нахуя?',
	'Руднев приехал, я наконец-то встретилась с @EugeniaVybor и поулыбалась молча на что?где?когда? - не в команде пм-пу, но в команде юрфака'
	);
foreach($tests as $test)
{
	$sents = split_sentences($test);
	echo '<p>'.$test.'</p>';
	echo '<table border="1">';
	foreach ($sents as $item)
	{
		echo '<tr><td>'.$item.'</td></tr>';
	}
	echo '</table>';
}
?>