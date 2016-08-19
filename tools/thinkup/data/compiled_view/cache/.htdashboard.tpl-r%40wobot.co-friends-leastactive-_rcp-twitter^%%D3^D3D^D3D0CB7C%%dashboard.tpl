513
a:5:{s:8:"template";a:7:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:9:"_user.tpl";b:1;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";b:1;s:11:"_footer.tpl";b:1;}s:11:"insert_tags";a:1:{s:9:"help_link";a:5:{i:0;s:6:"insert";i:1;s:9:"help_link";i:2;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";i:3;i:3;i:4;b:0;}}s:9:"timestamp";i:1333121158;s:7:"expires";i:1333121758;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
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
                                                   <li class="selected">
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
  f8d698aea36fcbead2b9d5359ffca76f{insert_cache a:2:{s:4:"name";s:9:"help_link";s:2:"id";s:19:"friends-leastactive";}}f8d698aea36fcbead2b9d5359ffca76f
  <h2><a href="?v=you-follow&u=_rcp&n=twitter">Who You Follow</a> &rarr; Quietest</h2>
  <h3>People you follow who tweet the least</h3></div>

    <div class="header">
            </div>










 





      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=377300045" title="belowu on Twitter">      <img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_3_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=377300045" title="belowu on Twitter">    belowu
    </a>    <div class="small gray">
            13 followers, 59 friends<br>
            <a href="https://twitter.com/intent/user?user_id=377300045" title="belowu on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=522782801" title="boomshakaIaka on Twitter">      <img src="http://a0.twimg.com/profile_images/1893319341/oie_13613432UFwT5BF_normal.gif" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=522782801" title="boomshakaIaka on Twitter">    boomshakaIaka
    </a>    <div class="small gray">
            8 followers, 5 friends<br>
            <a href="https://twitter.com/intent/user?user_id=522782801" title="boomshakaIaka on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Wow. Fantastic Baby.</p>    <span class="small gray">
      singapore                            <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=68947525" title="callmejerry on Twitter">      <img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_3_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=68947525" title="callmejerry on Twitter">    callmejerry
    </a>    <div class="small gray">
            30 followers, 2 friends<br>
            <a href="https://twitter.com/intent/user?user_id=68947525" title="callmejerry on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                                  <br>15x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=234320993" title="deggustator on Twitter">      <img src="http://a0.twimg.com/profile_images/1597490149/image_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=234320993" title="deggustator on Twitter">    deggustator
    </a>    <div class="small gray">
            4 followers, 7 friends<br>
            <a href="https://twitter.com/intent/user?user_id=234320993" title="deggustator on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=152344821" title="dj_aristocrat on Twitter">      <img src="http://a0.twimg.com/profile_images/962561197/x_901aa7db_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=152344821" title="dj_aristocrat on Twitter">    dj_aristocrat
    </a>    <div class="small gray">
            7 followers, 5 friends<br>
            <a href="https://twitter.com/intent/user?user_id=152344821" title="dj_aristocrat on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
      Moscow                            <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=396764133" title="Doloreskoi2973 on Twitter">      <img src="http://a0.twimg.com/profile_images/1603074350/348428550155-27-bg_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=396764133" title="Doloreskoi2973 on Twitter">    Doloreskoi2973
    </a>    <div class="small gray">
            394 followers, 1,095 friends<br>
            <a href="https://twitter.com/intent/user?user_id=396764133" title="Doloreskoi2973 on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
      Baltimore                </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=59973767" title="JasmineCurry on Twitter">      <img src="http://a0.twimg.com/profile_images/371061647/m_70a5f1ab2885a325b9b3fab5a9b4b9f9_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=59973767" title="JasmineCurry on Twitter">    JasmineCurry
    </a>    <div class="small gray">
            31 followers, 25 friends<br>
            <a href="https://twitter.com/intent/user?user_id=59973767" title="JasmineCurry on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                                  <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=215265937" title="Kookonya on Twitter">      <img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_5_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=215265937" title="Kookonya on Twitter">    Kookonya
    </a>    <div class="small gray">
            1 followers, 1 friends<br>
            <a href="https://twitter.com/intent/user?user_id=215265937" title="Kookonya on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=384812360" title="LexaSab on Twitter">      <img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_2_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=384812360" title="LexaSab on Twitter">    LexaSab
    </a>    <div class="small gray">
            5 followers, 41 friends<br>
            <a href="https://twitter.com/intent/user?user_id=384812360" title="LexaSab on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=133844306" title="Mr__Po on Twitter">      <img src="http://a0.twimg.com/profile_images/828555071/____2_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=133844306" title="Mr__Po on Twitter">    Mr__Po
    </a>    <div class="small gray">
            20 followers, 11 friends<br>
            <a href="https://twitter.com/intent/user?user_id=133844306" title="Mr__Po on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
      Москва, город Москва                            <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=243677546" title="pi4en on Twitter">      <img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_0_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=243677546" title="pi4en on Twitter">    pi4en
    </a>    <div class="small gray">
            <a href="https://twitter.com/intent/user?user_id=243677546" title="pi4en on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=328559550" title="prostofert on Twitter">      <img src="http://a0.twimg.com/profile_images/1664258396/image_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=328559550" title="prostofert on Twitter">    prostofert
    </a>    <div class="small gray">
            31 followers, 208 friends<br>
            <a href="https://twitter.com/intent/user?user_id=328559550" title="prostofert on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=391322608" title="RUAlexFisher on Twitter">      <img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_5_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=391322608" title="RUAlexFisher on Twitter">    RUAlexFisher
    </a>    <div class="small gray">
            5 followers, 21 friends<br>
            <a href="https://twitter.com/intent/user?user_id=391322608" title="RUAlexFisher on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=280388988" title="ulikemenot on Twitter">      <img src="http://a0.twimg.com/profile_images/1307981260/029_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=280388988" title="ulikemenot on Twitter">    ulikemenot
    </a>    <div class="small gray">
            18 followers, 21 friends<br>
            <a href="https://twitter.com/intent/user?user_id=280388988" title="ulikemenot on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
      mandaluyong city                </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=268385256" title="Zapretnaya on Twitter">      <img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_6_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=268385256" title="Zapretnaya on Twitter">    Zapretnaya
    </a>    <div class="small gray">
            19 followers, 106 friends<br>
            <a href="https://twitter.com/intent/user?user_id=268385256" title="Zapretnaya on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    &#160;    <span class="small gray">
                      </span>
  </div>
</div>  


<div class="view-all" id="older-posts-div">
      <a href="/tools/thinkup/?v=friends-leastactive&u=_rcp&n=twitter&page=2" id="next_page">&#60; Older</a>
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