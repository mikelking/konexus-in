<?php

 /**
 * Check the value of nexus_access_key and nexus_access_secret
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 * 
 * @param object $user
 */
function nexus_set_access_properties() { 

	global $nexus;
	global $user;		

	$nexus_access_key = get_the_author_meta('nexus_access_key', $user->ID);
	$nexus_access_secret = get_the_author_meta('nexus_access_secret', $user->ID);
	$nexus_nonce = get_the_author_meta('nexus_nonce', $user->ID);
	printf("The userid is: %s <br>\nnonce is: %s<br />\n", $user->ID, $nexus_nonce);

	/**
	 * If we do not have these two properties then we need to offer the option of getting them
	 * This means that the user will be redirected to LinkedIn via OAuth and hopefully authorize
	 * the connection. Should everything go as planned the user will recieve a nonce which they
	 * will enter into the apprpriate field to request the access token pair. Once those are 
	 * recieved we save them as properties in the user's profile.
	 * 
	 * Sounds easy enough... what's the worst that could happen? 
	 */
	
	if( !$nexus_nonce ) {
			
		// setup a basic linkedin connection to request a nonce
		include_once('nexus-auxilary-connector.php');
		
		$request_token = $nexus->nonce_based_token_request();
		$request_url="https://www.linkedin.com/uas/oauth/authenticate?oauth_token";
		
		print('<h3>Authorize LinkedIn Connectivity</h3>');
		print('Please visit this URL:<br />\n\n');
		printf('<a href="%s=%s" target="new_window_baby">Connect to LinkedIn<br />', $request_url, $request_token["oauth_token"]);
		print("\n\nIn your browser and then return here to input the numerical pin you are provided: ");
			
		// setup the Nonce capture form element
		$fmt = '<table><tr><th><label for="nexus_nonce">Verify PIN:</label></th>';
		$fmt .= '<td><input type="text" name="nexus_nonce" id="nexus_nonce" value="" />';
		$fmt .= '<br />Please update the profile after entering the PIN.</span></td></tr></table>';

		return($fmt);
			
		/**
		 * pressing Update Profile will save the nonce value which is silly because it's 
		 * only actually needed once but we're going to hang onto it for logical purposes.
		 *
		 * This should alos give entry into the next segment.
		 */
			
		} 
	/**
	* This logic has been vetted and basically works but the else statement is not cooperating 100%.
	*/
	if( $nexus_nonce && !$nexus_access_key && !$nexus_access_secret ) {

				/**
				 * setup the hidden access form elements
				 * I think at this point we should display the key pair and ask 
				 * the user to update the profile one more time to save them into the db.
				 * It's not the most elegant solution but definitely functional
				 */
			$fmt = '<table><tr><th><label for="nexus_nonce">Verify PIN:</label></th>';
			$fmt .= '<td><input type="text" name="nexus_nonce" id="nexus_nonce" value="" />';
			$fmt .= '<br />Please update the profile after entering the PIN.</span></td></tr></table>';
		
	} /* else {

		// Do the stuff we normally do
		$fmt="We have a nonce is: %s<br /> A key: %s<br />And a secret:%s<br />\n";
		printf($fmt, $nexus_nonce, $nexus_access_key, $nexus_access_secret);
		
		// setup the linkedin connection
		include_once('nexus-primary-connector.php');

		// sets up the widget
		include_once('nexus-widget-preflight.php');

		// adds the byline from the linkedin headline option
		include_once('nexus-custom-byline.php');

		// add the linkedin avatar option
		include_once('nexus-custom-avatar.php');
	

	} */
} //end nexus_set_access_properies

add_action( 'show_user_profile', 'nexus_set_access_properties' );
add_action( 'edit_user_profile', 'nexus_set_access_properties' );


?>