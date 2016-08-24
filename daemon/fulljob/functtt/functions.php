<?php
class KeyWorsText {
                    // данные (свойства):
                    var $s;
                    var $hasKeys;

                    // методы:
                    function KeyWorsText($text,$has) {
                     $this->s = trim($text);
                     $this->hasKeys=$has;
                     }
                    
                     function PrintIt() {
                     echo $this->s;
                     }
                     
                     function Mark(array $keys) {
                        foreach($keys as $key)
                        {
                            $this->s=preg_replace('/(\b'.$key.'.*?\b)/isu', '<span style="background:#5fc9f6">$1</span>', $this->s);
                        }
                     }

                }

function getKeyTexts($par_mas,$keys,$mark)
    {
        $n=0;
                
                foreach($par_mas as $elem)
                {
//                    echo $elem;
//                    echo '<br/>';
                    if (trim($elem)!=='')
                    {
                        $print=false;
                        $elem=str_replace("\n","",$elem);
						$elem=preg_replace('/\s+/is',' ',$elem);
                        for ($k=0;$k<count($keys) && !$print;$k++)
                        {
                            $print=preg_match('/'.$keys[$k].'/isu',$elem)>0;
//                            echo $keys[$k]."      ";
                        }
//                        if ($print===true)
//                        {
//                            echo 'true';
//                        }
//                        else echo'false';
//                        echo '<br/>';
                        $paragraphs[$n]=new KeyWorsText($elem,$print);
                        if ($print && $mark)
                        {
                            //$paragraphs[$n]->Mark($keys);
                        }
                        $n++;
                    }
                }
                return $paragraphs;
    }

function parseURL1($url)
    {
            // create curl resource
            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $url);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      //-----------FUCKING SHIT OPTIONS!!!!!!!!----------
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         curl_setopt($ch, CURLOPT_ENCODING, "");
      curl_setopt($ch, CURLOPT_TIMEOUT, 120);
         curl_setopt($ch, CURLOPT_FAILONERROR, 1);
      curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
