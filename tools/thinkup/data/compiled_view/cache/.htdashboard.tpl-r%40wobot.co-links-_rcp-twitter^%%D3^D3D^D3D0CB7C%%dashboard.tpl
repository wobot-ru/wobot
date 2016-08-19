320
a:4:{s:8:"template";a:7:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:9:"_link.tpl";b:1;s:53:"/var/www/tools/thinkup/plugins/twitter/view/links.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1335787621;s:7:"expires";i:1335788221;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/Article">
<head>
  <meta charset="utf-8">
  <title>_rcp on Twitter | ThinkUp</title>
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
                                                <option value="/tools/thinkup/?u=Roman+Yudin&n=facebook">Roman Yudin - Facebook</option>
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
  
  <div id="app-title"><a href="/tools/thinkup/?u=_rcp&n=twitter">
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
                <a href="/tools/thinkup/?u=_rcp&n=twitter">Dashboard</a>
              </li>
                                                    <li>
                                                <a href="/tools/thinkup/?v=tweets&u=_rcp&n=twitter">Tweets</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=followers&u=_rcp&n=twitter">Followers</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=you-follow&u=_rcp&n=twitter">Who You Follow</a></li>
                                                   <li class="selected">
                                                <a href="/tools/thinkup/?v=links&u=_rcp&n=twitter">Links</a></li>
                                                                                                                                                                                                                                                                                                                                                                                                               
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
                    <img src="http://a0.twimg.com/profile_images/702090982/president_normal.JPG" class="avatar2"/>
                    <img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/>
                  </div>
                </div>
                <div class="grid_15 omega">
                  <span class="tweet">_rcp <span style="color:#ccc">Twitter</span></span><br />
                  <div class="small">
                    Updated 17 mins  ago                  </div>
                </div>
              </div>
            </div>
          
                      
<div class="section">
    <h2>Links by Friends</h2>
              <div class="header clearfix">
    <div class="grid_1 alpha">&nbsp;</div>
    <div class="grid_3 right">name</div>
    <div class="grid_13">post</div>
  </div>

<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=19683274">
    <img src="http://a0.twimg.com/profile_images/565112058/hseinc-square_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=19683274">hseinc</a>
  </div>
  <div class="grid_13">
                <small>
        <a href="http://inc.hse.ru/article/2012/04/26/_2078.htm" title="http://inc.hse.ru/article/2012/04/26/_2078.htm">http://inc.hse.ru/article/2012/04/26/_2078.htm</a>
      </small>
              <div class="post">
              От партнеров: открытая лекция от руководителей Живого Журнала.&#10;&#10;- Как работает крупнейшая в России и странах СНГ... http://t.co/f6JuZgbc
                  <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/hseinc/status/194714957181497344">6 days </a>
       Moscow, Russia</span>&nbsp;</div>
  </div>
  </div>
</div>            
<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=19683274">
    <img src="http://a0.twimg.com/profile_images/565112058/hseinc-square_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=19683274">hseinc</a>
  </div>
  <div class="grid_13">
                <small>
        <a href="http://www.youtube.com/watch?v=NP8BKp-b2wA&feature=youtu.be&a" title="http://www.youtube.com/watch?v=NP8BKp-b2wA&feature=youtu.be&a">http://www.youtube.com/watch?v=NP8BKp-b2wA&feature=youtu.be&a</a>
      </small>
              <div class="post">
              Видео запись 6 занятия, к сожалению, не велась по техническим причинам. (<a href="/tools/thinkup/user/?u=YouTube&n=&i=">@YouTube</a> http://t.co/DAkD9HvY)
                  <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/hseinc/status/194727976468877313">6 days </a>
       Moscow, Russia</span>&nbsp;</div>
  </div>
  </div>
</div>            
<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=19683274">
    <img src="http://a0.twimg.com/profile_images/565112058/hseinc-square_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=19683274">hseinc</a>
  </div>
  <div class="grid_13">
                <small>
        <a href="http://inc.hse.ru/article/2012/04/03/_2031.htm" title="http://inc.hse.ru/article/2012/04/03/_2031.htm">http://inc.hse.ru/article/2012/04/03/_2031.htm</a>
      </small>
              <div class="post">
              Сегодня последний день оплаты по сниженной стоимости при ранней регистрации Международного образовательного курса... http://t.co/chEGZjPr
                  <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/hseinc/status/195086529524285440">5 days </a>
       Moscow, Russia</span>&nbsp;</div>
  </div>
  </div>
</div>            
<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=19683274">
    <img src="http://a0.twimg.com/profile_images/565112058/hseinc-square_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=19683274">hseinc</a>
  </div>
  <div class="grid_13">
                <small>
        <a href="http://www.youtube.com/watch?v=JXLGQfenx8A&feature=youtu.be&a" title="http://www.youtube.com/watch?v=JXLGQfenx8A&feature=youtu.be&a">http://www.youtube.com/watch?v=JXLGQfenx8A&feature=youtu.be&a</a>
      </small>
              <div class="post">
              Мне понравилось видео на <a href="/tools/thinkup/user/?u=YouTube&n=&i=">@YouTube</a> http://t.co/xcZ706os Мастер-класс &#34;Как писать рекламные тексты&#34;
                  <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/hseinc/status/195390933230030848">4 days </a>
       Moscow, Russia</span>&nbsp;</div>
  </div>
  </div>
</div>            
<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=19683274">
    <img src="http://a0.twimg.com/profile_images/565112058/hseinc-square_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=19683274">hseinc</a>
  </div>
  <div class="grid_13">
                <small>
        <a href="http://saas-conference.ru/" title="http://saas-conference.ru/">http://saas-conference.ru/</a>
      </small>
              <div class="post">
              RT <a href="/tools/thinkup/user/?u=verbinka&n=&i=">@verbinka</a>: 11 апреля на конференции SaaS в России выступит Михаил Смолянов <a href="/tools/thinkup/user/?u=msmolyanov&n=&i=">@msmolyanov</a> из <a href="/tools/thinkup/user/?u=megaplan&n=&i=">@megaplan</a>, детали тут http://t.co/CKNe6QsE  ...
                  <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/hseinc/status/186720255970775041">4 weeks </a>
       Moscow, Russia</span>&nbsp;</div>
  </div>
  </div>
</div>        <div class="view-all"><a href="?v=links-friends&u=_rcp&n=twitter">More...</a></div>
</div>


<div class="section">
    <h2>Photos by Friends</h2>
              <div class="header clearfix">
    <div class="grid_1 alpha">&nbsp;</div>
    <div class="grid_3 right">name</div>
    <div class="grid_13">post</div>
  </div>

<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=52817407">
    <img src="http://a0.twimg.com/profile_images/1578957090/14122010036_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=52817407">tuganbaev</a>
  </div>
  <div class="grid_13">
          <a href="http://twitpic.com/2qsmpk"><div class="pic"><img src="http://twitpic.com/show/thumb/2qsmpk" /></div></a>
        <div class="post">
              загадка - чем увлекается мой старший сын? http://twitpic.com/2qsmpk
                  <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/tuganbaev/status/25188906906">2 years </a>
       Moscow</span>&nbsp;</div>
  </div>
  </div>
</div>            
<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=24899969">
    <img src="http://a0.twimg.com/profile_images/648624468/m4_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=24899969">Lady1337</a>
  </div>
  <div class="grid_13">
          <a href="http://t.co/TeKpGxqn"><div class="pic"><img src="http://instagr.am/p/Hv2nIsg3b0/media/" /></div></a>
        <div class="post">
              Just voted.  @ Школа N13, ул. Урицкого, д.10, Люберцы http://t.co/TeKpGxqn
                  <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/Lady1337/status/176260513276248064">2 months </a>
       Atherton, Silicon Valley</span>&nbsp;</div>
  </div>
  </div>
</div>            
<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=14527198">
    <img src="http://a0.twimg.com/profile_images/908440113/SN0FCWVIIRBJFHW4_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=14527198">rossomakha</a>
  </div>
  <div class="grid_13">
          <a href="http://twitpic.com/3vg0a4"><div class="pic"><img src="http://twitpic.com/show/thumb/3vg0a4" /></div></a>
        <div class="post">
              oh yeah! ))) RT <a href="/tools/thinkup/user/?u=Niketas&n=&i=">@Niketas</a> <a href="/tools/thinkup/user/?u=rossomakha&n=&i=">@rossomakha</a> http://twitpic.com/3vg0a4
                    [<a href="/tools/thinkup/post/?t=&n=">in reply to</a>]
            <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/rossomakha/status/32497185912262658">1 year </a>
       White Noise Music Studio, 14726 SW Grayling Ln, Beaverton, OR 97007-3674, USA</span>&nbsp;</div>
  </div>
  </div>
</div>        <div class="view-all"><a href="?v=links-photos&u=_rcp&n=twitter">More...</a></div>
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
       <a href="http://thinkupapp.com">ThinkUp 1.0.4</a> &#8226; 
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