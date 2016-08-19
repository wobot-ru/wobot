377
a:4:{s:8:"template";a:8:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:26:"_post.counts_no_author.tpl";b:1;s:26:"_post.author_no_counts.tpl";b:1;s:54:"/var/www/tools/thinkup/plugins/facebook/view/posts.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1337207741;s:7:"expires";i:1337208341;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/Article">
<head>
  <meta charset="utf-8">
  <title>Wobot on Facebook page | ThinkUp</title>
  <link rel="shortcut icon" type="image/x-icon" href="/tools/thinkup/assets/img/favicon.png">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/base.css">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/positioning.css">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/style.css">
    <!-- jquery -->
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/jquery-ui-1.8.13.css">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/jquery-ui-1.7.1.custom.css">
  <script type="text/javascript" src="/tools/thinkup/assets/js/jquery.min-1.4.js"></script>
  <script type="text/javascript" src="/tools/thinkup/assets/js/jquery-ui.min-1.8.js"></script>

  <!-- google chart tools -->
  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>

  <script type="text/javascript" src="/tools/thinkup/plugins/twitter/assets/js/widgets.js"></script>
  <script type="text/javascript">var site_root_path = '/tools/thinkup/';</script>
    

  <!-- custom css -->
  
  <style>
  .line { background:url('/tools/thinkup/assets/img/border-line-470.gif') no-repeat center bottom;
  margin: 8px auto;
  height: 1px;
  }

  </style>
  
  

  <script type="text/javascript">
  $(document).ready(function() {
      $(".post").hover(
        function() { $(this).children(".small").children(".metaroll").show(); },
        function() { $(this).children(".small").children(".metaroll").hide(); }
      );
      $(".metaroll").hide();
    });
  </script>


</head>
<body>
  <script type="text/javascript">
    $(document).ready(function() {
      function changeMe() {
        var _mu = $("select#instance-select").val();
        if (_mu != "null") {
          document.location.href = _mu;
        }
      }
    });
  </script>


<div id="status-bar" class="clearfix"> 

  <div class="status-bar-left">
          <!-- the user has selected a particular one of their instances -->
      
        <script type="text/javascript">
          function changeMe() {
            var _mu = $("select#instance-select").val();
            if (_mu != "null") {
              document.location.href = _mu;
            }
          }
        </script>
      
      
            <span id="instance-selector">
        <select id="instance-select" onchange="changeMe();">
          <option value="">-- Switch service user --</option>
                                    <option value="/tools/thinkup/?u=Roman+Yudin&n=google%2B">Roman Yudin - Google+</option>
                                                                      <option value="/tools/thinkup/?u=Roman+Yudin&n=facebook">Roman Yudin - Facebook</option>
                                                <option value="/tools/thinkup/?u=_rcp&n=twitter">_rcp - Twitter</option>
                              </select>
      </span>
                <a href="/tools/thinkup/crawler/updatenow.php?log=full" class="linkbutton">Update now</a>  </div> <!-- .status-bar-left -->
  
  <div class="status-bar-right text-right">
    <ul> 
              <li>Logged in as admin: r@wobot.co <script src="/tools/thinkup/install/checkversion.php"></script><a href="/tools/thinkup/account/?m=manage" class="linkbutton">Settings</a> <a href="/tools/thinkup/session/logout.php" class="linkbutton">Log Out</a></li>
          </ul>
  </div> <!-- .status-bar-right -->

  
</div> <!-- #status-bar -->

<div id="page-bkgd">

<div class="container clearfix">
  
  <div id="app-title"><a href="/tools/thinkup/?u=Wobot&n=facebook+page">
    <h1><span id="headerthink">Think</span><span id="headerup">Up</span></h1>
  </a></div> <!-- end #app-title -->
  
</div> <!-- end .container -->
<div class="container_24">
  <div class="clearfix">

    <!-- begin left nav -->
    <div class="grid_4 alpha omega">
              <div id="nav">
        <ul id="top-level-sidenav">
                              <li>
                <a href="/tools/thinkup/?u=Wobot&n=facebook+page">Dashboard</a>
              </li>
                                                    <li class="selected">
                                                <a href="/tools/thinkup/?v=posts&u=Wobot&n=facebook+page">Posts</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=friends&u=Wobot&n=facebook+page">Fans</a></li>
                                                                                                                                       
                        </ul>
      </div>
            </div>

    <div class="thinkup-canvas round-all grid_20 alpha omega prepend_20 append_20" style="min-height:340px">
      <div class="prefix_1 suffix_1">

                    
                  <!--begin public user dashboard-->
                      <div class="grid_18 alpha omega">
              <div class="clearfix alert stats round-all" id="">
                <div class="grid_2 alpha">
                  <div class="avatar-container">
                    <img src="https://graph.facebook.com/111467238912879/picture" class="avatar2"/>
                    <img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon2"/>
                  </div>
                </div>
                <div class="grid_15 omega">
                  <span class="tweet">Wobot <span style="color:#ccc">Facebook Page</span></span><br />
                  <div class="small">
                    Updated 4 mins  ago                  </div>
                </div>
              </div>
            </div>
          
                      <div class="section">
    <h2>Your Posts</h2>
            
  <div class="header clearfix">
    <div class="grid_13 alpha">&#160;</div>
    <div class="grid_2 center">
              likes         </div>
    <div class="grid_2 center omega">
      replies
    </div>
  </div>

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Заходите в гости после первых майских:)
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=3543532&n=facebook+page">3 weeks  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        &#160;
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        В компанию Wobot срочно требуется опытный Javascript Front-end разработчик!&#10;&#10;Условия:&#10;-Полностью белая ЗП по результатам собеседования;&#10;-Работа в офисе на м.Алексеевская, 7 минут пешком от метро;&#10;-Обустроенное рабочее место, super-столовая в 3-х минутах от двери офиса; &#10;-разнообразные плюшки:)&#10;&#10;repost needed:)
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=346304202095847&n=facebook+page">3 weeks  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=346304202095847&n=facebook+page&v=likes">3</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=346304202095847&n=facebook+page">4<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Ну может не сам он, но один из первых фидбеков был примерно таким:)
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=821223&n=facebook+page">3 weeks  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        &#160;
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Главное, что все по делу:)
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=821119&n=facebook+page">3 weeks  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        &#160;
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Мнение одного из первых, кто протестировал новый Wobot :)
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=345563648836569&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=345563648836569&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=345563648836569&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=345563665503234&n=facebook+page">3 weeks  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=345563665503234&n=facebook+page&v=likes">10</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=345563665503234&n=facebook+page">5<!-- replies--></a>
        </span>
          </div>
  </div>
