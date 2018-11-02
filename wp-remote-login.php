<?php

/*
 * *
 * * Plugin Name: Wp remote login
 *
 *
 * * Plugin for handling login from known another site or app 
 *
 * */

register_activation_hook( __FILE__, 'my_plugin_install_function');
function my_plugin_install_function()
{	
	/* Create needed database table */
	global $wpdb;
	$table_name = $wpdb->prefix . 'autologin';

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    	$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			avatar VARCHAR(45) NULL,
			random_key VARCHAR(45) NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

update_option('table if create','yes '.$sql);
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}else{
		//silence is golden
	}
	/* Create pages in wp-admin and assign templates */
    $api_page = get_option('api_page'); 
    $autologin_page = get_option('autologin_page');
    if(!$api_page){
        //post status and options
        $post = array(
              'comment_status' => 'closed',
              'ping_status' =>  'closed' ,
              'post_author' => 1,
              'post_date' => date('Y-m-d H:i:s'),
              'post_name' => 'autologin-api',
              'post_status' => 'publish' ,
              'post_title' => 'Autologin-api',
              'post_type' => 'page',
        );  
        //insert page and save the id
        $newvalue = wp_insert_post( $post, false );
        if ( $newvalue && ! is_wp_error( $newvalue ) ){
            update_post_meta( $newvalue, '_wp_page_template', 'tpl-autologin-api.php' );
        }
        //save the id in the database
        update_option( 'api_page', $newvalue );
    }
    if(!$autologin_page){
        //post status and options
        $post = array(
              'comment_status' => 'closed',
              'ping_status' =>  'closed' ,
              'post_author' => 1,
              'post_date' => date('Y-m-d H:i:s'),
              'post_name' => 'autologin',
              'post_status' => 'publish' ,
              'post_title' => 'Autologin',
              'post_type' => 'page',
        );  
        //insert page and save the id
        $newvalue = wp_insert_post( $post, false );
        if ( $newvalue && ! is_wp_error( $newvalue ) ){
            update_post_meta( $newvalue, '_wp_page_template', 'tpl-autologin.php' );
        }
        //save the id in the database
        update_option( 'autologin_page', $newvalue );
    }
}

add_action('admin_menu', 'test_plugin_setup_menu');
 
function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}
 
function test_init(){
        echo "<h1>Hello World!</h1>";
        print_r(get_option('api_page'));echo '</br>';
        print_r(get_option('autologin_page'));
        echo '<br>';
        print_r(get_option('table if create'));

}
function cpte_force_template( $template )
{	
    if( is_page( 'autologin' ) ) {
        $template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/tpl-autologin.php';
	}
 
	if( is_page( 'autologin-api' ) ) { //die('this');
        $template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/tpl-autologin-api.php';
	}
 
    return $template;
}
add_filter( 'template_include', 'cpte_force_template',10,1 );


 



