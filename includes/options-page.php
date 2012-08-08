<style>
  .valid_credentials{color:green;}
  .invalid_credentials{color:red;}
  .size-1{width:400px;}
</style>


<div class="wrap">

	<h2> Old Post Promoter Plugin's Settings Page </h2>
	
	<form action="" method="post">
		<input type="hidden" name="oldpost-promoter-submitted" value="Y" />
		<table class="form-table">
			<th>
				<label for="oldpostpromoter-apikey">Api Key</label> 
			</th>
			<td>
				<input class="<?php ?>" type="text" id="oldpostpromoter-apikey" name="oldpostpromoter-apikey" value="" />
				<img src="<?php echo self::get_images_for_stop(); ?>" alt="invalid" title="invalid api key" />
			</td>
			<tr>
				<td> <input type="submit" name="activate" value="Activate" class="button-primary"> </td>
			</tr>
		</table>		
	</form>
	
</div>