curl_setopt ($ch, CURLOPT_COOKIE, 'adult_concepts=1' );

      //-------------------------------------------------

            // $output contains the output string
            $output = curl_exec($ch);

            // close curl resource to free up system resources
            curl_close($ch);

            return $output;
    }

    function parseURL( $url )
    {
      global $mproxy;
      $attemp=0;
      do
      {
        $uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
        //$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";

        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);       // останавливаться после 10-ого редиректа
  	  curl_setopt ($ch, CURLOPT_COOKIE, 'adult_concepts=1' );
        if ($mproxy[$attemp]!='')
          {
            echo 'proxy='.$mproxy[$attemp];
              curl_setopt($ch, CURLOPT_PROXY, $mproxy[$attemp]);
          }
        //curl_setopt($ch, CURLOPT_COOKIEJAR, "z://coo.txt");
        //curl_setopt($ch, CURLOPT_COOKIEFILE,"z://coo.txt");

        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
        $attemp++;
        //print_r($header);
        echo "\n".'code='.$header['http_code'];
      }
      while ((($header['http_code']!=200) || ($content=='')) && ($attemp<10));

      /*$header['errno']   = $err;
      $header['errmsg']  = $errmsg;
      $header['content'] = $content;
      return $header;*/
      return $content;
    }
    
    function MakeTexts($keys,$site)
    {
        $content=parseURL1($site);
                ///echo $content;
                preg_match_all('/charset=([-a-z0-9_]+)/is',$content,$charset);
                //print_r($charset);
                 if (($charset[1][0]!='') || ($charset[1][0]!='utf-8'))
                 {
                     //echo 'first if';
                  if ($charset[1][0]!="utf-8")
                  {
                      //echo '2 if';
                   $content=iconv($charset[1][0], "utf-8", $content);
                  }
                }
                $i=0;
                //удаление ненужных тегов
                $ptn= '/<head[^>]*>.*<\/head>/isU';  
                $content = preg_replace($ptn,'',$content);
                $ptn= '/<script[^>]*>.*<\/script>/isU';  
                $content = preg_replace($ptn,'',$content);
                $ptn= '/<form[^>]*>.*<\/form>/isU';  
                $content = preg_replace($ptn,'',$content);
                $ptn= '/\/\*.*?\*\//isU';  
                $content = preg_replace($ptn,'',$content);
                
                $ptn= '/<div[^>]*>/isU';
                $content = preg_replace($ptn,'<div><br/>',$content);
                $ptn= '/<td[^>]*>/isU';
                $content = preg_replace($ptn,'<td><br/>',$content);
                
                $ptn= '/<h\d[^>]*>/isU';
                $content = preg_replace($ptn,'<p>',$content);
                $ptn= '/<\/h\d[^>]*>/isU';
                $content = preg_replace($ptn,'</p>',$content);
                
                $ptn= '/<p[^>]*>/isU';
                $content = preg_replace($ptn,'<p>',$content);
                $ptn= '/<\/p[^>]*>/isU';
                $content = preg_replace($ptn,'</p>',$content);
                
                $ptn= '/<li[^>]*>/isU';
                $content = preg_replace($ptn,'<p>',$content);
                $ptn= '/<\/li[^>]*>/isU';
                $content = preg_replace($ptn,'</p>',$content);
                
                $content = strip_tags($content,'<p><br>');
                
                
                $ptn= '/<br\s*?\/\s*?>/isU';
                $content = preg_replace($ptn,'<br/>',$content);
                $content = preg_replace('/(<br\/>\s*?)+?/isU','<br/>',$content);
                //echo $content;
                
                //разбиение на параграфы
                $n=0;
                $pos = strpos($content, '<');
                
                while ($pos!==false)
                {
                    if ($pos>0)
                    {
                        $par_mas[$n++]=substr($content, 0, $pos);
                        $content = substr($content, $pos);
                        $pos = strpos($content, '<');
                    }
                    switch ($content{1})
                        {
                            case 'p':
                                $content = substr($content, 3);
                                $pos = strpos($content, '</p>');
                                $par_mas[$n++]=substr($content, 0, $pos);
                                $content = substr($content, $pos+4);
                                break;
                            case 'b':
                                $content = substr($content, $pos+5);
                                break;
                            default :
                                $pos = strpos($content, '>');
                                $content = substr($content, $pos+1);
                                break;
                            
                        }
                        $pos = strpos($content, '<');
                }
                if (strlen($content)>0)
                {
                    $par_mas[$n++]=$content;
                }
                
                
                //изменение ключевых слов
                
                //$keys=array('ado.net');
                for ($k=0;$k<count($keys);$k++)
                {
                    $keys[$k]=preg_replace('/[я,ы,у,а,и,е,о,э,ю]$/isu','', $keys[$k]);
                    $keys[$k]=addslashes($keys[$k]);
                }
                
                
                $paragraphs=getKeyTexts($par_mas,$keys,true);
                
                
                //выявление теста для показа
                $n=0;
                $num=-1;
                for ($k=0;$k<count($paragraphs);$k++)
                {
                    if ($paragraphs[$k]->hasKeys === true)
                    {
                        if(strlen($paragraphs[$k]->s)<200)
                        {
                            if ($k>0)
                            {
                                if ($paragraphs[$k-1]->hasKeys)
                                {
                                    $res[$num].=$paragraphs[$k]->s;
                                }
                                else
                                {
                                    $res[++$num]=$paragraphs[$k-1]->s.'<br/>'.$paragraphs[$k]->s;
                                }
                            }
                            else
                            {
                                $res[++$num]=$paragraphs[$k]->s;
                            }
                        }
                        elseif(strlen($paragraphs[$k]->s)>1000)
                        {
                            //$res[++$num]=$paragraphs[$k]->s;
                            //разбить на предложения
                            
                            $predl = preg_split('/\./', $paragraphs[$k]->s,0,PREG_SPLIT_NO_EMPTY);
                            $predl=getKeyTexts($predl,$keys,false);
                            for($m=0;$m<count($predl);$m++)
                            {
                                if ($predl[$m]->hasKeys === true)
                                {
                                  //echo 'predl!!!!!!!!!              '.$predl[$m]->s.'<br/>';
                                    if ((strlen($predl[$m]->s)<200) && $m>0)
                                    {
                                        if ($predl[$m-1]->hasKeys === true)
                                        {
                                            $res[$num].=' '.$predl[$m]->s.'.';
                                        }
                                        else
                                        {
                                            $res[++$num]=$predl[$m-1]->s.'. '.$predl[$m]->s.'.';
                                        }
                                    }
                                    else
                                    {
                                        $res[++$num]=$predl[$m]->s.'.';
                                    }
                                }
                            }
                        }
                        else
                        {
                            $res[++$num]=$paragraphs[$k]->s;
                        }
                    }
                }
                return $res;
    }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
