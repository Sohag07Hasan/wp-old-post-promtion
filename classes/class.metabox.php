<?php
if(class_exists('OP_metaboxes')) return;

class OP_metaboxes{

	//some important constants
	static $days = 360;
	static $days_variance = 300;
	static $log = array();
	
	//initialize everything
	static function init(){
		//add meta boxes
		add_action( 'add_meta_boxes', array(get_class(), 'manage_metaboxes' ));
		
		//import the csv data
		add_action('save_post', array(get_class(), 'import_contents'), 100, 2);
		
		//delete the post
		add_action('deleted_post', array(get_class(), 'delete_post_from_scheduler'));
	}
	
	/*
	 * add metaboxes
	 * */
	static function manage_metaboxes(){
		$post_types=get_post_types();
		foreach($post_types as $post_type){
			if($post_type == 'page') continue;
			add_meta_box( 'OldPostPromoter_scheduler', 'Old Post Promoter Schedule', array(get_class(), 'promotion_scheduler'), $post_type, 'advanced', 'high');
			add_meta_box( 'OldPostPromoter_csv_contentUploader', 'Old Post Promoter Content Uploader', array(get_class(), 'post_content_uploader'), $post_type, 'advanced', 'high');
			add_meta_box( 'OldPostPromoter_single_contentUploader', 'Single Content Uploader', array(get_class(), 'post_singlecontent_uploader'), $post_type, 'advanced', 'high');
		}
	}
	
	
	/*
	 * post promotion schedule
	 * */
	static function promotion_scheduler(){
		global $post;
		$schedule_info = self::get_scheduler_info($post->ID);
		$enabled = self::get_op_enalbe_status($post->ID);	
		include OLDPOSTPROMOTER_DIR . '/metaboxes/promotion-schedule.php';
	}
	
	
	//return the post promotion status
	static function get_op_enalbe_status($post_id){
		return get_post_meta($post_id, "enable_post_promotion", true);
	}
	
	
	/*
	 * returns the scheduled days
	 * */
	static function get_scheduled_days($day){
		$select = '<select name="bulk_day_interval">';
		
			for($i=0; $i<self::$days; $i++){
				$select .= "<option value='$i' ".selected($i, $day, false)."> $i </option>";
			}
		$select .= '</select>';
		
		return $select;
	}
	
	//return the scheduled variance
	static function get_scheduled_variance($day){
		$select = '<select name="bulk_day_variance">';
		
			for($i=0; $i<self::$days_variance; $i++){
				$select .= "<option value='$i' ".selected($i, $day, false)."> $i </option>";
			}
		$select .= '</select>';
		
		return $select;
	}
	
	//post content uploader
	static function post_content_uploader(){
		global $post;
		$statistics = self::get_statistics($post->ID);
		include OLDPOSTPROMOTER_DIR . '/metaboxes/postcontent-uploader.php';
	}
	
	//single post content uploader
	static function post_singlecontent_uploader(){
		include OLDPOSTPROMOTER_DIR . '/metaboxes/single-postcontent-uploader.php';
	}
	
	
	/*
	 * import the csv data
	 * */
	static function import_contents($post_ID, $post){
			
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	
		//is boolean is checking if the post is not a revision
		if($_POST['op_upload_post_id'] == $post_ID) :
			self::save_scheduler_info($post_ID, $post);
			self::upload_csv($post_ID);
		endif;
	}
	
	
	/*
	 * It saves the scheduled date
	 * */
	static function save_scheduler_info($post_ID, $post){
		global $wpdb;
		$table = Op_postpromoter::get_scheduler_table();		
		
		if($_POST["enable_post_promotion"] == "Y") {			
			update_post_meta($post_ID, 'enable_post_promotion', "Y");
			$last_imported = strtotime($post->post_date); 
			//$wpdb->insert($table, array(''));
			if(self::is_scheduled($post_ID, $table)){
				$wpdb->update($table, array('interval'=>$_POST['bulk_day_interval'], 'variance'=>$_POST['bulk_day_variance'], 'last_imported'=>$last_imported), array('post_id'=>$post_ID), array("%d", "%d", '%s'), array("%d"));
			}
			else{
				$wpdb->insert($table, array('last_imported'=>$last_imported, 'post_id'=>$post_ID, 'interval'=>$_POST['bulk_day_interval'], 'variance'=>$_POST['bulk_day_variance']), array("%s", "%d", "%d", "%d"));
			}
		}
		else{
			$wpdb->query("DELETE FROM $table WHERE post_id = '$post_ID'");
			delete_post_meta($post_ID, "enable_post_promotion");
		}
	}
	
	
	/*
	 * returns true, if the current post is scheduled
	 * */
	static function is_scheduled($post_ID, $table){
		global $wpdb;
		return $wpdb->get_var("SELECT post_id FROM $table WHERE post_id = '$post_ID'");
	}
	
	
	/*
	 * returns the scheduler inforamtion of the post
	 * */
	static function get_scheduler_info($post_id){
		global $wpdb;
		$table = Op_postpromoter::get_scheduler_table();
		return $wpdb->get_row("SELECT * FROM $table WHERE post_id = '$post_id'");
		
	}
	
	
	/*
	 * If a post is deleted from the post table it is not necessary to keep it in scheduler table
	 * */
	static function delete_post_from_scheduler($post_id){
		global $wpdb;
		$table = Op_postpromoter::get_scheduler_table();
		$wpdb->query("DELETE FROM $table WHERE post_id = '$post_id'");
		
		$content_table = Op_postpromoter::get_post_content_table();
		$wpdb->query("DELETE FROM $content_table WHERE post_id = '$post_id'");
	}
	
