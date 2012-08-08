<div class="wrap">
	<h2>Upload your content that to append it when promote the post</h2>
	
	<div id="oldpost-promoter-ajax-loader"  style="text-align: center; display: none"><img  src="<?php echo $image;?>" /></div>
	<input type="hidden" id="oldpost-promoter-post-id" value="<?php echo $post->ID; ?>" />
	<div id="oldpost-promoter-ajaxresponse" style="display:none"></div>
	<table class="form-table" id="manual-comment-uploader-table">
		<tr>
			<td>
				<textarea rows="3" cols="88" id="oldpost-promoter-post-content"></textarea>
			</td>			
		</tr>
		<tr>
			<td>
				<input type="button" value="add to the scheduler" id="oldpost-promoter-add-to-the-scheduler" />
			</td>
		</tr>
	</table>
	
</div>