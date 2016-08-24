/* 
 по идее нужно при изменении тегов менять все надписи к постам, где он есть в назначенных тегах, то же при удалении
также при открытии попапа назначенные теги нужно изначально зачекать те теги, которые назначены
 */

var tags=Array('тег1','тег2','тег3','тег4','tag5');

var words=Array('слово1','слово2','слово3','слово4','слово5');

var cities = {"не определено":null,"Россия":{"Центр":{"Московская обл-ть":{"Москова":null,"Королёв":null,"Люберцы":null,"Одинцово":null},"Тверская обл-ть":null,"Владимирская обл-ть":null,"Санкт-Петербург":null,"Кострома":null},"Север":{"Московская обл-ть":null,"Тверская обл-ть":null,"Владимирская обл-ть":null,"Восток":null,"Запад":null},"Юг":null,"Восток":null,"Запад":null},
    "Белоруссия":{"Центр":null,"Север":null,"Юг":null,"Восток":null,"Запад":null}};

var resources = {"не определено":null,"Россия":{"Центр":{"Московская обл-ть":{"Москова":null,"Королёв":null,"Люберцы":null,"Одинцово":null},"Тверская обл-ть":null,"Владимирская обл-ть":null,"Санкт-Петербург":null,"Кострома":null},"Север":{"Московская обл-ть":null,"Тверская обл-ть":null,"Владимирская обл-ть":null,"Восток":null,"Запад":null},"Юг":null,"Восток":null,"Запад":null},
    "Белоруссия":{"Центр":null,"Север":null,"Юг":null,"Восток":null,"Запад":null}};

