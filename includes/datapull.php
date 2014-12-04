<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class DataPull {

    var $version = '1.0.0';

    protected static $settings = array();

    function __construct() {
        self::get_settings();
        self::register_shortcodes();
        self::register_callbacks();
        self::register_scripts_stylesheets();
        self::includes();
    }

    // plugin custom codes

    function get_settings() {
        //TODO: implement as dashboard panel settings
        self::$settings = array(
            'option_1' => $value_1,
            'option_n' => $value_n
        );
    }

    function register_shortcodes() {
        add_shortcode( 'datapull', array( $this, 'api_shortcode' ));
    }

    function register_callbacks() {

    }

    function register_scripts_stylesheets() {
        add_action( 'admin_enqueue_scripts', array( $this, 'custom_scripts' ));
        add_action( 'wp_enqueue_scripts', array( $this, 'custom_scripts' ));
        add_action( 'wp_enqueue_scripts', array( $this, 'custom_stylesheets' ));
        add_action( 'wp_print_styles', array( $this, 'custom_print_styles'), 100 );
    }

    function custom_scripts() {
        //wp_enqueue_script( 'tablesort', self::$settings['program']['dir_url'] . 'includes/jquery-tablesorter/jquery.tablesorter.min.js' );
        //wp_enqueue_script( 'tablesort-widget', self::$settings['program']['dir_url'] . 'includes/jquery-tablesorter/jquery.tablesorter.widgets.min.js' );
        wp_enqueue_script( 'datapull-script', self::$settings['program']['dir_url'] . 'script.js' );
    }

    function custom_stylesheets() {
        wp_enqueue_style( 'datapull-style', self::$settings['program']['dir_url'] . 'style.css' );
    }

    function custom_print_styles() {
        //wp_deregister_style( 'wp-admin' );
    }

    function includes() {
        //include( self::$settings['program_dir_path'] . 'includes/database.php' );
    }

    //__________________________________________________________________________________________________________________

    var $data = array();

    function api_shortcode( $atts, $content = null ) {
        global $current_user;

        // list of apis
        $apis = array(
            'data' => 'pull_data'   //action maps to function
        );

        // arguments passed from shortcode
        $args = shortcode_atts( array(
            'user' => $current_user->ID,
            'action' => 'data',
            'source' => null,     //table or view name
            'fields' => '*',   // defaults to all fields
            'filter' => null,
            'template' => $content
        ), $atts );

        $action = $args['action'];

        // call action if exist
        return method_exists( $this, $apis[ $action ] ) ? call_user_func( array( $this, $apis[ $action ] ), $args ) : print_r( $atts, true );
    }

    function pull_data( $args ) {
        extract( $args );
        // ( $source, $fields, $filter, $template );

        $query_string = $this->map_object_name( 'REQUEST', $_REQUEST );
        $template = $this->do_template( $template, $query_string );
        $filter and $filter = $this->do_template( $filter, $query_string );

        // process data
        $output = '';
        $data = $this->get_data( $source, $fields, $filter );
        foreach ( $data as $values ) {
            $output .= $this->do_template( do_shortcode( $template ), (array) $values );
        };
        return $output;
    }

    function get_data( $table, $fields, $filter ) {
        global $wpdb;

        empty( $filter ) and $filter = 1;

        $query = "SELECT $fields FROM $table WHERE $filter";
        return $wpdb->get_results( $query );
    }

    function map_object_name( $object, array $values ) {
        foreach ( $values as $key => $value ) {
            $new_key = "{$object}.{$key}";
            $values[ $new_key ] = $value;
            unset( $values[ $key ] );
        }
        return $values;
    }

    function do_template( $template, array $values ) {
        $keys = array_map( function( $key ){ return '{{' . $key . '}}'; }, array_keys( $values ));
        return str_replace( $keys, $values, $template );
    }
}