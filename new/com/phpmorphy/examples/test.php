<?

require_once( '../src/common.php');
 
// Укажите путь к каталогу со словарями
$dir = '../dicts';
 
// Укажите, для какого языка будем использовать словарь.
// Язык указывается как ISO3166 код страны и ISO639 код языка, 
// разделенные символом подчеркивания (ru_RU, uk_UA, en_EN, de_DE и т.п.)
 
$lang = 'ru_RU';
 
// Укажите опции
// Список поддерживаемых опций см. ниже
$opts = array(
    'storage' => PHPMORPHY_STORAGE_FILE,
);
 
// создаем экземпляр класса phpMorphy
// обратите внимание: все функции phpMorphy являются throwable т.е. 
// могут возбуждать исключения типа phpMorphy_Exception (конструктор тоже)
try {
	// for ($i=0;$i<100000;$i++)
	{
	    $morphy = new phpMorphy($dir, $lang, $opts);
	    $words = array(
	'СОБАКИ',
	'КОШКА',
);
 
$result = array();
foreach($words as $word) {
	$result[$word] = $morphy->lemmatize($word);
}
print_r($result);
	    // ($morphy->lemmatize('ГУБКИ', phpMorphy::NORMAL)); // ГЛОКАЯ
	}
	print_r($morphy->getAllFormsWithGramInfo('Я', true));
	// print_r($morphy->isLastPredicted()); // TRUE
	// слово было предсказано
	 
	// print_r ($morphy->lemmatize('ГЛОКАЯ', phpMorphy::IGNORE_PREDICT)); // FALSE 
	// print_r($morphy->isLastPredicted()); // FALSE
	// // если предыдущий вызов (lemmatize к примеру) вернул FALSE isLastPredicted() возвращает FALSE
	 
	// $morphy->lemmatize('ТЕСТ', phpMorphy::NORMAL);
	// print_r($morphy->isLastPredicted()); // FALSE
	// // слово ТЕСТ было найдено в словаре
	 
	// $morphy->lemmatize('ТЕСТ', phpMorphy::ONLY_PREDICT);
	// print_r($morphy->isLastPredicted()); // TRUE
} catch(phpMorphy_Exception $e) {
    die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
}

?>