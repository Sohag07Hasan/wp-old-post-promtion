<?php
/*
 * promotes the old posts to the new one
 * */

if(class_exists("OP_postpromoter")) return;

class Op_postpromoter{
	
	//initializing 
	static function init(){
		register_activation_hook(OLDPOSTPROMOTER_FILE, array(get_class(), 'create_tables'));
		register_deactivation_hook(OLDPOSTPROMOTER_FILE, array(get_class(), 'delete_tables'));
	}
	
	/*
	 * creats the tables
	 * */
	static function create_tables(){
		global $wpdb;
		
		$scheduler_table = self::get_scheduler_table();
		$scheduler_sql = "CREATE TABLE IF NOT EXISTS $scheduler_table(
			`post_id` bigint unsigned NOT NULL,
			`last_imported` varchar(30) NOT NULL,
			`interval` int unsigned NOT NULL,
			`variance` int unsigned NOT NULL,
			UNIQUE(post_id)
		)";		
		
		$container_table = self::get_post_content_table();
		$container_sql = "CREATE TABLE IF NOT EXISTS $container_table(
			`id` bigint unsigned NOT NULL AUTO_INCREMENT,
			`post_id` bigint unsigned NOT NULL,
			`content` longtext NOT NULL,
			PRIMARY KEY(id) 
		)";

		if(!function_exists('dbDelta')) :
				include ABSPATH . 'wp-admin/includes/upgrade.php';
		endif;
		dbDelta($scheduler_sql);
		dbDelta($container_sql);
	}
	
	
	/*
	 * this will delete the tables if the plguin is deacativated
	 * */
	static function delete_tables(){
		global $wpdb;
		$scheduler_table = self::get_scheduler_table();
		$container_table = self::get_post_content_table();
		
		$wpdb->query("DROP TABLE $scheduler_table");
		$wpdb->query("DROP TABLE $container_table");
	}
	
	
	/*
	 * post contents holding table
	 * */
	static function get_post_content_table(){
		global $wpdb;
		return $wpdb->prefix . 'op_postcontainer';
	}
	
	
	/*
	 * returns the scheduler table
	 * */
	static function get_scheduler_table(){
		global $wpdb;
		return $wpdb->prefix . 'op_scheduler';
	}
}

