<?

set_time_limit(0);
ini_set("max_execution_time", "200000");

//$root='/var/www/daemon/';

//require_once($root.'phpmorphy/phpmorphy-0.3.7/src/common.php');


require_once($root.'sentimentjob/phpmorphy/phpmorphy-0.3.7/src/common.php');

$poses=array('Г','ДЕЕПРИЧАСТИЕ','ИНФИНИТИВ','КР_ПРИЛ','КР_ПРИЧАСТИЕ', 'Н','П','ПРЕДК','ПРИЧАСТИЕ','С');
//error_reporting (E_ALL & ~E_NOTICE);


/*

ini_set('default_charset','utf-8');
ini_set('memory_limit','-1');
ob_implicit_flush();
date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();
$poses=array('Г','ДЕЕПРИЧАСТИЕ','ИНФИНИТИВ','КР_ПРИЛ','КР_ПРИЧАСТИЕ', 'Н','П','ПРЕДК','ПРИЧАСТИЕ','С');
error_reporting (E_ALL & ~E_NOTICE);
*/

class linguistics
{
    protected $morphy;
    protected $morphyEn;

    function __construct()
    { global $root;

        //опции морфологии phpmorphy
        $opts = array(
            'storage' => PHPMORPHY_STORAGE_MEM,
            'predict_by_suffix' => true, // Enable prediction by prefix
            'predict_by_db' => true,
            'graminfo_as_text' => true
        );

        //путь к морфологии $root.'phpmorphy/phpmorphy-0.3.7/
        $dir = $root.'sentimentjob/phpmorphy/phpmorphy-0.3.7/dicts';
        //$dir = '/var/www/sentiment/kabinet/phpmorphy/phpmorphy-0.3.7/dicts';
        //$lang = 'ru_RU';
        $lang='ru_RU';

        // Create phpMorphy instance
        try {
            $this->morphy = new phpMorphy($dir, $lang, $opts);
        } catch(phpMorphy_Exception $e) {
            die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
        }

        $lang='en_EN';
        try {
            $this->morphyEn = new phpMorphy($dir, $lang, $opts);
        } catch(phpMorphy_Exception $e) {
            die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
        }

        //проверка кодировки, если не верно слово не обрабатываем
        /*if(function_exists('iconv')) {
            foreach($words as &$word) {
                $word = iconv('windows-1251', $morphy->getEncoding(), $word);
            }
            unset($word);
        }*/

    }

    public  function getFirstLemma($word)
    {
        //$word=mb_strtoupper($word,"utf-8");
        $base = $this->morphy->getBaseForm($word); //начальная форма слова

        //echo "<h1>".$base."</h1>";
        if ($base == false) $base= $this->morphyEn->getBaseForm($word);
        if ($base == false) $base[]=$word;
        return $base[0];
        //print_r ($base);
    }

    public  function getAllLemmas($word)
    {
        //echo "!!!!!!all";
        //$word=mb_strtoupper($word,"utf-8");
        $base = $this->morphy->getBaseForm($word); //начальная форма слова

        //print_r($base);
        //echo "<h1>!".$base."</h1>";
        if ($base == false) $base= $this->morphyEn->getBaseForm($word);
        if ($base == false) $base[]=$word;

        $pos = $this->morphy->getPartOfSpeech($word);

        $return = array (0=>$base, 1=>$pos);
        return $return;
        //print_r ($base);
    }




    public function cleanText($text)
    {
        return html_entity_decode($text,ENT_QUOTES, 'UTF-8');
        $text=preg_replace('/\s+/isu',' ',$text);
        $text=trim($text);

    }

    protected function getSentences($text)
    {
        //регулярка для разбития текста на предложения
        $re='/(?<=[.!?]|[.!?][\'"])\s+/';
        return preg_split($re, $text, -1, PREG_SPLIT_NO_EMPTY);
    }



    protected function getWords($text)
    {
        $text=preg_replace('/\s+/isu',' ',$text);
        //echo $text."<br><br>";
        $text=preg_replace('/([^а-яА-Яa-zA-ZёЁ])/isu',' ',$text);
        $text=mb_strtoupper($text,'utf-8'); //верхний регистр слова
        $words=preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $text, null, PREG_SPLIT_NO_EMPTY);
        //PREG_SPLIT_NO_EMPTY |

