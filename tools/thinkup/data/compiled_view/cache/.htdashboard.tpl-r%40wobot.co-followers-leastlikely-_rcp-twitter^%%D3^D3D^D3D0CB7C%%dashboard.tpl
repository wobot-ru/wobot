513
a:5:{s:8:"template";a:7:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:9:"_user.tpl";b:1;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";b:1;s:11:"_footer.tpl";b:1;}s:11:"insert_tags";a:1:{s:9:"help_link";a:5:{i:0;s:6:"insert";i:1;s:9:"help_link";i:2;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";i:3;i:3;i:4;b:0;}}s:9:"timestamp";i:1333121131;s:7:"expires";i:1333121731;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
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
                                                   <li class="selected">
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
  f8d698aea36fcbead2b9d5359ffca76f{insert_cache a:2:{s:4:"name";s:9:"help_link";s:2:"id";s:21:"followers-leastlikely";}}f8d698aea36fcbead2b9d5359ffca76f
  <h2><a href="?v=followers&u=_rcp&n=twitter">Followers</a> &rarr; Discerning</h2>
  <h3>Followers with the greatest follower-to-friend ratio</h3></div>

    <div class="header">
            </div>










 





      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=145192600" title="ikosmetika on Twitter">      <img src="http://a0.twimg.com/profile_images/1217239367/26637431_normal.jpeg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=145192600" title="ikosmetika on Twitter">    ikosmetika
    </a>    <div class="small gray">
            10,033 followers, 1,516 friends<br>
            <a href="https://twitter.com/intent/user?user_id=145192600" title="ikosmetika on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>О косметике, красоте и макияже. Make-up, beauty, cosmetics.</p>    <span class="small gray">
            1 posts per day over the past 2 years                       <br>7x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=179931035" title="suite48media on Twitter">      <img src="http://a0.twimg.com/profile_images/1552312680/logo-tiny_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=179931035" title="suite48media on Twitter">    suite48media
    </a>    <div class="small gray">
            75,934 followers, 32,795 friends<br>
            <a href="https://twitter.com/intent/user?user_id=179931035" title="suite48media on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Suite48 Media is a boutique creative web agency which provides individuals and businesses worldwide with online services.</p>    <span class="small gray">
      Toronto, Canada      0 posts per day over the past 2 years                       <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=198154276" title="mikeyduturbure on Twitter">      <img src="http://a0.twimg.com/profile_images/1813324156/Michael_Duturbure_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=198154276" title="mikeyduturbure on Twitter">    mikeyduturbure
    </a>    <div class="small gray">
            29,214 followers, 13,293 friends<br>
            <a href="https://twitter.com/intent/user?user_id=198154276" title="mikeyduturbure on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>I am a fibromyalgia sufferer striving to climb Machu Picchu by end of 2012. I am a fundraiser. Come join me and help out. Visit my blog thanks.</p>    <span class="small gray">
      Sydney, Australia      2 posts per day over the past 1 year                       <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=75122622" title="MyBinding on Twitter">      <img src="http://a0.twimg.com/profile_images/420949502/mybinding.mark_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=75122622" title="MyBinding on Twitter">    MyBinding
    </a>    <div class="small gray">
            61,770 followers, 28,567 friends<br>
            <a href="https://twitter.com/intent/user?user_id=75122622" title="MyBinding on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>MyBinding.com specializes in binding equipment, laminators, binding supplies, report covers and laminating supplies.  1-800-944-4573</p>    <span class="small gray">
      Hillsboro, OR      17 posts per day over the past 3 years                       <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=11633492" title="wili on Twitter">      <img src="http://a0.twimg.com/profile_images/1200216734/Ville_Miettinen_square_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=11633492" title="wili on Twitter">    wili
    </a>    <div class="small gray">
            32,667 followers, 17,358 friends<br>
            <a href="https://twitter.com/intent/user?user_id=11633492" title="wili on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Founder & CEO of @Microtask. Hacker, author, serial entrepreneur, investor, blogger, photographer, world traveller, @HackFwd referrer.</p>    <span class="small gray">
      60.317643,24.964914      1 posts per day over the past 4 years                       <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=190551728" title="harshavee on Twitter">      <img src="http://a0.twimg.com/profile_images/1932386112/5546_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=190551728" title="harshavee on Twitter">    harshavee
    </a>    <div class="small gray">
            19,020 followers, 10,894 friends<br>
            <a href="https://twitter.com/intent/user?user_id=190551728" title="harshavee on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Im 15yrs old guy !!! A arts student hoping that ill have a bright future ! A lovable & Close frnd in this world @ MyDad !!:) Welcome to my Twitter World !</p>    <span class="small gray">
      India      66 posts per day over the past 2 years                       <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=39365101" title="4thWeb on Twitter">      <img src="http://a0.twimg.com/profile_images/1653072276/4thWeb_logo_03_180x180_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=39365101" title="4thWeb on Twitter">    4thWeb
    </a>    <div class="small gray">
            89,787 followers, 51,513 friends<br>
            <a href="https://twitter.com/intent/user?user_id=39365101" title="4thWeb on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Multidimensional Marketing, Online Presence Management, Viral Seeding</p>    <span class="small gray">
            3 posts per day over the past 3 years                       <br>2x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=27087844" title="mandystadt on Twitter">      <img src="http://a0.twimg.com/profile_images/1789071655/profile_normal.JPG" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=27087844" title="mandystadt on Twitter">    mandystadt
    </a>    <div class="small gray">
            68,703 followers, 48,110 friends<br>
            <a href="https://twitter.com/intent/user?user_id=27087844" title="mandystadt on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Life shrinks or expands in proportion to one's courage. - Anais Nin</p>    <span class="small gray">
      New York City      6 posts per day over the past 3 years                       <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=409407347" title="New_Galleon on Twitter">      <img src="http://a0.twimg.com/profile_images/1632332543/5885635722-tipok_ru_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=409407347" title="New_Galleon on Twitter">    New_Galleon
    </a>    <div class="small gray">
            11,880 followers, 9,021 friends<br>
            <a href="https://twitter.com/intent/user?user_id=409407347" title="New_Galleon on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Море , оно и в Африке море
 #rufollowback #sledui #ru_ff #ff_ru</p>    <span class="small gray">
      Дебальцево      10 posts per day over the past 5 months                       <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=8071902" title="zaibatsu on Twitter">      <img src="http://a0.twimg.com/profile_images/1765976458/1111_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=8071902" title="zaibatsu on Twitter">    zaibatsu
    </a>    <div class="small gray">
            167,337 followers, 128,840 friends<br>
            <a href="https://twitter.com/intent/user?user_id=8071902" title="zaibatsu on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Just a guy who loves Social Media, Tech, Photos and Humor. Want to know what our company does:  The 1st Rule of Fightclub: you don't talk about Fightclub</p>    <span class="small gray">
      Denver      40 posts per day over the past 5 years                       <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=5417132" title="Andrew303 on Twitter">      <img src="http://a0.twimg.com/profile_images/1184029606/twitter_303_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=5417132" title="Andrew303 on Twitter">    Andrew303
    </a>    <div class="small gray">
            112,821 followers, 88,175 friends<br>
            <a href="https://twitter.com/intent/user?user_id=5417132" title="Andrew303 on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Former Creative Director at NewsCorp & Razorfish, now author of 'Rethinking Digital Media' and founder of Future Content Lab. </p>    <span class="small gray">
      London      1 posts per day over the past 5 years                       <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=350624714" title="BrenKelly1 on Twitter">      <img src="http://a0.twimg.com/profile_images/1747651110/30500_429398718763_591098763_5584154_2448831_n_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=350624714" title="BrenKelly1 on Twitter">    BrenKelly1
    </a>    <div class="small gray">
            22,072 followers, 17,874 friends<br>
            <a href="https://twitter.com/intent/user?user_id=350624714" title="BrenKelly1 on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Visionary Leader & Entrepreneur✦Ambassador of Potentiality✦Trailblazer✦Explorer of the Outer Edges of Experience & Thought✦Founder of iZignite</p>    <span class="small gray">
      Ireland      0 posts per day over the past 8 months                       <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=28428838" title="Sheri_ls on Twitter">      <img src="http://a0.twimg.com/profile_images/1161467780/nygala1_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=28428838" title="Sheri_ls on Twitter">    Sheri_ls
    </a>    <div class="small gray">
            179,168 followers, 146,949 friends<br>
            <a href="https://twitter.com/intent/user?user_id=28428838" title="Sheri_ls on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Retired Ballet dancer,  Choreographer, p/t ballet/ballroom instructor. Passionate animal lover, interested in psychology,inspiration, fashion.</p>    <span class="small gray">
      CA(San Francisco) & Louisiana      11 posts per day over the past 3 years                       <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=106368459" title="KevinKennethLau on Twitter">      <img src="http://a0.twimg.com/profile_images/1777443169/_9727684338_normal.jpg" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=106368459" title="KevinKennethLau on Twitter">    KevinKennethLau
    </a>    <div class="small gray">
            18,181 followers, 15,035 friends<br>
            <a href="https://twitter.com/intent/user?user_id=106368459" title="KevinKennethLau on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Community Manager for Google TV | (http://bit.ly/KevinKLauEbook) | Your Network Marketing Business Coach and Social Media Consultant. </p>    <span class="small gray">
      Bay Area      6 posts per day over the past 2 years                       <br>1x more followers than friends
          </span>
  </div>
</div>      <div class="individual-tweet prepend_20 clearfix article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <a href="https://twitter.com/intent/user?user_id=374695502" title="twiends on Twitter">      <img src="http://a0.twimg.com/profile_images/1545831762/twittericon_normal.png" class="avatar"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon"/>
      </a>    </div>
  </div>
  <div class="grid_4 small">
    <a href="https://twitter.com/intent/user?user_id=374695502" title="twiends on Twitter">    twiends
    </a>    <div class="small gray">
            60,306 followers, 51,144 friends<br>
            <a href="https://twitter.com/intent/user?user_id=374695502" title="twiends on Twitter"><span class="sprite ui-icon-person"></span></a>    </div>
  </div>
  <div class="grid_12 omega">
    <p>Twiends is a Twitter directory that can help you grow your twitter following safely and responsibly. </p>    <span class="small gray">
            0 posts per day over the past 6 months                       <br>1x more followers than friends
          </span>
  </div>
</div>  


<div class="view-all" id="older-posts-div">
      <a href="/tools/thinkup/?v=followers-leastlikely&u=_rcp&n=twitter&page=2" id="next_page">&#60; Older</a>
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