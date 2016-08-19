377
a:4:{s:8:"template";a:8:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:26:"_post.counts_no_author.tpl";b:1;s:26:"_post.author_no_counts.tpl";b:1;s:54:"/var/www/tools/thinkup/plugins/twitter/view/tweets.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1340569817;s:7:"expires";i:1340570417;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
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
                                                    <li class="selected">
                                                <a href="/tools/thinkup/?v=tweets&u=_rcp&n=twitter">Tweets</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=followers&u=_rcp&n=twitter">Followers</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=you-follow&u=_rcp&n=twitter">Who You Follow</a></li>
                                                   <li>
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
                    Updated 5 mins  ago                  </div>
                </div>
              </div>
            </div>
          
                      <div class="section">
    <h2>Your Tweets</h2>
            
  <div class="header clearfix">
    <div class="grid_13 alpha">&#160;</div>
    <div class="grid_2 center">
              retweets         </div>
    <div class="grid_2 center omega">
      replies
    </div>
  </div>

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Бородатость и языки программирования... http://t.co/cZB7DsBD
                              
                            <br>                         <span class="small"><a href="http://blogerator.ru/uploads/pix2012/boroda-humor-17_beard-programmers-final-two.jpg" title="http://t.co/cZB7DsBD">http://blogerator.ru/uploads/pix2012/boroda-humor-17_beard-programmers-final-two.jpg</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=215887573996937216&n=twitter">3 days  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=215887573996937216"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=215887573996937216"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=215887573996937216"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        #cossa Почему реклама ВКонтакте не работает? &#9;&#9;&#9;&#9;&#9;&#10;&#9;&#9; http://t.co/EaVrLWjm via <a href="https://twitter.com/intent/user?screen_name=cossa_ru">@cossa_ru</a>
                              
                            <br>                         <span class="small"><a href="http://cossa.ru/articles/234/18534/#18643" title="http://t.co/EaVrLWjm">http://cossa.ru/articles/234/18534/#18643</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=215393913106731008&n=twitter">4 days  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=215393913106731008"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=215393913106731008"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=215393913106731008"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Почему реклама ВКонтакте не работает?. Читайте на Cossa.ru http://t.co/khO2hPcg
                              
                            <br>                         <span class="small"><a href="http://cossa.ru/articles/234/18534/" title="http://t.co/khO2hPcg">http://cossa.ru/articles/234/18534/</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=215393885977968641&n=twitter">4 days  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=215393885977968641"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=215393885977968641"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=215393885977968641"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Смерть айпеда, &#34;правильный офис&#34;, стилизованные метро приложения, которые резко понижают затраты на дизайн... http://t.co/nWWUtp3e
                              
                            <br>                         <span class="small"><a href="http://vk.com/wall1054336_118?hash=bef6b6f67d861ace5b&og=1" title="http://t.co/nWWUtp3e">http://vk.com/wall1054336_118?hash=bef6b6f67d861ace5b&og=1</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=215026231987548160&n=twitter">5 days  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=215026231987548160"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=215026231987548160"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=215026231987548160"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Свершилось, новое поколение MacBook Pro, ушел cd-привод и былая толщина, появился Retina-дисплей с разрешением 2880x180 http://t.co/509O58HR
                              
                            <br>                         <span class="small"><a href="http://www.apple.com/macbook-pro/" title="http://t.co/509O58HR">http://www.apple.com/macbook-pro/</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=212555024574840833&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=212555024574840833"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=212555024574840833"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=212555024574840833"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
</div>        <div class="view-all"><a href="?v=tweets-all&u=_rcp&n=twitter">More...</a></div>
</div>

<div class="section">

    <h2>Tweets to You</h2>
            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=284412415" title="clclt on Twitter">        <img src="http://a0.twimg.com/profile_images/1320301978/40px-Activity-calculate.svg_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=284412415" title="clclt on Twitter">clclt</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=284412415" title="clclt on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              <a href="https://twitter.com/intent/user?screen_name=_rcp">@_rcp</a>! Скоро получит 900-го фолловера! #numbers #числа
                          

            <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=208623540780535808&n=twitter">3 weeks  ago</a>
        <!--         - <a href="http://twitter.com/?status=@clclt%20&in_reply_to_status_id=208623540780535808&in_reply_to=clclt" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                The Solar System, Earth
                   <a href="http://twitter.com/intent/tweet?in_reply_to=208623540780535808"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=208623540780535808"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=208623540780535808"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=417160078" title="pedakan on Twitter">        <img src="http://a0.twimg.com/profile_images/1758031208/838727266638_normal.gif" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=417160078" title="pedakan on Twitter">pedakan</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=417160078" title="pedakan on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              <a href="https://twitter.com/intent/user?screen_name=_rcp">@_rcp</a> Всем смотреть! Ученые обнаружили у Ксюши С. <a href="https://twitter.com/intent/user?screen_name=xenia_sobchak">@xenia_sobchak</a> Синдром блядства http://t.co/n5ciIKVB
                          

                            <br>                         <span class="small"><a href="http://www.youtube.com/watch?v=yudYw6dL4Q4" title="http://t.co/n5ciIKVB">http://www.youtube.com/watch?v=yudYw6dL4Q4</a>
            </span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=190450434996051968&n=twitter">2 months  ago</a>
        <!--         - <a href="http://twitter.com/?status=@pedakan%20&in_reply_to_status_id=190450434996051968&in_reply_to=pedakan" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                Бердск
                   <a href="http://twitter.com/intent/tweet?in_reply_to=190450434996051968"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=190450434996051968"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=190450434996051968"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=453773764" title="ukywapycap on Twitter">        <img src="http://a0.twimg.com/profile_images/1767871173/160964979010957_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=453773764" title="ukywapycap on Twitter">ukywapycap</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=453773764" title="ukywapycap on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              <a href="https://twitter.com/intent/user?screen_name=_rcp">@_rcp</a> Секреты траха светской львицы <a href="https://twitter.com/intent/user?screen_name=xenia_sobchak">@xenia_sobchak</a>? http://t.co/M3eM7zxX
                          

                            <br>                         <span class="small"><a href="http://www.youtube.com/watch?v=yudYw6dL4Q4" title="http://t.co/M3eM7zxX">http://www.youtube.com/watch?v=yudYw6dL4Q4</a>
            </span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=190546268467101696&n=twitter">2 months  ago</a>
        <!--         - <a href="http://twitter.com/?status=@ukywapycap%20&in_reply_to_status_id=190546268467101696&in_reply_to=ukywapycap" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                Oboyan'
                   <a href="http://twitter.com/intent/tweet?in_reply_to=190546268467101696"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=190546268467101696"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=190546268467101696"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=224385752" title="OLBANIA on Twitter">        <img src="http://a0.twimg.com/profile_images/1185869897/653464545_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=224385752" title="OLBANIA on Twitter">OLBANIA</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=224385752" title="OLBANIA on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              <a href="https://twitter.com/intent/user?screen_name=_rcp">@_rcp</a> Камрад +1 #sledui
                          

            <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=25506102309289985&n=twitter">1 year  ago</a>
        <!--         - <a href="http://twitter.com/?status=@OLBANIA%20&in_reply_to_status_id=25506102309289985&in_reply_to=OLBANIA" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                Russia
                   <a href="http://twitter.com/intent/tweet?in_reply_to=25506102309289985"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=25506102309289985"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=25506102309289985"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=224385752" title="OLBANIA on Twitter">        <img src="http://a0.twimg.com/profile_images/1185869897/653464545_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=224385752" title="OLBANIA on Twitter">OLBANIA</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=224385752" title="OLBANIA on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              <a href="https://twitter.com/intent/user?screen_name=_rcp">@_rcp</a> Пачемута мну кажетса, вышла на славу новасть #sledui
                          

            <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=26622704727625729&n=twitter">1 year  ago</a>
        <!--         - <a href="http://twitter.com/?status=@OLBANIA%20&in_reply_to_status_id=26622704727625729&in_reply_to=OLBANIA" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                Russia
                   <a href="http://twitter.com/intent/tweet?in_reply_to=26622704727625729"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=26622704727625729"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=26622704727625729"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>        <div class="view-all"><a href="?v=tweets-messages&u=_rcp&n=twitter">More...</a></div>
</div>

<div class="section">
    <h2>Most Replied-To All Time</h2>
            
  <div class="header clearfix">
    <div class="grid_13 alpha">&#160;</div>
    <div class="grid_2 center">
              retweets         </div>
    <div class="grid_2 center omega">
      replies
    </div>
  </div>

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        <a href="https://twitter.com/intent/user?screen_name=nickm197">@nickm197</a> <a href="https://twitter.com/intent/user?screen_name=e_serg">@e_serg</a> обращайтесь посчитаем)
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=3772859453284353&n=twitter">2 years  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=3772859453284353"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=3772859453284353"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=3772859453284353"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
              </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                       &#160;
                        </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=3772859453284353&n=twitter">2<!-- replies--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Смотрю трансляцию, изучаю новые технологии windows 8, azure и phone #isvid #windows8 #azure #windowsphone
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=180207925766275072&n=twitter">3 months  ago</a>
                from Moscow, Russia
                       <a href="http://twitter.com/intent/tweet?in_reply_to=180207925766275072"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=180207925766275072"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=180207925766275072"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
              </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                       &#160;
                        </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=180207925766275072&n=twitter">1<!-- reply--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        <a href="https://twitter.com/intent/user?screen_name=DRAG0MIR">@DRAG0MIR</a> не то слово
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=178417720067506176&n=twitter">3 months  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=178417720067506176"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=178417720067506176"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=178417720067506176"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
              </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                       &#160;
                        </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=178417720067506176&n=twitter">1<!-- reply--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Москва будет исследовать социальные сети http://t.co/Cq5pY06p <a href="https://twitter.com/intent/user?screen_name=emoskva">@emoskva</a>
                              
                            <br>                         <span class="small"><a href="http://dit.mos.ru/ideas/other/proposal220.html" title="http://t.co/Cq5pY06p">http://dit.mos.ru/ideas/other/proposal220.html</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=146921262399234048&n=twitter">6 months  ago</a>
                from Moscow, Russia
                       <a href="http://twitter.com/intent/tweet?in_reply_to=146921262399234048"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=146921262399234048"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=146921262399234048"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
              </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                       &#160;
                        </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=146921262399234048&n=twitter">1<!-- reply--></a>
        </span>
          </div>
  </div>
