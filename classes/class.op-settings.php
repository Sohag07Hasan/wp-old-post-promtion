<?php

class OP_options{
	
	//initialize
	static function init(){
		add_action('admin_menu', array(get_class(), 'add_optionsPage'));
	}
	
	
	/*
	 * options page
	 * */
	static function add_optionsPage(){
		add_options_page('Activation Page for Old Post Promoter', 'Post Promoter', 'manage_options', 'old-post-promoter', array(get_class(), 'optionsPage_postPromoter'));
	}
	
	
	/*
	 * options page content 
	 * */
	static function optionsPage_postPromoter(){
		include OLDPOSTPROMOTER_DIR . '/includes/options-page.php';
	}
	
	//returns a symbolic image to show valid api key status
	static function get_images_for_tick(){
		return OLDPOSTPROMOTER_URL . '/images/tick.png';
	}
	
	
	//returns a symbolic image to show invalid api  key status
	static function get_images_for_stop(){
		return OLDPOSTPROMOTER_URL . '/images/stop.png';
	}
}