</div>        <div class="view-all"><a href="?v=posts-all&u=Wobot&n=facebook page">More...</a></div>
</div>

<div class="section">
    <h2>Posts on Your Wall</h2>
            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/100001579124901/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Artur Shaikhutdinov
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Wobot и CDC Group проведут второй ежегодный конкурс разработчиков IT-Fighting 2012 в конце сентября - начале октября. Присоединяйтесь к сообществу и следите за обновлениями.
                          

                            <br>                         <span class="small"><a href="http://www.facebook.com/ItFighting" title="http://www.facebook.com/ItFighting">http://www.facebook.com/ItFighting</a>
            <br><small>Community</small></span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=342188625840738&n=facebook page">3 weeks  ago</a>
        <!---->
                from 
                Moscow, Russia
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/344213785638749/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              InvestBear.ru
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Wobot и CDC Group при информационной поддержке InvestBear.ru проведут второй ежегодный конкурс разработчиков IT-Fighting 2012 в конце сентября - начале октября. Присоединяйтесь к сообществу и следите за обновлениями.
                          

                            <br>                         <span class="small"><a href="http://www.facebook.com/ItFighting" title="http://www.facebook.com/ItFighting">http://www.facebook.com/ItFighting</a>
            <br><small>Community</small></span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=342194492506818&n=facebook page">3 weeks  ago</a>
        <!---->
                from 
                
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/138818376190282/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              CDC Group
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Wobott и CDC Group проведут второй ежегодный конкурс разработчиков IT-Fighting 2012 в конце сентября - начале октября. Присоединяйтесь к сообществу и следите за обновлениями.
                          

                            <br>                         <span class="small"><a href="http://www.facebook.com/ItFighting" title="http://www.facebook.com/ItFighting">http://www.facebook.com/ItFighting</a>
            <br><small>Community</small></span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=342196719173262&n=facebook page">3 weeks  ago</a>
        <!---->
                from 
                
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/111467238912879/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Wobot
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Wobot и CDC Group при информационной поддержке InvestBear.ru проведут второй ежегодный конкурс разработчиков IT-Fighting 2012 в конце сентября - начале октября. Присоединяйтесь к сообществу и следите за обновлениями.
                          

                            <br>                         <span class="small"><a href="http://www.facebook.com/ItFighting" title="http://www.facebook.com/ItFighting">http://www.facebook.com/ItFighting</a>
            <br><small>Community</small></span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=365013013535379&n=facebook page">3 weeks  ago</a>
        <!---->
                from 
                
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/1714089001/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Mikhail Brusov
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Друзья, завтра буду рад увидеть вас на презентации нового Wobot. http://www.facebook.com/events/157714484354076/  Приходите, пообщаемся в реале с глаза на глаз!
                          

                            <br>                         <span class="small"><a href="http://www.facebook.com/events/157714484354076/" title="http://www.facebook.com/events/157714484354076/">http://www.facebook.com/events/157714484354076/</a>
            <br><small>Wednesday, April 25 at 4:00pm at Башня "Федерация"</small></span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=342913895768211&n=facebook page">3 weeks  ago</a>
        <!---->
                from 
                Moscow, Russia
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>        <div class="view-all"><a href="?v=posts-toyou&u=Wobot&n=facebook page">More...</a></div>
</div>

