var facebookUrl = './facebook/index.php';
var facebookUserList = {};
var from_group = false;
var page_access_token = null;

$(document).ready(function(){
  $.getJSON(facebookUrl, function(response){
    $.getScript('//connect.facebook.net/ru_RU/all.js', function(){
      FB.init({
        appId: response.appid
      });
    });
  });

  $('.addFacebookAccount').live('click', function(){
    var block = $(this).parents('.answer_message');
    $('.answer-block', block).toggle();
    FB.login(function(){
      FB.api('/me', function(response){
        facebookUserList[response.id] = response;
        $.cookie('facebook_user', JSON.stringify(facebookUserList));
      });
    }, {scope: 'publish_actions,manage_pages'});
  });

  $('.logoutFacebookAccount').live('click', function(){
    var block = $(this).parents('.answer_message');
    $('.answer-block', block).toggle();
    facebookUserList = {};
    $.cookie('facebook_user', null);
    FB.logout();
  });

  $('.answerFacebook').live('click', function(){
    var block = $(this).parents('.answer_message');
    var text = $('textarea[name="message_body"]', block).val();
    var link = $('input[name="message_link"]', block).val();
    var message_id = $(this).data('message_id');
    if( text.length > 0 && link.length > 0 )
    {
      var object_id = '';
      page_access_token = null;

      if( link.indexOf('permalink') >= 0 )
      {
        var link_arr = link.split('?');
        var story_fbid = '';
        var fbid = '';
        link_arr = link_arr[1].split('&');
        $.each(link_arr, function(i, val){
          if( val.indexOf('story_fbid=') >= 0 )
          {
            story_fbid = val.split('story_fbid=').join('');
          }else if( val.indexOf('id=') >= 0 )
          {
            fbid = val.split('id=').join('');
          }
        });
        object_id = fbid+'_'+story_fbid;
      }
      else
      {
        var link_arr = link.split('?');
        fbid = link_arr[1];
        if( fbid.indexOf('fbid=') >= 0 )
        {
          object_id = fbid.split('fbid=').join('');
        }
      }

      var user = {};
      $.each(facebookUserList, function(id, user_data){
        user = user_data;
      });

      from_group = $('.group-checkbox input', block).is(':checked');

      if( from_group )
      {
        FB.api('/me/accounts', 'GET', function (response) {
          if( response.data )
          {
            $.each(response.data, function(key, page_data){
              if( page_data.id == object_id )
              {
                page_access_token = page_data.access_token;
              }
            });
          }
          else
          {
            from_group = false;
          }
        });
      }

      FB.api('/'+object_id+'/comments', 'POST', {"message": text}, function (response) {
        if (response && !response.error) {
          var data = {
            'order_id' : id,
            'post_id' : message_id,
            'reaction_blog_login' : user.id,
            'reaction_blog_nick' : user.name,
            'reaction_content' : text
          };
          $.postJSON(postURL_AddReaction, data, function (responce) {
            if( responce.status == 'ok' )
            {
            var message = $('#msg-'+data.post_id);
            if ($(".full_text", message).attr("loaded") == "false") {
            $(".full_text", message).toggle();
            $(".full_text", message).html('<img src="/img/tonloader.gif" />');
            $.postJSON(ajaxURL_GetFullText, { id: data.post_id, order_id: data.order_id }, function (resp) {
              $(".answer", message).remove();
                $(".full_text", message).html("");
                $(".full_text", message).html(resp.full_content);
                if(resp.reaction){
                  var react_text = '';
                  for(var z=0; z<resp.reaction.length; z++){
                    react_text += '<div class="answer" style="display: block;"><img class="avatar" src="'+resp.reaction[z].reaction_blog_ico+'"/><div class="name">'+resp.reaction[z].reaction_blog_nick+'</div><div class="text">'+resp.reaction[z].reaction_content+'</div></div>';
                  }
                }
                react_text +="";
                $(".full_text", message).after(react_text);
                $(".full_text", message).attr("loaded", "true");
              });
            } else if ($(".full_text", message).attr("loaded") == "true") {
              //$(".full_text", message).toggle();
              $(".answer", message).remove();
              $(".full_text", message).html('<img src="/img/tonloader.gif" />');
              $.postJSON(ajaxURL_GetFullText, { id: data.post_id, order_id: data.order_id }, function (resp) {
                $(".full_text", message).html("");
                $(".full_text", message).html(resp.full_content);
                if(resp.reaction){
                  var react_text = '';
                  for(var z=0; z<resp.reaction.length; z++){
                  react_text += '<div class="answer" style="display: block;"><img class="avatar" src="'+resp.reaction[z].reaction_blog_ico+'"/><div class="name">'+resp.reaction[z].reaction_blog_nick+'</div><div class="text">'+resp.reaction[z].reaction_content+'</div></div>';
                  }
                }
                react_text +="";
                $(".full_text", message).after(react_text);
                $(".full_text", message).attr("loaded", "true");
              });
            }
            //$('.full_text', $('#msg-'+data.post_id)).after('<div class="answer"><img class="avatar" src="'+data.reaction_blog_ico+'"/><div class="name">'+data.reaction_blog_nick+'</div><div class="text">'+data.reaction_content+'</div></div>');
            //$('a', block).addClass('disabled');
            $('textarea[name="message_body"]', block).val("");
            $('.answer-block', block).toggle();
            return false;            }
          });
        }
        else
        {
          alert('Возникла ошибка при отправке сообщения. Описание ошибки: '+response.error.message);
        }
      });
    }
    else
    {
      $('.error', block).show();
    }
    return false;
  });

});

var updateFacebookAccounts = function()
{
	facebookUserList = $.cookie('facebook_user') ? JSON.parse($.cookie('facebook_user')) : {};
	var user_list_block = $('.account-list', $('.facebook-message'));
	var answer_button = $('.answerFacebook', $('.facebook-message'));
	var add_button = $('.addFacebookAccount', $('.facebook-message'));
	var logout_button = $('.logoutFacebookAccount', $('.facebook-message'));

	user_list_block.html('');
  answer_button.attr('disabled', 'disabled');
  add_button.show();
  logout_button.hide();

  $.each(facebookUserList, function(id, data){
    user_list_block.append('<label>'+data.name+'</label>');
    answer_button.removeAttr('disabled');
    add_button.hide();
    logout_button.show();
  });
};