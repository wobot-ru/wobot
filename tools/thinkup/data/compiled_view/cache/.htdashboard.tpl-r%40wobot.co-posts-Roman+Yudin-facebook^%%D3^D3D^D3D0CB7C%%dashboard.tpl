377
a:4:{s:8:"template";a:8:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:26:"_post.counts_no_author.tpl";b:1;s:26:"_post.author_no_counts.tpl";b:1;s:54:"/var/www/tools/thinkup/plugins/facebook/view/posts.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1340569836;s:7:"expires";i:1340570436;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/Article">
<head>
  <meta charset="utf-8">
  <title>Roman Yudin on Facebook | ThinkUp</title>
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
                                                <option value="/tools/thinkup/?u=Wobot&n=facebook+page">Wobot - Facebook Page</option>
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
  
  <div id="app-title"><a href="/tools/thinkup/?u=Roman+Yudin&n=facebook">
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
                <a href="/tools/thinkup/?u=Roman+Yudin&n=facebook">Dashboard</a>
              </li>
                                                    <li class="selected">
                                                <a href="/tools/thinkup/?v=posts&u=Roman+Yudin&n=facebook">Posts</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=friends&u=Roman+Yudin&n=facebook">Friends</a></li>
                                                                                                                                       
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
                    <img src="https://graph.facebook.com/1548406154/picture" class="avatar2"/>
                    <img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon2"/>
                  </div>
                </div>
                <div class="grid_15 omega">
                  <span class="tweet">Roman Yudin <span style="color:#ccc">Facebook</span></span><br />
                  <div class="small">
                    Updated 5 mins  ago                  </div>
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        знание фиговой тучи плагинов и надстроек jquery, а также их кастом необходим
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/wobot.ru/posts/346304202095847" title="http://www.facebook.com/wobot.ru/posts/346304202095847">http://www.facebook.com/wobot.ru/posts/346304202095847</a>
            <br><small>В компанию Wobot срочно требуется опытный Javascript Front-end разработчик!

Условия:
-Полностью белая ЗП по результатам собеседования;
-Работа в офисе </small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=215438828571958&n=facebook">2 months  ago</a>
                from Moscow, Russia
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
        
                                                     <span class="small"><a href="http://www.facebook.com/ruslan.ishmametov?ref=nf_fr" title="http://www.facebook.com/ruslan.ishmametov?ref=nf_fr">http://www.facebook.com/ruslan.ishmametov?ref=nf_fr</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=3050360788525&n=facebook">2 months  ago</a>
                from Moscow, Russia
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        &#39;bout Facebook&#39;s IPO delay...&#10;http://venturebeat.com/2012/04/25/facebook-ipo-real-delay/
                              
                            <br>                         <span class="small"><a href="http://venturebeat.com/2012/04/25/facebook-ipo-real-delay/" title="http://venturebeat.com/2012/04/25/facebook-ipo-real-delay/">http://venturebeat.com/2012/04/25/facebook-ipo-real-delay/</a>
            <br><small>Facebook's IPO was scheduled for May 2012, or so every analyst across the country was saying a couple months ago. However, that just ain't gonna happen, and we're here to tell you why.</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=378631742175036&n=facebook">2 months  ago</a>
                from Moscow, Russia
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
        
                                                     <span class="small"><a href="http://www.facebook.com/LifeFinancialGroup" title="http://www.facebook.com/LifeFinancialGroup">http://www.facebook.com/LifeFinancialGroup</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=3047518997482&n=facebook">2 months  ago</a>
                from Moscow, Russia
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
        
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=3003508657251&n=facebook">2 months  ago</a>
                from Moscow, Russia
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
</div>        <div class="view-all"><a href="?v=posts-all&u=Roman+Yudin&n=facebook">More...</a></div>
</div>

<div class="section">
    <h2>Posts on Your Wall</h2>
            <div class="clearfix article">
<div class="individual-tweet post clearfix private">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/100000251914902/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Mikhail Mamonov
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              wtf!! roman i cant believe youre tagged in this vid
                          

                            <br>                         <span class="small"><a href="http://banfish.info/boom.swf?bgimg=i.imgur.com/o0LtR.png&bgimg2=i.imgur.com/lEANf.png&img=i.imgur.com/NlMK1.png&instructX=60&instructY=100&retarded=true&name=&description=&caption=&message=&length=2%3A52&action_name=&payload_url=&buttonText=Play&gwid=1013" title="http://banfish.info/boom.swf?bgimg=i.imgur.com/o0LtR.png&bgimg2=i.imgur.com/lEANf.png&img=i.imgur.com/NlMK1.png&instructX=60&instructY=100&retarded=true&name=&description=&caption=&message=&length=2%3A52&action_name=&payload_url=&buttonText=Play&gwid=1013">http://banfish.info/boom.swf?bgimg=i.imgur.com/o0LtR.png&bgimg2=i.imgur.com/lEANf.png&img=i.imgur.com/NlMK1.png&instructX=60&instructY=100&retarded=true&name=&description=&caption=&message=&length=2%3A52&action_name=&payload_url=&buttonText=Play&gwid=1013</a>
            </span>
                      <br clear="all">




      <div class="small gray">
              <span class="sprite icon-locked"></span>
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=102739406482631&n=facebook">1 year  ago</a>
        <!---->
                from 
                Moscow, Russia
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix private">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/1415673573/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Darius Kamil
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Happy Belated Birthday, Friend....!!&#10;Thanks for the add :)&#10;All the best!
                          

            <br clear="all">




      <div class="small gray">
              <span class="sprite icon-locked"></span>
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=1901807595413&n=facebook">11 months  ago</a>
        <!---->
                from 
                Sintok
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix private">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/1502920741/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Eugenia Kozhevnikova
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Поздравляю главного разработчика!)
                          

            <br clear="all">




      <div class="small gray">
              <span class="sprite icon-locked"></span>
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=1273466727284&n=facebook">2 years  ago</a>
        <!---->
                from 
                Moscow, Russia
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix private">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/706436/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Marcus Dahlem
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              happy birthday!
                          

            <br clear="all">




      <div class="small gray">
              <span class="sprite icon-locked"></span>
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=1345880377580&n=facebook">2 years  ago</a>
        <!---->
                from 
                
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix private">
    <div class="grid_2 alpha">
      <div class="avatar-container">
                <img src="https://graph.facebook.com/736330523/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
              </div>
    </div>
    <div class="grid_3 small">
              Aleksandra Markova
      
            <br>
                  </div>
    <div class="grid_12 omega">
      <div class="post">
                              Привет! Рада, что вы хорошо добрались! Будем на связи! Скоро выложу фотографии вашего нового хай-тек BBQ старт-апа)
                          

            <br clear="all">




      <div class="small gray">
              <span class="sprite icon-locked"></span>
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=1401873337369&n=facebook">2 years  ago</a>
        <!---->
                from 
                Atherton, CA, USA
                    </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>        <div class="view-all"><a href="?v=posts-toyou&u=Roman+Yudin&n=facebook">More...</a></div>
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        MSID: рынок salesforce $2B в сша, у microsoft crm - $20B
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=2033929778385&n=facebook">9 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        &#160;
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=2033929778385&n=facebook">4<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
        
                                                     <span class="small"><a href="http://www.facebook.com/photo.php?fbid=437095869145&set=at.437095519145.239133.193844639145.1548406154&type=1" title="http://www.facebook.com/photo.php?fbid=437095869145&set=at.437095519145.239133.193844639145.1548406154&type=1">http://www.facebook.com/photo.php?fbid=437095869145&set=at.437095519145.239133.193844639145.1548406154&type=1</a>
            <br><small>http://hse-inc.ru/article/2010/09/20/_1289.htm</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=1516002430525&n=facebook">1 year  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=1516002430525&n=facebook&v=likes">4</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=1516002430525&n=facebook">4<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        evernote on windows 8 =)
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=2743887006872&set=a.2743886966871.2109476.1548406154&type=1" title="http://www.facebook.com/photo.php?fbid=2743887006872&set=a.2743886966871.2109476.1548406154&type=1">http://www.facebook.com/photo.php?fbid=2743887006872&set=a.2743886966871.2109476.1548406154&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=2743887166876&n=facebook">4 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=2743887166876&n=facebook&v=likes">2</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=2743887166876&n=facebook">3<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Какая партия победит на выборах?
                              
                            <br>                         <span class="small"><a href="http://static.slidesharecdn.com/swf/doc_player.swf?doc=random-111201055310-phpapp02&stripped_title=ss-10414294&hostedIn=fb_feed" title="http://static.slidesharecdn.com/swf/doc_player.swf?doc=random-111201055310-phpapp02&stripped_title=ss-10414294&hostedIn=fb_feed">http://static.slidesharecdn.com/swf/doc_player.swf?doc=random-111201055310-phpapp02&stripped_title=ss-10414294&hostedIn=fb_feed</a>
            <br><small>Анализ предвыборной активности пользователей в социальных медиа от компании Wobot</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=224934534244649&n=facebook">7 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        &#160;
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=224934534244649&n=facebook">3<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
        
                                                     <span class="small"><a href="http://www.facebook.com/photo.php?fbid=10150525071893908&set=a.10150120182938908.294924.184860593907&type=1" title="http://www.facebook.com/photo.php?fbid=10150525071893908&set=a.10150120182938908.294924.184860593907&type=1">http://www.facebook.com/photo.php?fbid=10150525071893908&set=a.10150120182938908.294924.184860593907&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=348371891857085&n=facebook">5 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=348371891857085&n=facebook&v=likes">1</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=348371891857085&n=facebook">2<!-- replies--></a>
        </span>
          </div>
  </div>
