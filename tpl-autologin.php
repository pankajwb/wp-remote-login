<?php
/**
* Template Name: Autologin page
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

/* api code */
global $wpdb;
// Check if user is already logged in, redirect to account if true
if (!is_user_logged_in()) {

    // Check if the key is set and not emtpy
    if(isset($_GET['key']) && !empty($_GET['key'])){

        // Sanitize the received key to prevent SQL Injections
        $received_key = sanitize_text_field($_GET['key']);
        
        // Find the username from the database using the received key 
        $get_username = $wpdb->get_var($wpdb->prepare("SELECT avatar FROM wp_autologin WHERE random_key = %s", $received_key ) );
        
        // Check if query returned a result, throw an error if false
        if(!empty($get_username)){
        
            // Get user info from username then save it to a variable
            $user = get_user_by('login', $get_username );
            
            // Get the user id then set the login cookies to the browser
            wp_set_auth_cookie($user->ID);
            
            // To make sure that the login cookies are already set, we double check.
            foreach($_COOKIE as $name => $value) {
                
                // Find the cookie with prefix starting with "wordpress_logged_in_"
                if(substr($name, 0, strlen('wordpress_logged_in_')) == 'wordpress_logged_in_') {
                
                    // Redirect to account page if the login cookie is already set.
                    wp_redirect( home_url('/account/') );
                    
                } else {
                
                    // If NOT set, we loop the URL until login cookie gets set to the browser
                    wp_redirect( home_url('/autologin/?key=' . $received_key ) );
                        
                }
            }
            
        } else {
            echo 'Invalid Authentication Key';
        }
    } else {
        wp_redirect( home_url() );
    }

} else {
    wp_redirect( home_url('/wp-admin/') );
    exit;
}



 ?>