	/*
	 * upload the csv files to use with the post
	 * */
	static function upload_csv($post_ID){
		if (!empty($_FILES['oldpostpromoter_content']['tmp_name'])) {
			if(self::is_csv($_FILES['oldpostpromoter_content'])){
				return self::upload_handler($post_ID);
			}		
		}
	}
	
	/*
	 * checking if the file has an .csv extention
	 */
	static function is_csv($file = array()){				
		return (strpos($file['name'], 'csv')) ? true : false;
	}
	
	
	/*
	 * uploads the csv files
	 * */
	static function upload_handler($post_id){
		if(!class_exists('File_CSV_DataSource')){
			include OLDPOSTPROMOTER_DIR . '/classes/class.csv-importer.php';
		}
		$time_start = microtime(true);
		$csv = new File_CSV_DataSource();
		$file = $_FILES['oldpostpromoter_content']['tmp_name'];
		self::stripBOM($file);

		//now loading file
		if (!$csv->load($file)) return;
			
		//uploading started		
		$csv->symmetrize();
		foreach ($csv->getRawArray() as $csv_data) {
			self::add_post_extention($csv_data, $post_id);				
		}
		
		
	}
	
	
	/*
	 * add post extention from csv file
	 * */
	static function add_post_extention($data, $post_id){
		global $wpdb;
		$table = Op_postpromoter::get_post_content_table();
		if(isset($data[0]) && !empty($data[0])){
			return $wpdb->insert($table, array('post_id'=>$post_id, 'content'=>$data[0]), array('%d', '%s'));			
		}
	}
	
	
	/*
	 * Stip the booms
	 * I don't know what is happening here I just copied the code
	 * */

	static function stripBOM($fname){
		 $res = fopen($fname, 'rb');
		if (false !== $res) {
			$bytes = fread($res, 3);
			if ($bytes == pack('CCC', 0xef, 0xbb, 0xbf)) {
				self::$log['notice'][] = 'Getting rid of byte order mark...';
				fclose($res);

				$contents = file_get_contents($fname);
				if (false === $contents) {
					trigger_error('Failed to get file contents.', E_USER_WARNING);
				}
				$contents = substr($contents, 3);
				$success = file_put_contents($fname, $contents);
				if (false === $success) {
					trigger_error('Failed to put file contents.', E_USER_WARNING);
				}
			} else {
				fclose($res);
			}
		} 
		else {
			slef::$log['error'][] = 'Failed to open file, aborting.';
		}
		
	}
	
	
	/*
	 * return the current post statistics
	 * */
	static function get_statistics($post_id){
		global $wpdb;
		$stat = array();
		$table = Op_postpromoter::get_post_content_table();
		$live = get_post_meta($post_id, "promoter_live", true);
		$remaining = $wpdb->get_var("SELECT COUNT(id) FROM $table WHERE post_id = '$post_id'");
		
		$stat['live'] = ($live) ? $live : 0;
		$stat['remaining'] = ($remaining) ? $remaining : 0;
		
		return $stat;
	}
}