var n;
$(document).ready(function(){
    
   
   //заполнение спикеров
   var text;
   var _id;
   for(var i=0;i<speakers.length;i++)
        {
            speakers[i].val=true;
            $('#popup_speakers_list').append(makeCheckboxSort(speakers[i],'speakers',i));
        }
   //заполнение промоутеров
   for(var i=0;i<promouters.length;i++)
        {
            promouters[i].val=true;
            $('#popup_promouters_list').append(makeCheckboxSort(promouters[i],'promouters',i));
        }
       
   //показ начальных значений фильтров     
   //заполнение дерева городов
    $('#cities_tree').html(fillTree('cities',cities,true));
    $('#cities_full').find('.choose >div').last().html(formChoose('cities'));
    $('#cities_wrapped').find('p').html(wrappedFilterTreeFill('cities'));
    
    //заполнение дерева ресурсов
    $('#res_tree').html(fillTree('res',resources,true));
    $('#res_full').find('.choose >div').last().html(formChoose('res'));
    $('#res_wrapped').find('p').html(wrappedFilterTreeFill('res'));
    
    //время
    HideDateFilters();
    $('#time_wrapped').find('span').first().html(time_beg);
    $('#time_wrapped').find('span').last().html(time_end);
    
    //теги
    var tagn=0;
    var element=$('#tags_list');
    var tag_text='без тегов';
    for(var _t in tags)
        {
            tagn++;
            element.append('<div class="clear"><label for="tag'+tagn+'">'+tags[_t]+'</label><input id="tag'+tagn+'" type="checkbox" name="tags" value="1" class="styled"  checked="true"/></div>');
            tag_text+=', '+tags[_t];
    }
    $('#tag_wrapped').find('p').html(tag_text);
    
    //инициализация дерева чекбоксов (города и ресурсы)
    $('.popuptree').checkboxTree({
        initializeChecked: 'collapsed',
        initializeUnchecked: 'collapsed',
        onCheck: {
                ancestors: 'check',
                descendants: 'check',
                node: 'expand'
            },
        onUncheck: {
                ancestors: 'uncheckIfFull',
                descendants: 'uncheck'
            }
    });
    
    //слова
    var element=$('#words_list');
    tagn=0;
    for(var _t in words)
        {
            
            element.append('<div class="clear"><label for="word'+tagn+'">'+words[_t]+'</label><input id="word'+tagn+'" type="checkbox" name="words" value="'+tagn+'" class="styled"  checked="true"/></div>');
            tagn++;
    }
    
    //развернуть фиьтр, на который кликнули
    $('.wrappedfilter').click(function(){
          HideFilters();
          $(this).slideUp(500,function(){
              var name=this.id.substr(0, this.id.length-7);
            $('#'+name+'full').slideDown(500);
            
            if(name=='speakers_' || name=='promouters_')
                {
                    var element=$('#'+name+'popup');
                    if (element.hasClass('doshow'))
                        element.fadeIn(500);
                }
        });
    });
        
    $('#content').click(HideFilters);
    
    $('#header').click(HideFilters);
    
    //
    $('input[name="words_rb"]').click(function(){
        doShowWords($(this).attr('value'));
        });
    $('input[name="promouters"]').click(function(){
        doShowSpPr($(this).attr('value'),$(this).attr('name'));
        });
    $('input[name="speakers"]').click(function(){
        doShowSpPr($(this).attr('value'),$(this).attr('name'));
        });
    $('input[name="time"]').click(function(){
        doShowTime($(this).attr('value'));
        });
    
    //посты - клик на иконку избранное
    $('.selectedicon').click(function(){
        ImgToggle($(this));
        });
        
    
    $('.tonalicon').click(function(){
        var regV = /_bw\./;     // шаблон 
        var result = $(this).attr("src").match(regV);  // поиск шаблона в юрл
        if (result)
        {
            //если были цветные, делаем чб
            var element=$(this).closest(".tonality").find('.bright');
            if (element.length)
                {
                    ImgToggle(element);
                }
                
        }
        ImgToggle($(this));
        });
    
    //popup speakers/promouters
    $('select.sort').change(function(){
        doChbSort($(this).attr('value'),$(this).attr('name'));
        });
    
    //popup cities/resourses
     $('#cities_full').find('.smallbtn').click(function(){
         $('#cities_popup').fadeIn(500);
         $('#cities_full').find('.choose').slideUp(500);
     });
     
     $('#res_full').find('.smallbtn').click(function(){
         $('#res_popup').fadeIn(500);
         $('#res_full').find('.choose').slideUp(500);
     });
     
     $('.treeselectall').click(function(){
        var element=$(this).closest('.popupfilter').find('.popuptree').attr('id');
        $('#'+element).checkboxTree('checkAll');
        });
        
     $('.treedeselectall').click(function(){
        var element=$(this).closest('.popupfilter').find('.popuptree').attr('id');
        $('#'+element).checkboxTree('uncheckAll');
        });
        
     $('.treeexpandall').click(function(){
        var element=$(this).closest('.popupfilter').find('.popuptree').attr('id');
        $('#'+element).checkboxTree('expandAll');
        });
        
     $('.treecollapseall').click(function(){
        var element=$(this).closest('.popupfilter').find('.popuptree').attr('id');
        $('#'+element).checkboxTree('collapseAll');
        });
        
//    $('#speakers_full').find('.smallbtn').click(function(){
//        if ($(this).hasClass("enabled"))
//            {
//                //$('#speakers_popup').css('margin-top',-($('#speakers_full').height()+18));
//                $('#speakers_popup').fadeIn(500);
//                
//            }
//        });
        
   
 //popup слова
   $('#add_word_btn').click(function(){
        ClearWordsInput($('#words_popup'));
        $('#words_popup').fadeIn(500);
        });
   $('.popupfilter').find('.cancel').click(function(){
        $(this).closest('.popupfilter').fadeOut(500);
        });
    $('#words_popup').find('.add').click(function(){
        var newword=$('#words_popup').find('input').val();
        if (newword!='')
            {
                if (("#"+words.join("#,#") + "#").search('#'+newword+'#') == -1)
                    {
                        $('#words_popup').fadeOut(500);
                        var n=words.length;
                        words[words.length]=newword;
                        $('#words_list').append('<div class="clear"><label for="word'+n+'">'+newword+'</label><input id="word'+n+'" type="checkbox" name="words" value="'+n+'" class="styled"  checked="true"/></div>');
                        initcb('#words_list input:last');
                    }
                else
                    {
                        alert('Добавляемое слово уже есть');
                    }
            }
            else
                alert('Вы ввели пустое слово, оно не может быть добавлено');
        });
        
  //popup добавить тег
   $('#addtag_btn').click(function(){
        ClearWordsInput($('#addtags_popup'));
        if ($('.tag_popup:visible').length>0)
            {
                $('.tag_popup:visible').fadeOut(100, function(){
                    $('#addtags_popup').fadeIn(500);
                });
            }
            else
                $('#addtags_popup').fadeIn(500);
        });
    $('#addtags_popup').find('.add').click(function(){
        var newword=$('#addtags_popup').find('input').val();
        if (newword!='')
            {
                $('#addtags_popup').fadeOut(500);
                var n=tags.length+1;
                tags[tags.length]=newword;
                $('#tags_list').append('<div class="clear"><label for="tag'+n+'">'+newword+'</label><input id="tag'+n+'" type="checkbox" name="words" value="'+n+'" class="styled"  checked="true"/></div>');
                initcb('#tags_list input:last');
            }
            else
                alert('Вы ввели пустой тег, он не может быть добавлен');
        });
    
    //popup удалить тег
   $('#deltag_btn').click(function(){
       if (tags.length>0)
           {
               var element=$('#deltags_popup').find('.list');
               element.html('');
               for(var _t in tags)
                {
                    element.append('<div class="clear"><label for="deltag'+_t+'">'+tags[_t]+'</label><input id="deltag'+_t+'" type="checkbox" name="deltags_popup" value="1" class="styled"/></div>');
                }
                initcb('#deltags_popup input');
                if ($('.tag_popup:visible').length>0)
                    {
                        $('.tag_popup:visible').fadeOut(100, function(){
                            $('#deltags_popup').fadeIn(500);
                        });
                    }
                    else
                        $('#deltags_popup').fadeIn(500);
           }
       else alert('Тегов нет');
        
        });
   $('#deltags_popup').find('.del').click(function(){
       
       var element=$('#deltags_popup').find('.list');
       var taglist=$('#tags_list');
       var nums=new Array();
       var k=0;
       element.find('input:checked').each(function(){
                var n=$(this).attr('id').replace('deltag','');
                tags.splice(n-k,1);
                k++;
                //alert('#tag'+(n));
                taglist.find('#tag'+(++n)).closest('div').remove();
            });
       
       $('#deltags_popup').fadeOut(500);
        });
  
  //popup изменить теги 
  $('#edittag_btn').click(function(){
       if (tags.length>0)
           {
               var element=$('#edittags_popup').find('.list');
               element.html('');
               for(var _t in tags)
                {
                    element.append('<div class="clear rows-2"><input type="text" name="'+_t+'" value="'+tags[_t]+'"/></div>');
                }
                if ($('.tag_popup:visible').length>0)
                    {
                        $('.tag_popup:visible').fadeOut(100, function(){
                            $('#edittags_popup').fadeIn(500);
                        });
                    }
                    else
                        $('#edittags_popup').fadeIn(500);
           }
       else alert('Тегов нет');
        });
    $('#edittags_popup').find('.save').click(function(){
       var element=$('#edittags_popup').find('.list');
       var taglist=$('#tags_list');
       element.find('input').each(function(){
                var n=$(this).attr('name');
                if (tags[n]!=$(this).val())
                    {
                        tags[n]=$(this).val();
                        var text='label[for="tag'+(++n)+'"]';
                        taglist.find(text).html($(this).val());
                    }
                
               
            });
       
       $('#edittags_popup').fadeOut(500);
        });
        
        
   //popup для постов - назначить теги
   $('#content').find('.assigntags').click(function(){
       $('.postpopup').remove();
       var tagn=0;
       var post=$(this).closest('.post');
       ////////////////////сформировать код попапа!!!///////////////////////
       var popoup_text='<div id="assigntags_popup" class="span-6 last postpopup popupfilter hide">';
       popoup_text+='<div class="popupmain span-6 border_bottom">';
       popoup_text+='<a class="row span-6 last"><img class="right close" src="images/filters/close.png"/></a>';
       popoup_text+='<div class="list span-6 scroll"></div>';
       popoup_text+='</div>';
       popoup_text+='<div class="span-6  last popupbottom rows-2">';
       popoup_text+='<a class="dottedgrey text-grey selectall">отметить все</a>';
       popoup_text+='<a class="dottedgrey text-grey deselectall">снять все</a>';
       popoup_text+='</div>';
       popoup_text+='</div>';
       
       ////////////////////вставить код после поста!!!///////////////////////
       post.after(popoup_text);
       
       ////////////////////добавить теги в попап!!!///////////////////////
        var element=$('#assigntags_popup').find('.list');
        for(var _t in tags)
            {
                tagn++;
                element.append('<div class="clear"><label for="tagpopup'+tagn+'">'+tags[_t]+'</label><input id="tagpopup'+tagn+'" type="checkbox" name="assigntags_popup" value="1" class="styled" /></div>');
        }
//        element.append('<div class="row clear"></div>');
        $('#assigntags_popup input').click(function(){doShowTags();});
        initcb('.postpopup input');
        initClose($('.postpopup').find('.close'));
        initSelect($('#assigntags_popup').find('.selectall'));
        initDeselect($('#assigntags_popup').find('.deselectall'));
        if ($('.post').slice(-2).first().html()==post.html() || $('.post').slice(-2).last().html()==post.html())
            $('#assigntags_popup').css('margin-top',-$('#assigntags_popup').height()-25);
        $('#assigntags_popup').fadeIn(500);
        });
        
       $('.spam').click(function(){
           $('.postpopup').remove();
          var regV = /_bw\./;     // шаблон 
            var name=$(this).attr("src");
            var result = $(this).attr("src").match(regV);  // поиск шаблона в юрл
            if (result)
            {
                //добавление в спам - показываем попап
               var tagn=0;
               var post=$(this).closest('.post');

               ////////////////////сформировать код попапа!!!///////////////////////
               var popoup_text='<div id="spam_popup" class="span-6 last popupfilter hide postpopup">';
               popoup_text+='<div class="rows-2 span-6 last">'
               popoup_text+='<h3 class="popupname span-5">Добавить в спам</h3>'
               popoup_text+='<a><img class="right close" src="images/filters/close.png"/></a>';
               popoup_text+='</div>';
               popoup_text+='<div class="popupmain span-6 border_bottom border_top">';
               popoup_text+='<div class="row clear"></div>';
               popoup_text+='<div class="list span-6 scroll"></div>';
               popoup_text+='</div>';
               popoup_text+='<div class="span-6  last popupbottom rows-2">';
               popoup_text+='<div class="rows-2 vert-center prepend-2">';
               popoup_text+='<a class="span-2 smallbtn del close">В спам</a>';
               popoup_text+='<a class="span-2 smallbtn cancel close">Отменить</a>';
               popoup_text+='</div>';
               popoup_text+='</div>';
               popoup_text+='</div>';

               ////////////////////вставить код до поста!!!///////////////////////
               post.before(popoup_text);

                var element=$('#spam_popup').find('.list');
                element.append('<div class="clear"><label for="spamthis">этот пост</label><input id="spamthis" type="checkbox" name="spam_popup" value="1" class="styled" checked="true"/></div>');
                element.append('<div class="clear"><label for="spamauth">автора</label><input id="spamauth" type="checkbox" name="spam_popup" value="1" class="styled"/></div>');
                element.append('<div class="clear"><label for="spamres">ресурс</label><input id="spamres" type="checkbox" name="spam_popup" value="1" class="styled"/></div>');
                 $('#spam_popup').find('.del').click(function(){delToSpam();});
                initSmallBtns($('#spam_popup .smallbtn'));
                initcb('.postpopup input');
                initClose($('.postpopup').find('.close'));
                initSelect($('#spam_popup').find('.selectall'));
                initDeselect($('#spam_popup').find('.deselectall'));
                $('#spam_popup').css('margin-top',18*5);
                $('#spam_popup').fadeIn(500);
                
               
            }
            else{
                //убрать из спама
                ImgToggle($(this));
            }
            
       
   
       
       
   });
   
   
   
   initClose($('.popupfilter').find('.close'));
   initDel();
   initSelect($('.selectall'));
   initDeselect($('.deselectall'));
});



