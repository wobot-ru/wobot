/*
	Открывает модельное окно попапа
*/
var av_order;

function loadmodal(href,width,height,type) {
	if (!width) width=604;
	if (!height) height=400;
			$.fancybox({
					'href' 				: href,
                    'width'				: width,
                    'height'			: height,
                    'scrolling'         : 'no',
                    'titleShow'         : false,
                    'padding'           : 0,
                    'autoScale'         : false,
                    'transitionIn'      : 'none',
                    'transitionOut'     : 'none',
                    type    			: type,
					onClosed : function() { $(".EXTERN").css('display','none');}
            });
}

/*
function compareThemes(i) {
	var parent = $(i).parent().parent().parent().parent().parent();
	
	var ids = [];
	parent.find('.FL[completed="1"] .checkbox[value="1"]').each(function(key, elm) {
		
        ids[ids.length] =$(elm).parent().parent().attr("id");
    });		
}
*/
function dateToWords(inDate) {	
    date = inDate.split(".");

    var month = "";
	switch (parseInt(date[1],10)) {
		case 1 : month = "янв";break;
		case 2 : month = "фев";break;break;
		case 3 : month = "мар";break;
		case 4 : month = "апр";break;
		case 5 : month = "мая";break;
		case 6 : month = "июн";break;
		case 7 : month = "июл";break;
		case 8 : month = "авг";break;
		case 9 : month = "сен";break;
		case 10 : month = "окт";break;
		case 11: month = "ноя";break;
		case 12: month = "дек";		break;																				
	};
	
	return parseInt(date[0],10)+" "+month+" '"+parseInt(date[2],10)%100;
}

function addTheme(name,keywordOr, keywordAnd, keywordNot ,start,end) {
    var status;
	$.postJSON(ajaxURL_AddTheme, {order_name: name, mw: keywordOr, mnw: keywordAnd, mew: keywordNot, order_start: start, order_end: end}, function(data) {

        status = data.status;
        if (status == 21)
        {
            $("#tip-cont-2").show();
            $("#newTheme-kwrd-tip").text("Неккоректно составлен список, проверьте правильность ввода.");
            $("#dialog-newTheme-keywordOr").addClass( "ui-state-error" );

        }
        else if (status == 22)
        {
            $("#tip-cont-2").show();
            $("#newTheme-kwrd-tip").text("Неккоректно составлен список, проверьте правильность ввода.");
            $("#dialog-newTheme-keywordAnd").addClass( "ui-state-error" );
        }
        else if (status == 23)
        {
            $("#tip-cont-2").show();
            $("#newTheme-kwrd-tip").text("Неккоректно составлен список, проверьте правильность ввода.");
            $("#dialog-newTheme-keywordNot").addClass( "ui-state-error" );
        }
        else if (status == 3)
        {
            $("#tip-cont-2").show();
            $("#newTheme-kwrd-tip").text("Для введенных ключевых слов превышено ограничение на количество сообщений. " +
                "Для увеличения количества сообщений Вы можете сменить тариф.");
            $("#dialog-newTheme-keywordAnd").addClass( "ui-state-error" );
        }
        else
        {
            $("#tip-cont-2").hide();
            $("#dialog-newTheme").dialog('close');
            loadContent();
        }
    });
}

function getResourceList() {

    $.postJSON(ajaxURL_GetResTable, {}, function(data) {
        $("#resTable").empty();
        $.each(data.src, function(key, source)
        {
            var statusStr;
            if (source.status == 0) statusStr = 'добавлен';
            else if (source.status == 1) statusStr = 'нельзя добавить';
            else if (source.status == 2) statusStr = 'в процессе обработки';
            $("#resTable").append("<tr><td>"+source.link+"</td><td>"+statusStr+"</td></tr>")
            //alert(source.link);
        });
        //alert(data.src[0].link);
    });

    return false;

}

function getSettings() {

     $.postJSON(ajaxURL_GetSettings, {}, function(data) {

         $("#dialog-Settings-name").val(data.contact_name);
         $("#dialog-Settings-company").val(data.comp_name);
         $("#dialog-Settings-email").val(data.user_mails);

         var radios = $('input:radio[name=radio]');
         if(radios.is(':checked') === false) {
         radios.filter('[value='+data.freq_mail+']').attr('checked', 'checked').button("refresh");
         }
         //radios.button("refresh");

         //alert(data.freq_mail);

         /*alert(data.comp_name);
         alert(data.contact_name);
         alert(data.user_mails);
         alert(data.freq_mail);*/
     });

}



