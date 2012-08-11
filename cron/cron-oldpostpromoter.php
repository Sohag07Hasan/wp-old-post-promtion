<?php

/*
 * this class handles both the wordpress default cron and server cron
 * */
class OP_cron_handler{
	
	/*
	 * init
	 */
	static function init(){
		
		//cron hook
		add_action('oldpostpromoter_scheduling', array(get_class(), 'handle_scheduler'));
	}
	
	
	/*
	 * It checks whether the request is default or server cron and handles everything 
	 */
	static function handle_scheduler(){
		$scheduled_posts = self::get_scheduled_posts();
		if(empty($scheduled_posts)) return;

		$current_time = current_time('timestamp');
		foreach ($scheduled_posts as $post){
			$last_updated = self::get_last_modifed($post->post_id);
			if($last_updated['status']){
				$interval = (int) $post->interval;
				$variance = (int) $post->variance;
				$min = 0 - $variance;
				$max = $variance;
				$random_variance = rand($min, $max);
				
				$exact_interval = $interval + $random_variance;
				$interval_timestamp = $exact_interval * 24 * 60 * 60;
				$total_timestamp = $interval_timestamp + $last_updated;
				
				if($total_timestamp > $current_time){
					self::update_the_post($post->post_id);
				}
			}
		}
		
	}

	
	/*
	 *returns the scheduled posts
	 */
	static function get_scheduled_posts(){
		global $wpdb;
		$table = Op_postpromoter::get_scheduler_table();
		
		return $wpdb->get_results("SELECT * FROM $table");
	}

	
	/*
	 * get last modified timestamp 
	 * */
	static function get_last_modifed($post_id){
		global $wpdb;
		$mysql_time = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$post_id' and post_status = 'publish'");
		if($mysql_time){
			$updated = array(
				'status' => true,
				'timstamp' => strtotime($mysql_time)
			);
		}
		else{
			$updated = array(
				'status' => false,
				'timstamp' => 0
			);
		}
		
		return $updated;
	}
	
	
	/*
	 * update the post time stamp
	 * */
	static function update_the_post($post_id){
		$new_data = array(
			'ID' => $post_id,
			'post_status' => 'publish',
			'post_date' => current_time('mysql'),
		);
		
		$additional_post_content = self::get_additional_post_content($post_id);
		if($additional_post_content){
			$content = $additional_post_content->content;
			$new_data['post_content'] = $additional_post_content;
		}
		
		$new_post_id = wp_insert_post($new_data);
		if($new_post_id){
			return self::remove_the_postcontent($additional_post_content);
		}
	}
	
	
	/**
	 * returns the additional post content to add with the previous post
	 * */
	static function get_additional_post_content($post_id){
		global $wpdb;
		$table = Op_postpromoter::get_post_content_table();
		return $wpdb->get_var("SELECT content FROM $table WHERE post_id = '$post_id' ORDER BY id LIMIT 1");
	}
	
	
	/*
	 * remove the content after updating
	 * */
	static function remove_the_postcontent($additional_post_content){
		$id = $additional_post_content->id;
		global $wpdb;
		$table = Op_postpromoter::get_post_content_table();
		
		return $wpdb->query("DELETE FROM $table WHERE id = '$id'");
	}
}