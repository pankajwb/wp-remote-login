<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
echo 'start</br>';
// Define the URL where we will be sending a request for a random key
    $api_url = "http://DOMAIN-NAME-HERE/autologin-api/";
    
    // If you are using WordPress on website A, you can do the following to get the currently logged in user:
    //global $current_user; 
    //$user_login = $current_user->user_login;
    
    // Set the parameters
    $params = array(
        'action'            => 'get_login_key', // The name of the action on Website B
        'key'               => '54321', // The key that was set on Website B for authentication purposes.
        'user_login'       => 'admin' // Pass the user_login of the currently logged in user in Website A
    );
    
    // Send the data using cURL
    $ch = curl_init($api_url); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $gbi_response = curl_exec($ch);
    print_r($gbi_response);
    curl_close($ch);
    //print_r($api_url);
    // Parse the response
    parse_str($gbi_response);
    
    // Convert the response from Website B to an array
    $data = json_decode($gbi_response, true);
    echo 'Data received is : ';print_r($data);
    // Set the received key to a variable
    $key = $data['key'];

    echo '<h2 style="text-align:center;color:blue;"><i><a style="color:blue;" target="_blank" href = "http://DOMAIN-NAME-HERE/autologin?key='.$key.'">Login to site two</a></i></h2>';

    ?>
    </main><!-- #main -->
    </div><!-- #primary -->
</div><!-- .wrap -->