//свернуть все фильтры
function HideFilters()
{
    
    $(".filter_color:visible").slideUp(500,function(){
            var name=this.id.substr(0, this.id.length-4);
            if(name=='speakers_' || name=='promouters_')
                {
                    var element=$('#'+name+'popup');
                    if (element.hasClass('doshow'))
                        element.fadeOut(500);
                }
                else
            if(name=='cities_' || name=='res_')
                {
                    
                    var element=$('#'+name+'popup');
                    if (element.is(':visible'))
                        {
                            $('#'+name+'full').find('.choose >div').last().html(formChoose(name.replace('_','')));
                            initDel();
                            $('#'+name+'full').find('.choose').show();
                            element.fadeOut(500);
                        }
                        
                }
            if (name=='words_')
                {
                    if ($('#words_popup').is(':visible'))
                    {
                        $('#words_popup').fadeOut(500);
                    }
                }
            if (name=='tag_')
                {
                    if ($('.tag_popup:visible').length>0)
                    {
                        $('.tag_popup:visible').fadeOut(500);
                    }
                }
            $('#'+name+'wrapped').slideDown(500);
        });
    $(".wrappedfilter:hidden").each(showWrappedFilter);
    //$(".wrappedfilter:hidden").slideDown(1000);
}

//смена изображения цв/чб
function ImgToggle(element)
{
    var regV = /_bw\./;     // шаблон 
    var name=element.attr("src");
    var result = element.attr("src").match(regV);  // поиск шаблона в юрл
    if (result)
        {
            //чёрно-белую меняем на цветную
            element.addClass('bright');
            element.attr("src",name.replace('_bw.','.'));
        }
        else
            {
                element.removeClass('bright');
                element.attr("src",name.replace('.','_bw.'));
            }
}
            
     uncheckPrettyCb = function(caller) {
        $(caller).each(function(){
         if($(this).is(':checked')){
           $('label[for="'+$(this).attr('id')+'"]').trigger('click');
           if($.browser.msie){
            $(this).attr('checked','checked');
           }else{
            $(this).trigger('click');
           };
         };
        });
        };

        checkPrettyCb = function(caller) {
        $(caller).each(function(){
         if($(this).is(':checked')){
         }else{
           $('label[for="'+$(this).attr('id')+'"]').trigger('click');
           if($.browser.msie){
            $(this).attr('checked','');
           }else{
            $(this).trigger('click');
           };
         };
         });
        };

