<div id=id_dialog_image>
	<br>
	<div style='height:344px;width:500px;' align=center>
		<table width=95% >
			<tr>
				<td valign=top width=300>Please Select Source:</td>
				<td align=right valing=top>		
					<select name=source id=id_image_source title='Please Select Source' style='width:274px;'>
						<option>Please Select Source</option>
						<option>Local Folder</option>
						<option>Flickr</option>
						<option>Picasa</option>
					</select>
				</td>
			</tr>
			<tr><td colspan=2 align=left style='font-size:10px;'>
				<br>
				<div id=id_image_content_local_folder style='display:none'>
					<div ><img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Relative path to directory folder that holds the images. Current URL :<?php echo $current_url;?>"> Relative Path Images Directory:
						<div style='float:right;'><input name=loc style='width:274px' id=id_image_local_path value="<?php echo "./../my-images/contentfolder"; ?>">
						</div>
					</div>
					<br>
					<div width='100%' align=right>
						CSS Class: 	<input name=loc size=4 id=id_image_local_class value="">
						&nbsp;&nbsp;
						Float Proppery: 	<input name=loc size=4 id=id_image_local_float value="none">
						&nbsp;&nbsp;
						Max Width: 	<input name=loc size=4 id=id_image_local_max_width value="400">
						&nbsp;&nbsp;
						Max Height: <input name=max_height size=4 id=id_image_local_max_height value="600">
					</div>

					<div id=id_image_content_local_folder_preview style='width:100%'>
					<br><br><br><br><br>
					<center>
						<button class="rounded" id=id_image_button_display_local>
						  <span>Import Images</span>					  
						</button>
					</center>
					</div>
				</div>
				<div id=id_image_content_flickr style='display:none'>
					<div >
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Will use these keywords to search Flickr for images."> Keywords:
						<div style='float:right;'><input name=flickr style='width:280px' id=id_image_flickr_keywords value=""> &nbsp;&nbsp;Max Results: <input name=flickr style='width:50px' id=id_image_flickr_max_results value="100">
						</div>
					</div>
					<br>
					<div width='100%' align=right>
						CSS Class: 	<input name=flickr size=4 id=id_image_flickr_class value="">
						&nbsp;&nbsp;
						Float Proppery: 	<input name=loc size=4 id=id_image_flickr_float value="none">
						&nbsp;&nbsp;
						Max Width: 	<input name=flickr size=4 id=id_image_flickr_max_width value="400">
						&nbsp;&nbsp;
						Max Height: <input name=flickr size=4 id=id_image_flickr_max_height value="600">
					</div>
					
					<div id=id_image_content_flickr_preview style='width:100%'>
					<br><br><br><br><br>
					<center>
						<button class="rounded" id=id_image_button_display_flickr>
						  <span>Import Images</span>					  
						</button>
					</center>
					</div>
				</div>
				<div id=id_image_content_picasa style='display:none'>
					<div >
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Will use these keywords to search picasa for images."> Keywords:
						<div style='float:right;'><input name=picasa style='width:280px' id=id_image_picasa_keywords value=""> &nbsp;&nbsp;Max Results: <input name=flickr style='width:50px' id=id_image_picasa_max_results value="100">
						</div>
					</div>
					<br>
					<div width='100%' align=right>
						CSS Class: 	<input name=picasa size=4 id=id_image_picasa_class value="">
						&nbsp;&nbsp;
						Float Proppery: 	<input name=loc size=4 id=id_image_picasa_float value="none">
						&nbsp;&nbsp;
						Max Width: 	<input name=picasa size=4 id=id_image_picasa_max_width value="400">
						&nbsp;&nbsp;
						Max Height: <input name=picasa size=4 id=id_image_picasa_max_height value="600">
					</div>
					
					<div id=id_image_content_picasa_preview style='width:100%'>
					<br><br><br><br><br>
					<center>
						<button class="rounded" id=id_image_button_display_picasa>
						  <span>Import Images</span>					  
						</button>
					</center>
					</div>
				</div>
				</td>
			</tr>
		</table>
	</div>
</div>
			

<div id=id_dialog_video>
	<br>
	<div style='height:344px;width:470px;' align=center>
		<div >
			<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Will use these keywords to search Youtube for videos."> Keywords:
			<input name=youtube style='width:145px' id=id_video_keywords value=""> &nbsp;&nbsp;Max Results: <input name=video style='width:50px' id=id_video_max_results value="25">&nbsp;&nbsp; Include Description?: <input type=checkbox name=video id=id_video_include_description value=on checked='true'>
			
		</div>
		<br>
		<div width='100%' align=right>
			CSS Class: 	<input name=loc size=4 id=id_video_class value="">
			&nbsp;&nbsp;				
			Video Width: 	<input name=loc size=4 id=id_video_max_width value="480">
			&nbsp;&nbsp;				
			Video Height: <input name=max_height size=4 id=id_video_max_height value="385">
		</div>
		
		<div id=id_video_preview style='width:100%'>
		<br><br><br><br><br><br><br>
		<center>
			<button class="rounded" id=id_video_button_display>
			  <span>Import Videos</span>					  
			</button>
		</center>
		</div>
	</div>
</div>