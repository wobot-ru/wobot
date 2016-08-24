<?php
require_once($root . 'sentimentjob/wobot.linguistics.php');

//инициализация = = = = = = = = = = = = = = = = = = = = = = = = = = = =
ini_set('default_charset', 'utf-8');

//$root='/var/www/sentiment/kabinet/';
require_once($root . 'com/config.php');
require_once($root . 'com/db.php');
//require_once($root.'wobot.sentiment.php');
//echo '1';


ini_set("memory_limit", "2048M");
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('default_charset', 'utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();


class NaiveBayes extends linguistics
{

    function __construct()
    {
        parent::__construct();
    }

    private $index = array();
    private $classes = array('pos', 'neg');
    private $classTokCounts = array('pos' => 0, 'neg' => 0);
    private $tokCount = 0;
    private $classDocCounts = array('pos' => 0, 'neg' => 0);
    private $docCount = 0;
    private $prior = array('pos' => 0.5, 'neg' => 0.5);

    public function addToIndex($file, $class, $limit = 0)
    {
        $fh = fopen($file, 'r');
        $i = 0;
        if (!in_array($class, $this->classes)) {
            echo "Invalid class specified\n";
            return;
        }
        while ($line = fgets($fh)) {
            if ($limit > 0 && $i > $limit) {
                break;
            }
            $i++;

            $this->docCount++;
            $this->classDocCounts[$class]++;
            $tokens = $this->tokenise($line);
            foreach ($tokens as $token) {
                if (!isset($this->index[$token][$class])) {
                    $this->index[$token][$class] = 0;
                }
                $this->index[$token][$class]++;
                $this->classTokCounts[$class]++;
                $this->tokCount++;
            }
        }
        fclose($fh);
    }

    public function classify($document)
    {
        //print_r($document);
        $this->prior['pos'] = $this->classDocCounts['pos'] / $this->docCount;
        $this->prior['neg'] = $this->classDocCounts['neg'] / $this->docCount;
        $tokens = $this->tokenise($document);
        $classScores = array();

        foreach ($this->classes as $class) {
            $classScores[$class] = 1;
            foreach ($tokens as $token) {
                $count = isset($this->index[$token][$class]) ?
                        $this->index[$token][$class] : 0;

                $classScores[$class] *= ($count + 1) /
                                        ($this->classTokCounts[$class] + $this->tokCount);
            }
            $classScores[$class] = $this->prior[$class] * $classScores[$class];
        }

        //print_r($classScores);
        arsort($classScores);
        return key($classScores);
    }

    private function tokenise($document)
    {
        $document = strtolower($document);
        //preg_match_all('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $document, $matches);
        preg_match_all('/\w+/', $document, $matches);
        $words=preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $document, null, PREG_SPLIT_NO_EMPTY);
        //print_r( $words); echo " ";
        //return $matches[0];
        return $words;
    }
}

/*
$op = new NaiveBayes();
$op->addToIndex('learn_NEG.txt', 'neg');
$op->addToIndex('learn_POS.txt', 'pos');

$i = 0; $t = 0; $f = 0;
$fh = fopen('test_NEG.txt', 'r');
while($line = fgets($fh)) { 
        if($i++ > 1) {
                if($op->classify($line) == 'neg') {
                        $t++;
                } else {
                        $f++;
                }
        }
}
echo "Accuracy: " . ($t / ($t+$f));*/

?>