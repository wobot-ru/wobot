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
     if (hour<10) hour ='0'+hour;
     var min = a.getMinutes();
     if (min<10) min ='0'+min;
     var sec = a.getSeconds();
     if (sec<10) sec ='0'+sec;
     var time = date+','+month+' '+year+' '+hour+':'+min+':'+sec ;
     return time;
 }

                        
                        
            var n1=0;
            var n2=0;
            var interval=1000;
            var twitter = document.getElementById("twitter_content");
            var twitterpost;
            var numtw=0;
            var numfb=0;
            
            function MakeRequest()
            {
                  $.get("http://bmstu.wobot.ru/project/getriw.php", {}, function(returned_data) { HandleResponse(returned_data);});
                  //$.get("getmsg.php", {}, function(returned_data) { HandleResponse(returned_data);});
                  //interval+=500;
            }
            
            
            function HandleResponse(response)
            {
                //alert(response);
                if (response!='null')
                    {
                        var res = eval('(' + response + ')');
                        //alert(res);
                        for (i=0;i<res.length;i++)
                         {
                            if(res[i].post_source=='interval')
                                interval=res[i].post_avatar;
                                else
                                    {
                                        
                                    
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
                            switch(res[i].post_source)
                            {
                               
                            case 'twitter1':
                                    n1++;
                                    $("#twitter_content").prepend('<div class="line"></div>');
                                    $("#twitter_content").prepend(twitterpost);
                                    //h=$("#twitter_content div.post:first-child").attr('height');
                                    $("#twitter_content div.post:first-child").css('height',130);
                                    $("#twitter_content div.post:first-child").slideDown(1000);
                                    numtw++;
                                    if(numtw>10)
                                        {
                                            $("#twitter_content div.post:last-child").remove();
                                                                                numtw=11;
                                        }
                                        break;
                            case 'twitter2':
                                    n2++;
                                    $("#fb_content").prepend('<div class="line"></div>');
                                    $("#fb_content").prepend(twitterpost);
                                    //h=$("#twitter_content div.post:first-child").attr('height');
                                    $("#fb_content div.post:first-child").css('height',130);
                                    $("#fb_content div.post:first-child").slideDown(1000);
                                    numfb++;
                                    if(numfb>10)
                                        {
                                            $("#fb_content div.post:last-child").remove();
                                                                                numfb=11;
                                        }
                                break;
                            }
                            }
                         }
                    }
                    //alert(interval);
               setTimeout(
                function()
                {
                    MakeRequest();
                }, interval);
                
            }
            
            
            setTimeout(
            function()
            {
                MakeRequest();
            }, interval);

        
        
        