//смена названия цв/чб
function NameToggle(name)
{
    var regV = /_bw\.png\b/;     // шаблон 
    var result = name.match(regV);  // поиск шаблона в юрл
    if (result)
        {
            //чёрно-белую меняем на цветную
            name=name.replace('_bw.png','.png');
        }
        else
            name=name.replace('.png','_bw.png');
    return name;
}

function doShowSpPr(value,name) {
       var element=$('#'+name+'_popup');
       if (value!="all")
       {
           if(!element.is(":visible"))
               {
                   
                   element.fadeIn(500);
                   element.addClass('doshow');
               }
               
       }
       else
       {
           element.fadeOut(500);
           element.removeClass('doshow');
       }
}

//отсортировать checkbox во всплыв окне со спикерами и промоутерами
function doChbSort(value,name)
{
    var i=0;
    var mas;
    if (name=='speakers')
        mas=speakers;
    else
        mas=promouters;
    $('#popup_'+name+'_list').find('input').each(function(){
                    mas[i].val=$(this).is(':checked');
                    //alert(mas[i].val+'   '+i);
                    i++;});
    if (value=='алфавиту')
        {
            mas.sort(sortCbNames);
            //alert('alph');
        }
        
    else
        mas.sort(sortCbRaiting);
    var text='';
    //$('#popup_'+name+'_list').html('');
    for (i=0;i<mas.length;i++)
        {
            text+=makeCheckboxSort(mas[i],name,i);
            
        }
    $('#popup_'+name+'_list').html(text);
    //Custom.init();
    initcb('#popup_'+name+'_list input');
}

