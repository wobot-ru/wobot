<?php
class Lingua_Stem_Ru
{
    var $VERSION = "0.02";
    var $Stem_Caching = 0;
    var $Stem_Cache = array();
    var $VOWEL = '/аеиоуыэюя/isu';
    var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/isu';
    var $REFLEXIVE = '/(с[яь])$/isu';
    var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|ая|яя|ою|ею)$/isu';
    var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/isu';
    var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|ят|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/isu';
    var $NOUN = '/(а|ьев|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|я)$/isu';
    var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/isu';
    var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/isu';
 
    function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }
 
    function m($s, $re)
    {
        return preg_match($re, $s);
    }
 
    function stem_word($word)
    {
		$mnot=array('тиньков','тинков','кето','юскан','воробьев');
        $word = mb_strtolower($word,'UTF-8');
		$word=preg_replace('/ё/isu','е',$word);
        //$word = strtr($word, 'ё', 'е');
        # Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
			echo 'gg';
        }
        $stem = $word;
        do {
          if (!preg_match($this->RVRE, $word, $p)) break;
          $start = $p[1];
          $RV = $p[2];
          if (!$RV) break;
 
          # Step 1
          if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
              $this->s($RV, $this->REFLEXIVE, '');
 
              if ($this->s($RV, $this->ADJECTIVE, '')) {
                  $this->s($RV, $this->PARTICIPLE, '');
              } else {
                  if (!$this->s($RV, $this->VERB, ''))
                      $this->s($RV, $this->NOUN, '');
              }
          }
          # Step 2
          $this->s($RV, '/и$/isu', '');
 
          # Step 3
          if ($this->m($RV, $this->DERIVATIONAL))
              $this->s($RV, '/ость?$/isu', '');
 
          # Step 4
          if (!$this->s($RV, '/ь$/isu', '')) {
              $this->s($RV, '/ейше?/isu', '');
              $this->s($RV, '/нн$/isu', 'н');
          }
 
          $stem = $start.$RV;
        } while(false);
        if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
      	//echo $stem."\n";
		if ((!in_array($word,$mnot)) && (mb_strlen($stem,'UTF-8')>3))
		{
        	return $stem;
		}
		else
		{
			return $word;
		}
    }
 
    function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/isu')) {
                die(__CLASS__ . "::stem_caching() - Legal values are '0′,'1′ or '2′. '.$caching_level.' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }
 
    function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }
}
// $word=new Lingua_Stem_Ru();
// $msg=$word->stem_word('юскан');
// echo '|';
// echo $msg;
?>