function saveSettings(fio, company, pass, ver_pass, emails, freq) {
    //alert('Сохраняем'+ajaxURL_SaveSettings);
     $.postJSON(ajaxURL_SaveSettings, {fio: fio, company: company, pass: pass, ver_pass: ver_pass, emails: emails, freq: freq}, function(data) {

         //alert(data.status);
         var status = data.status;

         if (status == 3)
         {
            $("#tip-cont-4").show();
            $("#dialog-Settings-passnew").addClass("ui-state-error");
            $("#dialog-Settings-passcheck").addClass("ui-state-error");
            $("#dialog-Settings-tip").text("Пароль должен состоять из 8 символов. Минимум 1 заглавная буква и 1 цифра.");

         }
         else if (status == 5)
         {
            $("#tip-cont-4").show();
            $("#dialog-Settings-email").addClass("ui-state-error");
            $("#dialog-Settings-tip").text("Некорректный список e-mail адресов. Проверьте правильнось ввода.");
         }
         else
         {
            $("#tip-cont-4").hide();
            $("#dialog-Settings").dialog("close");
         }
/*
 * параметры fio-ФИО , company-название компании , pass ver_pass - пароли , emails - мыла , freq - частота
[11.04.12 17:27:47] z_me_i: передаеш постом
[11.04.12 17:28:37] z_me_i: ошибки: fali = 1 - фио плохое , 2 - компания плохая , 3 - пароли некорректные , 4 - пароли не совпадают , 5 - мыла неправильные
[11.04.12 17:28:54] z_me_i: fail = 0 значит все ок
  * */


     });

}


function getSignature(sum) {

    $.postJSON(ajaxURL_GetBilling, {sum: sum}, function(data) {


        $("#InvId").val(data.bill_id);
        $("#Sign").val(data.sign);
        $("#OutSum").val(sum);
        
        $("#billingForm").submit();
        //$("#billingForm").submit();

        //alert(data.bill_id);
        //alert(data.sign);
        
    });
}


function getBilling(){

    //
    
    $("#billingTable").empty();
     $.postJSON(ajaxURL_GetBilling, {}, function(data) {

        if (data == null)
        {
            $("#billingTable").append("<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>");
            return false;
        }
        else
        {
            $.each(data.bill, function(key, bill) {

                var billtype;
                if (bill.type == 1) billtype = 'пополнение счета';
                else if (bill.type == 2) billtype = 'оплата тарифа';

                var billstatus;
                if (bill.status == 1) billstatus = 'успешно';
                else if (bill.status == 0) billstatus = 'ошибка';

                $("#billingTable").append("<tr><td>"+bill.date+"</td><td>"+bill.money+"</td><td>"+billtype+"</td><td>"+billstatus+"</td></tr>");
                //alert(bill.date+' '+bill.money+' '+billtype+' '+billstatus);
            });

            //alert(data.bill_id);
            //$("")
            //alert($("#InvId"));
        }

        //alert(data.) ;

         /*
          * date: "21.03.2012",
money: "0.01",
type: 1,
status: 1
}
]
}
type=1 - пополнение счета, type=2 - оплата тарифа
status=1 - успешно, status=0 - не успешно    */
     });

}

