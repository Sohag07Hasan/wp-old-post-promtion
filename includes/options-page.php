<style>
  .valid_credentials{color:green;}
  .invalid_credentials{color:red;}
  .size-1{width:400px;}
</style>


<div class="wrap">

	<h2> Old Post Promoter Plugin's Settings Page </h2>
	
	<h4>Activate the plugin</h4>
	<small>Please use the unique api key provided when purchase</small>
	<form action="" method="post">
		<input type="hidden" name="oldpost-promoter-activation-submitted" value="Y" />
		<table class="form-table">
			<tr>
				<th>
					<label for="oldpostpromoter-apikey">Api Key</label> 
				</th>
				<td>
					<input class="<?php ?>" type="text" id="oldpostpromoter-apikey" name="oldpostpromoter-apikey" value="" />
					<img src="<?php echo self::get_images_for_stop(); ?>" alt="invalid" title="invalid api key" />
				</td>
			</tr>
			<tr>
				<td> <input type="submit" name="activate" value="Activate" class="button-primary"> </td>
			</tr>
		</table>		
	</form>
	
	<br/><hr/><br/>
	<h4>Cron Settings</h4>
	<small>You can select anyone that suits you</small>
	<form action="" method="post">
		<input type="hidden" name="oldpost-promoter-cron-submitted" value="Y" />
		<table class="form-table">
			<tr>
				<td>
					<input type="radio" id="wp-default-cron" name="wp-default-cron" value="Y" > 
					<label for="wp-default-cron"> Wordpress Default Cron </label>
				</td>				
			</tr>
			<tr>
				<td>
					Please choose a suitable scheduler time
					<select name="wp-default-timeframe">
						<option value="hourly">Hourly</option>
						<option value="twicedaily">Twicedaily</option>
						<option value="daily">Daily</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>
					<input type="radio" id="server-default-cron" name="wp-default-cron" value="N" > 
					<label for="server-default-cron"> Server Cron </label>
				</td>				
			</tr>
			<tr>
				<td colspan="3"> Please use the following link in the server cron scheduler with a suitable time. Before doing this please hit the Change button </td>
			</tr>
			<tr>
				<td colspan="3">
					<input size="85" type="text" value="<?php echo self::get_cron_script(); ?>" readonly />
				</td>
			</tr>
			
			<tr>
				<td> <input type="submit" value="Change" class="button-primary" > </td>
			</tr>
			
		</table>
	</form>
	
</div>