<div class="section">
    <h2>Most Replied-To Posts</h2>
            
  <div class="header clearfix">
    <div class="grid_13 alpha">&#160;</div>
    <div class="grid_2 center">
              likes         </div>
    <div class="grid_2 center omega">
      replies
    </div>
  </div>

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        В социальных медиа набирают силу обсуждения кандидатов в Президенты РФ. Мы решили не оставаться в стороне и начали анализировать активность политиков. Краткий отчёт за январь показывает, что уровень дискуссий с участием Путина очень велик, даже если убрать шум &#34;кремлёвских ботов&#34;. Прохоров, как предмет обсуждения пользователей медиа, крепчает с каждым днём. Миронов и Зюганов по сравнению с первыми двумя кандидатами особого внимания еще не вызвали, за исключением всплеска обсуждений вокруг лидера КПРФ после шоу &#34;К барьеру&#34;. Жириновский сдаёт позиции и пока ничем, кроме судебных скандалов, внимание общественности не привлёк. &#10;Нам интересна обратная связь: какие аналитические данные и метрики политических персон в социальных сетях вам интересны? &#10;Если этот формат найдёт одобрение и поддержку, будем делать подобные регулярно.
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page">4 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page&v=likes">15</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page">11<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Много хорошего в social media говорят о ЛДПР, СР, КПРФ. В регионах много пишут о КПРФ. Упоминаний о ЕР очень много, 27% негативных. Остальное изучайте на нашей предвыборной аналитике. http://www.slideshare.net/MikhailBrusov/ss-10414294
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=249814858411449&n=facebook+page">5 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=249814858411449&n=facebook+page&v=likes">8</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=249814858411449&n=facebook+page">10<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Анализ политики в Рунете. Замечательный отчёт-исследование
                              
                            <br>                         <span class="small"><a href="http://cyber.law.harvard.edu/sites/cyber.law.harvard.edu/files/Public_Discourse_in_the_Russian_Blogosphere-RUSSIAN.pdf" title="http://cyber.law.harvard.edu/sites/cyber.law.harvard.edu/files/Public_Discourse_in_the_Russian_Blogosphere-RUSSIAN.pdf">http://cyber.law.harvard.edu/sites/cyber.law.harvard.edu/files/Public_Discourse_in_the_Russian_Blogosphere-RUSSIAN.pdf</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=110771002340961&n=facebook+page">1 year  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=110771002340961&n=facebook+page&v=likes">1</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=110771002340961&n=facebook+page">9<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
        
                                                     <span class="small"><a href="http://www.facebook.com/notes/wobot/%D1%81%D1%82%D0%B0%D1%80%D1%82%D0%B0%D0%BF/156376881073372" title="http://www.facebook.com/notes/wobot/%D1%81%D1%82%D0%B0%D1%80%D1%82%D0%B0%D0%BF/156376881073372">http://www.facebook.com/notes/wobot/%D1%81%D1%82%D0%B0%D1%80%D1%82%D0%B0%D0%BF/156376881073372</a>
            <br><small>