</div>            

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        http://t.co/5n3mLh9A Slow VC Fund-Raising Portends Hard Winter For Start-Ups
                              
                            <br>                         <span class="small"><a href="http://blogs.wsj.com/venturecapital/2011/10/10/slow-vc-fund-raising-portends-hard-winter-for-start-ups/?mod=wsj_share_twitter" title="http://t.co/5n3mLh9A">http://blogs.wsj.com/venturecapital/2011/10/10/slow-vc-fund-raising-portends-hard-winter-for-start-ups/?mod=wsj_share_twitter</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=123510943568236544&n=twitter">8 months  ago</a>
                from Moscow, Russia
                       <a href="http://twitter.com/intent/tweet?in_reply_to=123510943568236544"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=123510943568236544"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=123510943568236544"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
              </span>&nbsp;</div>
      </div><!--end post-->
      
    </div>
    <div class="grid_2 center">
                       &#160;
                        </div>
    <div class="grid_2 center omega">
              <span class="reply-count">
        <a href="/tools/thinkup/post/?t=123510943568236544&n=twitter">1<!-- reply--></a>
        </span>
          </div>
  </div>
</div>        <div class="view-all"><a href="?v=tweets-mostreplies&u=_rcp&n=twitter">More...</a></div>
</div>



<div class="section">
    <h2>Favorites</h2>
            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=88034785" title="_rcp on Twitter">        <img src="http://a0.twimg.com/profile_images/702090982/president_normal.JPG" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=88034785" title="_rcp on Twitter">_rcp</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=88034785" title="_rcp on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              RT <a href="https://twitter.com/intent/user?screen_name=forbesrussia">@forbesrussia</a> Люди, рассчитывающие заработать на бренд-трекерах. | Forbes.ru #WOBOT http://bit.ly/cov9B3
                          

                            <br>                         <span class="small"><a href="http://www.forbes.ru/svoi-biznes-photogallery/56774-56584-sistemy-slezheniya/photo/4" title="http://bit.ly/cov9B3">Люди, рассчитывающие заработать на бренд-трекерах | Forbes.ru</a>
            </span>
                      <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=25456515238&n=twitter">2 years  ago</a>
        <!--         - <a href="http://twitter.com/?status=@_rcp%20&in_reply_to_status_id=25456515238&in_reply_to=_rcp" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                Moscow, Russia
                   <a href="http://twitter.com/intent/tweet?in_reply_to=25456515238"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=25456515238"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=25456515238"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=35749236" title="olegtinkov on Twitter">        <img src="http://a0.twimg.com/profile_images/1512525841/OT_KSB_Oblozhka_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=35749236" title="olegtinkov on Twitter">olegtinkov</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=35749236" title="olegtinkov on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              Олег Анисимов установил абсолютный рекорд по кол-ву комментариев здесьhttp://oleg-anisimov.livejournal.com/282053.html
                          

            <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=9636677804&n=twitter">2 years  ago</a>
        <!--         - <a href="http://twitter.com/?status=@olegtinkov%20&in_reply_to_status_id=9636677804&in_reply_to=olegtinkov" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                55042 Forte dei Marmi Lucca, Italy
                   <a href="http://twitter.com/intent/tweet?in_reply_to=9636677804"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=9636677804"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=9636677804"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>            <div class="clearfix article">
