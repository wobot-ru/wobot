var chked=0;

function pressCheckBox(elm){
	var checked = $(elm).parent().attr("value");
	
	var ids = $.cookie("compare_ids");
	if (ids == undefined || ids == null || ids == "") ids = [];	
	else ids = ids.split(",");	
	
	var id;
		id = $(elm).parents(".FL").attr("id");
		//alert('my id '+id);		
	var index;

	if (checked == 0) {
		$(elm).css("opacity","1"); 		
		$(elm).parent().attr("value","1");

		if ($.inArray(id,ids) == -1) ids.push(id);
		//chked++;
		
		//chked=0;
		/*$(".lr0").each(function(index, value){
			if ($(index).parent().attr("value")!=0)
			{
				chked++;
			}
		});*/
		
		//alert(ids.length);
		if (ids.length<2)
		{
			//alert("<2");
			$(".lr0").css("display","none");
		}
		else
		{
			//alert(">1")
			$(elm).parent().find(".lr0").css("display","block");
		}
		
		if (ids.length==2)
		{
			//alert("=2");
			$(".checkbox[value=1]").children(".lr0").css("display","block");	
		}		
		
		//alert(chked);
		//alert($(".lr0[chk=\"1\"]").length);
	} else  {
		$(elm).css("opacity","0");
		$(elm).parent().attr("value","0");		
		$(elm).parent().find(".lr0").css("display","none");
			
		//alert("before "+ids.join(","));
		/*ids = $.grep(ids, function(n, i){ 
			//if (i != ""+id && i != "") alert("save "+i); 
			return (i !== id && i != ""); 
		});*/
		//ids.remove(id);
		var indx = $.inArray(id, ids);
		if(indx != -1)
		{
		  ids.splice(indx, 1);
		}
		//alert("after "+ids.join(","));
		
		if (ids.length<2)
		{
			$(".lr0").css("display","none");
		}
	}
	
    $.cookie("compare_count",ids.length)			
	ids = ids.join(",");
    $.cookie("compare_ids",ids)	
}

function compareThemes() {
	var count = $.cookie("compare_count");
	chked=count;
	if (count != undefined && count != null && parseInt(count,10)  >= 2) {
		window.location = inernalURL_themesCompare;
	} else {
		window.location = inernalURL_themePage+$.cookie("compare_ids");
	}
}