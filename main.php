<?php 
/*
 * plugin name: Old posts Promoter by cron 
 * author: Mahibul Hasan Sohag
 * plguin url: http://google.com
 * author url: http://sohag07hasan.elance.com
 * */


define("OLDPOSTPROMOTER_DIR", dirname(__FILE__));
define("OLDPOSTPROMOTER_FILE", __FILE__);
define("OLDPOSTPROMOTER_URL", plugins_url('', __FILE__));

//includign the classes
include OLDPOSTPROMOTER_DIR . '/classes/class.old-post-promoter.php';
OP_postpromoter::init();

include OLDPOSTPROMOTER_DIR . '/classes/class.op-settings.php';
OP_options::init();

include OLDPOSTPROMOTER_DIR . '/classes/class.metabox.php';
OP_metaboxes::init();
	

?>