<div class="individual-tweet post clearfix">
    <div class="grid_2 alpha">
      <div class="avatar-container">
         <a href="https://twitter.com/intent/user?user_id=94863794" title="rollakis on Twitter">        <img src="http://a0.twimg.com/profile_images/609929670/RolandsLakisRU_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
        </a>      </div>
    </div>
    <div class="grid_3 small">
              <a href="https://twitter.com/intent/user?user_id=94863794" title="rollakis on Twitter">rollakis</a>
      
            <br>
                                          <a href="https://twitter.com/intent/user?user_id=94863794" title="rollakis on Twitter"><span class="sprite ui-icon-person"></span></a>
                        </div>
    <div class="grid_12 omega">
      <div class="post">
                              LIVE OK! 3 diena - liela ciena mazais futbols. Dziedataji vai sportisti?
                          

            <br clear="all">




      <div class="small gray">
            
       <span class="metaroll">
        <a href="/tools/thinkup/post/?t=9583697850&n=twitter">2 years  ago</a>
        <!--         - <a href="http://twitter.com/?status=@rollakis%20&in_reply_to_status_id=9583697850&in_reply_to=rollakis" target="_blank">Reply on Twitter</a><span class="ui-icon ui-icon-newwin"></span>
        -->
                from 
                Riga, Latvia
                   <a href="http://twitter.com/intent/tweet?in_reply_to=9583697850"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
      <a href="http://twitter.com/intent/retweet?tweet_id=9583697850"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
      <a href="http://twitter.com/intent/favorite?tweet_id=9583697850"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
             </span>&nbsp;</div>
      </div>
    </div>
  </div>
