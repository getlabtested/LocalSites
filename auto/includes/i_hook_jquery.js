   function insertAtCaret(areaId,text) { var txtarea = document.getElementById(areaId); var scrollPos = txtarea.scrollTop; var strPos = 0; var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false ) ); if (br == "ie") { txtarea.focus(); var range = document.selection.createRange(); range.moveStart ('character', -txtarea.value.length); strPos = range.text.length; } else if (br == "ff") strPos = txtarea.selectionStart; var front = (txtarea.value).substring(0,strPos); var back = (txtarea.value).substring(strPos,txtarea.value.length); txtarea.value=front+text+back; strPos = strPos + text.length; if (br == "ie") { txtarea.focus(); var range = document.selection.createRange(); range.moveStart ('character', -txtarea.value.length); range.moveStart ('character', strPos); range.moveEnd ('character', 0); range.select(); } else if (br == "ff") { txtarea.selectionStart = strPos; txtarea.selectionEnd = strPos; txtarea.focus(); } txtarea.scrollTop = scrollPos; } 
   
   $("#id_add_custom_field_button").click(function(){
		 $('#id_custom_fields tr:last').after('<tr><td  align=left style="font-size:13px;"><img class=remove_custom_field onClick="$(this).parent().parent().remove();" src="./../nav/remove.png" style="cursor:pointer;">&nbsp;<input size=28 name="custom_field_name[]" value=""></td><td  align=right style="font-size:13px;"><input size=35 name="custom_field_value[]" value=""></td></tr>');
	});

	$("#id_add_button_remote_publishing_email").click(function(){

		 $('#id_table_remote_publishing_email tr:last').after("<tr><td valign=top align=left style=\"font-size:13px;\">"
											+"<img onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">&nbsp;"
											+"<input size=26 class='class_remote_publishing_email' name=\"remote_publishing_api_email[]\" value=''></td>"
											+"<td valign=top align=left style=\"font-size:13px;\">"
											+"&nbsp;<input size='26' name=\"remote_publishing_api_email_footer[]\" value=''>"
										 +" </td></tr>");
	});
	
	$("#id_add_button_remote_publishing_bs").click(function(){
		 $('#id_table_remote_publishing_bs tr:last').after("<tr><td  align=left style=\"font-size:13px;\"><img onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">&nbsp;<input size=70 name=\"remote_publishing_api_bs[]\" value=''></td></tr>");
	});
	
	$("#id_add_button_remote_publishing_xmlrpc").click(function(){
		var cid =  $('.class_remote_publishing_xmlrpc').length;
		if (cid==0){ var nid = cid; }
		else { var nid = cid++;}
		 $('#id_table_remote_publishing_xmlrpc tr:last').after("<tr><td  align=left style=\"font-size:13px;\"><img class=remove_custom_field onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">&nbsp;<input size=54 name=\"remote_publishing_api_xmlrpc[]\" class='class_remote_publishing_xmlrpc' value=''></td><td  align=left style='font-size:13px;'>&nbsp;<input type=checkbox name='remote_publishing_api_xmlrpc_spin_"+nid+"[]' value='on' ></td></tr>");
	});
	
	$("#id_add_button_remote_publishing_pp").click(function(){
		 $('#id_table_remote_publishing_pp tr:last').after("<tr><td valign=top align=left style=\"font-size:13px;\"><img onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">&nbsp;<input size=25 name=\"remote_publishing_api_pp_email[]\" class='class_remote_publishing_pixelpipe' value=''></td><td><textarea name='remote_publishing_api_pp_routing[]'></textarea></td></tr>");
	});
	
	$("#id_add_custom_filters_button").click(function(){
		 var selects_cats = $('#id_td_selects_category').clone().html(); 

		 $('#id_table_autocategorize tr:last').after('<tr class=\'class_tr_autocategorization_custom_filters\'><td style="font-size:12px;"><img onClick="$(this).parent().parent().remove();" src="./../nav/remove.png" style="cursor:pointer;">&nbsp; <input name=\'autocategorize_filter_keywords[]\' value=\'\' size=16></td><td style="font-size:12px;" id="id_td_autocategory_filter_categories">'+selects_cats+'<td></td></tr>');
		$('#id_td_autocategory_filter_categories select').attr('name', "autocategorize_filter_categories[]");
	});
   
   $('#id_select_autocategorization_method').change(function(){
	   var input =$(this).val();
	   //alert(1);
	   if (input=='4')
	   {
			$('.class_tr_autocategorize_custom_filters').show();
			$('.class_tr_autocategorize_custom_filters_list').hide();
	   }
	   else if(input=='5')
	   {
			$('.class_tr_autocategorize_custom_filters').hide();
			$('.class_tr_autocategorize_custom_filters_list').show();
	   }
	   else
	   {
			$('.class_tr_autocategorize_custom_filters').hide();
			$('.class_tr_autocategorize_custom_filters_list').hide();
	   }
   });
   
   $('#id_select_autotag_method').change(function(){
	   var input =$(this).val();
	   if (input=='3'||input=='4')
	   {
			$('.class_tr_autotag_custom_tags').show();
	   }
	   else
	   {
			$('.class_tr_autotag_custom_tags').hide();
	   }
   });
	
	
   $("#id_dialog_image").dialog({
		autoOpen:false,
		 buttons: { 						
			 'Hook': function(){
			        var srcs = new Array();
					$('.grid_pic[border=3]').each(function(){
					   var src = $(this).attr('src');
					   srcs[srcs.length] = $(this).attr('src');
					});
					var images = srcs.join(',');
					//var images = '1,2,3';
					var source = $('#id_image_source').val();
					switch(source)
					{
						case "Local Folder":
							$('#id_image_content_local_folder_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
							var max_width = $('#id_image_local_max_width').val();
							var max_height = $('#id_image_local_max_height').val();
							var img_float = $('#id_image_local_float').val();
							var img_class = $('#id_image_local_class').val();
							break;
						case "Flickr":
							$('#id_image_content_flickr_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
							var max_width = $('#id_image_flickr_max_width').val();
							var max_height = $('#id_image_flickr_max_height').val();
							var img_float = $('#id_image_flickr_float').val();
							var img_class = $('#id_image_flickr_class').val();
							break;
						case "Picasa":
							$('#id_image_content_picasa_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
							var max_width = $('#id_image_picasa_max_width').val();
							var max_height = $('#id_image_picasa_max_height').val();
							var img_float = $('#id_image_picasa_float').val();
							var img_class = $('#id_image_picasa_class').val();
							break;
					}
					
					var hook_content = ' [hook type="image" pool="'+images+'" css="'+img_class+'" max_width="'+max_width+'" max_height="'+max_height+'" float="'+img_float+'"]';
			        insertAtCaret('id_post_template',hook_content);
					 $("#id_dialog_image").dialog('close');
					 $(this).dialog("close");
			 }
		 },
		 close: function() {
					$('#id_image_content_local_folder_preview').empty();
					$('#id_image_content_local_folder_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_local><span>Import Images</span></button><br><br></center>');
					$('#id_image_content_flickr_preview').empty();
					$('#id_image_content_flickr_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_flickr><span>Import Images</span></button><br><br></center>');
					$('#id_image_content_picasa_preview').empty();
					$('#id_image_content_picasa_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_picasa><span>Import Images</span></button><br><br></center>');
				
				},
		 width: 'auto',
		 height: 'auto',
		 hide: 'slide',
		 modal:true,
		 
		 title: 'Image Hook Settings'
   });
   
   $("#id_dialog_video").dialog({
		autoOpen:false,
		 buttons: { 						
			 'Hook': function(){					
			        var srcs = new Array();
					$('.grid_pic[border=3]').each(function(){
					   var src = $(this).attr('alt');
					   srcs[srcs.length] = $(this).attr('alt');
					});
					var videos = srcs.join(',');
					$('#id_video_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
					var width = $('#id_video_max_width').val();
					var height = $('#id_video_max_height').val();
					var img_class = $('#id_video_class').val();
					var descriptions = $('#id_video_include_description');
					if (descriptions.is(':checked')) 
					{
					   descriptions = 'on';
					}
					else
					{
						descriptions = 'off';
					}
					var hook_content = ' [hook type="video" pool="'+videos+'" css="'+img_class+'" width="'+width+'" height="'+height+'" descriptions="'+descriptions+'"]';
			        insertAtCaret('id_post_template',hook_content);
					$("#id_dialog_video").dialog('close');
			 }
		 },
		 close: function() {
					$('#id_video_preview').empty();
					$('#id_video_preview').html('<br><br><br><br><br><br><br><center><button class="rounded" id=id_video_button_display><span>Import Video</span></button><br><br></center>');
				},
		 width: '500',
		 height: 'auto',
		 hide: 'slide',
		 modal:true,
		 
		 title: 'Youtube Hook Settings'
   });
   
   $("#id_dialog_phpBay").dialog({
		autoOpen:false,
		 buttons: { 						
			 'Hook': function(){					
			       
					
					var keywords = $('#id_phpbay_keywords').val();
					var limit = $('#id_phpbay_limit').val();
					var category = $('#id_phpbay_category').val();
					var exclude = $('#id_phpbay_exclude').val();
					var zip_code = $('#id_phpbay_zip_code').val();
					var seller_id= $('#id_phpbay_seller_id').val();
					var minimum_price = $('#id_phpbay_minimum_price').val();
					var maximum_price = $('#id_phpbay_maximum_price').val();
					var minimum_bid = $('#id_phpbay_minimum_bid').val();
					var maximum_bid = $('#id_phpbay_maximum_bid').val();
					var buy_it_now = $('#id_phpbay_buy_it_now').val();
					var campaign_id = $('#id_phpbay_campaign_id').val();
					var sort_order = $('#id_phpbay_sort_order').val();
					var country = $('#id_phpbay_country').val();
					var collumns = $('#id_phpbay_collumns').val();
					var free_shipping = $('#id_phpbay_free_shipping').val();
					var paging_override = $('#id_phpbay_paging_override').val();
					var items_per_page = $('#id_phpbay_items_per_page').val();
					
					
					var hook_content = ' [phpbay]'+keywords+','+limit+','+category+','+exclude+','+zip_code+','+seller_id+','+minimum_price+','+maximum_price+','+minimum_bid+','+maximum_bid+','+buy_it_now+','+campaign_id+','+sort_order+','+country+','+collumns+','+free_shipping+','+paging_override+','+items_per_page+'[/phpbay]';
			        insertAtCaret('id_post_template',hook_content);
				    $("#id_dialog_phpBay").dialog('close');
			 }
		 },
		 close: function() {
					},
		 width: '700',
		 height: 'auto',
		 hide: 'slide',
		 modal:true,
		 title: 'phpBay Hook Settings'
   });
   
   $("#id_dialog_phpZon").dialog({
		autoOpen:false,
		 buttons: { 						
			 'Hook': function(){					
			       
					
					var keywords = $('#id_phpzon_keywords').val();
					var number = $('#id_phpzon_number').val();
					var searchindex = $('#id_phpzon_searchindex').val();
					var browsenodeid = $('#id_phpzon_browsenodeid').val();
					var asin = $('#id_phpzon_asin').val();
					var sort = $('#id_phpzon_sort').val();
					var country = $('#id_phpzon_country').val();
					var trackingid = $('#id_phpzon_trackingid').val();
					var minimumprice = $('#id_phpzon_minimumprice').val();
					var maximumprice = $('#id_phpzon_maximumprice').val();
					var merchantid = $('#id_phpzon_merchantid').val();
					var manufacturer = $('#id_phpzon_manufacturer').val();
					var publisher = $('#id_phpzon_publisher').val();
					var condition = $('#id_phpzon_condition').val();
					var debug = $('#id_phpzon_debug').val();
					var maxresults = $('#id_phpzon_maxresults').val();
					var maxreviews = $('#id_phpzon_maxreviews').val();
					var description = $('#id_phpzon_description').val();
					var truncate = $('#id_phpzon_truncate').val();
					var collumns = $('#id_phpzon_collumns').val();
					var paging = $('#id_phpzon_paging').val();
					var reviewsort = $('#id_phpzon_reviewsort').val();
					var templatename = $('#id_phpzon_templatename').val();
					
					var hook_content = " [phpzon ";
					
					if (keywords)	{ 	hook_content = hook_content + ' keywords="'+keywords+'" '; }
					if (number)	{ 	hook_content = hook_content + ' num="'+number+'" '; }
					if (searchindex)	{ 	hook_content = hook_content + ' searchindex="'+searchindex+'" '; }
					if (browsenodeid)	{ 	hook_content = hook_content + ' browsenodeid="'+browsenodeid+'" '; }
					if (asin)	{ 	hook_content = hook_content + ' asin="'+asin+'" '; }
					if (sort)	{ 	hook_content = hook_content + ' sort="'+sort+'" '; }
					if (country)	{ 	hook_content = hook_content + ' country="'+country+'" '; }
					if (trackingid)	{ 	hook_content = hook_content + ' trackingid="'+trackingid+'" '; }
					if (minimumprice)	{ 	hook_content = hook_content + ' minimumprice="'+minimumprice+'" '; }
					if (maximumprice)	{ 	hook_content = hook_content + ' maximumprice="'+maximumprice+'" '; }
					if (merchantid)	{ 	hook_content = hook_content + ' merchantid="'+merchantid+'" '; }
					if (manufacturer)	{ 	hook_content = hook_content + ' manufacturer="'+manufacturer+'" '; }
					if (publisher)	{ 	hook_content = hook_content + ' publisher="'+publisher+'" '; }
					if (condition)	{ 	hook_content = hook_content + ' condition="'+condition+'" '; }
					if (debug)	{ 	hook_content = hook_content + ' debug="'+debug+'" '; }
					if (maxresults)	{ 	hook_content = hook_content + ' maxresults="'+maxresults+'" '; }
					if (maxreviews)	{ 	hook_content = hook_content + ' maxreviews="'+maxreviews+'" '; }
					if (description)	{ 	hook_content = hook_content + ' description="'+description+'" '; }
					if (truncate)	{ 	hook_content = hook_content + ' truncate="'+truncate+'" '; }
					if (collumns)	{ 	hook_content = hook_content + ' collumns="'+collumns+'" '; }
					if (paging)	{ 	hook_content = hook_content + ' paging="'+paging+'" '; }
					if (reviewsort)	{ 	hook_content = hook_content + ' reviewsort="'+reviewsort+'" '; }
					if (templatename)	{ 	hook_content = hook_content + ' templatename="'+templatename+'" '; }
					hook_content = hook_content + ']';
					
					//var hook_content = ' [phpzon keywords="'+keywords+'" num="'+number+'" searchindex="'+searchindex+'" browsenodeid="'+browsenodeid+'" asin="'+asin+'" sort="'+sort+'" country="'+country+'" trackingid="'+trackingid+'" minimumprice="'+minimumprice+'" maximiumprice="'+maximumprice+'", merchantid="'+merchantid+'" manufacturer="'+manufacturer+'" publisher="'+publisher+'" condition="'+condition+'" debug="'+debug+'" maxresults="'+maxresults+'" maxreviews="'+maxreviews+'" description="'+description+'" truncate="'+truncate+'" collumns="'+collumns+'" paging="'+paging+'" reviewsort="'+reviewsort+'" templatename="'+templatename+'"]';
			        insertAtCaret('id_post_template',hook_content);
				    $("#id_dialog_phpZon").dialog('close');
			 }
		 },
		 close: function() {
					},
		 width: '700',
		 height: 'auto',
		 hide: 'slide',
		 modal:true, 
		 title: 'phpZon Hook Settings'
   });
   
   $("#id_dialog_wpMage").dialog({
		autoOpen:false,
		 buttons: { 						
			 'Hook': function(){					
			       
					var keywords = $('#id_mage_keywords').val();
					var token_type = $('#id_mage_token_type').val();
					var source = $('#id_mage_source_primary').val();
					var language = $('#id_mage_language').val();
					var results = $('#id_mage_results').val();
					var backup_source = $('#id_mage_source_backup').val();
					
					if (token_type=='mage') {
						var hook_content = ' [' + token_type + ' lang="'+language+'" source="' + source + '" results="' + results + '" backup="' + backup_source + '"]' + keywords + '[/' + token_type +']';
					}
					else { 
						var hook_content = ' [' + token_type + ' source="' + source + '" results="' + results + '"]' + keywords + '[/' + token_type +']';
					}
					
					insertAtCaret('id_post_template',hook_content);
				    $("#id_dialog_wpMage").dialog('close');
			 }
		 },
		 close: function() {
					},
		 width: '530',
		 height: 'auto',
		 hide: 'slide',
		 modal:true,
		 title: 'wpMage Hook Settings'
   });
   
   
   $("#id_dialog_wpRobot").dialog({
		autoOpen:false,
		 buttons: { 						
			 'Hook': function(){					
			       
					var keywords = $('#id_wprobot_keywords').val();
					var source = $('#id_wprobot_source').val();
					var results = $('#id_wprobot_results').val();
					var start = $('#id_wprobot_start').val();
					
					
					var hook_content = '{wprobot module="' + source + '" keyword="' + keywords + '"  start="' + start + '" num="' + results + '"}';
					
					
					insertAtCaret('id_post_template',hook_content);
				    $("#id_dialog_wpRobot").dialog('close');
			 }
		 },
		 close: function() {
					},
		 width: '530',
		 height: 'auto',
		 hide: 'slide',
		 modal:true,
		 title: 'wpRobot Hook Settings'
   });
   
   $("#id_dialog_prosperent").dialog({
		autoOpen:false,
		 buttons: { 						
			 'Hook': function(){					
			       
					
					var channel_id = $('#id_prosperent_channel_id').val();
					var debug_mode = $('#id_prosperent_debug_mode').val();
					var keyword = $('#id_prosperent_keyword').val();
					var keyword_use_search_referrer = $('#id_prosperent_keyword_use_search_referrer').val();
					var keyword_use_title = $('#id_prosperent_keyword_use_title').val();
					var keyword_use_title_as_backup = $('#id_prosperent_keyword_use_title_as_backup').val();
					var keyword_append_global = $('#id_prosperent_keyword_append_global').val();
					var templates = $('#id_prosperent_templates').val();
					var use_replace_price = $('#id_prosperent_use_replace_price').val();
					var replace_price_text = $('#id_prosperent_replace_price_text').val();
					var use_pagination = $('#id_prosperent_use_pagination').val();
					var limit_page = $('#id_prosperent_limit_page').val();
					var link_no_follow = $('#id_prosperent_link_no_follow').val();
					var link_new_page = $('#id_prosperent_link_new_page').val();
					
					
					var hook_content = '[wpp channel_id="' + channel_id + '" debug_mode="' + debug_mode + '"  keyword="' + keyword + '" keyword_use_search_referrer="' + keyword_use_search_referrer + '" keyword_use_title="' + keyword_use_title +'" keyword_use_title_as_backup="' + keyword_use_title_as_backup +'" keyword_append_global="' + keyword_append_global +'" templates="' + templates +'" use_replace_price="' + use_replace_price + '" replace_price_text="' + replace_price_text + '" use_pagination="' + use_pagination + '" limit_page="' + limit_page + '" link_no_follow="' + link_no_follow +'" link_new_page="' + link_new_page +'"]';
					insertAtCaret('id_post_template',hook_content);
				    $("#id_dialog_prosperent").dialog('close');
			 }
		 },
		 close: function() {
					},
		 width: '700',
		 height: 'auto',
		 hide: 'slide',
		 modal:true,
		 title: 'phpBay Hook Settings'
   });
   
   
   $('#id_hook_image').live('click', function(){
		$("#id_dialog_image").dialog('open');
   });
   
   $('#id_hook_video').live('click', function(){
		$("#id_dialog_video").dialog('open');
   });
   
   $('#id_hook_ebay_autodetect').live('click', function(){
		var hook_content = '{ebay:5336672902:5:400px}';
		insertAtCaret('id_post_template',hook_content);
	}); 
   
   $('#id_hook_flickr_autodetect').live('click', function(){
		var hook_content = '{flickr:1:275px:300px}';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_googleimg_autodetect').live('click', function(){
		var hook_content = '{googleimg:keyword here:1:275px:300px:first}';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_youtube_autodetect').live('click', function(){
		var hook_content = '{youtube:1:keywords here:optional,exclude,keywords,here}';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_rss_random').live('click', function(){
		var hook_content = '{rss:urlhere:1:<div><p><span><font><br><ul><ol><li><b><strong><i><u><table><img><a>}';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_ifnoimage').live('click', function(){
		var hook_content = '[IFNOIMAGE]{googleimg:%title_filtered%:1:275px:300px:first}[/IFNOIMAGE]';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_insert').live('click', function(){
		var hook_content = '[insert:middle]content here[/insert]';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_substr').live('click', function(){
		var hook_content = '[substring:0:500]content here[/substring]';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_rand').live('click', function(){
		var hook_content = '[rand]shortcode here||shortcode here[/rand]';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_amazon_content_autodetect').live('click', function(){
		var hook_content = '{amazon:4:content:%title_filtered%}';
		insertAtCaret('id_post_template',hook_content);
   }); 
   
   $('#id_hook_amazon_widget_autodetect').live('click', function(){
		var hook_content = '{amazon:4:widget:%title_filtered%}';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_hook_spyntax_title').live('click', function(){
		var hook_content = '[spyntax]INSERT CONTENT HERE[/spyntax]';
		insertAtCaret('id_title_template',hook_content);				
   });
   
   $('#id_hook_spyntax_post').live('click', function(){
		var hook_content = '[spyntax]INSERT CONTENT HERE[/spyntax]';
		insertAtCaret('id_post_template',hook_content);
					
   });
   
   $('#id_hook_date_title').live('click', function(){
		var hook_content = '[date format="F j, Y"]';
		insertAtCaret('id_title_template',hook_content);				
   });
   
    $('#id_hook_date_post').live('click', function(){
		var hook_content = '[date format="F j, Y"]';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_hook_phpBay').live('click', function(){
		$("#id_dialog_phpBay").dialog('open');
   });
   
   $('#id_hook_phpZon').live('click', function(){
		$("#id_dialog_phpZon").dialog('open');
   });
   
   $('#id_hook_wpMage').live('click', function(){
		$("#id_dialog_wpMage").dialog('open');
   });
   
   $('#id_hook_wpRobot').live('click', function(){
		$("#id_dialog_wpRobot").dialog('open');
   });
  
   $('#id_hook_prosperent').live('click', function(){
		$("#id_dialog_prosperent").dialog('open');
   });
   
   $('#id_titlevar_keyword').live('click', function(){
		var hook_content = '%keyword%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_postvar_keyword').live('click', function(){
		var hook_content = '%keyword%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_titlevar_title').live('click', function(){
		var hook_content = '%keyword%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_title_filtered').live('click', function(){
		var hook_content = '%title_filtered%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_link').live('click', function(){
		var hook_content = '%link%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_campaign_name').live('click', function(){
		var hook_content = '%campaign_name%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_campaign_query').live('click', function(){
		var hook_content = '%campaign_query%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_tag_title').live('click', function(){
		var hook_content = '%tag_title%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_tag_postbody').live('click', function(){
		var hook_content = '%tag_postbody%';
		insertAtCaret('id_title_template',hook_content);			
   });
   
   $('#id_postvar_title').live('click', function(){
		var hook_content = '%title%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_title_filtered').live('click', function(){
		var hook_content = '%title_filtered%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_postbody').live('click', function(){
		var hook_content = '%postbody%';
		insertAtCaret('id_post_template',hook_content);			
   });
   
   $('#id_postvar_link').live('click', function(){
		var hook_content = '%link%';
		insertAtCaret('id_post_template',hook_content);			
   });  
   
   $('#id_postvar_campaign_name').live('click', function(){
		var hook_content = '%campaign_name%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_campaign_query').live('click', function(){
		var hook_content = '%campaign_query%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_image_1').live('click', function(){
		var hook_content = '%image_1%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_image_2').live('click', function(){
		var hook_content = '%image_2%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_author_name').live('click', function(){
		var hook_content = '%author_name%';
		insertAtCaret('id_post_template',hook_content);
   });
   
    $('#id_postvar_domain_name').live('click', function(){
		var hook_content = '%domain_name%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_blog_url').live('click', function(){
		var hook_content = '%blog_url%';
		insertAtCaret('id_post_template',hook_content);
   });
   
   $('#id_postvar_video_embed').live('click', function(){
		var hook_content = '%video_embed%';
		insertAtCaret('id_post_template',hook_content);		
   });
   
   $('#id_postvar_video_thumbnail').live('click', function(){
		var hook_content = '%video_thumbnail%';
		insertAtCaret('id_post_template',hook_content);			
   });
   
   $('#id_postvar_video_description').live('click', function(){
		var hook_content = '%video_description%';
		insertAtCaret('id_post_template',hook_content);			
   });
   
   $('#id_postvar_tag_title').live('click', function(){
		var hook_content = '%tag_title%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_tag_postbody').live('click', function(){
		var hook_content = '%tag_postbody%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_product_title').live('click', function(){
		var hook_content = '%amazon_product_title%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_price').live('click', function(){
		var hook_content = '%amazon_price%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_small_image_url').live('click', function(){
		var hook_content = '%amazon_small_image_url%';
		insertAtCaret('id_post_template',hook_content);				
   });
  
   $('#id_postvar_amazon_medium_image_url').live('click', function(){
		var hook_content = '%amazon_medium_image_url%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_large_image_url').live('click', function(){
		var hook_content = '%amazon_large_image_url%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_product_description').live('click', function(){
		var hook_content = '%amazon_product_description%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_product_features').live('click', function(){
		var hook_content = '%amazon_product_features%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_customer_review_content_1').live('click', function(){
		var hook_content = '%amazon_customer_review_content_1%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_customer_review_content_2').live('click', function(){
		var hook_content = '%amazon_customer_review_content_2%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_customer_review_content_3').live('click', function(){
		var hook_content = '%amazon_customer_review_content_3%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_customer_review_authorname_1').live('click', function(){
		var hook_content = '%amazon_customer_review_authorname_1%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
    $('#id_postvar_amazon_customer_review_authorname_2').live('click', function(){
		var hook_content = '%amazon_customer_review_authorname_2%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
    $('#id_postvar_amazon_customer_review_authorname_3').live('click', function(){
		var hook_content = '%amazon_customer_review_authorname_3%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
    $('#id_postvar_amazon_buyitnow_button').live('click', function(){
		var hook_content = '%amazon_buyitnow_button%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_brand_name').live('click', function(){
		var hook_content = '%amazon_brand_name%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_postvar_amazon_model').live('click', function(){
		var hook_content = '%amazon_model%';
		insertAtCaret('id_post_template',hook_content);				
   });
   
   $('#id_titlevar_amazon_model').live('click', function(){
		var hook_content = '%amazon_model%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_amazon_brand_name').live('click', function(){
		var hook_content = '%amazon_brand_name%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('#id_titlevar_amazon_price').live('click', function(){
		var hook_content = '%amazon_price%';
		insertAtCaret('id_title_template',hook_content);
   });
   
   $('.class_custom_var_post').live('click', function(event){
		var variable = event.target.id;
		insertAtCaret('id_post_template',variable);				
   });
   
   $('.class_custom_var_title').live('click', function(event){
		var variable = event.target.id;
		insertAtCaret('id_post_template',variable);				
   });
   //********************************************
   //Hook: Image ********************************
   //********************************************
       $('.grid_pic').live('click',function(){
			var cur_id = this.id.replace('id_grid_pic_','');
			var cur_border = $('#id_grid_pic_'+cur_id).attr('border');
			if (cur_border==3)
			{
				$('#id_grid_pic_'+cur_id).attr('border','0');
			}else
			{
			   $('#id_grid_pic_'+cur_id).attr('border','3');
			}
	   });
   
	   $('#id_image_source').val('Please Select Source');
	   $('#id_image_source').change(function(){
				var source = $('#id_image_source').val();
				switch (source){
					case "Local Folder":
						$('#id_image_content_flickr').fadeOut("fast", function(){
								$('#id_image_content_picasa').fadeOut("fast",function(){						
									$('#id_image_content_local_folder').fadeIn("slow");
								});
						});
						$('#id_image_content_flickr_preview').empty();
						$('#id_image_content_flickr_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_flickr><span>Import Images</span></button><br><br></center>');
						$('#id_image_content_picasa_preview').empty();
						$('#id_image_content_picasa_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_picasa><span>Import Images</span></button><br><br></center>');
						break;
					case "Flickr":					
						$('#id_image_content_local_folder').fadeOut("fast", function(){
								$('#id_image_content_picasa').fadeOut("fast",function(){						
									$('#id_image_content_flickr').fadeIn("slow");
								});
						});
						$('#id_image_content_local_folder_preview').empty();
						$('#id_image_content_local_folder_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_local><span>Import Images</span></button><br><br></center>');
						$('#id_image_content_picasa_preview').empty();
						$('#id_image_content_picasa_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_picasa><span>Import Images</span></button><br><br></center>');						
						break;
					case "Picasa":
						$('#id_image_content_local_folder').fadeOut("fast", function(){
								$('#id_image_content_flickr').fadeOut("fast",function(){						
									$('#id_image_content_picasa').fadeIn("slow");
								});
						});	
						$('#id_image_content_local_folder_preview').empty();
						$('#id_image_content_local_folder_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_local><span>Import Images</span></button><br><br></center>');
						$('#id_image_content_flickr_preview').empty();
						$('#id_image_content_flickr_preview').html('<br><br><br><br><br><center><button class="rounded" id=id_image_button_display_flickr><span>Import Images</span></button><br><br></center>');
						break;
				}
		});
		
		$('#id_image_button_display_local').live('click', function(){
			var loc = $('#id_image_local_path').val();
			$('#id_image_content_local_folder_preview').empty();
			$('#id_image_content_local_folder_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
			$.get('./../includes/photogrid/servlet.php', { mode: 'loc', loc: loc},  
				function(data){ 
					$('#id_image_content_local_folder_preview').empty();				
					document.getElementById('id_image_content_local_folder_preview').innerHTML = data;  
					$("#grid_slider").slider({
						value: 30,
						max: 100,
						min: 30,
						slide: function(event, ui) {
							$('#id_photos').css('font-size',ui.value+"px");
						}
						});			
					$("div#id_photos img").each(function() {
						var width = $(this).width() / 100 + "em";
						var height = $(this).height() / 100 + "em";
						$(this).css("width",width);
						$(this).css("height",height);
					});
				});
			
		});
		
		$('#id_image_button_display_flickr').live('click', function(){
			var keywords = $('#id_image_flickr_keywords').val();
			var max_results = $('#id_image_flickr_max_results').val();
			$('#id_image_content_flickr_preview').empty();
			$('#id_image_content_flickr_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
			$.get('./../includes/photogrid/servlet.php', { mode: 'flickr', keywords: keywords, max_results: max_results},  
				function(data){ 
					$('#id_image_content_flickr_preview').empty();				
					document.getElementById('id_image_content_flickr_preview').innerHTML = data;  
					$("#grid_slider").slider({
						value: 30,
						max: 100,
						min: 30,
						slide: function(event, ui) {
							$('#id_photos').css('font-size',ui.value+"px");
						}
						});
					$("div#id_photos img").each(function() {
						var width = $(this).width() / 100 + "em";
						var height = $(this).height() / 100 + "em";
						$(this).css("width",width);
						$(this).css("height",height);
					});
				});
			
		});
		
		$('#id_image_button_display_picasa').live('click', function(){
			var keywords = $('#id_image_picasa_keywords').val();
			var max_results = $('#id_image_picasa_max_results').val();
			$('#id_image_content_picasa_preview').empty();
			$('#id_image_content_picasa_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
			$.get('./../includes/photogrid/servlet.php', { mode: 'picasa', keywords: keywords, max_results: max_results},  
				function(data){ 
					$('#id_image_content_picasa_preview').empty();				
					document.getElementById('id_image_content_picasa_preview').innerHTML = data;  
					$("#grid_slider").slider({
						value: 30,
						max: 100,
						min: 30,
						slide: function(event, ui) {
							$('#id_photos').css('font-size',ui.value+"px");
						}
						});			
					$("div#id_photos img").each(function() {
						var width = $(this).width() / 100 + "em";
						var height = $(this).height() / 100 + "em";
						$(this).css("width",width);
						$(this).css("height",height);
					});
				});
			
		});
		
		$('#id_video_button_display').live('click', function(){
			var keywords = $('#id_video_keywords').val();
			var max_results = $('#id_video_max_results').val();
			$('#id_video_preview').empty();
			$('#id_video_preview').html('<br><br><br><br><center><img src="./../nav/loading.gif"></center>');
			$.get('./../includes/photogrid/servlet.php', { mode: 'youtube', keywords: keywords, max_results: max_results},  
				function(data){ 
					$('#id_video_preview').empty();				
					document.getElementById('id_video_preview').innerHTML = data;  
					
				});
			
		});
		
		$('#id_mage_token_type').change(function(){
				var source = $('#id_mage_token_type').val();
				switch (source){
					case "mage":
						var html = "<select name=mage id='id_mage_source_primary'><option value='article'>Article</option><option value='answers'>Answers</option><option value='flickr'>Flickr</option><option value='youtube'>Youtube</option><option value='tags'>Tags</option></select>";
						var backup = "<table width='100%'><td valign=top width=300><img src=\"./../nav/tip.png\" style=\"cursor:pointer;\" border=0 title=\"Select a backup source in case the primary source fails or leave blank for none.\">Backup Source:</td><td align=right valign=top><select name=mage id='id_mage_source_backup'><option value=0>None</option><option value='article'>Article</option><option value='answers'>Answers</option><option value='flickr'>Flickr</option><option value='youtube'>Youtube</option><option value='tags'>Tags</option></select></td></table>";
						$('#id_mage_source_options').html(html);
						$('#id_mage_backup').html(backup);
						break;
					case "affmage":					
						var html = "<select name=mage id='id_mage_source_primary'><option value='amazon'>Amazon</option><option value='chitika'>Chitka</option><option value='ebay'>Ebay</option><option value='overstock'>Overstock</option><option value='cj'>Commission Junkie</option><option value='clickbank'>Clickbank</option><option value='linkshare'>LinkShare</option></select>";
						var backup = "";
						$('#id_mage_source_options').html(html);
						$('#id_mage_backup').html(backup);
						break;
				}
		});