У вас есть стартап.
 
Про вас пишут 200-300-400 сообщений в месяц в блогах, соц.сетях, каких-то ресурсах. Может больше.
Часть из них вы находите в г�</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=156376881073372&n=facebook+page">1 year  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=156376881073372&n=facebook+page&v=likes">6</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=156376881073372&n=facebook+page">9<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Друзья, НЕ планируйте ничего масштабного на вторую половину 25 апреля, Воботы готовят вам большой сюрприз!
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=333672770025657&n=facebook+page">1 month  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        &#160;
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=333672770025657&n=facebook+page">6<!-- replies--></a>
        </span>
          </div>
  </div>
</div>        <div align="right"><a href="?v=posts-mostreplies&u=Wobot&n=facebook page">More...</a></div>
</div>

<div class="section">
    <h2>Most Liked Posts</h2>
            
  <div class="header clearfix">
    <div class="grid_13 alpha">&#160;</div>
    <div class="grid_2 center">
              likes         </div>
    <div class="grid_2 center omega">
      replies
    </div>
  </div>

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        В социальных медиа набирают силу обсуждения кандидатов в Президенты РФ. Мы решили не оставаться в стороне и начали анализировать активность политиков. Краткий отчёт за январь показывает, что уровень дискуссий с участием Путина очень велик, даже если убрать шум &#34;кремлёвских ботов&#34;. Прохоров, как предмет обсуждения пользователей медиа, крепчает с каждым днём. Миронов и Зюганов по сравнению с первыми двумя кандидатами особого внимания еще не вызвали, за исключением всплеска обсуждений вокруг лидера КПРФ после шоу &#34;К барьеру&#34;. Жириновский сдаёт позиции и пока ничем, кроме судебных скандалов, внимание общественности не привлёк. &#10;Нам интересна обратная связь: какие аналитические данные и метрики политических персон в социальных сетях вам интересны? &#10;Если этот формат найдёт одобрение и поддержку, будем делать подобные регулярно.
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page">4 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page&v=likes">15</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page">11<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
        
                                                     <span class="small"><a href="http://video.ak.fbcdn.net/cfs-ak-snc6/79200/470/1653962560953_5628.mp4?oh=74e5eaa50831bb1ccdec61352fe1e804&oe=4F747200&__gda__=1333031424_3ee6c54ef9958cca25b01d8038110db8" title="http://video.ak.fbcdn.net/cfs-ak-snc6/79200/470/1653962560953_5628.mp4?oh=74e5eaa50831bb1ccdec61352fe1e804&oe=4F747200&__gda__=1333031424_3ee6c54ef9958cca25b01d8038110db8">http://video.ak.fbcdn.net/cfs-ak-snc6/79200/470/1653962560953_5628.mp4?oh=74e5eaa50831bb1ccdec61352fe1e804&oe=4F747200&__gda__=1333031424_3ee6c54ef9958cca25b01d8038110db8</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=1653962560953&n=facebook+page">1 year  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=1653962560953&n=facebook+page&v=likes">10</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=1653962560953&n=facebook+page">3<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Мнение одного из первых, кто протестировал новый Wobot :)
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=345563648836569&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=345563648836569&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=345563648836569&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=345563665503234&n=facebook+page">3 weeks  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=345563665503234&n=facebook+page&v=likes">10</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=345563665503234&n=facebook+page">5<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        http://blog.wobot.ru/post/2896577967  Вот так и портится репутация компании.
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=103356929742031&n=facebook+page">1 year  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=103356929742031&n=facebook+page&v=likes">9</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Много хорошего в social media говорят о ЛДПР, СР, КПРФ. В регионах много пишут о КПРФ. Упоминаний о ЕР очень много, 27% негативных. Остальное изучайте на нашей предвыборной аналитике. http://www.slideshare.net/MikhailBrusov/ss-10414294
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=249814858411449&n=facebook+page">5 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=249814858411449&n=facebook+page&v=likes">8</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=249814858411449&n=facebook+page">10<!-- replies--></a>
        </span>
          </div>
  </div>