function addResource(url) {

    $.postJSON(ajaxURL_AddResource, {Url: url}, function(data) {
    //alert(data.status);
    if (data.status == 'ok')
    {
        loadContent();
        $(this).dialog("close");
    }
    else if (data.status == 'fail')
    {
        $("#add_src_url").addClass( "ui-state-error" );
        updateTips("Проверьте правильность адреса" ,"#newUrlTip");
        $("#tip-cont-3").show();
    }
    else if (data.status == 'fail2')
    {
        $("#add_src_url").addClass( "ui-state-error" );
        updateTips("Ресурс уже существует" ,"#newUrlTip");
        $("#tip-cont-3").show();
    }


});

}
function loadContent() {
	$.postJSON(ajaxURL_Orders, function(data) {
		$("#user_email").text(data.user_email);
		$("#popup-user").text(data.user_email);


		$("#dyn2").text(data.orders.length);

		exp=data.tarif_exp;
		if (exp==null) exp=0;
		if (exp==0) exp="0";
		if (exp==null||exp<4||exp=="0") $("#user_exp").addClass("warn");
		$("#user_exp").text(exp);
		$("#popup-exp").text(exp);

        av_order = data.av_order;

		$("#user_email").unbind("click").click(function(e) {/* заглушка для popup-а */ return false;});


		$("#exit").attr("href",inernalURL_logout);
    $("#access").attr("href", inernalURL_accessSetup);

		$("#user_tariff").text(data.user_tarif);
        $("#popup-tarif").text(data.user_tarif);


		$("#user_tariff").attr("href",inernalURL_tariff+data.tarif_id);
		$("#user_tariff").unbind("click").click(function(e) { loadmodal(inernalURL_tariff+data.tarif_id,300,400,"iframe"); return false;});

		$("#user_money").html(data.user_money+"&nbsp;<span class=\"rur\">p<span></span></span>");
		$("#popup-money").html(data.user_money);



		$("#billing").attr("href",inernalURL_billing+"?user_id="+data.user_id);
		$("#billing").unbind("click").click(function(e) { loadmodal(inernalURL_billing+"?user_id="+data.user_id,"50","100%","iframe"); return false;});

		$("#user_consultant").text(data.user_consultant);
		/* НЕ ОПРЕДЕЛЕНО ПО ТЗ */
		$("#user_consultant").attr("href","http://reformal.ru/widget/58144");
		$("#user_consultant").unbind("click").click(function(e) { loadmodal("http://reformal.ru/widget/58144","75%","75%","iframe"); return false;});
		/*:~ */

		$("#faq").attr("href",inernalURL_faq);
		$("#faq").unbind("click").click(function(e) { loadmodal(inernalURL_faq,"75%","75%","iframe"); return false;});

		var ready = 0;

	 	var showAll = true;

		var ids = $.cookie("compare_ids");
		if (ids == undefined || ids == null || ids == "") ids = [];
		else ids = ids.split(",");

		$.cookie("user_id",data.user_id);
		$.cookie("user_email",data.user_email);
		$.cookie("user_exp",data.tarif_exp);
		$.cookie("user_tarif",data.user_tarif);
		$.cookie("tarif_id",data.tarif_id);
		$.cookie("user_money",data.user_money);

        $("#billingItem").val(data.user_id);
        


		$(".FL[real='real']").remove();
		$.each(data.orders, function(key, order) {
			// Подготовка
			var theme = null;

			if (order.ready == false)
				theme = $("#theme-notready").clone();
			else {
				ready++;
				theme = $("#theme-ready").clone();
			}

			$(theme).css("display","block");

			// Заполнение полей

			$(".order-image",theme).attr("src",imgURL_themesGraph.replace('%order_id%',order.id));
			$(".rss",theme).attr("href",imgURL_themesRSS.replace('%order_id%',order.id));
			$(".rss",theme).click( function (e) { e.stopPropagation(); } );
			$(theme).attr("id",order.id);
			$(theme).attr("real","real");
			$(".order-href",theme).css("cursor","pointer").click( function(e) {
				window.location.href = inernalURL_themePage+order.id;
			});

			$(theme).find(".order-keyword").html(order.keyword);
			$(theme).find(".order-keyword").attr("href",inernalURL_themePage+order.id);
			$(theme).find(".order-dates").html(dateToWords(order.start)+' &#8211; <br/>'+dateToWords(order.end));
			$(theme).find(".popup").each(function(index, element) {
                $(this).attr("id",index+"-"+order.id);
            });


			//$(theme).find(".r0").css("height",$(theme).find(".order-keyword").height() + 30);

			var delta = "+ 0";

			if (order.ready != false) {
				$(theme).find(".order-posts").text(order.posts);
				$(theme).find(".order-posts-delta").removeClass("pls");
				$(theme).find(".order-posts-delta").removeClass("mns");
				if (parseInt(order.din_posts,10) < 0)
					$(theme).find(".order-posts-delta").addClass("mns");
				else if (parseInt(order.din_posts,10) >= 0)
					$(theme).find(".order-posts-delta").addClass("pls");
				$(theme).find(".order-posts-delta").text(order.din_posts);

				$(theme).find(".order-src").text(order.src);
				$(theme).find(".order-src-delta").removeClass("pls");
				$(theme).find(".order-src-delta").removeClass("mns");
				if (parseInt(order.din_src,10) < 0)
					$(theme).find(".order-src-delta").addClass("mns");
				else if (parseInt(order.din_src,10) >= 0)
					$(theme).find(".order-src-delta").addClass("pls");
				$(theme).find(".order-src-delta").text(order.din_src);

				$(theme).find(".order-value").text(order.value);
				$(theme).find(".order-value-delta").removeClass("pls");
				$(theme).find(".order-value-delta").removeClass("mns");
				if (parseInt(order.div_value,10) < 0)
					$(theme).find(".order-value-delta").addClass("mns");
				else if (parseInt(order.div_value,10) >= 0)
					$(theme).find(".order-value-delta").addClass("pls");
				$(theme).find(".order-value-delta").text(order.div_value);

				//$(theme).find(".order-image").attr("src",order.graph);

				$(theme).find(".order-ready").text(dateToWords(order.ready));

				if ($.inArray(order.id+"",ids) == -1) {
					$(theme).find(".checkbox").attr("value","0");
					$(theme).find(".checkbox .lr2").css("opacity","0");
					$(theme).find(".checkbox .lr0").css("display","none");
				} else {
					$(theme).find(".checkbox").attr("value","1");
					$(theme).find(".checkbox .lr2").css("opacity","1");
					$(theme).find(".checkbox .lr0").css("display","block");
				}
			} else {
				if (showAll)
					$(theme).css("display", "block");
				else
					$(theme).css("display", "none");
			}


			$("#body").append(theme);
 	 });
		prepareToolTips();
	$("#themes-ready").text(ready);
});
}