//function doShowSpPr(value,name) {
//       var element=$("#"+name+"_full").find('.smallbtn');
//       if (value!="all")
//       {
//               //$("#"+name+"_choose").slideDown(500);
//               var regV = /\bdisabled\b/;     // шаблон 
//               var result = element.attr('class').match(regV);  // поиск шаблона в юрл
//               if (result)
//                   {
//                       element.removeClass('disabled');
//                       element.addClass('enabled');
//                   }
//               
//       }
//       else
//       {
//           //$("#"+name+"_choose").slideUp(500);
//           element.removeClass('enabled');
//           element.addClass('disabled');
//       }
//}

function doShowWords(value) {
       if (value=="selected" || value=="except")
       {
               $("#words_choose").slideDown(500);
       }
       else
       {
           $("#words_choose").slideUp(500);
       }
}

function showWrappedFilter()
{
    var id=$(this).attr('id');
    var name=id.replace('_wrapped','');
    var text_html='';
    switch (name)
    {
        case 'time':
            $('#'+id).find('span').first().html($('#sd').val());
            $('#'+id).find('span').last().html($('#ed').val());
            break;
        case 'ton':
            $('#ton_full').find('input:checked').each(function(){
                //alert('xdvxv');
                    text_html+=$("label[for='"+$(this).attr('id')+"']").html()+', ';});
            if (text_html=='')
                text_html='не выбрана';
            else
                text_html=text_html.substr(0, text_html.length-2)
            $('#'+id).find('p').html(text_html);
            
            break;
        case 'tag':
            $('#tag_full').find('input:checked').each(function(){
                //alert('xdvxv');
                    text_html+=$("label[for='"+$(this).attr('id')+"']").html()+', ';});
            if (text_html=='')
                text_html='не выбраны';
            else
                text_html=text_html.substr(0, text_html.length-2)
            $('#'+id).find('p').html(text_html);
            break;
       case 'ref':
            $('#ref_full').find('input:checked').each(function(){
                    text_html=$("label[for='"+$(this).attr('id')+"']").html();});
            $('#'+id).find('p').html(text_html);
            break;
       case 'promouters':
       case 'speakers':
           var checked=$('#'+name+'_full').find('input:checked').attr('value');
           if (checked=='all')
               $('#'+id).find('p').html('все');
           else
               {
                   $('#'+name+'_popup').find('input:checked').each(function(){
                    text_html+=$("label[for='"+$(this).attr('id')+"']").html().split(' <span',1)+', ';});
                   if (text_html=='')
                        text_html='не выбрано';
                    else
                        text_html=text_html.substr(0, text_html.length-2);
                    if (checked=='except')
                       text_html='<span class="bold">все, кроме: </span>'+text_html;
                    $('#'+id).find('p').html(text_html);
               }
           break;
        case 'res':
        case 'cities':
           $('#'+name+'_wrapped').find('p').html(wrappedFilterTreeFill(name));
           break;
         case 'words':
           var checked=$('#'+name+'_full').find('input:checked').attr('value');
           if (checked=='all')
               $('#'+id).find('p').html('все');
           else
               {
                   $('#words_full').find('input:checked[type="checkbox"]').each(function(){
                //alert('xdvxv');
                    text_html+=$("label[for='"+$(this).attr('id')+"']").html()+', ';});
                    if (text_html=='')
                        text_html='не выбраны';
                    else
                        text_html=text_html.substr(0, text_html.length-2);
                    if (checked=='except')
                       text_html='<span class="bold">все, кроме: </span>'+text_html;
                    $('#'+id).find('p').html(text_html);
               }
           break;
    }
}


