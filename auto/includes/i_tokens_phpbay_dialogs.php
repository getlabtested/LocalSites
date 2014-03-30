<div id=id_dialog_phpBay>
	<br>
	<div style='height:344px;width:500px;' align=center>
		<table width=95% >
			<tr>
				<td valign=top width=300>
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Use custom keywords or token variables to populate this field.">
					Keyword:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_keywords' value='%tag_title%'>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="the number of listings to show. If no number is specified, this value will default to 100 automatically">	
					Number Limit:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_limit' value='10'>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This is for the Ebay category. This is optional and doesn't have to be set, however, if you want to fine tune your results from a specific Ebay category, add the category number.">
				Ebay Category:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_category' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: This is for the Ebay category. This is optional and doesn't have to be set, however, if you want to fine tune your results from a specific Ebay category, add the category number.">
				Exclude Keywords:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_exclude' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: This is for displaying auctions from a given postal code. Enter the postal or zip code in this field and phpBay Pro will search for items by the keyword specified within 50 miles of the postal zip code. If you want to specify a distance greater or lesser than the default 50 miles, use the | character as a separator. Example: [phpbay]mp3 player, 20, “”, “”, “75214|75”[/phpbay]">
				Postal Code (Geo Targeting):
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_zip_code' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: This is for listing auctions by a specific seller. Enter the seller's Ebay ID in this field to list only their auctions. This does not return items in store inventory format. Only auctions the specific seller currently has.">
				Specific Seller ID:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_seller_id' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: specifies the minimum price in order to return auction listings. For example, if you are wanting to list Mustang exhaust systems, but are getting a lot of non-exhaust system products in your listings, you could set this value to 200. This would exclude any items below 200 in price, and list only items that are greater than the currency value of 200. Only a numerical value is required. The currency or decimal is not required">
				Minimum Price:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_minimum_price' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: specifies the maximum price in order to return auction listings. For example, if you want to create a store called “DVDs for Ten Dollars or Less,” then you could set the maximum price to 10. Only a numerical value is required. The currency or decimal is not required.">
				Maximum Price:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_maximum_price' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: specifies the minimum bid an auction item must have before being returned. This can be a very useful parameter to encourage impulsive buying/bidding with your users. People generally want a good deal and if they see something of interest and others are bidding on it, they may be more likely to participate. If you use this parameter, you should experiment with it to get the best results. Setting it too high may exclude too many items, while setting it too low may include more items with lower bids.">
				Minimum Bid:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_minimum_bid' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: specifies the maximum bid an auction item must have before being returned. Generally the minimum bid parameter above should be sufficient, but you can use this to cap the maximum number of bids listings have.">
				Maximum Bid:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_maximum_bid' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="optional: This is for listing auctions that are Buy It Now only. In this parameter, enter the number 1 to return Buy It Now auction listings. To return “auctions only,” enter 2. Enter 2 will omit “Buy It Now” items and only list auction items.">
				Buy it Now v Auctions:
				</td>
				<td align=right valign=top>		
					<select name=loc id='id_phpbay_buy_it_now'>
						<option value='' SELECTED=TRUE></option>
						<option value=1>Buy it Now Auctions Only</option>
						<option value=2>Auctions Only</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional:This is for use with affiliates in the Ebay Partner Network. A Custom ID is entered in the phpBay Pro Admin panel, however, you can set this parameter to a different Custom ID for tracking.">
				Campaign Id:
				</td>
				<td align=right valign=top>		
					<input name=loc size=25 id='id_phpbay_campaign_id' value='<?php echo $phpbay_campaign_id;?>'>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: This specifies a different sort order for the auction listing. Sort order is globally set in the phpBay Pro Admin panel, however, if you need to specify a special sort order for a single page or post, use this parameter. Acceptable values for this field are 0 to 6:">
				Sort Order:
				</td>
				<td align=right valign=top>		
					<select name=loc id='id_phpbay_sort_order'>
						<option value='' SELECTED=TRUE></option>
						<option value=0>Best Match</option>
						<option value=1>Time: Ending Soonest</option>
						<option value=2>Time: Newly Listed</option>
						<option value=3>Time: Lowest First</option>
						<option value=4>Time: Highest First</option>
						<option value=5>Price + Shipping: lowest first</option>
						<option value=6>Price + Shipping: highest first</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: This specifies the country to list items from. The country setting is globally set in the phpBay Pro Admin panel, but you can override the global setting by specifying a numerical value that represents the country to display items. This would be useful if you want to specify auction items from Spain in one post, the US in another post, and Australia in yet another post. The values that can be used for this parameter are the Ebay country codes, which are listed below. For example, if you want to set the post to display items by keyword from Australia, you would set this parameter to 15.">
				Country:
				</td>
				<td align=right valign=top>		
					<select name=loc id='id_phpbay_country'>
						<option value='' SELECTED=TRUE></option>
						<option value=0>US Auctions</option>
						<option value=15>Austrailian Auctions</option>
						<option value=16>Austrian Auctions</option>
						<option value=123>Belgian Auctions</option>
						<option value=2>Canadian Auctions</option>
						<option value=71>French Autctions</option>
						<option value=77>German Auctions</option>
						<option value=201>Hong Kong Auctions</option>
						<option value=203>Indian Auctions</option>
						<option value=205>Irish Auctions</option>
						<option value=101>Italian Auctions</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: This specifies using a column layout. This parameter would be used, if Columns display were disabled in the phpBay Pro Admin panel and using a traditional row layout for auction listings. Yet, you may have a need to display a few auction listings in a Column format. The value for this parameter would typically be 2, 3, or 4 where the number represents the number of columns you want to display auctions in. See the phpbay pdf to view examples of how Column listings look. Note that the illustration has three columns. Set this parameter to 3 in order to display items in a three column format as illustrated below.">
				Display Collumns:
				</td>
				<td align=right valign=top>		
						<input name=loc size=25 id='id_phpbay_collumns' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: specifies free shipping. To list only items that have free shipping, set this parameter to 1.">
				Free Shipping:
				</td>
				<td align=right valign=top>		
						<input name=loc size=25 id='id_phpbay_free_shipping' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: specifies paging override. This value should be a 0 or 1. 0 turns paging off. 1 turns paging on. This will override the global paging setting in the phpBay Pro Options Page in Wordpress.">
				Paging Override:
				</td>
				<td align=right valign=top>		
						<input name=loc size=25 id='id_phpbay_paging_override' value=''>
				</td>
			</tr>
			<tr>
				<td valign=top width=300>
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional: This value should ideally be between 2 and 20, representing the number of items to list per page. This overrides the global “items per page” value set in the phpBay Pro Options Page in Wordpress.">
				# of Items per Page:
				</td>
				<td align=right valign=top>		
						<input name=loc size=25 id='id_phpbay_items_per_page' value=''>
				</td>
			</tr>
		</table>		
	</div>
</div>
