var popup_params = 'menubar=no,location=yes,resizable=no,scrollbars=yes,status=no,toolbar=no,height=500,width=600';
var vkUserList = {};
var vkWindow = false;
var vkUrl = './vkontakte/index.php';
var checkLinkUrl = './vkontakte/check_link.php';
var appId;
var vk_login_popup;
var message_text;
var message_id;
var from_group = false;

$.getJSON(vkUrl, function (response) {
  appId = response.appid;
  VK.init({
    apiId: response.appid
  });
});


$(document).ready(function () {
  vk_login_popup = $('#vk_login_popup');

  $('.loginVk').live('click', function () {
    var block = $(this).parents('.answer_message');
    $('.answer-block', block).toggle();
    vk_login_popup.show();
    $('.error', vk_login_popup).hide();
  });

  $('.logoutVkAccount').live('click', function(){
    var block = $(this).parents('.answer_message');
    $('.answer-block', block).toggle();
    vkUserList = {};
    $.cookie('vk_user', null);
    VK.Auth.logout();
  });

  $('a.vk-window', vk_login_popup).live('click', function(){
    vkWindow = window.open('https://oauth.vk.com/authorize?client_id='+appId+'&scope=wall&redirect_uri=https://oauth.vk.com/blank.html. &display=page&v=5.21&response_type=token ', 'vk_auth', popup_params);
    return false;
  });

  $('.addVkAccount', vk_login_popup).live('click', function () {
    var access_token = $('input[name="access_token"]', vk_login_popup).val();
    if (access_token.length > 0) {
      var user_data = {};
      if (access_token.indexOf('https://oauth.vk.com/blank.html#access_token=') >= 0) {
        access_token = access_token.split('https://oauth.vk.com/blank.html#access_token=').join('').split('&');
        user_data['access_token'] = access_token[0];
        user_data['expires_in'] = access_token[1].split('expires_in=').join('');
        user_data['user_id'] = access_token[2].split('user_id=').join('');
        VK.Api.call('users.get', {user_ids: user_data['user_id'], fields: 'photo_50,screen_name'}, function (data) {
          if (data.response) {
            user_data['name'] = data.response[0].first_name + ' ' + data.response[0].last_name;
            user_data['photo'] = data.response[0].photo_50;
            vkUserList[user_data['user_id']] = user_data;
            $.cookie('vk_user', JSON.stringify(vkUserList));
          }
        });
      }
      $('input[name="access_token"]', vk_login_popup).val('');
      $(vk_login_popup).hide();
      if( vkWindow )
      {
        vkWindow.close();
      }
    }
    else {
      $('.error', vk_login_popup).show();
    }
    return false;
  });

  $('.close', vk_login_popup).live('click', function () {
    $('input[name="access_token"]', vk_login_popup).val('');
    $(vk_login_popup).hide();
  });

  $('.answerVk').live('click', function () {
    var block = $(this).parents('.answer_message');
    var text = $('textarea[name="message_body"]', block).val();
    var link = $('input[name="message_link"]', block).val();
    message_id = $(this).data('message_id');
    if (text.length > 0 && link.length > 0) {
      $.get(checkLinkUrl, {link: link}, function (responce) {
        var message_url = responce;
        var message_data = {};
        if (message_url.indexOf('wall') >= 0) {
          message_url = message_url.split('wall');
          message_url = message_url[1].split('_');
          message_data['owner_id'] = message_url[0].indexOf('-') == 0 ? message_url[0] : '-' + message_url[0];
          if (message_url[1].indexOf('reply') >= 0) {
            var reply_url = message_url[1].split('?reply=');
            message_data['post_id'] = reply_url[0];
            message_data['reply_to_comment'] = reply_url[1];
          }
          else {
            message_data['post_id'] = message_url[1];
          }
          var method_params = '';
          $.each(message_data, function (key, value) {
            method_params += key + '=' + value + '&'
          });

          from_group = $('.group-checkbox input', block).is(':checked');

          if( from_group )
          {
            method_params += 'from_group=1&';
          }

          method_params += 'text=' + encodeURIComponent(text);
          message_text = text;

          var user = {};
          vkUserList = $.cookie('vk_user') ? JSON.parse($.cookie('vk_user')) : {};
          $.each(vkUserList, function (id, user_data) {
            user = user_data;
          });

          var script = document.createElement('SCRIPT');
          script.src = 'https://api.vk.com/method/wall.addComment?' + method_params + '&v=5.22&access_token=' + user.access_token + '&callback=vkCallbackFunc';
          document.getElementsByTagName("head")[0].appendChild(script);
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

var updateVkAccounts = function()
{
	vkUserList = $.cookie('vk_user') ? JSON.parse($.cookie('vk_user')) : {};
	var user_list_block = $('.account-list', $('.vk-message'));
	var answer_button = $('.answerVk', $('.vk-message'));
	var logout_button = $('.logoutVkAccount', $('.vk-message'));
	var login_button = $('.loginVk', $('.vk-message'));

	user_list_block.html('');
  answer_button.attr('disabled', 'disabled');
  login_button.show();
  logout_button.hide();

  $.each(vkUserList, function(id, data){
    user_list_block.append('<label>'+data.name+'</label>');
    answer_button.removeAttr('disabled');
    login_button.hide();
    logout_button.show();
  });
};

var vkCallbackFunc = function (result) {
  var block = $('#msg-'+message_id+' .answer_message');
  if (result.error) {
    alert('Возникла ошибка при отправке сообщения. Код ошибки: ' + result.error.error_code + '. Описание ошибки: ' + result.error.error_msg);
    $('.answer-block', block).toggle();
  }
  else
  {
    var user = {};
    $.each(vkUserList, function(id, user_data){
      user = user_data;
    });
    var data = {
      'order_id' : id,
      'post_id' : message_id,
      'reaction_blog_login' : user.user_id,
      'reaction_blog_nick' : user.name,
      'reaction_blog_ico' : user.photo,
      'reaction_content' : message_text
    };

    if( from_group )
    {
      data.reaction_blog_nick = $.cookie('user_email');
    }

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
        return false;
      }
    });
  }
};