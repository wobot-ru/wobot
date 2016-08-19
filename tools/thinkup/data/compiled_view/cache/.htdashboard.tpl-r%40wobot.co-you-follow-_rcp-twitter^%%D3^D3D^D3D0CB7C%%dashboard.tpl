309
a:4:{s:8:"template";a:6:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:62:"/var/www/tools/thinkup/plugins/twitter/view/who_you_follow.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1335787618;s:7:"expires";i:1335788218;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
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
                    Updated 17 mins  ago                  </div>
                </div>
              </div>
            </div>
          
                          <div class="section">
    <h2>Chatterboxes</h2>
    <div class="article" style="padding-left : 0px; padding-top : 0px;">
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=224385752" title="OLBANIA"><img src="http://a0.twimg.com/profile_images/1185869897/653464545_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=303215167" title="Ispetc"><img src="http://a0.twimg.com/profile_images/1364532597/a_6ad7ffc2_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=29414182" title="korobkov"><img src="http://a0.twimg.com/profile_images/1940369856/korobkov_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=356684063" title="sry_ayu_lestari"><img src="http://a0.twimg.com/profile_images/1976362617/sry_ayu_lestari_1464995020179177224_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=176502559" title="_marsi"><img src="http://a0.twimg.com/profile_images/1215448352/___normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=446679929" title="FourSevenJay"><img src="http://a0.twimg.com/profile_images/1959721176/n6KR7U1M_normal" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=14720411" title="mikepfs"><img src="http://a0.twimg.com/profile_images/1734712455/image_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=231184385" title="collegefelton"><img src="http://a0.twimg.com/profile_images/1807971151/photo__1__normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=300992118" title="YoungChlorine"><img src="http://a0.twimg.com/profile_images/1962382167/money_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=339081060" title="Narodniy_Front"><img src="http://a0.twimg.com/profile_images/1453323791/______-_______normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=204099617" title="vlenxx"><img src="http://a0.twimg.com/profile_images/1874837218/image_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=299195782" title="nano_rus"><img src="http://a0.twimg.com/profile_images/1354817157/i_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=190551728" title="harshavee"><img src="http://a0.twimg.com/profile_images/1932386112/5546_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
        <br /><br /><br />
    </div>
    <div class="view-all"><a href="?v=friends-mostactive&u=_rcp&n=twitter">More...</a></div>
    </div>

    <div class="section">
        <h2>Quietest</h2>
        <div class="article" style="padding-left : 0px; padding-top : 0px;">
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=377300045" title="belowu"><img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_3_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=234320993" title="deggustator"><img src="http://a0.twimg.com/profile_images/1597490149/image_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=152344821" title="dj_aristocrat"><img src="http://a0.twimg.com/profile_images/962561197/x_901aa7db_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=59973767" title="JasmineCurry"><img src="http://a0.twimg.com/profile_images/371061647/m_70a5f1ab2885a325b9b3fab5a9b4b9f9_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=215265937" title="Kookonya"><img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_5_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=384812360" title="LexaSab"><img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_2_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=133844306" title="Mr__Po"><img src="http://a0.twimg.com/profile_images/828555071/____2_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=243677546" title="pi4en"><img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_0_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=328559550" title="prostofert"><img src="http://a0.twimg.com/profile_images/1664258396/image_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=391322608" title="RUAlexFisher"><img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_5_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=268385256" title="Zapretnaya"><img src="http://a0.twimg.com/sticky/default_profile_images/default_profile_6_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=268647453" title="frontrowmusic"><img src="http://a0.twimg.com/profile_images/1526976049/frontrowfinal_normal.PNG" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=230820311" title="ru_fastsecond"><img src="http://a0.twimg.com/profile_images/1199395035/1293393277_business-contact_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                <br /><br /><br />
        </div>
        <div class="view-all"><a href="?v=friends-leastactive&u=_rcp&n=twitter">More...</a></div>
    </div>

    <div class="section">
        <h2>Popular</h2>
        <div class="article" style="padding-left : 0px; padding-top : 0px;">
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=27260086" title="justinbieber"><img src="http://a0.twimg.com/profile_images/1927291188/newjbpic_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=50393960" title="BillGates"><img src="http://a0.twimg.com/profile_images/1884069342/BGtwitter_normal.JPG" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=17093617" title="hootsuite"><img src="http://a0.twimg.com/profile_images/541333937/hootsuite-icon_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=11348282" title="NASA"><img src="http://a0.twimg.com/profile_images/188302352/nasalogo_twitter_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=8161232" title="richardbranson"><img src="http://a0.twimg.com/profile_images/1752306612/DSC_0993_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=2384071" title="timoreilly"><img src="http://a0.twimg.com/profile_images/1777004587/tim-oreilly-apr2010-200_straightened_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=153812887" title="MedvedevRussia"><img src="http://a0.twimg.com/profile_images/1178224832/user_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=264498030" title="paulwesley"><img src="http://a0.twimg.com/profile_images/1743871643/L1020003_normal.JPG" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=18498684" title="wefollow"><img src="http://a0.twimg.com/profile_images/481883810/wf-icon_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=15234407" title="CERN"><img src="http://a0.twimg.com/profile_images/117374181/twitter_cern_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=17850012" title="Radioblogger"><img src="http://a0.twimg.com/profile_images/1616831345/behind-the-curtain_normal.gif" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=93957809" title="ericschmidt"><img src="http://a0.twimg.com/profile_images/565244113/edited_twit_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                  <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=65442728" title="tina_kandelaki"><img src="http://a0.twimg.com/profile_images/1850791642/avatar_11_1_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
                <br /><br /><br />
        </div>
        <div class="view-all"><a href="?v=friends-mostfollowed&u=_rcp&n=twitter">More...</a></div>
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