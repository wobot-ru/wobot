
<?php
require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
date_default_timezone_set('Europe/Moscow');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>

        <?php
        header('Content-Type: text/html; charset=utf-8');

        $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $username = 'axestal.post@gmail.com';
        $password = 'xtkjdtrgsktcjc';
        //echo 1;
        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

        //$emails = imap_search($inbox, 'FROM "for.uki@gmail.com" SINCE "1 December 2012"');

        //$emails = imap_search($inbox, 'FROM "noreply@wobot.ru" SINCE "15 January 2013" SEEN');
        $emails = imap_search($inbox, 'FROM "kk@wobot.co" SINCE "1 January 2013"');
        //$emails = imap_search($inbox, 'FROM "dkolbin@wobot-research.com" SINCE "1 January 2013"');
        var_dump($emails);
        //$emails = imap_search($inbox, 'FROM "for.uki@gmail.com" SINCE "1 January 2013"');
        //$unseen=imap_search($inbox, 'UNSEEN');
        //echo "UNSEEN ".var_dump($unseen)."<br>";
        //$unseen=CountUnreadMail($hostname, $username, $password);
        //    echo "Count: ".$unseen."<br>";
        //$emails = imap_search($inbox, 'FROM "do-not-reply@trello.com" SINCE "1 January 2013"');
        //$emails = imap_search($inbox, 'FROM "xydoshnik@gmail.com"');
        //$emails = imap_search($inbox, 'FROM "welcome@linkedin.com"');
        //var_dump($emails);
        if ($emails) {
            $output = '';

            foreach ($emails as $email_number) {
                $structure=imap_fetchstructure($inbox,$email_number);
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                $message = imap_fetchbody($inbox, $email_number, 2);
                //$message = imap_body($inbox, $email_number);
                //var_dump($overview);
                //echo "<br>";
                var_dump($structure);
               // var_dump($structure->parts[1]->encoding);
                //echo "charset";
                //var_dump($structure->parts[1]->parameters[0]->value);
                //echo date('d.m.Y',strtotime($overview[0]->date));
                //echo $overview[0]->date;
                $charset=$structure->parts[1]->parameters[0]->value;
               // echo "<br>------//-----------<br>";
                $encoding=$structure->parts[1]->encoding;
                /*$output.= '<div class="toggler ' . (imap_utf8($overview[0]->seen) ? 'read' : 'unread') . '">';
                $output.= '<span class="subject">' . imap_utf8($overview[0]->subject) . '</span> ';

                $output.= '<span class="from">' . imap_utf8($overview[0]->from) . '</span>';
                $output.= '<span class="date">on ' . imap_utf8($overview[0]->date) . '</span>';
                $output.= '</div>';*/

                /* output the email body */
                //$output.= '<div class="body">' . imap_utf8($message) . '</div>';
               // var_dump($encoding);
                switch ($encoding) {
                    case 0:
                        {
                            /*$output.= '<div class="toggler ' . (($overview[0]->seen) ? 'read' : 'unread') . '">';
                            $output.= '<span class="subject">' . ($overview[0]->subject) . '</span> ';

                            $output.= '<span class="from">' . ($overview[0]->from) . '</span>';
                            $output.= '<span class="date">on ' . ($overview[0]->date) . '</span>';
                            $output.= '</div>';

                            $output.= '<div class="body">' . ($message) . '</div>';
                                $output=mb_convert_encoding($output, 'UTF-8');
                            echo $output;
                            unset($output);*/

                            //echo imap_body($inbox,$email_number);


                            //var_dump($structure);
                        //  echo "----------//--------------<br>";
                           /* if($charset=="KOI8-R"){
                                //echo "!!!!!!!!!";
                                $output=mb_convert_encoding($output, 'UTF-8', $charset);
                            }*/
                            //echo $output;
                            //unset($output);
                        }
                        break;
                    case 1:
                        {
                            //$output.= '<div class="toggler ' . (imap_8bit($overview[0]->seen) ? 'read' : 'unread') . '">';
                            $output.= '<span class="subject">' . imap_8bit($overview[0]->subject) . '</span> ';

                            $output.= '<span class="from">' . imap_8bit($overview[0]->from) . '</span>';
                            $output.= '<span class="date">on ' . date('d.m.Y',strtotime($overview[0]->date)) . '</span>';
                            $output.= '</div>';

                            /* output the email body */
                            $output.= '<div class="body">' . imap_8bit($message) . '</div>';
                            if($charset=="KOI8-R"){
                                //echo "!!!!!!!!!";
                                $output=mb_convert_encoding($output, 'UTF-8');
                            }
                            echo $output;
                            unset($output);
                        }
                        break;
                    case 2:
                        {

                        }
                        break;
                    case 3:
                        {
                            //$output.= '<div class="toggler ' . (imap_base64($overview[0]->seen) ? 'read' : 'unread') . '">';
                            //var_dump(imap_mime_header_decode($overview[0]->subject));
                            //$output.= '<span class="subject">' . imap_base64($overview[0]->subject) . '</span> ';
                            echo '<span class="subject">';
                            $elements = imap_mime_header_decode($overview[0]->subject);
                                for ($i=0; $i<count($elements); $i++) {
                                    //echo "Charset: ".$elements[$i]->charset."\n";
                                    echo "".mb_convert_encoding($elements[$i]->text,"UTF-8",$elements[$i]->charset)."\n\n";
                                }
                            echo '</span>';
                            $output.= '<span class="from">' . htmlspecialchars(imap_base64($overview[0]->from)) . '</span>';
                            $output.= '<span class="date">on ' . date('d.m.Y',strtotime($overview[0]->date)) . '</span>';
                            $output.= '</div>';

                            /* output the email body */
                            $output.= '<div class="body">' . imap_base64($message) . '</div>';
                            if($charset=="KOI8-R"){
                                //echo "!!!!!!!!!";
                                $output=mb_convert_encoding($output, 'UTF-8',$charset);
                            }
                            echo $output;
                            unset($output);
                        }
                        break;
                    case 4:
                        {
                            //$output.= '<div class="toggler ' . (quoted_printable_decode($overview[0]->seen) ? 'read' : 'unread') . '">';
                            //$test=imap_mime_header_decode($overview[0]->subject);
                            //var_dump($test[0]->charset);
                            //$output.= '<span class="subject">' . (quoted_printable_decode($overview[0]->subject)) . '</span> ';
                            $output.='<span class="subject">';
                            $elements = imap_mime_header_decode($overview[0]->subject);
                                for ($i=0; $i<count($elements); $i++) {
                                    //echo "Charset: ".$elements[$i]->charset."\n";
                                    $output.="".mb_convert_encoding($elements[$i]->text,"UTF-8",$elements[$i]->charset)."\n\n";
                                }
                            $output.= '</span>';
                            /*$test=imap_mime_header_decode($overview[0]->subject);
                            if($test[0]->charset=="KOI8-R"){
                                //$output.= '<span class="subject">' . (mb_convert_encoding(quoted_printable_decode($overview[0]->subject),'UTF-8','KOI8-R')) . '</span> ';
                                $output.= '<span class="subject">' . quoted_printable_decode((mb_convert_encoding($overview[0]->subject,'UTF-8','KOI8-R'))) . '</span> ';
                            }*/
                            $output.= '<span class="from">' . htmlspecialchars(quoted_printable_decode($overview[0]->from)) . '</span> ';
                            $output.= '<span class="date">on ' . date('d.m.Y',strtotime($overview[0]->date)) . '</span> ';
                            $output.= '</div>';

                            /* output the email body */
                            $output.= '<div class="body">' . quoted_printable_decode($message) . '</div>';
                            if($charset=="KOI8-R"){
                                //echo "!!!!!!!!!";
                                $output=mb_convert_encoding($output, 'UTF-8',$charset);
                            }
                            echo $output;
                            unset($output);
                        }
                        break;
                    default:
                        {

                        }
                        break;
                }

            }

            //echo $output;
        }
        function decode7Bit($text) {
          // If there are no spaces on the first line, assume that the body is
          // actually base64-encoded, and decode it.
          $lines = explode("\r\n", $text);
          $first_line_words = explode(' ', $lines[0]);
          if ($first_line_words[0] == $lines[0]) {
            $text = base64_decode($text);
          }
          // Manually convert common encoded characters into their UTF-8 equivalents.
          $characters = array(
            '=20' => ' ', // space.
            '=E2=80=99' => "'", // single quote.
            '=0A' => "\r\n", // line break.
            '=A0' => ' ', // non-breaking space.
            '=C2=A0' => ' ', // non-breaking space.
            "=\r\n" => '', // joined line.
            '=E2=80=A6' => '…', // ellipsis.
            '=E2=80=A2' => '•', // bullet.
          );

          // Loop through the encoded characters and replace any that are found.
          foreach ($characters as $key => $value) {
            $text = str_replace($key, $value, $text);
          }

          return $text;
        }
        function CountUnreadMail($host, $login, $passwd) {
                $mbox = imap_open($host, $login, $passwd);
                $count = 0;
                if (!$mbox) {
                    echo "Error";
                } else {
                    $headers = imap_headers($mbox);
                    foreach ($headers as $mail) {
                        $flags = substr($mail, 0, 4);
                        $isunr = (strpos($flags, "U") !== false);
                        if ($isunr)
                        $count++;
                    }
                }

                imap_close($mbox);
                return $count;
            }
        imap_close($inbox);
        ?>
    </body>
</html>