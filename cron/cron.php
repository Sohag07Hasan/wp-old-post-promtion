<?php

/*
 * This script runs by cron
 */

set_time_limit(300);

include '../../../../wp-load.php';
OP_cron_handler :: handle_scheduler();

?>
