var selectedMessageId = null;
var selectedMessageObject = null;
var CTRL_KEY = 17;
var ALT_KEY = 18;
var controlKeys = {
  40: 'nextMessage',
  38: 'prevMessage',
  39: 'showDubs',
  37: 'hideDubs'
};
var actionKeys = {
  81: 'negative_tone', // q
  87: 'neutral_tone', // w
  69: 'positive_tone', // e
  46: 'delete', // delete
  83: 'not_delete', // s (only group)
  76: 'favorite', // l
  78: 'not_favorite', // n (only group)
  84: 'full_text', // t (only single)
  9: 'mark_message', // Tab (only single)
  48: 'tag_0', // 1
  49: 'tag_1', // 1
  50: 'tag_2', // 2
  51: 'tag_3', // 3
  52: 'tag_4', // 4
  53: 'tag_5', // 5
  54: 'tag_6', // 6
  55: 'tag_7', // 7
  56: 'tag_8', // 8
  57: 'tag_9' // 9
};
var numCodes = {
  48: 0, // 0
  49: 1, // 1
  50: 2, // 2
  51: 3, // 3
  52: 4, // 4
  53: 5, // 5
  54: 6, // 6
  55: 7, // 7
  56: 8, // 8
  57: 9 // 9
};
var comb_code = [];

$(document).ready(function () {

  $(document).bind('click', function (e) {
    var _clicked = $(e.target);
    if (!_clicked.hasClass('message') && !_clicked.parents().hasClass('message')) {
      clearSelectedMessage();
    }
  });

  $('#ML').delegate('.message', 'click', function (e) {
    if (!$(this).hasClass('selected-message')) {
      setSelectedMessage($(this));
    }
  });

  $(document).keydown(function (e) {

    var code = e.keyCode || e.which;
    if( !$(e.target).is('input') && !$(e.target).is('textarea') )
    {
      if(e.ctrlKey )
      {
        if( code != CTRL_KEY && code != ALT_KEY )
        {
          comb_code.push(code);
        }
      }

      var func_prefix = e.altKey ? 'mass_' : '';

      var func = '';
      if (controlKeys.hasOwnProperty(code)) {
        func = controlKeys[code];
      }
      else if (actionKeys.hasOwnProperty(code)) {
        func = actionKeys[code];
      }

      if (func.length > 0) {
        e.preventDefault();
        if (func.indexOf('tag_') >= 0 && ( comb_code.length == 0 || comb_code.length == 2 ) ) {
          var arg = func.substr(4);
          if( comb_code.length == 2 )
          {
            arg = '';
            $.each(comb_code, function(i, val){
              arg += numCodes[val];
            });
            comb_code = [];
          }
          if( arg > 0 )
          {
            try {
              eval(func_prefix+'tag_func(' + arg + ')');
            }
            catch (err) {
            }
          }
        }
        else {
          hideTagList();
          try {
            eval(func_prefix + func + '_func()');
          }
          catch (err) {
          }
        }
      }
    }
  }).keyup(function(e){
    if( e.keyCode == CTRL_KEY || e.which == CTRL_KEY )
    {
      comb_code = [];
    }
  });
});

var hideTagList = function(){
  if (selectedMessageObject !== null) {
    $('.tag_add-db', selectedMessageObject).hide();
  }
};

var clearSelectedMessage = function () {
  selectedMessageId = null;
  selectedMessageObject = null;
  $('.message').removeClass('selected-message');
};

var setSelectedMessage = function (message) {
  selectedMessageObject = message;
  selectedMessageId = message.attr('pk');
  $('.message').removeClass('selected-message');
  selectedMessageObject.addClass('selected-message');
};

var nextMessage_func = function () {
  if (selectedMessageObject !== null) {
    if (selectedMessageObject.next().hasClass('message') && !selectedMessageObject.next().is('#template')) {
      setSelectedMessage(selectedMessageObject.next());
      scrollMessagesIfNeed();
    }
    else if( selectedMessageObject.next().find('div.message').first().hasClass('message') && !selectedMessageObject.next().find('div.message').first().is('#template') )
    {
      if( selectedMessageObject.next().is(':visible') )
      {
        setSelectedMessage(selectedMessageObject.next().find('div.message').first());
        scrollMessagesIfNeed();
      }
      else
      {
        setSelectedMessage(selectedMessageObject.next().next());
        scrollMessagesIfNeed();
      }
    }
    else if( selectedMessageObject.parent().next().hasClass('message') && !selectedMessageObject.parent().next().is('#template') )
    {
      setSelectedMessage(selectedMessageObject.parent().next());
      scrollMessagesIfNeed();
    }
  }
};

