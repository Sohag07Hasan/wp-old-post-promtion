<div class="wrap">
	
	<input type="hidden" name="op_upload_post_id" value="<?php echo $post->ID; ?>" />
	
	<h2>Old Post Promoter Schedule </h2>
	<p>Enable the Scheduler <input type="checkbox"  <?php checked('Y', $enabled) ?>  name="enable_post_promotion" value="Y" /></p>
	<table class="form-table">
			
		<tr>
			<td>SCHEDULER :</td>
			<td>DAY <?php echo self::get_scheduled_days($schedule_info->interval); ?></td>
			<td>VARIANCE (+-days) <?php  echo self::get_scheduled_variance($schedule_info->variance); ?> </td>
		</tr>
	</table>
		
	
</div>
