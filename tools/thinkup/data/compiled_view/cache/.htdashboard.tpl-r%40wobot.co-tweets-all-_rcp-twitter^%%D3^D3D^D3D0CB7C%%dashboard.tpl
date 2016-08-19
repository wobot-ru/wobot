559
a:5:{s:8:"template";a:8:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:16:"_grid.search.tpl";b:1;s:26:"_post.counts_no_author.tpl";b:1;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";b:1;s:11:"_footer.tpl";b:1;}s:11:"insert_tags";a:1:{s:9:"help_link";a:5:{i:0;s:6:"insert";i:1;s:9:"help_link";i:2;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";i:3;i:3;i:4;b:0;}}s:9:"timestamp";i:1332863686;s:7:"expires";i:1332864286;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
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
                                    <option value="/tools/thinkup/?u=Wobot&n=facebook+page">Wobot - Facebook Page</option>
                                                <option value="/tools/thinkup/?u=Roman+Yudin&n=facebook">Roman Yudin - Facebook</option>
                                                                      <option value="/tools/thinkup/?u=Roman+Yudin&n=google%2B">Roman Yudin - Google+</option>
                              </select>
      </span>
                <a href="/tools/thinkup/crawler/updatenow.php" class="linkbutton">Update now</a>  </div> <!-- .status-bar-left -->
  
  <div class="status-bar-right text-right">
    <ul> 
              <li>Logged in as admin: r@wobot.co <script src="/tools/thinkup/install/checkversion.php"></script><a href="/tools/thinkup/account/?m=manage" class="linkbutton">Settings</a> <a href="/tools/thinkup/session/logout.php" class="linkbutton">Log Out</a></li>
          </ul>
  </div> <!-- .status-bar-right -->

  
</div> <!-- #status-bar -->

<div id="page-bkgd">

<div class="container clearfix">
  
  <div id="app-title"><a href="/tools/thinkup/?u=Roman+Yudin&n=google%2B">
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
                    Updated 1 hour  ago                  </div>
                </div>
              </div>
            </div>
          
                      <div class="section">
<div class="clearfix">
  f8d698aea36fcbead2b9d5359ffca76f{insert_cache a:2:{s:4:"name";s:9:"help_link";s:2:"id";s:10:"tweets-all";}}f8d698aea36fcbead2b9d5359ffca76f
  <h2><a href="?v=tweets&u=_rcp&n=twitter">Tweets</a> &rarr; Your tweets</h2>
  <h3>All your tweets</h3></div>

    <div class="header">
    <a href="#" class="grid_search" title="Search" onclick="return false;"><span id="grid_search_icon">Search</span></a>     | <a href="/tools/thinkup/post/export.php?u=_rcp&n=twitter">Export</a>    </div>

    <div id="grid_search_template">
<div id="grid_overlay_div" class="grid_overlay_div2">
<script type="text/javascript">
    GRID_TYPE=2;
</script>
<iframe class="grid_iframe2" id="grid_iframe" src="/tools/thinkup/assets/img/ui-bg_glass_65_ffffff_1x400.png" 
frameborder="0" scrolling="no"></iframe>
<div id="close_grid_search_div"><a href="#" 
id="close_grid_search" onclick="return false;"><img src="/tools/thinkup/assets/img/close-icon.gif" /></a></div>
</div>
</div>    <script type="text/javascript" src="/tools/thinkup/assets/js/grid_search.js"></script>

<div id="all-posts-div">
      
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
                                        http://t.co/aNtEbEsJ
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=183285378520846336&n=twitter">4 days  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=183285378520846336"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=183285378520846336"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=183285378520846336"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Смотрю трансляцию, изучаю новые технологии windows 8, azure и phone #isvid #windows8 #azure #windowsphone
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=180207925766275072&n=twitter">2 weeks  ago</a>
                from Moscow
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
                                        New iOS features... Nice... http://t.co/HnucckAa
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=180047543814721537&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=180047543814721537"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=180047543814721537"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=180047543814721537"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Status doesn&#39;t affect accuracy.
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=179669945758662657&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=179669945758662657"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=179669945758662657"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=179669945758662657"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        RT <a href="https://twitter.com/intent/user?screen_name=RomanZaripov">@RomanZaripov</a>: Распознавание лиц в iOS 5.1. Расист. http://t.co/ZlF1nmyA
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=179159918589378561&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=179159918589378561"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=179159918589378561"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=179159918589378561"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Если вы крутой маркетолог и SMM специалист, то вам сюда http://t.co/GgcYkR4U #wobot #smm #marketing #followme
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=178534627869331456&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=178534627869331456"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=178534627869331456"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=178534627869331456"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        siri vs siri http://t.co/Vbc6yfHQ #iphone #siri
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=178479318048063489&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=178479318048063489"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=178479318048063489"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=178479318048063489"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Russian Student cracks Chrome and get get Top Prize at Google Hackathon http://t.co/GwYgD5qz #googlechrome #followme
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=178431844801458176&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=178431844801458176"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=178431844801458176"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=178431844801458176"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Российский программист за несколько минут взломал браузер Google Chrome http://t.co/qLrvo9r1
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=178430839640698880&n=twitter">2 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=178430839640698880"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=178430839640698880"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=178430839640698880"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        <a href="https://twitter.com/intent/user?screen_name=DRAG0MIR">@DRAG0MIR</a> не то слово
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=178417720067506176&n=twitter">2 weeks  ago</a>
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
                                        yeah! visual studio 11 beta is out! #followme #visualstudio #microsoft http://t.co/K9tHhU53
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=177652140733501440&n=twitter">3 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=177652140733501440"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=177652140733501440"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=177652140733501440"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Вышла бета Visual Studio 11 http://t.co/epAqTJ3z
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=177651421372628993&n=twitter">3 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=177651421372628993"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=177651421372628993"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=177651421372628993"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        <a href="https://twitter.com/intent/user?screen_name=Lady1337">@Lady1337</a> я не далеко
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=176288902318002177&n=twitter">3 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=176288902318002177"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=176288902318002177"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=176288902318002177"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Подборочка лучших избирательных участков http://t.co/iE5ejoSO http://t.co/XuyvOC7X
                              
                                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=176259955731595264&n=twitter">3 weeks  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=176259955731595264"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=176259955731595264"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=176259955731595264"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
        <a href="/tools/thinkup/post/?t=176250859997495296&n=twitter">3 weeks  ago</a>
                from Moscow
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
</div>  </div>








 








<div class="view-all" id="older-posts-div">
      <a href="/tools/thinkup/?v=tweets-all&u=_rcp&n=twitter&page=2" id="next_page">&#60; Older</a>
    </div>

</div>           <!-- end if $data_template -->
        
        
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