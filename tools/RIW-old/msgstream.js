/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function timeConverter(UNIX_timestamp){
 var a = new Date(UNIX_timestamp*1000);
 var months = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
     var year = a.getFullYear();
     var month = months[a.getMonth()];
     var date = a.getDate();
     var hour = a.getHours();
     var min = a.getMinutes();
     var sec = a.getSeconds();
     var time = date+','+month+' '+year+' '+hour+':'+min+':'+sec ;
     return time;
 }

                        
                        
            var n=0;
            var twitter = document.getElementById("twitter_content");
            var twitterpost;
            var numtw=0;
            var numfb=0;
            
//            function displayTweet(limit){
//            var i = 0;
//            var myInterval = window.setInterval(function () {
//            var element = $("#twitter-results div:first-child");
//            //var element = twitterpost;
//            $("#twitter-results").prepend(element);
//            element.fadeIn(1000);
//            i++;
//            if(i==limit){
//            window.setTimeout(function () {
//            clearInterval(myInterval);
//            });
//            }
//            },2000);}
            
            function getXMLHttp()
            {
              var xmlHttp

              try
              {
                //Firefox, Opera 8.0+, Safari
                xmlHttp = new XMLHttpRequest();
              }
              catch(e)
              {
                //Internet Explorer
                try
                {
                  xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                }
                catch(e)
                {
                  try
                  {
                    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                  }
                  catch(e)
                  {
                    alert("Your browser does not support AJAX!")
                    return false;
                  }
                }
              }
              return xmlHttp;
            }
            
            function MakeRequest()
            {
                  //var xmlHttp = getXMLHttp();
                  //alert("open");
                  //xmlHttp.open("GET", "http://bmstu.wobot.ru/project/getriw.php", true); 
                  //alert('234');
                  $.get("http://bmstu.wobot.ru/project/getriw.php", {}, function(returned_data) { HandleResponse(returned_data);});
                  //xmlHttp.send(null);
                  //alert("sent");
//                  xmlHttp.onreadystatechange = function()
//                  {
//                    if(xmlHttp.readyState == 4)
//                    {
//                      HandleResponse(xmlHttp.responseText);
//                    }
//                  }
                  
            }
            
            
            function HandleResponse(response)
            {
                //alert(response);
                var res = eval('(' + response + ')');
                //alert(res);
                //alert(res);
                for (i=0;i<res.length;i++)
                 {
                    n++;
                    twitterpost = '';
                    twitterpost += '<div class="post">';
                    twitterpost += '<img class="left avatar" src="'+res[i].post_avatar+'"/>';
                    twitterpost += ' <div class="left text_post">';
                    twitterpost += '<h2>';
                    twitterpost += res[i].post_nick;
                    twitterpost += '</h2>';
                    twitterpost += '<p>';
                    twitterpost += res[i].post_msg;
                    twitterpost += '</p>';
                    twitterpost += '<p class="comment">';
                    twitterpost += timeConverter(res[i].post_date);
                    twitterpost += '</p>';
                    twitterpost += '</div>';
                    twitterpost += '</div>';
                    if (res[i].post_source=="twitter.com")
                        {
                            $("#twitter_content").prepend('<div class="line"></div>');
                            $("#twitter_content").prepend(twitterpost);
                            //h=$("#twitter_content div.post:first-child").attr('height');
                            //$("#twitter_content div.post:first-child").css('height',h+30);
                            $("#twitter_content div.post:first-child").slideDown(1000);
                            numtw++;
                            if(numtw>10)
                                {
                                    $("#twitter_content div.post:last-child").remove();
									numtw=11;
                                }
                        }
                    else
                        {
                            $("#fb_content").prepend('<div class="line"></div>');
                            $("#fb_content").prepend(twitterpost);
                            h=$("#fb_content div.post:first-child").height();
                            $("#fb_content div.post:first-child").css('height',h+30);
                            $("#fb_content div.post:first-child").slideDown(1000);
                            numfb++;
                            if(numfb>10)
                                {
                                    $("#fb_content div.post:last-child").remove();
									numfb=11;
                                }
                        }
                 }
            }
            
            
            setInterval(
            function()
            {
                MakeRequest();
            }, 5000);

        
        
        
