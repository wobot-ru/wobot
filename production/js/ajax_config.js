var
	ajaxURL_AddResource = "./api/0/addsource", 
	ajaxURL_AddTheme    = "./api/0/addorder",
	ajaxURL_GetResTable = "./api/0/get_src",
	ajaxURL_GetSettings = "./api/0/get_settings",
	ajaxURL_GetBilling = "./api/0/get_billing",
	ajaxURL_SaveSettings = "./api/0/save_settings",
	ajaxURL_ToneChange  = "./api/0/nastr",	// nastr
	ajaxURL_GetFullText = "./api/0/get_fulltext2",  // get_fulltext
	ajaxURL_SetFavourite= "./api/0/setfav",  // setfav
	ajaxURL_SetFavouriteAdv= "./api/0/setfav_adv",  // setfav_adv
	ajaxURL_SetSpam     = "./api/0/setspam2",	// setspam
	ajaxURL_SetSpam3     = "./api/0/setspam3",	// setspam
	ajaxURL_SetSpamAdv     = "./api/0/setspam_adv",	// setspam_adv
	ajaxURL_GetTags		= "./api/0/gettags",
  ajaxURL_SetTag		= "./api/0/settag2",
  ajaxURL_SetTagAdv		= "./api/0/settag_adv",
	ajaxURL_DelTag		= "./api/0/deltag2",
	//ajaxURL_DelTag		= "./AJAX/ok",
	ajaxURL_AddTag		= "./api/0/addtag2",
	ajaxURL_AddTagFull  = "./api/0/addtagfull",
	ajaxURL_EditTag		= "./api/0/edittag",
	ajaxURL_EditTagFull	= "./api/0/edittagfull",
	ajaxURL_ShareTag	= "./api/0/sharetag",
	ajaxURL_ToneChangeAdv  = "./api/0/nastr_adv",	// nastr
	ajaxURL_RandomTheme  = "./api/0/randomorder",
	ajaxURL_Orders      = "./api/0/redis_orders2",
	ajaxURL_Order       = "./api/0/redis_order2",
	ajaxURL_GraphData       = "./api/0/func_graph_data",
	ajaxURL_GetWnsi       = "./api/0/get_wnsi",
	ajaxURL_Filters     = "./api/0/redis_filters2",
	ajaxURL_Compare     = "./api/0/order_compare",
	ajaxURL_Comments    = "./api/0/comment",
	ajaxURL_CommentDup    = "./api/0/comment2dup",
	ajaxURL_CommentCount    = "./api/0/comment_count_by_date",
	ajaxURL_RemovePosts    = "./api/0/remove_posts",
	ajaxURL_setAdvSettings    = "./api/0/save_advsettings",
	ajaxURL_orderRemove    = "./api/0/order_remove",

	ajaxURL_GetPresets  = "./api/0/load_preset",		//load_preset
	ajaxURL_AddPreset   = "./api/0/save_preset",			//save_preset
	ajaxURL_LoadPreset  = "./api/0/get_preset",	//get_preset
	ajaxURL_DeletePreset= "./api/0/del_preset",			//del_preset
	ajaxURL_getThemeSettings= "./api/0/get_theme_settings",	
	ajaxURL_saveThemeSettings= "./api/0/save_theme_settings",
	ajaxURL_groupsAdd = "./api/0/groups_add",
	ajaxURL_groupsGet = "./api/0/groups_get",
	ajaxURL_groupsDel = "./api/0/groups_del",
	ajaxURL_groupsEdit = "./api/0/groups_edit",
	ajaxURL_groupsSearch = "./api/0/groups_search",
	ajaxURL_objectSearch = "./api/0/getobject",
	postURL_Export		= "./api/0/export3",
	postURL_Email		= "./api/0/export2",	
	ajaxURL_userSecList 		= "./api/0/user_sec_list ",
	postURL_userSecAdd 		= "./api/0/user_sec_add ",
	postURL_userSecDel 		= "./api/0/user_sec_del ",
	ajaxURL_sync			= "./api/0/sync";

	postURL_GetExports		= "./api/0/getExports",	
	postURL_AddExport		= "./api/0/addExport",

	postURL_GetHotkeys		= "./api/0/get_hotkeys",
	postURL_SaveHotkeys		= "./api/0/save_hotkeys",

  ajaxURL_GetServer  =  './api/0/getserver', // получение инфо о сервере
  postURL_AddReaction  =  './api/0/reaction_add', // добавление комментария

  postURL_ExportXls = './api/0/xlsx', // экспорт в XLSX
  postURL_ExportDocx = './api/0/docx', // экспорт отчетов в DOC
  postURL_ExportDoc = './api/0/doc', // экспорт упоминаний в DOC
  postURL_ExportAuthors = './api/0/getAuthors', // экспорт авторов в CSV
  postURL_ExportMentions = './api/0/getMentions', // экпорт упоминаний в CSV

  postURL_ExportXlsCompare = './api/0/xlsx_compare', // экпорт в XLSX сравнение

	inernalURL_themesList 	 = "themes_list.html",      
	inernalURL_logout		 = "logout.php",	
	inernalURL_accessSetup	= "access_setup.html",
	inernalURL_tariff		 = "tariff.php?tariff_id=",
	inernalURL_billing		 = "http://bmstu.wobot.ru/tools/payment/robokassa/billing.php",
	inernalURL_faq			 = "http://www.wobot.ru/faq",	
	inernalURL_themePage     = "theme_page.html#",
//	inernalURL_themesCompare = "diagramm_master.html",
	inernalURL_themesCompare = "theme_compare.html",
	inernalURL_messages      = "messages_list.html#",
	
	imgURL_themesRSS	     = "./api/0/rss?order_id=%order_id%",
	imgURL_themesGraph		 = "/img/graph/%order_id%_main_2.png",
	
	map_cities_count		 = 10;
	
	// %order_id% - макрос который будет заменяться
	themapage_Templates = {
		posts	: 	"/img/graph/%order_id%_main.png", // - кол-во постов
		aud		:	"/img/graph/%order_id%_aud.png" , // - аудитория
		eng		:   "/img/graph/%order_id%_eng.png" , // - вовлеченность
		src		:	"/img/graph/%order_id%_src.png" , // - кол-во ресурсов
		uniq    :   "/img/graph/%order_id%_uniq.png"  // - кол-во уникальных авторов
	};
	
	
