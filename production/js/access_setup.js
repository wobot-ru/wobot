var priv_list = {
  1: 'Администратор',
  2: 'Редактор',
  3: 'Читатель'
};
$(document).ready(function(){
  $(".error_message").hide();

  $("#user_email").text($.cookie("user_email"));

  exp = $.cookie("user_exp");
  if (exp == null) exp = 0;
  if (exp == null || exp < 4 || exp == "0" || exp == "Аккаунт заблокирован") $("#user_exp").addClass("warn");
  $("#user_exp").text(exp);

  $("#exit").attr("href", inernalURL_logout);
  $("#access").attr("href", inernalURL_accessSetup);

  $("#user_tariff").text($.cookie("user_tariff"));
  $("#user_tariff").attr("href", inernalURL_tariff + $.cookie("tarif_id"));
  $("#user_tariff").unbind("click").click(function (e) {
    loadmodal(inernalURL_tariff + $.cookie("tarif_id"), 300, 400, "iframe");
    return false;
  });

  $("#user_money").html($.cookie("user_money") + "&nbsp;<span class=\"rur\">p<span></span></span>");

  $("#billing").attr("href", inernalURL_billing + "?user_id=" + $.cookie("user_id"));
  $("#billing").unbind("click").click(function (e) {
    loadmodal(inernalURL_billing + "?user_id=" + $.cookie("user_id"), "50", "100%", "iframe");
    return false;
  });

  $("#user_consultant").text($.cookie("user_consultant"));
  $("#user_consultant").attr("href", "FOO");
  $("#user_consultant").unbind("click").click(function (e) {
    loadmodal("FOO", "75%", "75%", "iframe");
    return false;
  });

  $("#faq").attr("href", inernalURL_faq);

  $.getJSON(ajaxURL_userSecList, {}, function (data) {
    if( data.users )
    {
      $.each(data.users, function(i, user_data){
        showUser(user_data);
      });
    }
  });

  $(".user .edit").tipTip({content: 'Настройки пользователя'});
  $(".user .delete").tipTip({content: 'Удаление пользователя'});

  $("#dialog-access_settings").dialog({
    modal: true,
    open: function () {
    },
    buttons: {
      "Сохранить": function () {
        var popup = $('#dialog-access_settings');
        var is_new = $('#access_settings_id').val().length <= 0;
        var is_valid = true;
        $('.error', popup).hide();
        if( $('#access_settings_email').val().length <= 0 )
        {
          $('.email-error', popup).show();
          is_valid = false;
        }
        else
        {
          var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
          if( !pattern.test($('#access_settings_email').val()) ){
            $('.email-format-error', popup).show();
            is_valid = false;
          }
        }

        if( $('#access_settings_password').val() != $('#access_settings_password_repeat').val() )
        {
          $('.confirm-pass-error', popup).show();
          is_valid = false;
        }
        else if( is_new && $('#access_settings_password').val().length <= 0 )
        {
          $('.pass-error', popup).show();
          is_valid = false;
        }

        if( is_valid )
        {
          var data = {
            'user_email' : $('#access_settings_email').val(),
            'user_priv' : $('#access_settings_priv').val()
          };
          if( $('#access_settings_password').val().length > 0 )
          {
            data.user_pass = $('#access_settings_password').val();
          }

          $.postJSON(postURL_userSecAdd, data, function (response) {
            if( response.status && response.status == 1 )
            {
              $('.email-domain-error', popup).show();
            }
            else
            {
              if( is_new )
              {
                showUser(response);
              }
              else
              {
                var user_dom = $('.user-list #'+$('#access_settings_id').val());
                $('.email', user_dom).text(response.user_email);
                $('.priv', user_dom).text(priv_list[response.user_mid_priv]).attr('priv', response.user_mid_priv);
              }
              $("#dialog-access_settings").dialog("close");
            }
          });
        }
      },
      "Отмена": function () {
        $(this).dialog("close");
      }
    },
    draggable: false,
    resizable: false,
    minWidth: 500,
    maxWidth: 500,
    autoOpen: false
  });

  $('#add_user').click(function(){
    clearUserAccessForm();
    $('#ui-dialog-title-dialog-access_settings').text('Добавление пользователя');
    $('#dialog-access_settings').dialog('open');
    return false;
  });

  $('.user .edit').live('click', function(){
    clearUserAccessForm();
    var parent = $(this).parents('.user');
    $('#access_settings_id').val(parent.attr('id'));
    $('#access_settings_email').val($('.email', parent).text()).attr('disabled', 'disabled');
    $('#access_settings_priv').val($('.priv', parent).attr('priv'));
    $('#ui-dialog-title-dialog-access_settings').text('Редактирование прав доступа для "'+$('.email', parent).text()+'"');
    $('#dialog-access_settings').dialog('open');
    return false;
  });

  $('.user .delete').live('click', function(){
    var parent = $(this).parents('.user');
    if( confirm('Вы действительно хотите удалить пользователя "'+$('.email', parent).text()+'"') )
    {
      $.postJSON(postURL_userSecDel, { user_id: parent.attr('id') }, function (response) {
        if( response.status && response.status == 'ok' )
        {
          parent.remove();
        }
      });
    }
    return false;
  });
});

var showUser = function(user_data)
{
  var user_dom = $('.user-list .template').clone();
  user_dom.removeClass('template').attr('id', user_data.user_id);
  $('.email', user_dom).text(user_data.user_email);
  $('.priv', user_dom).text(priv_list[user_data.user_mid_priv]).attr('priv', user_data.user_mid_priv);
  $('.user-list').append(user_dom);
};

var clearUserAccessForm = function()
{
  $('#dialog-access_settings .error').hide();
  $('#access_settings_id').val('');
  $('#access_settings_email').val('').removeAttr('disabled');
  $('#access_settings_priv').val('');
  $('#access_settings_password').val('');
  $('#access_settings_password_repeat').val('');
};