</div>        <div class="view-all"><a href="?v=posts-mostlikes&u=Wobot&n=facebook page">More...</a></div>
</div>

<div class="section">
    <h2>Inquiries</h2>
            
  <div class="header clearfix">
    <div class="grid_13 alpha">&#160;</div>
    <div class="grid_2 center">
              likes         </div>
    <div class="grid_2 center omega">
      replies
    </div>
  </div>

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Мы провели анализ упоминаний сервисов по поиску авиабилетов. Какой же из них оказался самым удобным? &#10;http://www.slideshare.net/MikhailBrusov/ss-12002615
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=319010041491930&n=facebook+page">2 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=319010041491930&n=facebook+page&v=likes">4</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Как оценить успешность фильма-премьеры? &#10;Конечно же, посмотреть, что пишут про него в социальных сетях. &#10;На примере двух значимых фильмов зимы-2012 &#34;О чём ещё говорят мужчины&#34; и &#34;Девушка с татуировкой дракона&#34; мы смотрим, как и почему фильмы становятся популярными, почему некоторые фильмы не окупаются быстро, и как пользователь выбирает тот или иной фильм на основе обсуждений в социальных сетях.&#10;&#10;Полный отчёт по ссылке:&#10;http://www.slideshare.net/MikhailBrusov/ss-11461419&#10;А вы уже посмотрели эти фильмы?
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=292400237486244&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=292400237486244&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=292400237486244&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=292400254152909&n=facebook+page">3 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=292400254152909&n=facebook+page&v=likes">5</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=292400254152909&n=facebook+page">4<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        В социальных медиа набирают силу обсуждения кандидатов в Президенты РФ. Мы решили не оставаться в стороне и начали анализировать активность политиков. Краткий отчёт за январь показывает, что уровень дискуссий с участием Путина очень велик, даже если убрать шум &#34;кремлёвских ботов&#34;. Прохоров, как предмет обсуждения пользователей медиа, крепчает с каждым днём. Миронов и Зюганов по сравнению с первыми двумя кандидатами особого внимания еще не вызвали, за исключением всплеска обсуждений вокруг лидера КПРФ после шоу &#34;К барьеру&#34;. Жириновский сдаёт позиции и пока ничем, кроме судебных скандалов, внимание общественности не привлёк. &#10;Нам интересна обратная связь: какие аналитические данные и метрики политических персон в социальных сетях вам интересны? &#10;Если этот формат найдёт одобрение и поддержку, будем делать подобные регулярно.
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=283551448371123&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page">4 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page&v=likes">15</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=283551481704453&n=facebook+page">11<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        eMarketer недавно провели опрос и выяснили, что пользователи довольно редко упоминают бренды в социальных сетях. 57.8% утверждают, что никогда не упоминали определённый бренд, 25,3% любят упоминать торговые марки лишь в положительном контексте и лишь 0,5% -- скептики, ругающие на всех социальных платформах продукты и услуги, которые им не подошли.&#10;&#10;Как создавать с пользователем диалог вокруг бренда? Если кратко, нужно наращивать аудиторию засчёт лидеров мнений в конкретном сегменте, участвовать в коммуникациях вокруг всех брендов выбранного сегмента рынка и не пытаться продавать сразу.&#10;http://memeburn.com/2012/01/we-don%E2%80%99t-talk-about-brands-online-so-what-are-we-talking-about/&#10;&#10;Касательно мониторинга социальных медиа эта статья служит ещё одним подтверждением того, что конкурентный анализ очень важен, особенно для долгосрочных проектов.&#10;&#10;Рабочей недели!&#10;
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=282518388469004&n=facebook+page">4 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=282518388469004&n=facebook+page&v=likes">3</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Новая система мониторинга от Cataphora отслеживает деятельность работников, выявляя лентяев и прогульщиков. Что думаете об эффективности подобной системы?
                              
                            <br>                         <span class="small"><a href="http://rnd.cnews.ru/math/news/top/index_science.shtml?2011%2F08%2F30%2F453280" title="http://rnd.cnews.ru/math/news/top/index_science.shtml?2011%2F08%2F30%2F453280">http://rnd.cnews.ru/math/news/top/index_science.shtml?2011%2F08%2F30%2F453280</a>
            <br><small>Новое программное обеспечение позволит выявить инициативных трудолюбивых сотрудников, а также обнаружить прогульщиков и людей, уклоняющ�</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=185796444826586&n=facebook+page">9 months  ago</a>
                from 
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=185796444826586&n=facebook+page&v=likes">1</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=185796444826586&n=facebook+page">2<!-- replies--></a>
        </span>
          </div>
  </div>
