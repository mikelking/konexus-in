<?php

/**
 * Session existance check.
 * 
 * Helper function that checks to see that we have a 'set' $_SESSION that we can
 * use for the demo.   

function oauth_session_exists() {
  if((is_array($_SESSION)) && (array_key_exists('oauth', $_SESSION))) {
    return TRUE;
  } else {
    return FALSE;
  }
}
*/ 

/**
 * Setup the LinkedInOauth object
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 * 
 * @return object LinkedInOauth
 */
function nexus_primary_connector(){

	//print("The session state is: " . is_array($_SESSION));

	global $nexus;
	global $user;
	global $linkedin_status;
	$access = array();


//	unset($nexus_nonce);
	$nexus_access_key = get_the_author_meta('nexus_access_key', $user->ID);
	$nexus_access_secret = get_the_author_meta('nexus_access_secret', $user->ID);
	$nexus_nonce = get_the_author_meta('nexus_nonce', $user->ID);

	/**
	 * Experienced some freaky with the nonce after the first save into the db.
	 * The system retained a string 0 "" value even afte rthe property was 
	 * deleted from teh db. I must say I am flumoxed so I found this solution 
	 * to set things back to origin.
	 */
	if(strlen($nexus_nonce)==0){
		unset($nexus_nonce);
	}

	/**
	 * These values do not work if out of scope and they need to be called
	 * just prior to the OAuth library. Otherwise they seem to get clobbered
	 * by Wordpress.
	 */
	include_once('consumer.php');
	
	// will be replaced with a user property check.
	// include_once('access.php');


	// load the LinkedIn OAuth class
	include_once('LinkedInOAuth.php');
	/**
	 * If the object is not then we get one. However, we should check for $access first byt examining the User's 
	 * options. Each user should have their own key & secret pair stored for all of this to work.
	 */

	if(!isset($nexus)) {

		if($nexus_access_key && $nexus_access_secret) {
			// open a normal connection
			try {
				$nexus = new LinkedInOAuth($consumer['key'], $consumer['secret'], $nexus_access_key, $nexus_access_secret);
			} catch (OAuthException $e) {
			$buffer = $e;
			}
		} else {
			if(!isset($nexus_nonce)){
				try {
					$nexus = new LinkedInOAuth($consumer['key'], $consumer['secret']);
					/**
					* Attempting to solve the oauth regitration issue
					* My theory is that the nonce aka PIN must be submitted back using
					* the same connection you initiated the token request from or it will
					* fail. The problem with Wordpress is the page reload which results
					* in a new connection being formed.
					*
					* So I am storing the $nexus object in a session var
					*/
					$_SESSION['oauth_register']=$nexus;
				} catch (exception $e) {
					$buffer = $e;
				}
				$linkedin_status=$nexus->status;
				if ( strcmp($nexus->status,'request_new_token')){
					$request_token = $nexus->nonce_based_token_request();
					// this may fail utterly
					$_SESSION['oauth_request_token']=$request_token['oauth_token'];
					$_SESSION['oauth_request_token_secret']=$request_token['oauth_token_secret'];
					
					$request_url="https://www.linkedin.com/uas/oauth/authenticate?oauth_token";
					
					$fmt  = '<h3>Authorize LinkedIn Connectivity</h3>Please visit this URL:<a href="';
					$fmt .= $request_url;
					$fmt .= '=';
					$fmt .= $request_token['oauth_token'];
					$fmt .= '" target="new_window_baby" > Connect to LinkedIn</a><br />';
					$fmt .='In your browser and then return here to input the numerical pin you are provided: ';

					// setup the Nonce capture form element
					$fmt .= '<table><tr><th><label for="nexus_nonce">Verify PIN:</label></th>';
					$fmt .= '<td><input type="text" name="nexus_nonce" id="nexus_nonce" value="" />';
					$fmt .= '<br />Please update the profile after entering the PIN.</span></td></tr></table>';

					print($fmt);
				}
			} else {
				print('<h3>LinkedIn Connectivity Authorized</h3>');
				/**
				* If the session var is set and it should be if we successfuly 
				* requested a nonce, then I am attempting to pull the oauth object
				* back out of storage and use it to finish the job.
				*/
					try {
						$nexus = new LinkedInOAuth($consumer['key'], $consumer['secret']);
					} catch (OAuthException $e) {
						$buffer = $e;
						print $e;
					}
					// this is an attempt to reuse an existing OAuth connection
					/*
					print($_SESSION['oauth_request_token']);
					print('<br />');
					print($_SESSION['oauth_request_token_secret']);
					print('<br />');*/
					$nexus->oauth->setToken($_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);
				$linkedin_status=$nexus->status;
				try {
				// get the access token now that we have the verifier pin
				$access=$nexus->oauth->getAccessToken("https://api.linkedin.com/uas/oauth/accessToken", "", $nexus_nonce);
				} catch (OAuthException $e) {
					print $e;
				}
				//$access=$nexus->verify_nonce($nexus_nonce);
				
				var_dump($access);
				$nexus->set_access_token($access['oauth_token'],$access['oauth_token_secret']);

				// setup the Nonce capture form element
				$fmt = '<table><tr><th><label for="nexus_access_key">Access Key Pair:</label></th><td>';
				$fmt .= $access['oauth_token'] . '<br />' . $access['oauth_token_secret'];
				$fmt .= '<br /><span>Please update the profile to save these values.</span></td></tr></table>';
				$fmt .= '<input type="hidden" name="nexus_access_key" id="nexus_access_key" value="';
				$fmt .= $access['oauth_token'];
				$fmt .= '" /><br /><input type="hidden" name="nexus_access_secret" id="nexus_access_secret" value="';
				$fmt .= $access['oauth_token_secret'];
				$fmt .= '" /><br />';

				print($fmt);
			}
		}
	} 
	
	// probably not necessary since it's global but I would like to phase out the global so...
	//return $nexus;
}

/**
 * Save Nonce Field
 * @author Mikel King <mikel.king@olivent.com>
 *
 * @param int $user_id
 */
function nexus_save_nonce_field( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		update_usermeta( $user_id, 'nexus_nonce', $_POST['nexus_nonce'] );
}
add_action( 'personal_options_update', 'nexus_save_nonce_field' );
add_action( 'edit_user_profile_update', 'nexus_save_nonce_field' );




/**
 * Save nexus_access_key_pair
 * @author Mikel King <mikel.king@olivent.com>
 *
 * @param int $user_id
 */
function nexus_access_key_pair( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		update_usermeta( $user_id, 'nexus_access_key', $_POST['nexus_access_key'] );
		update_usermeta( $user_id, 'nexus_access_secret', $_POST['nexus_access_sercret'] );
}
add_action( 'personal_options_update', 'nexus_access_key_pair' );
add_action( 'edit_user_profile_update', 'nexus_access_key_pair' );




?>