var twitterUrl = './twitter/index.php';
var twitterUserList = {};
var twitterWindow;
var params = 'menubar=no,location=yes,resizable=no,scrollbars=yes,status=no,toolbar=no,height=500,width=600';

$(document).ready(function(){
	$('.addTwitterAccount').live('click', function(){
    var block = $(this).parents('.answer_message');
		$('.answer-block', block).toggle();
		twitterWindow = window.open(twitterUrl+'?start=1', 'twitter_auth', params);
	});

  $('.logoutTwitterAccount').live('click', function(){
    var block = $(this).parents('.answer_message');
		$('.answer-block', block).toggle();
    $.getJSON(twitterUrl+'?logout=1');
	});

	$('.answerTwitter').live('click', function(){
    var block = $(this).parents('.answer_message');
    var text = $('textarea[name="message_body"]', block).val();
    var link = $('input[name="message_link"]', block).val();
    var message_id = $(this).data('message_id');
    if( text.length > 0 && link.length > 0 )
    {
      var answer = {
        'theme_id': id,
        'message_id': message_id,
        'text': text,
        'link': link
      };
      $.getJSON(twitterUrl+'?answer='+JSON.stringify(answer), {}, function(responce){
        var status = responce.status;
        if( status == 200 )
        {
          var user = {};
          $.each(twitterUserList, function(id, user_data){
            user = user_data;
          });

          var data = {
            'order_id' : id,
            'post_id' : message_id,
            'reaction_blog_login' : user.screen_name,
            'reaction_blog_nick' : decodeURIComponent(user.name),
            'reaction_blog_ico' : user.avatar,
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
            return false;
            }
          });
        }
        else
        {
          alert('Возникла ошибка при отправке сообщения. Код ошибки: '+status);
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

var updateTwitterAccounts = function()
{
	twitterUserList = $.cookie('twitter_user') ? JSON.parse($.cookie('twitter_user')) : {};
	var user_list_block = $('.account-list', $('.twitter-message'));
	var answer_button = $('.answerTwitter', $('.twitter-message'));
	var add_button = $('.addTwitterAccount', $('.twitter-message'));
	var logout_button = $('.logoutTwitterAccount', $('.twitter-message'));

	user_list_block.html('');
  answer_button.attr('disabled', 'disabled');
  add_button.show();
  logout_button.hide();

  $.each(twitterUserList, function(id, data){
    user_list_block.append('<label>'+decodeURIComponent(data.name)+'</label>');
    answer_button.removeAttr('disabled');
    add_button.hide();
    logout_button.show();
  });
};
