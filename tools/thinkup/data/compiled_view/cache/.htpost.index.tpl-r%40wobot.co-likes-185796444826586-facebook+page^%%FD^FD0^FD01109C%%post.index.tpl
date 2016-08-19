511
a:5:{s:8:"template";a:8:{s:14:"post.index.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:24:"post.index._top-post.tpl";b:1;s:9:"_user.tpl";b:1;s:68:"/var/www/tools/thinkup/plugins/facebook/view/facebook.post.likes.tpl";b:1;s:11:"_footer.tpl";b:1;}s:11:"insert_tags";a:1:{s:14:"dashboard_link";a:5:{i:0;s:6:"insert";i:1;s:14:"dashboard_link";i:2;s:14:"post.index.tpl";i:3;i:13;i:4;b:0;}}s:9:"timestamp";i:1332866836;s:7:"expires";i:1332867436;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/Article">
<head>
  <meta charset="utf-8">
  <title>Post Details | ThinkUp</title>
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


 
<meta itemprop="name" content="Facebook Page post by Wobot on ThinkUp">
<meta itemprop="description" content="Новая система мониторинга от Cataphora отслеживает деятельность работников, выявляя лентяев и прогульщиков. Что думаете об эффективности подобной системы?">
<meta itemprop="image" content="http://thinkupapp.com/assets/img/thinkup-logo_sq.png">
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
          <!-- the user has not selected an instance -->
                </div> <!-- .status-bar-left -->
  
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
    
    <div class="grid_4 alpha omega" style=""> <!-- begin left nav -->
      <div id="nav">
        <ul id="top-level-sidenav">
        
        
          <li><a href="f8d698aea36fcbead2b9d5359ffca76f{insert_cache a:1:{s:4:"name";s:14:"dashboard_link";}}f8d698aea36fcbead2b9d5359ffca76f">Dashboard</a></li>
          <li>
          <a href="?t=185796444826586&n=facebook+page">Replies&nbsp;&nbsp;&nbsp;</a>
          </li>
                      <li id="grid_search_input" style="padding:10px;">
          <form id="grid_search_form" action="/tools/thinkup/post">
          <input type="hidden" name="t" value="185796444826586" />
          <input type="hidden" name="n" value="facebook page" />
            <input type="text" name="search" id="grid_search_sidebar_input" onclick="clickclear(this, 'Search')" onblur="clickrecall(this,'Search')" value="Search" style="margin-top: 3px;" size="5"/>&nbsp;<input type="submit" href="#" class="grid_search" onclick="$('#grid_search_form').submit(); return false;" value="Go">
          </form>
            </li>
<script type="text/javascript">
function clickclear(thisfield, defaulttext) {
if (thisfield.value == defaulttext) {
thisfield.value = "";
}
}

function clickrecall(thisfield, defaulttext) {
if (thisfield.value == "") {
thisfield.value = defaulttext;
}
}
</script>
            
                                                  <li class="selected"><a href="?v=likes&t=185796444826586&n=facebook+page">Likes&nbsp;&nbsp;&nbsp;</a></li>
                            </ul>

      </div>
    </div> <!-- end left nav -->

    <div class="thinkup-canvas round-all grid_20 alpha omega prepend_20 append_20" style="min-height:340px">
      <div class="prefix_1">

                    
                    
                      <div class="clearfix alert stats">
<div class="grid_2 alpha">
  <div class="avatar-container">
    <img src="https://graph.facebook.com/111467238912879/picture" class="avatar2"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon2"/>
  </div>
</div>

<div class="grid_10">
  <div class="br" style="min-height:110px;margin-bottom:1em">
    <div class="tweet pr">
                            Новая система мониторинга от Cataphora отслеживает деятельность работников, выявляя лентяев и прогульщиков. Что думаете об эффективности подобной системы?
                        
                    </div>

        <div class="clearfix" style="word-wrap:break-word;">
                             <span class="small"><a href="http://rnd.cnews.ru/math/news/top/index_science.shtml?2011%2F08%2F30%2F453280" title="http://rnd.cnews.ru/math/news/top/index_science.shtml?2011%2F08%2F30%2F453280">http://rnd.cnews.ru/math/news/top/index_science.shtml?2011%2F08%2F30%2F453280</a>
          <br><small>Новое программное обеспечение позволит выявить инициативных трудолюбивых сотрудников, а также обнаружить прогульщиков и людей, уклоняющ�</small></span>
            </div>
    
    <div class="clearfix gray" id="more-detail">
    <br>
            Aug 31, 2011 12:27 PM
            
                          </div> <!-- /#more-detail -->
  </div>
</div>
              <div class="grid_6 center keystats omega">
                <div class="big-number">
                                             <h1>1</h2>
                      <h3>like                    
                     in 7 months </h3>
                    <!-- end if favds -->
                </div>
              </div>
            </div> <!-- /.clearfix -->
           <!-- end if post -->


<div class="prepend">
  <div class="append_20 clearfix section">
      <h2>Likes</h2>
            <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
              <img src="https://graph.facebook.com/100001833591244/picture" class="avatar"/><img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon"/>
          </div>
  </div>
  <div class="grid_4 small">
        Vladimir Rybakov
        <div class="small gray">
                </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
      Moscow, Russia                </span>
  </div>
</div>      </div>
</div>



<script type="text/javascript" src="/tools/thinkup/assets/js/linkify.js"></script>
                  
      </div> <!-- /.prefix_1 -->
    </div> <!-- /.thinkup-canvas -->
  </div> <!-- /.clearfix -->
</div> <!-- /.container_24 -->

  <script type="text/javascript" src="/tools/thinkup/assets/js/linkify.js"></script>
      <script type="text/javascript">post_username = 'Wobot';</script>
    <script type="text/javascript" src="/tools/thinkup/assets/js/grid_search.js"></script>
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