</div>        <div class="view-all"><a href="?v=ftweets-all&u=_rcp&n=twitter">More...</a></div>
</div>

<div class="section">
    <h2>Inquiries</h2>
            
  <div class="header clearfix">
    <div class="grid_13 alpha">&#160;</div>
    <div class="grid_2 center">
              retweets         </div>
    <div class="grid_2 center omega">
      replies
    </div>
  </div>

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        #cossa Почему реклама ВКонтакте не работает? &#9;&#9;&#9;&#9;&#9;&#10;&#9;&#9; http://t.co/EaVrLWjm via <a href="https://twitter.com/intent/user?screen_name=cossa_ru">@cossa_ru</a>
                              
                            <br>                         <span class="small"><a href="http://cossa.ru/articles/234/18534/#18643" title="http://t.co/EaVrLWjm">http://cossa.ru/articles/234/18534/#18643</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=215393913106731008&n=twitter">4 days  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=215393913106731008"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=215393913106731008"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=215393913106731008"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Windows Azure + Hadoop = ? https://t.co/V6EbuzuQ #hadoop #azure #windowsazure #mapreduce
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=188737304880361473&n=twitter">3 months  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=188737304880361473"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=188737304880361473"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=188737304880361473"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Интересуешься Windows Azure и облачными технологиями? #azure #sledui #followme
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=176250859997495296&n=twitter">4 months  ago</a>
                from Moscow, Russia
                       <a href="http://twitter.com/intent/tweet?in_reply_to=176250859997495296"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=176250859997495296"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=176250859997495296"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Откуда они взяли такую урну??? http://t.co/WL3wZ8xU
                              
                            <br>                         <span class="small"><a href="http://webvybory2012.ru/auth" title="http://t.co/WL3wZ8xU">http://webvybory2012.ru/auth</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=176228964304568322&n=twitter">4 months  ago</a>
                from Moscow, Russia
                       <a href="http://twitter.com/intent/tweet?in_reply_to=176228964304568322"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=176228964304568322"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=176228964304568322"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        RT <a href="https://twitter.com/intent/user?screen_name=AKomissarov">@AKomissarov</a>: В ленте много негатива и  мало конструктива. Кто готов поработать и изменить Мск в лучшую сторону? Присоединяйтесь! Ест ...
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=144178827499806720&n=twitter">7 months  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=144178827499806720"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=144178827499806720"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=144178827499806720"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
</div>        <div class="view-all"><a href="?v=tweets-questions&u=_rcp&n=twitter">More...</a></div>
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