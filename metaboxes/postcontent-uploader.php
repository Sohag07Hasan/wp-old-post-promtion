<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#post').attr('enctype', 'multipart/form-data');
	});
</script>

<div class="wrap">
	
	<h2>Import Post Content </h2>
				
	<table class="form-table">
		<tr>
			<td>Content Statistics </td>
			<td> Live contents : <?php echo $statistics['live']; ?> </td>
			<td> Remaining contents: <span id="oldpostpromoter-remaining"><?php echo $statistics['remaining']; ?></span> </td>
		</tr>
		<tr>
			<td>Upload a csv file (contents)</td>
			<td> <input type="file" name="oldpostpromoter_content" /> </td>
		</tr>	
		
	</table>
</div>