        return $words;
    }



    protected function getWordsFreq($text)
    {
        $poses=array('Г','ДЕЕПРИЧАСТИЕ','ИНФИНИТИВ','КР_ПРИЛ','КР_ПРИЧАСТИЕ', 'Н','П','ПРЕДК','ПРИЧАСТИЕ','С');
        //echo 'word freq'.$text;
        $text = $this->cleanText($text);
            //echo $text;
            $words = $this->getWords($text);
            //print_r($words);
            //создание ассоциативного массива частот слов в тексте
            foreach ($words as $word)
            {
                $base = $this->getFirstLemma($word); //начальная форма слова
                //if (!$base) $base = $this->morphyEn->getBaseForm($word);
                //if (!$base) continue;

                $pos = $this->morphy->getPartOfSpeech($word);
                //print_r($pos);
                if (!in_array($pos[0],$poses)) continue;

                $first_base=$base;
                //echo $first_base;
                $freqs[$first_base]=$freqs[$first_base]+1;

                $initial_form[$first_base]=$word;


            }

        //print_r($words);
        //$word_freq = asort($word_freq);
        //print_r($word_freq);
        arsort($freqs);
        return $freqs;
    }

    private function utf8_byte_offset_to_unit($string, $boff) {
        return mb_strlen(substr($string, 0, $boff), "UTF-8");
    }

    protected function getWordsOffset($text)
    {
        //echo "<br><br><br>".$text;
        //регулярка удаляет все кроме букв
        //$text=preg_replace('/([^а-яА-Яa-zA-ZёЁ])/isu',' $1 ',$text);

        //тоже самое с функцией обработки найденного
        /*$text=preg_replace_callback('/[^а-яА-Яa-zA-ZёЁ]/isu',create_function(
              '$matches',
              'return $matches[1]." $";'
          ),$text);*/

        $text=mb_strtoupper($text,'utf-8'); //верхний регистр слова
        //$words=preg_split('/([^а-яА-Яa-zA-ZёЁ])/isu', $text, null, PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $words=preg_split('/([^а-яА-Яa-zA-ZёЁ])/isu', $text, -1, PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // $text2=iconv("utf-8","windows-1251",$text);
        // $words2=preg_split('/([^а-яА-Яa-zA-ZёЁ])/', $text2, -1, PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);


        // preg_match_all('/([^а-яА-Яa-zA-ZёЁ])/isu', $text, $matches, PREG_OFFSET_CAPTURE);
        //echo "<pre>";

        //print_r($matches);
        //print_r($words2);
        //для разделения на предложения
        //$words=preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $text, null, PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);
        //PREG_SPLIT_NO_EMPTY |

        foreach ($words as $key=>$word)
        {
            $offset = $this->utf8_byte_offset_to_unit($text,$word[1]);
            //$wordsRes[$key]=array (0=>$word[0],1=>$word[1],2=>mb_strlen($word[0],"utf-8"));
            $wordsRes[$key]=array (0=>$word[0],1=>$offset,2=>mb_strlen($word[0],"utf-8"));
            //$wordsOffset[$key]=$word[1];
            //$wordsLenght[$key]=strlen($word[0]);
        }

        unset($words);

        /*$words[0]=$wordsRes;
        $words[1]=$wordsOffset;
        $words[2]=$wordsLenght;
*/
        //echo "<pre>";
        //print_r($wordsRes);
        return $wordsRes;

    }


    protected function makeHighlight($text, $offset, $lenght, $style)
    {

        $text=$this->cleanText($text);
        //echo '<br><br><br><br>Зашли в хайлайт по офсету '.$offset.' длине '.$lenght.'<br>';
        //echo $offset;
        //echo "<br>";
        $textLenght=mb_strlen($text, "utf-8");

        $temp1 = mb_substr($text, 0, $offset, "utf-8");
        $temp2 = mb_substr($text, $offset, $lenght, "utf-8");
        $temp3 = mb_substr($text,$offset+$lenght, $textLenght, "utf-8");

        //var_dump($temp1);
        //var_dump($temp2);
        $text = $temp1."<span style='$style'>".$temp2."</span>".$temp3;
        //echo $text."<br>";
        return $text;
    }

}



?>