function doShowTime(value) {
    //alert(value);
        switch(value)
        {
            case 'day':
                $('#sd').val(time_end);
                $('#ed').val(time_end);
                break
            case 'week':
                var end=StrToDate(time_end);
                $('#ed').val(time_end);
                end.setDate(end.getDate()-7);
                $('#sd').val(DateToStr(end));
                break
            case 'month':
                var end=StrToDate(time_end);
                $('#ed').val(time_end);
                var end=StrToDate(time_end);
                end.setMonth(end.getMonth()-1);
                $('#sd').val(DateToStr(end));
                break
            case 'all':
                $('#sd').val(time_start);
                $('#ed').val(time_end);
                break;
            case 'different':
                break
        }
        var sd = datePickerController.getDatePicker("sd");
        var ed = datePickerController.getDatePicker("ed");
        var dt = datePickerController.dateFormat($('#sd').val(), sd.format.charAt(0) == "m");
        ed.setRangeLow( dt );
}

function makeCheckboxSort(sp,name,i)
{
    var _id=name+'_popup'+i;
    var text='<div class="clear"><label for="'+_id+'">';
    text+=sp.name;
    text+=' <span class="comment text-grey">(';
    text+=sp.num;
    text+=')</span></label>';

    text+='<input id="'+ _id+'" type="checkbox" name="'+name+'_popup" value="'+i+'" class="styled"';
    if(sp.val)
        text+=' checked="'+sp.val+'"';
    text+='/></div>';
    return text;
}