var prevMessage_func = function () {
  if (selectedMessageObject !== null) {
    if (selectedMessageObject.prev().hasClass('message') && !selectedMessageObject.prev().is('#template')) {
      setSelectedMessage(selectedMessageObject.prev());
      scrollMessagesIfNeed();
    }
    else if( selectedMessageObject.prev().find('div.message').last().hasClass('message') && !selectedMessageObject.prev().find('div.message').last().is('#template') )
    {
      if( selectedMessageObject.prev().is(':visible') )
      {
        setSelectedMessage(selectedMessageObject.prev().find('div.message').last());
        scrollMessagesIfNeed();
      }
      else
      {
        setSelectedMessage(selectedMessageObject.prev().prev());
        scrollMessagesIfNeed();
      }
    }
    else if( selectedMessageObject.parent().prev().hasClass('message') && !selectedMessageObject.parent().prev().is('#template') )
    {
      setSelectedMessage(selectedMessageObject.parent().prev());
      scrollMessagesIfNeed();
    }
  }
};

var scrollMessagesIfNeed = function () {

  if (selectedMessageObject.offset().top < $(window).scrollTop() + 150) {
    $(window).scrollTop(selectedMessageObject.offset().top - 150);
  }
  else if (( selectedMessageObject.offset().top + selectedMessageObject.height() ) > ( $(window).scrollTop() + $(window).height() )) {
    $(window).scrollTop(selectedMessageObject.offset().top - $(window).height() + selectedMessageObject.height());
  }
};

var showDubs_func = function() {
  if (selectedMessageObject !== null && $('.dup-link a', selectedMessageObject).is('a')) {
    var dubs_div = $('#msg-'+$('.dup-link a', selectedMessageObject).attr('href')+'dups');
    if( !dubs_div.is('div') || !dubs_div.is(':visible') )
    {
      $('.dup-link a', selectedMessageObject).click();
    }
  }
};

var hideDubs_func = function() {
  if (selectedMessageObject !== null && $('.dup-link a', selectedMessageObject).is('a')) {
    var dubs_div = $('#msg-'+$('.dup-link a', selectedMessageObject).attr('href')+'dups');
    if( dubs_div.is('div') && dubs_div.is(':visible') )
    {
      $('.dup-link a', selectedMessageObject).click();
    }
  }
};

var positive_tone_func = function () {
  if (selectedMessageObject !== null) {
    $('.order_tone-changer .positive', selectedMessageObject).click();
  }
};

var negative_tone_func = function () {
  if (selectedMessageObject !== null) {
    $('.order_tone-changer .negative', selectedMessageObject).click();
  }
};

var neutral_tone_func = function () {
  if (selectedMessageObject !== null) {
    $('.order_tone-changer .neutral', selectedMessageObject).click();
  }
};

var delete_func = function () {
  if (selectedMessageObject !== null) {
    $('.order_spam', selectedMessageObject).click();
  }
};

var favorite_func = function () {
  if (selectedMessageObject !== null) {
    $('.order_fav', selectedMessageObject).click();
  }
};

var full_text_func = function () {
  if (selectedMessageObject !== null) {
    $('.order_open', selectedMessageObject).click();
  }
};

var mark_message_func = function () {
  if (selectedMessageObject !== null) {
    $('.group-action-item', selectedMessageObject).click();
  }
};

var tag_func = function (tag_id) {
  if (selectedMessageObject !== null) {
    var tag_div = $('.message-tag-container .m.tag', selectedMessageObject).eq(tag_id-1);
    if (tag_div.is('div')) {
      hideTagList();
      $('.tag_add-db', selectedMessageObject).show();
      $('.e1 a', tag_div).click();
      var tags_height = $('.rinline.add-tag', selectedMessageObject).height();
      if (( selectedMessageObject.offset().top + selectedMessageObject.height() + tags_height ) > ( $(window).scrollTop() + $(window).height() )) {
        $(window).scrollTop(selectedMessageObject.offset().top - $(window).height() + selectedMessageObject.height() + tags_height);
      }
    }
  }
};

var mass_positive_tone_func = function () {
  if ($('#group-actions .actions').is(':visible')) {
    $('#group-actions .actions a[href="positive"]').click();
  }
};

var mass_negative_tone_func = function () {
  if ($('#group-actions .actions').is(':visible')) {
    $('#group-actions .actions a[href="negative"]').click();
  }
};

var mass_neutral_tone_func = function () {
  if ($('#group-actions .actions').is(':visible')) {
    $('#group-actions .actions a[href="neutral"]').click();
  }
};

var mass_delete_func = function () {
  if ($('#group-actions .actions').is(':visible')) {
    $('#group-actions .actions a[href="spam"]').click();
  }
};

var mass_not_delete_func = function () {
  if ($('#group-actions .actions').is(':visible')) {
    $('#group-actions .actions a[href="not_spam"]').click();
  }
};

var mass_favorite_func = function () {
  if ($('#group-actions .actions').is(':visible')) {
    $('#group-actions .actions a[href="favorite"]').click();
  }
};

var mass_not_favorite_func = function () {
  if ($('#group-actions .actions').is(':visible')) {
    $('#group-actions .actions a[href="not_favorite"]').click();
  }
};

var mass_tag_func = function (tag_id) {
  if ( $('#group-actions .tags').is(':visible') ) {
    var tag_link = $('#group-actions .tags a').not($(('#group-action-add-tag'))).eq(tag_id-1);
    if (tag_link.is('a') && !tag_link.is('#group-action-add-tag')) {
      tag_link.click();
    }
  }
};