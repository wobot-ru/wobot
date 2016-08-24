function createDropDown(sourceID,width){
	var source = $("#"+sourceID);
	$(source ).css("display","none");
  var selected = source.find("option[selected]");
  if( selected.length == 0 )
  {
    selected = source.find("option[value='"+source.val()+"']");
  }
  if( selected.length == 0 )
  {
    selected = $('option', source).eq(0);
  }
	var options = $("option", source);
  var pid = $(source).attr("id");
	var id = 'tdd-'+pid;
	$('<dl id="'+id+'" class="dropdown" value="'+selected.val()+'"></dl>').insertAfter(source);
	$("#"+id).append('<dt style="overflow: hidden; white-space: nowrap;"><a href="#" onclick="return false;">' + selected.text() + '<span class="value">' + selected.val() + '</span></a></dt>');
	$("#"+id).append('<dd><ul></ul></dd>');
	$("#"+id).css("width", width);
	if (width == null) 
  {
    $("#"+id+" dd").css("min-width", $(source).css("width")-2);
  }
	else
  {
    $("#"+id+" dd").css("min-width", width);
  }

	options.each(function(){		
		$("#"+id+" dd ul").append('<li style="white-space: nowrap; padding-left: '+(parseInt($(this).attr("lvl"),10)*7)+'px;" value="'+$(this).attr("value")+'"><a href="#" onclick="return false;">'+ $(this).text() + '<span class="value">' + $(this).val() + '</span></a></li>');
	});
	
	$("#"+id).parent().click(function() {
		$(document).click();
		$('html').click();
		$(this).find("dd ul").toggle();
		return false;
	});

	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if ((! $clicked.parents().hasClass("dropdown")))
    {
      $(".dropdown dd ul").hide();
    }
	});
	
 	$("#"+id).find("dd ul li a").click(function() {
		var text = $(this).html();
		var dropdown = $(this).parent().parent().parent().parent();
		$(dropdown).find("dt a").html(text);
		$(dropdown).find("dd ul").hide();
		
		var source = $("#"+$(dropdown).attr("id").substr(4));
		source.val($(this).find("span.value").html());
		$("#"+id).attr("value",$(this).find("span.value").text());
		$("#"+id).change();
		return false;
	});	
	$(".dropdown dd ul").css("min-width", width);
} 