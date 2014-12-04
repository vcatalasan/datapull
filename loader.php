<?php
/*
Plugin Name: Data Pull Plugin
Plugin URI: http://www.bscmanage.com/datapull/
Description: Shortcode for pulling data from database table
Version: 1.0.0
Requires at least: WordPress 2.9.1 / Formidable Pro
Tested up to: WordPress 2.9.1 / BuddyPress 1.2
License: GNU/GPL 2
Author: Val Catalasan
Author URI: http://www.bscmanage.com/staff-profiles/
*/
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require( plugin_dir_path( __FILE__) . 'plugin.php' );

// initialize plugin
add_action( 'init', array( 'DataPullPlugin', 'get_instance' ) );

$plugin_name = DataPullPlugin::get_instance( __FILE__ );