function updateTips( t, tip ) {$(tip).text( t );}




$(document).ready(function () {

    
    var newThemeName = $( "#dialog-newTheme-name" ),
        newThemeDate1 = $("#dialog-newTheme-datepicker1"),
        newThemeDate2 = $("#dialog-newTheme-datepicker2"),
        keywordOr = $("#dialog-newTheme-keywordOr"),
        keywordAnd = $("#dialog-newTheme-keywordAnd"),
        keywordNot = $("#dialog-newTheme-keywordNot");

    var settingsName = $("#dialog-Settings-name"),
        settingsCompany = $("#dialog-Settings-company"),
        settingsPassnew = $("#dialog-Settings-passnew"),
        settingsPasscheck = $("#dialog-Settings-passcheck"),
        settingsEmail = $("#dialog-Settings-email");

    var settingsTips = $( [] ).add($("#dialog-Settings-name-tip"))
        .add($("#dialog-Settings-company-tip"))
        .add($("#dialog-Settings-passnew-tip"))
        .add($("#dialog-Settings-passcheck-tip"))
        .add($("#dialog-Settings-email-tip"));

    var allSettings = $( [] ).add(settingsName)
        .add(settingsCompany)
        .add(settingsPassnew)
        .add(settingsPasscheck)
        .add(settingsEmail);
    

    //var tips = $( "#newTheme-name-tip" );
    var allTips = $( [] ).add($("#newTheme-name-tip"))
        .add($("#newTheme-date-tip"))
        .add($("#newTheme-kwrd-tip"))
        .add($("#newTheme-kwrd2-tip"))
        .add($("#newTheme-kwrd3-tip"));

    var allFields = $( [] ).add( newThemeName )
        .add(newThemeDate1)
        .add(newThemeDate2)
        .add(keywordOr)
        .add(keywordAnd)
        .add(keywordNot);

    $('#scrollbar1').tinyscrollbar();
    $('#scrollbar2').tinyscrollbar();
    $('#robokassaSubmit').button();

    getSettings();
    //email = $( "#email" ),
    // password = $( "#password" ),

    //allFields = $( [] ).add( name ).add( email ).add( password ),

//by wobot dev


    function checkLength( o, n, min, max, tip ) {
        //alert(tip);
        if ( o.val().trim().length > max || o.val().trim().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( n, tip );
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp( o, regexp, n, tip ) {
        var test = o.val().match(regexp);
        if ( test!=null ) {
            o.addClass( "ui-state-error" );
            updateTips( n, tip );
            return false;
        } else {
            return true;
        }
    }

    function isValidDate(controlName, format){
        var isValid = true;

        try{
            jQuery.datepicker.parseDate(format, jQuery('#' + controlName).val(), null);

        }
        catch(error){

            isValid = false;
        }

        return isValid;
    }

function isValidURL(url){
return /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/.test(url);
}

//end

    

	createDropDown("order-by");

	$("#logo a").attr("href",inernalURL_themesList);

	$(".btn-all")
		.css("cursor","pointer")
		.click( function() {
				$(".btn-ready").removeClass("selected");
				$(".btn-all").addClass("selected");
				$(".FL[real='real']").fadeIn(200);
		});


	$(".btn-ready")
		.css("cursor","pointer")
		.click( function() {
				$(".btn-all").removeClass("selected");
				$(".btn-ready").addClass("selected");
				$(".FL[completed='0']").fadeOut(200);
		});

    $("#yourRes")
    .click (function()
    {
       //$("#add_src_url").clone().val('').appendTo("#addMoreUrlCont");
        

       $("#scrollbar1").slideToggle('slow');
        $('#scrollbar1').tinyscrollbar_update();

    });



    $("#robokassaSubmit").click(function() {

        getSignature($("#billingSum").val());


    });

	$("#dialog-newTheme").dialog({

		modal: true,
		open: function(event, ui){

            $("#dialog-newTheme-datepicker1").datepicker( "setDate" , "0");
            $("#dialog-newTheme-datepicker2").datepicker( "setDate" , "+1m");
            $("#tip-cont-1").hide();
            $("#tip-cont-2").hide();


            if (av_order<1)
            {
                $(":button:contains('Добавить')").attr("disabled",true).addClass("ui-state-disabled");
            }
            else
            {
                $("#dialog-newTheme-message").hide();
                $("#dialog-newTheme-hide").show();
                $("#dialog-newTheme").dialog("option", "position", "center");
            }
        },
        close: function(event, ui)
        {
            $(":button:contains('Добавить')").attr("disabled",false).removeClass("ui-state-disabled");
            $("#reset")[0].reset();
            allFields.removeClass( "ui-state-error" );
            allTips.text('');
        },
        buttons: {
			"Добавить": function() {
                var bValid1 = true;
                var bValid2 = true;
                allFields.removeClass( "ui-state-error" );
                allTips.text('');
                $("#tip-cont-1").hide();
                $("#tip-cont-2").hide();

                bValid1 = bValid1 && checkLength( newThemeName, "Длина названия должна быть от 3 до 50 символов.", 3, 50, "#newTheme-name-tip" );

                if (!isValidDate("dialog-newTheme-datepicker1", "dd.mm.yy"))
                {
                    newThemeDate1.addClass( "ui-state-error" );
                    updateTips("Неверный формат даты." ,"#newTheme-name-tip");
                    bValid1 = false;
                }
                if (!isValidDate("dialog-newTheme-datepicker2", "dd.mm.yy"))
                {
                    newThemeDate2.addClass( "ui-state-error" );
                    updateTips("Неверный формат даты." ,"#newTheme-name-tip");
                    bValid1 = false;
                }
                if ($("#dialog-newTheme-datepicker1").val() > $("#dialog-newTheme-datepicker2").val())
                {
                    updateTips("Окончание периода должно быть после или в день начала." ,"#newTheme-name-tip");
                    newThemeDate1.addClass( "ui-state-error" );
                    newThemeDate2.addClass( "ui-state-error" );
                    bValid1 = false;
                }

                if (bValid1) $("#tip-cont-1").hide(); else $("#tip-cont-1").show();

                bValid2 = bValid2 && checkLength( keywordOr, "Длина слова должна быть от 3 символов.", 3, 255, "#newTheme-kwrd-tip");
                bValid2 = bValid2 && checkRegexp(keywordOr, /(,{1}|^)([A-Za-zА-Яа-я0-9 ]*"{1}[A-Za-zА-Яа-я0-9-&. ]*(,{1}|$))/, "Неккоректно составлен список слов, проверьте правильность ввода.", "#newTheme-kwrd-tip");

                if (keywordAnd.val().length > 0)
                {
                    bValid2 = bValid2 && checkLength( keywordAnd, "Длина слова должна быть от 3 символов.", 3, 255, "#newTheme-kwrd-tip");
                    bValid2 = bValid2 && checkRegexp(keywordAnd, /(,{1}|^)([A-Za-zА-Яа-я0-9 ]*"{1}[A-Za-zА-Яа-я0-9-&. ]*(,{1}|$))/, "Неккоректно составлен список обязательных слов, проверьте правильность ввода.", "#newTheme-kwrd-tip");
                }
                if (keywordNot.val().length > 0)
                {
                    bValid2 = bValid2 && checkLength( keywordNot, "Длина слова должна быть от 3 символов.", 3, 255, "#newTheme-kwrd-tip");
                    bValid2 = bValid2 && checkRegexp(keywordNot, /(,{1}|^)([A-Za-zА-Яа-я0-9 ]*"{1}[A-Za-zА-Яа-я0-9-&. ]*(,{1}|$))/, "Неккоректно составлен список стоп-слов, проверьте правильность ввода.", "#newTheme-kwrd-tip");
                }

                if (!bValid2) $("#tip-cont-2").show();

                if (bValid1 && bValid2)
                {
                    var status = addTheme(
					$("#dialog-newTheme-name").val(),
					$("#dialog-newTheme-keywordOr").val(),
					$("#dialog-newTheme-keywordAnd").val(),
					$("#dialog-newTheme-keywordNot").val(),
					$("#dialog-newTheme-datepicker1").val(),
					$("#dialog-newTheme-datepicker2").val()
				);
                }
			},
			"Отменить": function() {
                $("#reset")[0].reset();
                $(this).dialog("close");
            }
		} ,

		draggable: false,
		resizable: false,
		minWidth: 700,
		maxWidth: 700,
        
				autoOpen: false
	});

	$("#dialog-Settings").dialog({
		modal: true,
		open: function() {

            $("#tip-cont-4").hide();
            getSettings();
            getBilling();
            $( '#radio' ).buttonset();
            
           // $('#radio').button("refresh");

	    },
		buttons: {
			"Сохранить": function() {

                allSettings.removeClass( "ui-state-error" );
                settingsTips.text('');
                $("#tip-cont-4").hide();

                bValid = true;

                bValid = bValid && checkLength( settingsName, "Длина имени должна быть от 4 до 50 символов.", 3, 50, "#dialog-Settings-tip" );
                //bValid = bValid && checkLength( settingsCompany, "Длина названия должна быть от 3 до 50 символов.", 3, 50, "#dialog-Settings-company-tip" );

                if (settingsPassnew.val() != settingsPasscheck.val())
                {
                    settingsPasscheck.addClass("ui-state-error");
                    updateTips("Пароли не совпадают.", $("#dialog-Settings-tip"));
                    bValid = false;

                }

                var mailFreq = $('input[name=radio]:checked').val();


                if (bValid)
                {
                saveSettings(settingsName.val(),
                    settingsCompany.val(),
                    settingsPassnew.val(),
                    settingsPasscheck.val(),
                    settingsEmail.val(),
                    mailFreq);
                //$(this).dialog("close");
                }
                else
                {
                    $("#tip-cont-4").show();
                }

/*
settingsName
settingsCompany
settingsPassnew
settingsPasscheck
settingsEmail 
				addTheme(
					$("#dialog-Settings-email").val(),
					$("#dialog-Settings-digest-daily").val(),
					$("#dialog-Settings-digest-weekly").val(),
					$("#dialog-Settings-digest-monthly").val(),
					$("#dialog-Settings-digest-disable").val()
				);*/

			},
			"Отменить": function() { $(this).dialog("close");}
		} ,

		draggable: false,
		resizable: false,
		minWidth: 700,
		maxWidth: 700,
				autoOpen: false
	});

	$("#dialog-newResource").dialog({
		modal: true,
        open: function() {

                getResourceList();
            $('#scrollbar1').tinyscrollbar_update();
            $("#tip-cont-3").hide();
            $('#scrollbar1').show();
            $("#dialog-newResource").dialog("option", "position", "center");
            $('#scrollbar1').hide();


        },
        close: function(){
                $("#add_src_url").val('');
                $("#add_src_url").removeClass( "ui-state-error" );
                $("#newUrlTip").text('');

            },
		buttons: {
			"Добавить": function() {
                $("#tip-cont-3").hide();
                addResource($("#add_src_url").val());
            },
			"Отменить": function() {  $(this).dialog("close");}


            
		} ,
		draggable: false,
		resizable: false,
		minWidth: 500,
		maxWidth: 500,
		autoOpen: false

	});



	$("#dialog-newTheme-datepicker1").datepicker({dateFormat: "dd.mm.yy"});
	$("#dialog-newTheme-datepicker2").datepicker({dateFormat: "dd.mm.yy"});




	$(".sort .btn a").click(function() {
		$(".dropdown dd ul").toggle();
		return false;
	});

	loadContent();
});