</div>        <div class="view-all"><a href="?v=posts-questions&u=Wobot&n=facebook page">More...</a></div>
</div>
           <!-- end if $data_template -->
        
        
      </div> <!-- /.prefix_1 -->
    </div> <!-- /.thinkup-canvas -->

  </div> <!-- /.clearfix -->
</div> <!-- /.container_24 -->

<script type="text/javascript" src="/tools/thinkup/assets/js/linkify.js"></script>

  <div class="small center" id="footer">
    <script type="text/javascript" src="/tools/thinkup/assets/js/linkify.js"></script>
  
    <div id="ft" role="contentinfo">
    <div id="">
      <p>
       <a href="http://thinkupapp.com">ThinkUp 1.0.6</a> &#8226; 
       <a href="http://thinkupapp.com/docs/">Documentation</a> 
       &#8226; <a href="http://groups.google.com/group/thinkupapp">Mailing List</a> 
       &#8226; <a href="http://webchat.freenode.net/?channels=thinkup">IRC Channel</a><br>
        It is nice to be nice.
        <br /> <br /><a href="http://twitter.com/thinkupapp"><img src="/tools/thinkup/assets/img/favicon_twitter.png"></a>
        <a href="http://facebook.com/thinkupapp"><img src="/tools/thinkup/assets/img/favicon_facebook.png"></a>
        <a href="http://gplus.to/thinkup"><img src="/tools/thinkup/assets/img/favicon_googleplus.png"></a>
      </p>
    </div>
    </div> <!-- #ft -->

  </div> <!-- .content -->

<div id="screen"></div>
</body>

</html>