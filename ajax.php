<?php
# include base application file
include_once( 'application.php' );

# Call all AJAX functions
ajax_insert_item();
ajax_remove_item();
ajax_tick_item();

die( 'No ajax actions detected.' );