function initcb(elem)
{
    var checkboxHeight = "25";
    var radioHeight = "25";
    var inputs = $(elem), span = Array(), textnode, option, active;
    for(a = 0; a < inputs.length; a++) {
        if((inputs[a].type == "checkbox" || inputs[a].type == "radio") && inputs[a].className == "styled") {
            span[a] = document.createElement("span");
            span[a].className = inputs[a].type;

            if(inputs[a].checked == true) {
                if(inputs[a].type == "checkbox") {
                    position = "0 -" + (checkboxHeight*2) + "px";
                    span[a].style.backgroundPosition = position;
                } else {
                    position = "0 -" + (radioHeight*2) + "px";
                    span[a].style.backgroundPosition = position;
                }
            }
            inputs[a].parentNode.insertBefore(span[a], inputs[a]);
            inputs[a].onchange = Custom.clear;
            if(!inputs[a].getAttribute("disabled")) {
                span[a].onmousedown = Custom.pushed;
                span[a].onmouseup = Custom.check;
            } else {
                span[a].className = span[a].className += " disabled";
            }
        }
    }
    document.onmouseup = Custom.clear;
}


function fillTree(name,mas,val)
{
    return _fillTree(name,mas,val,0)[0];
}

function _fillTree(name,mas,val,n)
{
    var id;
    var text='';
    var res;
    for(var _node in mas)
        {
            id=name+'_chb'+n++;
            text+='<li><input type="checkbox"';
            if (val) text+=' checked="true"';
            text+='id="'+id+'"><label for="'+id+'">';
            text+=_node;
            text+='</label>';
            if (mas[_node]!=null)
                {
                    text+='<ul>';
                    res=_fillTree(name,mas[_node],val,n);
                    text+=res[0];
                    n=res[1]
                    text+='</ul>';
                }
        }
    return [text,n];
}

