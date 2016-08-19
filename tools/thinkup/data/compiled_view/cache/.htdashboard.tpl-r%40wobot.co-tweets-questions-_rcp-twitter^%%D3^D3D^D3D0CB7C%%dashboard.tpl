531
a:5:{s:8:"template";a:7:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:26:"_post.counts_no_author.tpl";b:1;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";b:1;s:11:"_footer.tpl";b:1;}s:11:"insert_tags";a:1:{s:9:"help_link";a:5:{i:0;s:6:"insert";i:1;s:9:"help_link";i:2;s:67:"/var/www/tools/thinkup/plugins/twitter/view/twitter.inline.view.tpl";i:3;i:3;i:4;b:0;}}s:9:"timestamp";i:1332926485;s:7:"expires";i:1332927085;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
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
                <a href="/tools/thinkup/crawler/updatenow.php" class="linkbutton">Update now</a>  </div> <!-- .status-bar-left -->
  
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
                    Updated 17 hours  ago                  </div>
                </div>
              </div>
            </div>
          
                      <div class="section">
<div class="clearfix">
  f8d698aea36fcbead2b9d5359ffca76f{insert_cache a:2:{s:4:"name";s:9:"help_link";s:2:"id";s:16:"tweets-questions";}}f8d698aea36fcbead2b9d5359ffca76f
  <h2><a href="?v=tweets&u=_rcp&n=twitter">Tweets</a> &rarr; Inquiries</h2>
  <h3>Inquiries, or tweets with a question mark in them</h3></div>

    <div class="header">
            </div>


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
</div>      

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Откуда они взяли такую урну??? http://t.co/WL3wZ8xU
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=176228964304568322&n=twitter">3 weeks  ago</a>
                from Moscow
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
        <a href="/tools/thinkup/post/?t=144178827499806720&n=twitter">4 months  ago</a>
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
</div>      

<div class="clearfix article">
  <div class="individual-tweet post clearfix">
    <div class="grid_13 alpha">
      <div class="post">
                                        Buffer and Facebook: Great marriage in a making? | Ask Aaron Lee http://t.co/S3Zeom7Y via <a href="https://twitter.com/intent/user?screen_name=AskAaronLee">@AskAaronLee</a>
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=134694441964404736&n=twitter">5 months  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=134694441964404736"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=134694441964404736"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=134694441964404736"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        RT <a href="https://twitter.com/intent/user?screen_name=devakaru">@devakaru</a>: RT <a href="https://twitter.com/intent/user?screen_name=MaximSpiridonov">@MaximSpiridonov</a> Испытание сбывшимися мечтами: должны ли мотивировать предпринимателя деньги? http://bit.ly/dxsgw6
                              
                            <br>                         <span class="small"><a href="http://spiridonov.ru/post/4178" title="http://bit.ly/dxsgw6">http://spiridonov.ru/post/4178</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=5047903240323072&n=twitter">1 year  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=5047903240323072"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=5047903240323072"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=5047903240323072"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        сегодня google developer day 2010, кто-нибудь еще участвует? #gddru
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=2860690679271424&n=twitter">1 year  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=2860690679271424"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=2860690679271424"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=2860690679271424"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Wisenut, the Google Killer? Nah... - Search Engine Watch (SEW) http://t.co/NUx03TR via <a href="https://twitter.com/intent/user?screen_name=sewatch">@sewatch</a>
                              
                            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=2705136581353472&n=twitter">1 year  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=2705136581353472"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=2705136581353472"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=2705136581353472"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        почему в Москве живут одни лодыри и лентяи? http://bit.ly/c3bJd6
                              
                            <br>                         <span class="small"><a href="http://www.the-village.ru/village/situation/columns/103051-strana-lentyaev" title="http://bit.ly/c3bJd6">http://www.the-village.ru/village/situation/columns/103051-strana-lentyaev</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=21764878697&n=twitter">2 years  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=21764878697"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=21764878697"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=21764878697"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Кто-нибудь слышал про Google Font API? http://code.google.com/intl/ru/apis/webfonts/
                              
                            <br>                         <span class="small"><a href="https://developers.google.com/webfonts/?hl=ru" title="http://code.google.com/intl/ru/apis/webfonts/">https://developers.google.com/webfonts/?hl=ru</a>
            </span>
                      <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=14452580668&n=twitter">2 years  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=14452580668"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=14452580668"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=14452580668"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        #studen а на яндекс фотках уже есть яндекс фотки. Где будет конкурс с флешками?
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=11416800328&n=twitter">2 years  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=11416800328"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=11416800328"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=11416800328"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Позитиву) RT <a href="https://twitter.com/intent/user?screen_name=dze">@dze</a> Если бы в ВКонтакте должна была бы появиться новая группа-миллионник, то чему бы она была посвящена?
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=10725534333&n=twitter">2 years  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=10725534333"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=10725534333"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=10725534333"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
                                        Дочка: - Мам! Акуда тампоны вставляют?&#10;Мама: - Ну... как тебе сказать... вобщем туда, откуда берутся дети.&#10;Дочка: - В аиста, что ли?
                              
            <br clear="all">

        
      <div class="small gray">
        <span class="metaroll">
        <a href="/tools/thinkup/post/?t=7012623072&n=twitter">2 years  ago</a>
                from Moscow
                       <a href="http://twitter.com/intent/tweet?in_reply_to=7012623072"><span class="ui-icon ui-icon-arrowreturnthick-1-w" title="reply"></span></a>
        <a href="http://twitter.com/intent/retweet?tweet_id=7012623072"><span class="ui-icon ui-icon-arrowreturnthick-1-e" title="retweet"></span></a>
        <a href="http://twitter.com/intent/favorite?tweet_id=7012623072"><span class="ui-icon ui-icon-star" title="favorite"></span></a>
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
      <a href="/tools/thinkup/?v=tweets-questions&u=_rcp&n=twitter&page=2" id="next_page">&#60; Older</a>
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