<?php
/**
* Template Name: Autologin-api
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
// Create initial default values for our data array
$err_succ = array(
    'key'   => 0,
    'status' => 'failed',
    'data' => '',
);
//print_r($_POST);
$err_succ['data'] = $_POST;
// Check if the received key is '54321' and if the action is 'get_login_key'
if( isset( $_POST ) && $_POST['key'] == '54321' && $_POST['action'] == 'get_login_key') {
    
    global $wpdb;
    
    // Check if we received a user_login from the POST, if yes - we sanitize it then save it to a variable
    $user_login = isset( $_POST['user_login'] ) ? sanitize_text_field( $_POST['user_login'] ) : '';
    
    // Get the random key of user from the database
    $user_random_key = $wpdb->get_var($wpdb->prepare("
        SELECT random_key FROM wp_autologin WHERE avatar = %s", $user_login) );
    
    // Count the number of user_login from the database. if query returns > 0, then it means it exists on the database.
    $check_user_login = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(user_login) FROM wp_users WHERE user_login = '%s'", $user_login ) );
    
    // Check if the received user_login exists on the wp_users table
    if ($check_user_login > 0) {    
        
        // Check if $user_random_key variable returned a random_key. If no, we generate another random key.
        if(empty($user_random_key)) {
        
            // Generate key using md5 random strings
            $hash_key = md5($user_login + rand(5, 15));
                    
            // Save the avatar(user_login) and key to the database
            $wpdb->insert(
                'wp_autologin', 
                array(
                    'avatar' => $user_login,
                    'random_key' => $hash_key
                )
            );
            
        } else {
            // If $user_random_key variable returned a random_key, we return it to the requesting client.
            $hash_key = $user_random_key;
        }
        
        // Return the hash_key and set the status as success
        $err_succ['key'] = $hash_key;
        $err_succ['status'] = 'success';
        $user = get_user_by('login',$user_login);
    wp_set_current_user($user->ID); 
if (wp_validate_auth_cookie()==FALSE)
{
    wp_set_auth_cookie($user->ID, true, false);
}
        $err_succ['logged'] = is_user_logged_in();
            
    } else {
        
        // If the received user_login does not exist on the database, we return a failed status to the requesting client
        $err_succ['status'] = 'failed';
    }
}

// Set the array to a variable
$result = $err_succ;

// JSON encode the result then send it back to the requesting client
echo json_encode ($result);



?>
