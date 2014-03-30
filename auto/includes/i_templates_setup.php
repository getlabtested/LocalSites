<div style="font-size:14px;width:500;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;margin-top:27px;" >

</div>
<div style="font-size:14px;width:500;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;" id='id_templating'>
	<h3 style='font-size:14px;'><a href="#">Title Template</a></h3>
	<div>
		<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;"> 
			<tr>
				<td valign='top' width=42 style="font-size:10px;">
				   Shortcodes:
				</td>
				 <td  valign=top  align=left valign=bottom style='font-size:10px;'>
					<a href='#spyntax'  class='token_items' id=id_hook_spyntax_title title='Add spinnable content to template.'>[Spyntax]</a>&nbsp;
					<a href='#date'  class='token_items' id=id_hook_date_title title='Add spinnable content to template.'>[Date]</a>	
				 </td>
			</tr>
			<tr>
				<td valign='top' style="font-size:10px;">
				   Variables:
				</td>
				<td valign=top  align=left valign=bottom style='font-size:10px;width:430px;'>
				<?php
					if ($module_type=='keywords')
					{
						?>
						<a href='#keyword'  class='token_items' id=id_titlevar_keyword title='Will call the keyword of focus from the keywords list.'>%keyword% &nbsp;&nbsp;&nbsp;</a>
						<?php
					}
					?>
					<a href='#article_title'  class='token_items' id=id_titlevar_title title='Will call the original title'>%title%</a> &nbsp;&nbsp;&nbsp;
					<a href='#title_filtered'  class='token_items' id=id_titlevar_title_filtered title='Calls title with common words stripped out.'>%title_filtered%</a> &nbsp;&nbsp;&nbsp;
					<a href='#article_link' class='token_items' id=id_titlevar_link title='Will call the orignal link'>%link% &nbsp;&nbsp;&nbsp;</a>
					<a href='#campaign_name' class='token_items' id=id_titlevar_campaign_name title='Will call the the name of the campaign'>%campaign_name% &nbsp;&nbsp;&nbsp;</a>
					<?php
					if ($module_type=='amazon'||$module_type=='yahoo'||$module_type=='video')
					{
					?>
					<a href='#campaign_query' class='token_items' id=id_titlevar_campaign_query title='Will call the query used in campaign setup. If Multiple queries are used then only the current query of focus will replace this token.'>%campaign_query% &nbsp;&nbsp;&nbsp;</a>
					<?php
					}
					?>
					<a href='#tag_title'  class='token_items' id=id_titlevar_tag_title title='Will generate a tag out of the title.'>%tag_title% &nbsp;&nbsp;&nbsp;</a>
					<a href='#tag_postbody'  class='token_items' id=id_titlevar_tag_postbody title='Will generate a tag out of the descripion.'>%tag_postbody% &nbsp;&nbsp;&nbsp;</a>
					<?php
					if ($module_type=='amazon')
					{
						?>
						<a href='#amazon_price'  class='token_items' id=id_titlevar_amazon_price title='Will call the price of the item'>%amazon_price% &nbsp;&nbsp;&nbsp;</a>
						<a href='#amazon_brand_name'  class='token_items' id=id_titlevar_amazon_brand_name title='This item is not garunteed to have content for every product.'>%amazon_brand_name% &nbsp;&nbsp;&nbsp;</a>
						<a href='#amazon_model'  class='token_items' id=id_titlevar_amazon_model title='This item is not garunteed to have content for every product.'>%amazon_model% &nbsp;&nbsp;&nbsp;</a>
						<?php
					}
					?>
					<?
					if ($module_type=='fileimport'&&$campaign_name=='csv_import')
					{
					?>
					<tr>
						<td valign='top' style="font-size:10px;">
						   CSV :
						</td>
						<td  valign=top align=left valign=bottom style='color:#444444;font-size:11;'>
							<?php 
							$lines = file("../my-csv-files/$campaign_source");
							
							$first_line = $lines[0];
							$collumns = explode($campaign_query,$first_line);
							foreach ($collumns as $k => $v)
							{
								$v = trim($v);
								echo "%{$v}% &nbsp;&nbsp;&nbsp;&nbsp;";
							}
							?>
						</td>
					</tr>
					<?php			
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan=2 align=right style="font-size:13px;">
					<textarea width=100% cols=71 rows=4 name='title_template' id=id_title_template class='class_template_box'><?php 
						if ($campaign_title_template)
						{
							echo $campaign_title_template;
						}
						else
						{
							echo $default_title_template;
						}
												
						?>
					</textarea>
				</td>
			</tr>
		</table>
	</div>
	<h3 style='font-size:14px;'><a href="#">Body Template</a></h3>
	<div>
		<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;"> 	
			<tr>
				<td valign='top' style="font-size:10px;">
				   Shortcodes:
				</td>
				 <td  valign=top  align=left valign=bottom style='font-size:10px;width:430px;'>
					<a href='#image'  class='token_items' id=id_hook_image title='Add random image to template.'>[Custom Images]</a>&nbsp;
					<a href='#youtube'  class='token_items' id=id_hook_video title='Add random youtube video to template by collecting a pool of youtube videos limited to a search paramater.'>[Custom Youtube]</a>&nbsp;
					<a href='#spyntax'  class='token_items' id=id_hook_spyntax_post title='Add spinnable content to template.'>[Spyntax]</a>&nbsp;	
					<a href='#date'  class='token_items' id=id_hook_date_post title='Add spinnable content to template.'>[Date]</a>&nbsp;
					<a href='./../includes/pdfs/Posting_Templates.pdf' target=_new>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Click here to read documentation on BlogSense's token and shortcodes.">
					</a>
					<br>
					<a href='#youtube_autodetect'  class='token_items' id=id_hook_youtube_autodetect title='Add random youtube video to template based on the title of the current post.'>[Youtube] &nbsp;</a>
					<a href='#amazon_autodetect'  class='token_items' id=id_hook_amazon_content_autodetect title='Auto search amazon.com using discovered tags. Display Image and Best Rated User Comment for Upselling.'>[Amazon:Review] &nbsp;</a>
					<a href='#amazon_autodetect'  class='token_items' id=id_hook_amazon_widget_autodetect title='Auto search amazon.com using discovered tags. Display in the form of a multicollumned widget. '>[Amazon:Widget] &nbsp;</a>
					<a href='#ebay_autodetect'  class='token_items' id=id_hook_ebay_autodetect title='Auto search ebay.com using discovered tags.'>[Ebay] &nbsp;</a>
					<a href='#flickr_autodetect'  class='token_items' id=id_hook_flickr_autodetect title='Auto search flikr for tag related items'>[Flickr] &nbsp;</a>
					<a href='#google_autodetect'  class='token_items' id=id_hook_googleimg_autodetect title='Auto search Google Images'>[GooleIMG] &nbsp;</a>
					<a href='#rss_random'  class='token_items' id=id_hook_rss_random title='Source random content items from an RSS feed.'>[RSS] &nbsp;</a>
					<a href='#insert'  class='token_items' id=id_hook_insert title='Inject content into the %postbody% content using special placement identifiers.'>[INSERT] &nbsp;</a>
					<a href='#substr'  class='token_items' id=id_hook_substr title='Trim content in various ways using the php substr() function. See pdf manual above for usage instructions. Example for turning full content into partial content: [substr:0:500]contenthere[/substr]'>[SUBSTRING] &nbsp;</a>
					<a href='#rand'  class='token_items' id=id_hook_rand title='Use this shortcode to randomize shortcodes. This feature is similiar to spyntax but with a slightly different forumla that helps preserve the shortcode formatting. See documentation above for more information (Click the question mark above to load documentation)'>[RAND] &nbsp;</a>
					<a href='#rand'  class='token_items' id=id_hook_ifnoimage title='If no image is detected in the postbody, whatever content is placed inbetween these tags will be inserted into the postbody. (Click the question mark above to load documentation)'>[IFNOIMAGE] &nbsp;</a>
					<a href='http://www.hatnohat.com/api/shortcodes/plugins/knol.google/shortcode_wizard.php?plugin=blogsense'  class='token_items' target='_blank' title='Know.Google.Com is an articles shortcode. Use it to generate an article. See documentation above for more information.'>[KNOL.GOOGLE] &nbsp;</a>
					<a href='http://www.hatnohat.com/api/shortcodes/plugins/articlebase/shortcode_wizard.php?plugin=blogsense'  class='token_items' target='_blank' title='Articlebase is an articles shortcode. Use it to generate an article. See documentation above for more information.)'>[ARTICLEBASE] &nbsp;</a>
					<a href='http://www.hatnohat.com/api/shortcodes/plugins/ezine/shortcode_wizard.php?plugin=blogsense'  class='token_items' target='_blank' title='Ezine is an articles shortcode. May require proxies. Use it to generate an article. See documentation above for more information.'>[EZINE] &nbsp;</a>
					<a href='http://www.hatnohat.com/api/shortcodes/plugins/associatedcontent/shortcode_wizard.php?plugin=blogsense'  class='token_items' target='_blank' title='AssociatedContent is a Yahoo Network provided service serving articles. Use it to generate an article. See documentation above for more information.'>[ASSOCIATEDCONTENT] &nbsp;</a>
					
					<br>
				 </td>
			</tr>
			<tr>
				<td valign='top' style="font-size:10px;">
				   3rd Party:
				</td>
				 <td  valign=top align=left valign=bottom style='font-size:10px;'>
					<?php 
					if ($phpBay==1)
					{
						echo "<a href='#phpBay'  class='token_items' id=id_hook_phpBay title='phpBay Shortcode Generation Wizard.'>[phpBay]&nbsp;</a>&nbsp;";
					}
					if ($phpZon==1)
					{
						echo "<a href='#phpZon'  class='token_items' id=id_hook_phpZon title='phpZon Shortcode Generation Wizard.'>[phpZon]&nbsp;</a>&nbsp;";
					}
					if ($wpMage==1)
					{
						echo "<a href='#wpMage'  class='token_items' id=id_hook_wpMage title='WP Mage Shortcode Generation Wizard.'>[wpMage]&nbsp;</a>&nbsp;";
					}
					if ($wpRobot==1)
					{
						echo "<a href='#wpRobot'  class='token_items' id=id_hook_wpRobot title='WPRobot Shortcode Generation Wizard.'>[wpRobot]&nbsp;</a>&nbsp;";
					}
					if ($prosperent==1)
					{
						echo "<a href='#prosperent'  class='token_items' id=id_hook_prosperent title='Prosperent Shortcode Generation Wizard.'>[prosperent]&nbsp;</a>&nbsp;";
					}
					?>		
					<a href='./../includes/pdfs/Posting_Templates.pdf' target=_new>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Click here to read documentation on BlogSense's token and shortcodes.">
					</a>	
				 </td>
			</tr>
			<tr >
				<td valign='top' style="font-size:10px;">
				   Variables:
				</td>
				<td valign=top  align=left valign=bottom style='font-size:10px;'>
					<?php
					if ($module_type=='keywords')
					{
						?>
						<a href='#keyword'  class='token_items' id=id_titlevar_keyword title='Will call the keyword of focus from the keywords list.'>%keyword% &nbsp;&nbsp;&nbsp;</a>
						<?php
					}
					?>
					<a href='#title'  class='token_items' id=id_postvar_title title='Will call the original title'>%title%</a> &nbsp;&nbsp;&nbsp;
					<a href='#title_filtered'  class='token_items' id=id_postvar_title_filtered title='Calls title with common words stripped out.'>%title_filtered%</a> &nbsp;&nbsp;&nbsp;
					<a href='#link'  class='token_items' id=id_postvar_link title='Will call the orignal link'>%link% &nbsp;&nbsp;&nbsp;</a>
					<a href='#post_body'  class='token_items' id=id_postvar_postbody title='Will call the orignal post content'>%postbody% &nbsp;&nbsp;&nbsp;</a>
					<a href='#campaign_name' class='token_items' id=id_postvar_campaign_name title='Will call the the name of the campaign'>%campaign_name% &nbsp;&nbsp;&nbsp;</a>
					<a href='#image_1' class='token_items' id=id_postvar_image_1 title='Will call the first detected image SRC URL.'>%image_1% &nbsp;&nbsp;&nbsp;</a>
					<a href='#image_2' class='token_items' id=id_postvar_image_2 title='Will call the second detected image SRC URL.'>%image_2% &nbsp;&nbsp;&nbsp;</a>
					<a href='#author_name' class='token_items' id=id_postvar_author_name title='Will call the author name associated with the campaign.'>%author_name% &nbsp;&nbsp;&nbsp;</a>
					<a href='#domain_name' class='token_items' id=id_postvar_domain_name title='Will call the domain name of the campaign source.'>%domain_name% &nbsp;&nbsp;&nbsp;</a>
					<a href='#blog_url' class='token_items' id=id_postvar_blog_url title='Will call the url base of the blog.'>%blog_url% &nbsp;&nbsp;&nbsp;</a>
					<?php
					if ($module_type=='amazon'||$module_type=='yahoo'||$module_type=='video')
					{
					?>
					<a href='#campaign_query' class='token_items' id=id_postvar_campaign_query title='Will call the query used in campaign setup. If Multiple queries are used then only the current query of focus will replace this token.'>%campaign_query% &nbsp;&nbsp;&nbsp;</a>
					<?php
					}
					?>
					<a href='#tag_title'  class='token_items' id=id_postvar_tag_title title='Will generate a tag out of the title.'>%tag_title% &nbsp;&nbsp;&nbsp;</a>
					<a href='#tag_postbody'  class='token_items' id=id_postvar_tag_postbody title='Will generate a tag out of the descripion.'>%tag_postbody% &nbsp;&nbsp;&nbsp;</a>
					<?php
					if ($module_type=='video')
					{
					?>
					<a href='#video_embed'  class='token_items' id=id_postvar_video_embed title='Will call the video embed'>%video_embed% &nbsp;&nbsp;&nbsp;</a>
					<a href='#video_thumbnail'  class='token_items' id=id_postvar_video_thumbnail title='Will call a thumbnail of the video'>%video_thumbnail% &nbsp;&nbsp;&nbsp;</a>
					<a href='#video_description'  class='token_items' id=id_postvar_video_description title='Will call a description of the video'>%video_description% &nbsp;&nbsp;&nbsp;</a>
					
					<?php
					}
						
					if ($module_type=='amazon')
					{
					?>
					<a href='#amazon_product_title'  class='token_items' id=id_postvar_amazon_product_title title='Will call the product_title of the item. %title% calls the final title outputed by the title template.'>%amazon_product_title% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_price'  class='token_items' id=id_postvar_amazon_price title='Will call the price of the item'>%amazon_price% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_small_image_url'  class='token_items' id=id_postvar_amazon_small_image_url title='small image url'>%amazon_small_image_url% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_medium_image_url'  class='token_items' id=id_postvar_amazon_medium_image_url title='medium image url'>%amazon_medium_image_url% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_large_image_url'  class='token_items' id=id_postvar_amazon_large_image_url title='large image url.'>%amazon_large_image_url% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_product_description'  class='token_items' id=id_postvar_amazon_product_description title='This item is not garunteed to have content for every product.'>%amazon_product_description% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_product_features'  class='token_items' id=id_postvar_amazon_product_features title='This item is not garunteed to have content for every product.'>%amazon_product_features% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_brand_name'  class='token_items' id=id_postvar_amazon_brand_name title='This item is not garunteed to have content for every product.'>%amazon_brand_name% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_model'  class='token_items' id=id_postvar_amazon_model title='This item is not garunteed to have content for every product.'>%amazon_model% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_customer_review_content_3'  class='token_items' id=id_postvar_amazon_customer_review_content_1 title='Content of 1st customer review.'>%amazon_customer_review_content_1% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_customer_review_authorname_3'  class='token_items' id=id_postvar_amazon_customer_review_authorname_1 title='Authorname of 1st customer review.'>%amazon_customer_review_authorname_1% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_customer_review_content_3'  class='token_items' id=id_postvar_amazon_customer_review_content_2 title='Content of 2nd customer review.'>%amazon_customer_review_content_2% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_customer_review_authorname_3'  class='token_items' id=id_postvar_amazon_customer_review_authorname_2 title='Authorname of 2nd customer review.'>%amazon_customer_review_authorname_2% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_customer_review_content_3'  class='token_items' id=id_postvar_amazon_customer_review_content_3 title='Content of 3rd customer review.'>%amazon_customer_review_content_3% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_customer_review_authorname_3'  class='token_items' id=id_postvar_amazon_customer_review_authorname_3 title='Authorname of 3rd customer review.'>%amazon_customer_review_authorname_3% &nbsp;&nbsp;&nbsp;</a>
					<a href='#amazon_buyitnow_button'  class='token_items' id=id_postvar_amazon_buyitnow_button title='Default buy it now image button'>%amazon_buyitnow_button% &nbsp;&nbsp;&nbsp;</a>
					
					<?php
					}
					if ($templates_custom_variable_id)
					{
						foreach ($templates_custom_variable_id as $k=>$v)
						{
							echo "<a href='#custom_variable_$k'  class='class_custom_var_post' id=".$templates_custom_variable_token[$k]." title='".$templates_custom_variable_name[$k]."'>".$templates_custom_variable_token[$k]." </a>&nbsp;&nbsp;&nbsp;";
						}
					}
					?>
				</td>
			</tr>
			<?
			if ($module_type=='fileimport'&&$campaign_name=='csv_import')
			{
			?>
			<tr>
				<td valign='top' style="font-size:10px;">
				   CSV :
				</td>
				<td  valign=top align=left valign=bottom style='color:#444444;font-size:11;'>
					<?php 
					foreach ($collumns as $k => $v)
					{
						$v = trim($v);
						echo "%{$v}% &nbsp;&nbsp;&nbsp;&nbsp;";
					}
					?>
				</td>
			</tr>
			<?php			
			}
			?>

				 
			<tr>
				 <td colspan=2 align=right style="font-size:13px;">
					<textarea width=100% cols=71 rows=14 name='post_template' id=id_post_template class='class_template_box'><?php
						if ($campaign_post_template)
						{
							echo $campaign_post_template;
						}
						else
						{
							echo $default_post_template;
						}
						
						
					?></textarea>
				 </td>
			</tr>	 
		</table>
	</div>
	<h3 style='font-size:14px;'><a href="#">Keyword Filtering</a></h3>
		<div>
			<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;" id=id_table_regex> 
				<tr>						
					<td  align=left valign=top style="font-size:11px; ">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="These keywords will determine what content makes into your blog. Leaving this field alone (or blank) to include all.">
						Must Contain:<br>
						<input type=radio name='include_keywords_scope' value='1' <?php if ($campaign_include_keywords_scope==1){echo 'checked="true"'; } ?>> Search Title 
						<input type=radio name='include_keywords_scope' value='2' <?php if ($campaign_include_keywords_scope==2){echo 'checked="true"'; } ?>> Search Body
						<input type=radio name='include_keywords_scope' value='3' <?php if ($campaign_include_keywords_scope==3||!$campaign_include_keywords_scope){echo 'checked="true"'; } ?>> Search Both 
					</td>
				</tr>
				<tr>
					<td align=right style="font-size:13px;">
						<textarea name=include_keywords style='width:100%'><?php if ($campaign_include_keywords){ echo $campaign_include_keywords; } else { echo "Separate with commas."; }?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan=2 align=left valign=top style="font-size:11px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="These keywords will determine what content is prevented from entering your blog. Leaving this field alone (or blank) to include all.">
						Cannot Contain:<br>
						<input type=radio name='exclude_keywords_scope' value='1' <?php if ($campaign_exclude_keywords_scope==1){echo 'checked="true"'; } ?>> Search Title 
						<input type=radio name='exclude_keywords_scope' value='2' <?php if ($campaign_exclude_keywords_scope==2){echo 'checked="true"'; } ?>> Search Body 
						<input type=radio name='exclude_keywords_scope' value='3' <?php if ($campaign_exclude_keywords_scope==3||!$campaign_include_keywords_scope){echo 'checked="true"'; } ?>> Search Both
					</td>
				</tr>
				<tr>
					<td align=right style="font-size:13px;">
						<textarea name=exclude_keywords style='width:100%'><?php if ($campaign_exclude_keywords){ echo $campaign_exclude_keywords; } else { echo "Separate with commas."; }?></textarea>
					</td>
				</tr>				
			</table>
		</div>
	<h3 style='font-size:14px;'><a href="#">Auto Tagging</a></h3>
		<div>
			<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;" id='id_table_autocategorize'> 
				<tr>			 
					<td  align=left valign=top style='font-size:13px;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This will tell blogsense where to look for matches in order to auto-sort into categories.">
						Auto Tagging:<br> 
					</td>
					<td  align=right style='font-size:13px;'>
						<select name=autotag_method id='id_select_autotag_method' >
							 <?php							 
							 if ($campaign_autotag_method==0&&!$_GET['edit']!=1)
							 {
								echo "<option value=0 selected=true>Disabled</option>";
								echo "<option value=1 >Use Yahoo Tag Extraction</option>";
								echo "<option value=2 >Generate Tags from the Title</option>";
								echo "<option value=3 >Select Tags from Wordpress Tag Database</option>";
								echo "<option value=4 >Use Custom Tags</option>";
								
							 }
							 else if ($campaign_autotag_method==1||!$campaign_autotag_method)
							 {
								echo "<option value=0 >Disabled</option>";
								echo "<option value=1 selected=true>Use Yahoo Tag Extraction</option>";
								echo "<option value=2 >Generate Tags from the Title</option>";
								echo "<option value=3 >Select Tags from Wordpress Tag Database</option>";
								echo "<option value=4 >Use Custom Tags</option>";
							 }
							 else if ($campaign_autotag_method==2)
							 {
								echo "<option value=0 >Disabled</option>";
								echo "<option value=1 >Use Yahoo Tag Extraction</option>";
								echo "<option value=2 selected=true>Generate Tags from the Title</option>";
								echo "<option value=3 >Select Tags from Wordpress Tag Database</option>";
								echo "<option value=4 >Use Custom Tags</option>";
							 }
							 else if ($campaign_autotag_method==3)
							 {
								echo "<option value=0 >Disabled</option>";
								echo "<option value=1 >Use Yahoo Tag Extraction</option>";
								echo "<option value=2 >Generate Tags from the Title</option>";
								echo "<option value=3 selected=true>Select Tags from Wordpress Tag Database</option>";
								echo "<option value=4 >Use Custom Tags</option>";
							 }
							 else if ($campaign_autotag_method==4)
							 {
								echo "<option value=0 >Disabled</option>";
								echo "<option value=1 >Use Yahoo Tag Extraction</option>";
								echo "<option value=2 >Generate Tags from the Title</option>";
								echo "<option value=3 >Select Tags from Wordpress Tag Database</option>";
								echo "<option value=4 selected=true>Use Custom Tags</option>";
							 }
							 ?>
						</select>
					</td>
				</tr>
				<tr>
					<td  align=left valign=top style="font-size:13px; width:180px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Set a minimum # of tags to add and a maximum number of tags to add. BlogSense will randomly choose x tags for each post,x being a number between the min and max paramaters.">
						# of Tags per Post<br>
					</td>
					<td align=right style="font-size:13px;">
						Min: <input size=1 name='autotag_min' value='<?php if ($campaign_autotag_min){ echo $campaign_autotag_min;}else{echo "5";} ?>'> &nbsp;&nbsp;Max: <input size=1 name='autotag_max' value='<?php if ($campaign_autotag_max){ echo $campaign_autotag_max;}else{echo "7";} ?>'>
					</td>
				</tr>
				<tr class='class_tr_autotag_custom_tags' style='display:<?php if ($campaign_autotag_method!=3&&$campaign_autotag_method!=4){echo"none";}?>;'>
					<td colspan=2 align="right" style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Separate tags with commas.">
						<textarea name='autotag_custom_tags' rows=5 cols=71><?php echo $campaign_autotag_custom_tags; ?></textarea>
					</td>
				</tr>				
			</table>
		</div>
	<h3 style='font-size:14px;'><a href="#">Auto Categorization</a></h3>
		<div>
			<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;" id='id_table_autocategorize'> 
				<tr>			 
					<td  align=left valign=top style='font-size:13px;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Enabling this setting will tell BlogSense to attempt to best-select the appropriate category for each item in this campaign. Please use the settings below to inform BlogSense on how to do this.">
						Auto-Categorize:<br> 
					</td>
					<td  align=right style='font-size:13px;'>
						<select name=autocategorize >
							 <?php
							 if ($campaign_autocategorize==0||!$campaign_autocategorize)
							 {
								echo "<option value=1>on</option>";
								echo "<option value=0 selected=true>off</option>";
							 }
							 if ($campaign_autocategorize==1)
							 {
								echo "<option value=1 selected=true>on</option>";
								echo "<option value=0>off</option>";
							 }
							 ?>
						</select>
					</td>
				</tr>
				<tr>			 
					<td  align=left valign=top style='font-size:13px;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This will tell blogsense where to look for matches in order to auto-sort into categories.">
						Filtering Method:<br> 
					</td>
					<td  align=right style='font-size:13px;'>
						<select name=autocategorize_search >
							 <?php							 
							 if ($campaign_autocategorize_search==1||!$campaign_autocategorize_search)
							 {
								echo "<option value=1 selected=true>Search Titles Only</option>";
								echo "<option value=2 >Search Content & Titles</option>";
							 }
							 if ($campaign_autocategorize_search==2)
							 {
								echo "<option value=1 >Search Titles Only</option>";
								echo "<option value=2 selected=true>Search Content & Titles</option>";
							 }
							 ?>
						</select>	
					</td>
				</tr>
				<tr>			 
					<td  align=left valign=top style='font-size:13px;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Automatch works like this: BlogSense will search for category slugs in content to discover which categories are suitble. If we set it up to use the FIRST MATCH ONLY then BlogSense will quit looking for additional matches after it finds it's first match. If no match is found then it is ignored. If ALL INCLUDING DEFAULT CATEGORY is selected then the post will be categorized into all matched categories as well as the default category; If no match is found then it will still be included as an item in the default campaign category.  If you select to exclude the default category then the post will be sorted into ALL matches excluding the default category and if no matches are found then the post will be ignored. Batch lists will allow you to create a very specific keyword-to-category matrix.">
						Categorization Method:<br> 
					</td>
					<td  align=right style='font-size:13px;'>
						<select name=autocategorize_method id='id_select_autocategorization_method'>
							 <?php
							 if ($campaign_autocategorize_method==1||!$campaign_autocategorize_method)
							 {
								echo "<option value=1 selected=true>Automatch & Use First Category Match Only</option>";
								echo "<option value=2 >Automatch ALL & Include Default Category</option>";
								echo "<option value=3 >Automatch ALL & Exclude Default Category</option>";
								echo "<option value=4 >Custom Filtering: Quick Define</option>";
								echo "<option value=5 >Custom Filtering: Use Batch List</option>";
							 }
							 if ($campaign_autocategorize_method==2)
							 {
								echo "<option value=1 >Automatch:First Category Only</option>";
								echo "<option value=2 selected=true>Automatch ALL: Include Parent Category</option>";
								echo "<option value=3 >Automatch ALL: Exclude Parent Category</option>";
								echo "<option value=4 >Custom Filtering</option>";
								echo "<option value=5 >Custom Filtering: Use Batch List</option>";
							 }
							 if ($campaign_autocategorize_method==3)
							 {
								echo "<option value=1 >Automatch:First Category Only</option>";
								echo "<option value=2 >Automatch ALL: Include Parent Category</option>";
								echo "<option value=3 selected=true>Automatch ALL: Exclude Parent Category</option>";
								echo "<option value=4 >Custom Filtering</option>";
								echo "<option value=5 >Custom Filtering: Use Batch List</option>";
							 }
							 if ($campaign_autocategorize_method==4)
							 {
								echo "<option value=1 >Automatch:First Category Only</option>";
								echo "<option value=2 >Automatch ALL: Include Parent Category</option>";
								echo "<option value=3 >Automatch ALL: Exclude Parent Category</option>";
								echo "<option value=4 selected=true>Custom Filtering</option>";
								echo "<option value=5 >Custom Filtering: Use Batch List</option>";
							 }
							 if ($campaign_autocategorize_method==5)
							 {
								echo "<option value=1 >Automatch:First Category Only</option>";
								echo "<option value=2 >Automatch ALL: Include Parent Category</option>";
								echo "<option value=3 >Automatch ALL: Exclude Parent Category</option>";
								echo "<option value=4 >Custom Filtering</option>";
								echo "<option value=5 selected=true>Custom Filtering: Use Batch List</option>";
							 }
							 ?>
						</select>	
					</td>
				</tr>
				<tr class='class_tr_autocategorize_custom_filters' style='display:<?php if ($campaign_autocategorize_method!=4){echo"none";}?>;'>
					<td colspan=2 align="left" style="font-size:13px;">
					<br>
					</td>
				</tr>
				<tr class='class_tr_autocategorize_custom_filters'  style='display:<?php if ($campaign_autocategorize_method!=4){echo"none";}?>;'>
					<td style='font-size:11px;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="One per line please. If this keyword is detected in the content, then the post will be applied to the responding category.">
						<i>Keyword</i>
					</td>
					<td style='font-size:11px;'>
						<i>Category Designation </i>
						&nbsp;
						&nbsp;
						
					</td>
					<td>
					<img src="./../nav/add.png" style="cursor:pointer;" id=id_add_custom_filters_button> <font style='text-decoration:italics; color:#aaaaaa; font-size:11px'>
					 
					</td>
				</tr>						
				<?php
				
				if ($campaign_autocategorize_method==4)
				{
					foreach ($campaign_autocategorize_filter_keywords as $k=>$v)
					{
					?>
						<tr class='class_tr_autocategorize_custom_filters'>
							<td style='font-size:13px;'>
								<img onClick="$(this).parent().parent().remove();" src="./../nav/remove.png" style="cursor:pointer;">							
								<input name='autocategorize_filter_keywords[]' value='<?php echo $campaign_autocategorize_filter_keywords[$k]; ?>' size=16 style="font-size:12px;">
							</td>
							<td>
								<select id=articles_selects_cats name='autocategorize_filter_categories[]' style="width:200px;font-size:12px;">	
								<?php
									foreach($categories as $a=>$b)
									{	
										
										if ($campaign_autocategorize_filter_categories[$k]==$cat_ids[$a]){$selected = "selected=true";}
										else{$selected = "";}
										echo "<option value='$cat_ids[$a]' $selected>$slugs[$a]</</option>";			
									}
								?>
								</select>
							</td>
						</tr>
					<?php
					}
				}
			
				?>
				
				<tr class='class_tr_autocategorize_custom_filters_list' <?php if ($campaign_autocategorize_method!=5) echo "style='display:none'"; ?>>
					<td style='font-size:12px;' colspan=2>
						<br>
						This feature allows for a quicker categorization filter setup for blogs with many categories and keywords.<br><br>
						<i>Example Line Format: keyword,keyword,keyword:cat_slug</i>
					</td>
				</tr>
				<tr class='class_tr_autocategorize_custom_filters_list' <?php if ($campaign_autocategorize_method!=5) echo "style='display:none'"; ?>>
					<td style='font-size:13px;' colspan=2>															
						<textarea name='autocategorize_filter_list' style="font-size:12px;width:480px;height:280px;"><?php echo $campaign_autocategorize_filter_list; ?></textarea>
						<?php
						echo "<br><br><b>Available slugs</b><br>";
						foreach ($slugs as $key=>$val)
						{
							echo $val."<br>";
						}
						
						?>
					</td>
				</tr>
			
			</table>
		</div>
	<h3 style='font-size:14px;'><a href="#">Custom Fields</a></h3>
	<div>
		<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;"> 
				<tr>
					<td colspan=2 align="left" style="font-size:13px;">
						
						<img src="./../nav/add.png" style="cursor:pointer;" id=id_add_custom_field_button> 
						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> <--- Add New Fields
						<br><br>
						 %image_1% will call the url of the first image found.<br>
						 %image_2% will call the second image url.<br>
						 If either of the above values are detected BlogSense will auto-generate an attached featured image for the post.<br>
						 You can also use any token variable provided in the post-body template area. 
						 </font>
						<br>
					 </td>
				</tr>
				<tr>
					<td colspan=2>
						<table id='id_custom_fields' width=100%>
							<tr>
								<td  align="center" style="font-size:11px;color:#aaaaaa" width='50%'>
									<i>Field Name</i>
								 </td>
								  <td  align="center" style="font-size:11px;color:#aaaaaa" width='50%'>
									<i>Value</i>
								</td>
							</tr>
							<?php
								if ($campaign_custom_field_name)
								{
									 foreach ($campaign_custom_field_name as $key=>$value)
									 {
										if ($campaign_custom_field_name[$key])
										{
											   echo "<tr>
													 <td  align=left style=\"font-size:13px;\">
														<img class=remove_custom_field onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">
														&nbsp;<input size=28 name=\"custom_field_name[]\" value='$campaign_custom_field_name[$key]'>
													 </td>
													 <td  align=right style=\"font-size:13px;\">
														<input size=35 name=\"custom_field_value[]\" value='$campaign_custom_field_value[$key]'>	
													</td>
												  </tr>";			      
										}
									 }
								}
							 ?>
						</table>
					</td>
				</tr>			
			</table>
		</div>	
		<h3 style='font-size:14px;'><a href="#">Bookmarking</a></h3>
		<div>
			<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;"> 
				<tr>
					<td colspan=3 align="left" style="font-size:13px;">
						<img src='./../nav/logo_twitter.png' style='max-width:100px;max-height:200px;'>
						<br>
						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> 
						</font>					
					 </td>
				</tr>
				<tr>
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='10%'>
						<i>Enable</i>
					 </td>	
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='50%'>
						<i>Twitter Account</i>
					 </td>		
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='50%'>
						<img src="./../nav/tip.png" style="cursor:pointer;"  title="Mark this campaign's tweets with a hashtag for further exposure." border=0>
						<i>Hashtag</i>
					 </td>	
				</tr>
				<?php
					if (strlen($twitter_user[0])>2)
					{
						$bookmark_twitter = @json_decode($bookmark_twitter);
						$bookmark_twitter_status = $bookmark_twitter[0];
						$bookmark_twitter_hash = $bookmark_twitter[1];
						
						//print_r($bookmark_twitter_status);
						 foreach ($twitter_user as $key=>$value)
						 {
							?>
							<tr>
								<td  align=left style="font-size:13px;color:#aaaaaa; font-size:11px">
									<input type='checkbox' name='bookmark_twitter_status_<?php echo $key; ?>' value='on' <?php if ($bookmark_twitter_status[$key]!='off'){echo 'checked=true';}?>>
								</td>
								<td  align=left style="font-size:13px;color:#aaaaaa; font-size:11px">
									<i><?php echo $value; ?></i>
								</td>
								<td  align=left style="font-size:13px;color:#aaaaaa; font-size:11px">
									<input size=25 name='bookmark_twitter_hash_<?php echo $key; ?>' value='<?php echo $bookmark_twitter_hash[$key]; ?>'>
								</td>
							</tr>
							<?php							
						 }
					}
				 ?>	
			</table>
			<table width=489 id='id_table_remote_publishing_pp' style="margin-left:auto;margin-right:auto;line-height:17px;">
				<tr>
					<td colspan=3 align="left" style="font-size:13px;">
						<br><br>
						<img src='./../nav/logo_pixelpipe.png' style='max-width:100px;max-height:200px;'>

						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> 
						</font>
						
					 </td>
				</tr>
				<tr>
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='10%'>
						<i>Enable</i>
					 </td>	
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='50%'>
						<i>Pixelpipe Account Email</i>
					 </td>		
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='50%'>
						<i>Pixelpipe Routing Tags</i>
					 </td>	
				</tr>
				<?php
					if (strlen($pixelpipe_email[0])>2)
					{
						$bookmark_pixelpipe = json_decode($bookmark_pixelpipe);
						$bookmark_pixelpipe_status = $bookmark_pixelpipe;
						
						//print_r($bookmark_pixelpipe_status);exit;
						 foreach ($pixelpipe_email as $key=>$value)
						 {
							?>
							<tr>
								<td  align=left style="font-size:13px;color:#aaaaaa; font-size:11px">
									<input type='checkbox' name='bookmark_pixelpipe_status_<?php echo $key; ?>' value='on' <?php if ($bookmark_pixelpipe_status[$key]!='off'){echo 'checked=true';}?>>
								</td>
								<td  align=left style="font-size:13px;color:#aaaaaa; font-size:11px">
									<i><?php echo $value; ?></i>
								</td>
								<td  align=left style="font-size:13px;color:#aaaaaa; font-size:11px">
									<?php echo $pixelpipe_routing[$key]; ?>
								</td>
							</tr>
							<?php							
						 }
					}
				 ?>				
			</table>
		</div>
		<h3 style='font-size:14px;'><a href="#">Regex Replacements</a></h3>
		<div>
			<table width=489 style="margin-left:auto;margin-right:auto;line-height:17px;" id=id_table_regex> 
				<tr>
					<td colspan=2 align="left" style="font-size:13px;">
						
						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> 
						 Regex is short for Regular Expressions, which are advanced short codes used to discover patterns in text. Unless you are an expert at building regular expressions, you may find this feature very complicated to work with. Still at times it is the only solution to cleaning up content.<br><br> Stackoverflow.com has a friendly community that will help you build reggular experssions; also try posting yor problem on the BlogSense community forums and maybe a more experienced use can help.</font>
						<br>
					 </td>
				</tr>
				<tr>
					<td colspan=2 align=middle style="font-size:13px;">
						<a href="./../includes/pdfs/Using_Regular_Expressions.pdf" target=_blank><img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Information on Regular Expressions." border=0></a>
						Regex Search & Replace 
					</td>
				</tr>
				<tr>
					<td colspan=2 align=middle style="font-size:13px;">
						<center>
							<img src="./../nav/add.png" style="cursor:pointer;" id="articles_string_edit_button_0" class="add_articles_string_edit">
						</center>
					</td>
				</tr>
				<tr>
					<td  align=middle style="font-size:11px;color:#aaaaaa">
						<i>Search String</i>
					</td>
					<td  align=middle style="font-size:11px;color:#aaaaaa">
						<i>Replace String</i>
					</td>
				</tr>
				<?php
				if ($_GET['edit']==1||$_GET['import']==1)
				{
					if (strlen($campaign_regex_search[0])>1)
					{
						foreach($campaign_regex_search as $k=>$v)
						{
							echo "<tr><td  align=left valign=middle style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea  name=\"regex_search[]\" cols=26>$v</textarea></td><td  align=right style=\"font-size:13px;\"><textarea cols=26  name=\"regex_replace[]\" >".$campaign_regex_replace[$k]."</textarea></td></tr>";
						}
					}
				}				
				?>
			</table>
		</div>
		<h3 style='font-size:14px;'><a href="#">Remote Publishing</a></h3>
		<div>
			<table width=489 id='id_table_remote_publishing_bs' style="margin-left:auto;margin-right:auto;line-height:17px;"> 
				<tr>
					<td colspan=2 align="left" style="font-size:13px;">
						<img src='./../nav/logo_blogsense.png' style='max-width:160px;max-height:200px;'>
						<br>
						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> 
						 This feature will allow the exportation of posts to external blogs that have BlogSense installed by sending the content to the remote Blogsense API for alteration(s) and publishing. Please add the API URL along with the secret key associated with the remote blog's api, and append any other API based paramaters onto the URL to affect how the content will be treated before being published on the remote blog. When selecting additional praramaters, take in account that the post_title, post_content, link, post_status, and post_date paramaters are auto-appended and are not required. Paramaters that are required include: secret_key, blog_id (if multisite). </font>
						</font>
						<br>
						<br>
						<img src="./../nav/add.png" style="cursor:pointer;" id=id_add_button_remote_publishing_bs> <font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> <--- Add Remote BlogSense Blog
						<br>
						<br>
					 </td>
				</tr>
				<tr>
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='50%'>
						<img src="./../nav/tip.png" style="cursor:pointer;"  title='Example: http://www.myblog.com/auto/blogsense_api.php?blog_id=1&secret_key=hello' border=0> 
						<i>Remote API URL</i>
					 </td>	
										 
				</tr>
				<?php
					if ($campaign_remote_publishing_api_bs)
					{
						$campaign_remote_publishing_api_bs = explode(';',$campaign_remote_publishing_api_bs);
						
						 foreach ($campaign_remote_publishing_api_bs as $key=>$value)
						 {
							$i = $key+1;
							echo "	<tr>
										 <td  align=left style=\"font-size:13px;color:#AAAAAA;\">
											<i>#$i</i>&nbsp;&nbsp;<img class=remove_custom_field onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">
											&nbsp;<input size=66 name=\"remote_publishing_api_bs[]\" value='$campaign_remote_publishing_api_bs[$key]'>
										 </td>
									</tr>";			      
							
						 }
					}
				 ?>	
			</table>
			<br>
			<hr>
			<table width=489 id='id_table_remote_publishing_xmlrpc' style="margin-left:auto;margin-right:auto;line-height:17px;"> 
				<tr>
					<td colspan=2 align="left" style="font-size:13px;">
						<img src='./../nav/logo_xml_rpc.gif' style='max-width:160px;max-height:200px;'>
						<br>
						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> 
						 This feature will take advantage the XML/RPC Remote publishing protocol, which is employed by content management systems such as Wordpress, Blogger, and many others. Please enter the XML/RPC url, username and password, and make sure that XML/RPC publishing is activated on your remote CMS. </font>
						</font>
						<br>
						<br>
						<img src="./../nav/add.png" style="cursor:pointer;" id='id_add_button_remote_publishing_xmlrpc'> <font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> <--- Add Remote XML/RPC Account
						<br>
						<br>
					 </td>
				</tr>
				<tr>
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='80%'>
						<img src="./../nav/tip.png" style="cursor:pointer;"  title='Format = http://www.blog.com/xmlrpc.php;username;password' border=0> 
						<i>Format = http://www.blog.com/xmlrpc.php;username;password</i>
					 </td>	
					  <td  align="left" style="font-size:11px;color:#aaaaaa" width=''>
						<img src="./../nav/tip.png" style="cursor:pointer;"  title='Check this box to have the BlogSense internal spinner spin the content before exporting it to the remote location' border=0> 
						<i>Spin text?</i>
					 </td>	
					
										 
				</tr>
				<?php
					$i = 1;
					if ($campaign_remote_publishing_api_xmlrpc)
					{
						$campaign_remote_publishing_api_xmlrpc = explode(':::',$campaign_remote_publishing_api_xmlrpc);
						$campaign_remote_publishing_api_xmlrpc_spin = explode(':::',$campaign_remote_publishing_api_xmlrpc_spin);
						
						 foreach ($campaign_remote_publishing_api_xmlrpc as $key=>$value)
						 {
							if ($campaign_remote_publishing_api_xmlrpc_spin[$key]=='on')
							{
								$checked = "checked='true'";
							}
							else
							{
								$checked = "'";
							}
							echo "	<tr>
										 <td  align=left style=\"font-size:13px;color:#AAAAAA;\">
											<i>#$i</i>&nbsp;&nbsp;<img class=remove_custom_field onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">
											&nbsp;<input size=48 class='class_remote_publishing_xmlrpc' name=\"remote_publishing_api_xmlrpc[]\" value='$campaign_remote_publishing_api_xmlrpc[$key]'>
										 </td>
										 <td  align=left style='font-size:13px;'>
											&nbsp;<input type=checkbox name='remote_publishing_api_xmlrpc_spin_{$key}[]' value='on' {$checked}>
										 </td>
									</tr>";			      
							$i++;
						 }
					}
				 ?>	
			</table>
			<br>
			<hr>
			<table width=489 id='id_table_remote_publishing_email' style="margin-left:auto;margin-right:auto;line-height:17px;"> 
				<tr>
					<td colspan=2 align="left" style="font-size:13px;">
						<img src='./../nav/logo_email.jpg' style='max-width:160px;max-height:200px;'>
						<br>
						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> 
						 This feature will allow the exportation of posts to external content management systems that accept email postings, such as blogs,Google Groups, etc. These emails are attempted to be sent in text/html format.</font>
						</font>
						<br>
						<br>
						<img src="./../nav/add.png" style="cursor:pointer;" id=id_add_button_remote_publishing_email> <font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> <--- Add Remote BlogSense Blog
						<br>
						<br>
					 </td>
				</tr>
				<tr>
					<td  align="left" style="font-size:11px;color:#aaaaaa;width:50%;">
						<img src="./../nav/tip.png" style="cursor:pointer;"  title='Enter the email address of the target remote CMS here.' border=0> 
						<i>Email Address</i>
					 </td>	
					 <td  align="left" style="font-size:11px;color:#aaaaaa" width=''>
						<img src="./../nav/tip.png" style="cursor:pointer;"  title='Some CMS platforms that accept remote email publishing will also accept action tags that perform certain duties. You can use this box to append additional content to the email body. Add "+spin" to spin the content before exporting it to the remote location.' border=0> 
						<i>Footer Content</i>
					 </td>	
										 
				</tr>
				<?php
					if ($campaign_remote_publishing_api_email)
					{
						$campaign_remote_publishing_api_email = explode(';',$campaign_remote_publishing_api_email);
						$campaign_remote_publishing_api_email_footer = explode(';',$campaign_remote_publishing_api_email_footer);
						
						 foreach ($campaign_remote_publishing_api_email as $key=>$value)
						 {
							echo "	<tr>
										 <td  align=left style='font-size:13px;color:#AAAAAA;'>
											<i>#$i</i>&nbsp;&nbsp;<img  onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">
											&nbsp;<input size=24 name=\"remote_publishing_api_email[]\" value='{$campaign_remote_publishing_api_email[$key]}'>
										 </td> 
										 <td  align=left style='font-size:13px;'>
											&nbsp;<input size=24 name='remote_publishing_api_email_footer[]' value='{$campaign_remote_publishing_api_email_footer[$key]}'>
										 </td>
									</tr>";			      
							
						 }
					}
				 ?>	
			</table>
			<br>
			<hr>
			<table width=489 id='id_table_remote_publishing_pp' style="margin-left:auto;margin-right:auto;line-height:17px;">
				<tr>
					<td colspan=2 align="left" style="font-size:13px;">
						<br><br>
						<img src='./../nav/logo_pixelpipe.png' style='max-width:160px;max-height:200px;'>
						<br>
						<br>
						<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> 
						Linking campaigns to a pixelpost account will allow us to distribute content through many multiple channels, called pipes (or accounts), using a special email address related to a unique pixelpipe account and routing tags, which will tell pixel pipe which accounts(or pipes) to use. Content sent out to pixel pipe cannot be unique-per-pipe because pixelpipe uses one copy to send to all locations. Still, we can spin the content at least once by adding '+spin' to the routing tags. <br><br>Note: Unfortunately pixel pipe's mail feature strips the post of all HTML, as they do not support the text/html format. It may be more benifitial to use pixel post as bookmarking utlity. 
						</font>
						<br>
						<br>
						<img src="./../nav/add.png" style="cursor:pointer;" id=id_add_button_remote_publishing_pp> <font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> <--- Add PixelPipe Account
						<br>
						<br>
					 </td>
				</tr>
				<tr>
					<td  align="left" style="font-size:11px;color:#aaaaaa" width='50%'>
						<img src="./../nav/tip.png" style="cursor:pointer;" id=id_add_button_remote_publishing title='Unique PP Email Address. To find: 1. Login to pixelpipe.com 2. Click on Software Solutions. 3. Press "Tell me More" under the Email/MMS section. '> 
						<i>Pixelpipe Account Email</i>
					 </td>		
					 <td  align="left" style="font-size:11px;color:#aaaaaa" width='50%'>
						<img src="./../nav/tip.png" style="cursor:pointer;"  title='PixelPipe Routing Tags. eg: @blogger. To find out what routing tags are available please login to pixelpipe and view "My Pipes". Separate routing tags with spaces.'> 
						<i>Pixelpipe Routing Tags</i>
					 </td>
					 
				</tr>
				<?php
					if ($campaign_remote_publishing_api_pp_email)
					{
						$campaign_remote_publishing_api_pp_email = explode(';',$campaign_remote_publishing_api_pp_email);
						$campaign_remote_publishing_api_pp_routing = explode(';',$campaign_remote_publishing_api_pp_routing);
						
						 foreach ($campaign_remote_publishing_api_pp_email as $key=>$value)
						 {
							echo "	<tr>
										 <td  valign=top align=left style=\"font-size:13px;color:#AAAAAA;\">
											<i>#$i</i>&nbsp;&nbsp;<img  onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">
											&nbsp;<input size=25 name=\"remote_publishing_api_pp_email[]\" value='$campaign_remote_publishing_api_pp_email[$key]'>
										 </td>
										 <td valign=top align=left style=\"font-size:13px;\">
											
											&nbsp;<textarea name=\"remote_publishing_api_pp_routing[]\" style='width: 220px; height: 39px;'>$campaign_remote_publishing_api_pp_routing[$key]</textarea>
										 </td>
						
									</tr>";			      
							
						 }
					}
				 ?>	
			</table>
			</div>	
	<br>
</div>