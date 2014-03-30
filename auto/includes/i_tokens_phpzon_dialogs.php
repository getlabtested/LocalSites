<div id=id_dialog_phpZon>
<br>
	<div style='height:344px;width:500px;' align=center>
		<table width=95% >
		<tr>
			<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Use custom keywords or token variables to populate this field.">
				Keywords:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_keywords' value='%tag_title%'>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional : num specifies the number of results to list per page. It's important to understand that Amazon returns 10 results on any query where SearchIndex is used. When paging is turned off, specify 1-10 results when only a few results are needed. When paging is turned on, leave the num field blank and it will automatically set to 10 results for paging mode.">	
				Number Limit:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_number' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="searchindex is Amazon's top level categories. The menu builders will automatically display available categories based upon the selected country. Not all countries have the same searchindex categories available. For manually building phpZon Pro tags, click here for a list of searchindex available by country. searchindex values are case sensitive. This shortcode is optional.">
			<a href='http://docs.amazonwebservices.com/AWSECommerceService/2009-11-01/DG/index.html?APPNDX_SearchIndexParamForItemsearch.html' target=_blank><img src="./../nav/link.gif" style="cursor:pointer;" border=0 title="Click Here to determine search indexes."></a>
			Search Index:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_searchindex' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: Browsenodes are Amazon's lower level categories for specific parts of their site. Using a browsenode id can help narrow searches down to a specific area of Amazon's site for better search results, however, this is an optional shortcode. An easy way to get the BrowseNodeID of a category is to visit the amazon.xxx site. For example, we go to www.amazon.com and then click on Movies -> Blu-ray. Look at the url in your browser. It should look something like this: &node=193640011">
			Browse node id:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_browsenodeid' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: Amazon assigns a unique identifier to each product on it's website. This unique id is called the ASIN. To list details about a specific product on Amazon, you will need the ASIN number. asin=B0011YZ018">
			ASIN:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_asin' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: Sort values can be used when a searchindex is used. Sort values range depending upon the country and the searchindex used. Not all sort values work for each country or searchindex. It's best to use the menu builders, as they are programmed to provide the exact sort values available for each country and searchindex. This shortcode is optional. Use the link below to determine custom sort variables.">
			<a href='http://docs.amazonwebservices.com/AWSECommerceService/2009-11-01/DG/index.html?APPNDX_SortValuesArticle.html' target=_blank><img src="./../nav/link.gif" style="cursor:pointer;" border=0 title="Click Here to determine search indexes."></a>
			Sorting:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_sort' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: The country shortcode will override the global default country setting. This is useful when you are targeting different countries. This shortcode is optional. Example: country=DE">
			Country:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_country' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: specifies the maximum price in order to return auction listings. For example, if you want to create a store called “DVDs for Ten Dollars or Less,” then you could set the maximum price to 10. Only a numerical value is required. The currency or decimal is not required.">
			Tracking ID:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_trackingid' value='<?php echo $amazon_affiliate_id; ?>'>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: minprice is used to specify a minimum price that an item must have, in order to be returned in the search results. This is useful to set a minimum price. This is important! Do not use currency symbols, thousands separators or cent separators. To use a minprice of $50.00, use: minprice=5000 To use a minprice of $3.00, use: minprice=300 This shortcode is optional.">
			Minimum Price:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_minimumprice' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: specifies the maximum bid an auction item must have before being returned. Generally the minimum bid parameter above should be sufficient, but you can use this to cap the maximum number of bids listings have.">
			Maximum Price:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_maximumprice' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: merchantid specifies a particular merchant id on Amazon. By default, merchant id=Amazon Use this shortcode to specify a particular merchant for returning product search results.">
			Merchant ID:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_merchantid' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: manufacturer is used to specify a specific manufacturer that corresponds with your keyword(s). For example, if you used the keyword 'jeans' but wanted to only display jeans from 'Guess,' you can do so with this shortcode.">
			Manufacturer:
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_manufacturer' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: This specifies a different sort order for the auction listing. Sort order is globally set in the phpBay Pro Admin panel, however, if you need to specify a special sort order for a single page or post, use this parameter. Acceptable values for this field are 0 to 6:">
			Publisher
			</td>
			<td align=right valign=top>		
				<input name=loc size=25 id='id_phpzon_publisher' value=''>
		
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: condition is set to 'All' by default. Valid conditions are: All (default, need not be specified), Used, Collectible,Refurbished">
			Condition:
			</td>
			<td align=right valign=top>		
				<select name=loc id='id_phpzon_condition'>
					<option value='' SELECTED=TRUE></option>
					<option value='All'>ALL</option>
					<option value='Used'>USED</option>
					<option value='Collectible'>COLLECTIBLE</option>
					<option value='Refurbished'>REFURBISHED</option>	
				</select>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional:debug, when set to 'true' will output debugging information about the query, including the amazon url and xml code returned by Amazon. This shortcode can be used to troubleshoot problems or see if there is an error. This shortcode is optional.">
			Debug:
			</td>
			<td align=right valign=top>		
				<select name=loc id='id_phpzon_debug'>
					<option value='' SELECTED=TRUE></option>
					<option value='true'>TRUE</option>
					<option value='false' >FALSE</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: When paging is turned on and using at popular search term, Amazon can return up to 400 pages of 10 results per page. That's a lot of pages! If you want to restrict this, and say return a max of 20 pages, use the shortcode: maxresults='200' This will provide 20 pages of ten results per page (20 x 10 = 200 results).">
			Max Results:
			</td>
			<td align=right valign=top>		
					<input name=loc size=25 id='id_phpzon_maxresults' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: By default, Amazon returns up to five reviews. In cases where you want less reviews, this shortcode will determine the number of customer reviews displayed.">
			Max Reviews:
			</td>
			<td align=right valign=top>		
					<input name=loc size=25 id='id_phpzon_maxreviews' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: When setting up global settings, there is a choice between full description and a compact (or truncated) description. However, there may be times when you need to override this setting. If description is set to 'true' and compact description is set as the default, then the full description will be used.">
			Description
			</td>
			<td align=right valign=top>		
					<select name=loc id='id_phpzon_description'>
					<option value='' SELECTED=TRUE></option>
					<option value='true'>TRUE</option>
					<option value='false' >FALSE</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: When setting up global settings, there is a choice between full description and a compact (or truncated) description. However, there may be times when you need to override this setting. If truncate is set to 'true' and compact description is set as the default, then the truncated description will be used.">
			Truncate
			</td>
			<td align=right valign=top>		
				<select name=loc id='id_phpzon_truncate'>
					<option value='' SELECTED=TRUE></option>
					<option value='true'>TRUE</option>
					<option value='false' >FALSE</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: Ideally you should use the menu builder for building search queries. columns should only be used when a template for columns is selected. columns represents the number of columns to display results. Keep in mind that Amazon will return ten results, so 2 columns is always the ideal setting.">
			Collumns:
			</td>
			<td align=right valign=top>		
					<input name=loc size=25 id='id_phpzon_collumns' value=''>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: Used to activate paging of results. Amazon will return ten items per page. If paging is turned on (set to 'true') then pagination controls are displayed for a user to navigate additional pages. This shortcode is optional.">
			Paging:
			</td>
			<td align=right valign=top>		
				<select name=loc id='id_phpzon_paging'>
					<option value='' SELECTED=TRUE></option>
					<option value='true'>TRUE</option>
					<option value='false' >FALSE</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: reviewsort tells Amazon how to sort reviews. Sometimes products can have inaccurate or unwarranted low reviews by a few customers. This shortcode tells Amazon how you want reviews returned.">
			Review Sort:
			</td>
			<td align=right valign=top>		
				<select name=loc id='id_phpzon_reviewsort'>
					<option value='' SELECTED=TRUE></option>
					<option value='-OverallRating'>Best Ratings First</option>
					<option value='OverallRating'>Worst Rating First</option>
					<option value='-HelpfulVotes'>Most Helpful Votes First</option>
					<option value='HelpfulVotes'>Least Helpful Votes First</option>	
					<option value='-Rank'>Best Rated Reviews First</option>	
					<option value='Rank'>Wors Rated Reviews First</option>	
					<option value='-SubmissionDate'>Newest Reviews First</option>	
					<option value='SubmissionDate'>Oldest Reviews First</option>	
				</select>
			</td>
		</tr>
		<tr>
			<td valign=top width=300>
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: IphpZon Pro has a template folder. Each template is contained in it's own folder. Specify the folder name of the template to be used, as the templatename.">
			Template Name:
			</td>
			<td align=right valign=top>		
					<input name=loc size=25 id='id_phpzon_templatename' value=''>
			</td>
		</tr>
	</table>
		
	</div>
</div>