function formTreeString(elem)
{
    var mas= new Object() ;
    var mas1;
    elem.find('> li').each(
        function(){
            //alert('dfsfsf');
            if($(this).find('> input:checked').length!=0)
                {
                    if ($(this).find('li input:checkbox:not(:checked)').length == 0)
                {
                    mas[$(this).find('> input:checked').first().attr('id')]=$(this).find('label').html();
                    
                }
            else
                {
                    
                    if ($(this).find('> ul').length)
                        {
                            mas1=formTreeString($(this).find('ul').first());
                            //alert(mas1);
                            for(var _t in mas1)
                                {
                                    mas[_t]=mas1[_t];
                                }
                            //mas=mas.concat(mas1);
                            //alert(mas);
                        }
                }
                }
            
        }
    );
    return mas;
}

function formChoose(name)
{
    var mas=formTreeString($('#'+name+'_tree'));
    var text='';
    for (var i in mas)
        {
            text+='<p>'+mas[i]+'<label for="'+i+'"><a><img class="cross_del" src="images/filters/cross.png"/></a></label></p>';
        }
     return text;
        //alert(text);
}

function wrappedFilterTreeFill(name)
{
    var text_html='';
    $('#'+name+'_full').find('.choose >div').last().find('p').each(function(){
        text_html+=$(this).html().split('<')[0]+', '
    });
     if (text_html=='')
                text_html='не выбраны';
            else
                text_html=text_html.substr(0, text_html.length-2)
    return(text_html);
}

function initDel()
{
    $('.cross_del').click(function(){
        var node=$(this).closest('p').html().split('<')[0]
        $(this).closest('p').remove();
        /////////////удалить в дереве
        var name = $(this).closest('.filter_color').attr('id').replace('_full','');
        
        });
}

function initClose(element)
{
    element.click(function(){
        $(this).closest('.popupfilter').fadeOut(500);
        var name = $(this).closest('.popupfilter').attr('id').replace('_popup','');
        ////заполнить выбор
        if (name=='cities' || name=='res')
            {
                //alert(name);
                $('#'+name+'_full').find('.choose >div').last().html(formChoose(name));
                initDel();
                $('#'+name+'_full').find('.choose').slideDown(500);
            }
        if ($(this).closest('.popupfilter').hasClass('postpopup'))
            {
                $(this).closest('.popupfilter').remove();
            }
       
        });
}

function initSelect(element)
{
    element.click(function(){
        var element=$(this).closest('.popupfilter').attr('id');
        var name=element.substr(0, element.length-5);
        checkPrettyCb('input[name="'+name+'popup"]');
        
         if (name=='assigntags_');
         {
             doShowTags();
         }
        });
  
}

function initDeselect(element)
{
    //popup speakers/promouters
    element.click(function(){
        var element=$(this).closest('.popupfilter').attr('id');
        var name=element.substr(0, element.length-5);
        uncheckPrettyCb('input[name="'+name+'popup"]');
        if (name=='assigntags_');
         {
             doShowTags();
         }
        });
}

function ClearWordsInput(element)
{
    element.find('input').val('');
}

function doShowTags()
{
    var text_html='';
    $('#assigntags_popup').find('input:checked').each(function(){
                    text_html+=$("label[for='"+$(this).attr('id')+"']").html()+', ';});
    if (text_html=='')
        text_html='не выбраны';
    else
        text_html=text_html.substr(0, text_html.length-2);
    $('#assigntags_popup').prev().find('.assigned_tags').html(text_html);
}

function delToSpam()
{
   if ($('#spam_popup').find('input:checked').length>0)
       {
           ImgToggle($('#spam_popup').next().find('.spam'));
       }
}

function HideDateFilters()
{
    var beg=StrToDate(time_beg);
    var end=StrToDate(time_end);
    var days=countDays(beg,end);
    if (days<31)
        {
            if ((end.getMonth()-beg.getMonth())<1 || ((end.getMonth()-beg.getMonth())==1 &&  (end.getDate()<beg.getDate())))
                {
                    $('#time_month').parent().addClass('hide');
                    if (days<7) $('#time_week').parent().addClass('hide');
                }
        }
}