</div>        <div align="right"><a href="?v=posts-mostreplies&u=Roman+Yudin&n=facebook">More...</a></div>
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Обзор: Что говорят про партии в социальных сетях?
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=186236631465575&n=facebook">7 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=186236631465575&n=facebook&v=likes">4</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=186236631465575&n=facebook">1<!-- reply--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
        
                                                     <span class="small"><a href="http://www.facebook.com/photo.php?fbid=437095869145&set=at.437095519145.239133.193844639145.1548406154&type=1" title="http://www.facebook.com/photo.php?fbid=437095869145&set=at.437095519145.239133.193844639145.1548406154&type=1">http://www.facebook.com/photo.php?fbid=437095869145&set=at.437095519145.239133.193844639145.1548406154&type=1</a>
            <br><small>http://hse-inc.ru/article/2010/09/20/_1289.htm</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=1516002430525&n=facebook">1 year  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=1516002430525&n=facebook&v=likes">4</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=1516002430525&n=facebook">4<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Мнишь, что ты крутой маркетолог? Знаешь не по наслышке, что такое SMM? Слезай с печки! Попробуй свои силы http://bit.ly/y72OzF
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=2778524152779&n=facebook">3 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=2778524152779&n=facebook&v=likes">3</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        поддерживаю
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/snoozeee/posts/2268973364674" title="http://www.facebook.com/snoozeee/posts/2268973364674">http://www.facebook.com/snoozeee/posts/2268973364674</a>
            <br><small>Мы не можем вычислить всех ботов в ваших группах, потому что большинство русских ведут свои странички как дол***бы, а отсеивать живых - немно�</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=205707099504348&n=facebook">7 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=205707099504348&n=facebook&v=likes">3</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Как дорожные камеры видят номера:
                              
                            <br>                         <span class="small"><a href="http://www.youtube.com/v/8fWzT9Istdc?version=3&autohide=1&autoplay=1" title="http://www.youtube.com/v/8fWzT9Istdc?version=3&autohide=1&autoplay=1">http://www.youtube.com/v/8fWzT9Istdc?version=3&autohide=1&autoplay=1</a>
            <br><small>Current video demonstrates principle of operation of new advanced "CORDON" multi-target photo radar system, developed by SIMICON, Scientific and Production C...</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=223835507684344&n=facebook">7 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=223835507684344&n=facebook&v=likes">3</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>        <div class="view-all"><a href="?v=posts-mostlikes&u=Roman+Yudin&n=facebook">More...</a></div>
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Мнишь, что ты крутой маркетолог? Знаешь не по наслышке, что такое SMM? Слезай с печки! Попробуй свои силы http://bit.ly/y72OzF
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=2778524152779&n=facebook">3 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=2778524152779&n=facebook&v=likes">3</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              &#160;
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Откуда они взяли не прозрачную урну???
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=2744783349280&set=a.2743886966871.2109476.1548406154&type=1" title="http://www.facebook.com/photo.php?fbid=2744783349280&set=a.2743886966871.2109476.1548406154&type=1">http://www.facebook.com/photo.php?fbid=2744783349280&set=a.2743886966871.2109476.1548406154&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=2744783469283&n=facebook">4 months  ago</a>
                from Moscow, Russia
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
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Какая партия победит на выборах?
                              
                            <br>                         <span class="small"><a href="http://static.slidesharecdn.com/swf/doc_player.swf?doc=random-111201055310-phpapp02&stripped_title=ss-10414294&hostedIn=fb_feed" title="http://static.slidesharecdn.com/swf/doc_player.swf?doc=random-111201055310-phpapp02&stripped_title=ss-10414294&hostedIn=fb_feed">http://static.slidesharecdn.com/swf/doc_player.swf?doc=random-111201055310-phpapp02&stripped_title=ss-10414294&hostedIn=fb_feed</a>
            <br><small>Анализ предвыборной активности пользователей в социальных медиа от компании Wobot</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=224934534244649&n=facebook">7 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        &#160;
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=224934534244649&n=facebook">3<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        Обзор: Что говорят про партии в социальных сетях?
                              
                            <br>                         <span class="small"><a href="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1" title="http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1">http://www.facebook.com/photo.php?fbid=249814835078118&set=a.114740145252255.14535.111467238912879&type=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=186236631465575&n=facebook">7 months  ago</a>
                from Moscow, Russia
                     </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                        <span class="reply-count">
            <a href="/tools/thinkup/post/?t=186236631465575&n=facebook&v=likes">4</a>
        </span>
                </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=186236631465575&n=facebook">1<!-- reply--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix private">
    <div class="grid_13 alpha">
      <div class="post">
                                        во вторник буду участником Cloud cессии: «Какие проблемы решают облака? Опыт российских компаний в Windows Azure» http://likeinternet.ru/
                              
                            <br>                         <span class="small"><a href="http://likeinternet.ru/" title="http://likeinternet.ru/">http://likeinternet.ru/</a>
            <br><small>INTERNET LIFE 2011</small></span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=193640394048042&n=facebook">7 months  ago</a>
                from Moscow, Russia
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
</div>        <div class="view-all"><a href="?v=posts-questions&u=Roman+Yudin&n=facebook">More...</a></div>
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