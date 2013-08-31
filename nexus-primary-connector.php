<?php 

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
	 * Experienced something freaky with the nonce after the first save into the db.
	 * The system retained a string 0 "" value even after the property was deleted
	 * from teh db. I must say I am flumoxed so I found this solution to set 
	 * things back to origin.
	 */
	if(strlen($nexus_nonce)==0){
		unset($nexus_nonce);
	}
	

	
	/**
	 * These values do not work if out of scope and they need to be called
	 * just prior to the OAuth library. Otherwise they seem to get clobbered
	 * by Wordpress. At some point I intend to move these into a plugin options
	 * page to store them in the WP system.
	 */
	include_once('consumer.php');

	// load the LinkedIn OAuth class
	include_once('LinkedInOAuth.php');

	/**
	 * If the object is not then we get one. However, we should check for $access first by examining the User's 
	 * options. Each user should have their own key & secret pair stored for all of this to work.
	 * note: trying to transition to session tracking.
	 */
	if( !isset($nexus) || !isset($_SESSION['nexus']) ) {
		$_SESSION['nexus_status']='not_ready';
		/**
		 * If we have the keys we need then let's open a connection
		 */
		if($nexus_access_key && $nexus_access_secret) {
			// open a normal connection
			try {
				$nexus = new LinkedInOAuth($consumer['key'], $consumer['secret'], $nexus_access_key, $nexus_access_secret);
				$_SESSION['nexus'] = $nexus;
				$_SESSION['nexus_status'] = 'ready';
			} catch (OAuthException $e) {
			$buffer = $e;
			}
		} else {
			/**
			 * So far this works more reliably than the get prperty form Wordpress
			 * I also think it's safer to just make a nonce in lieu of asking the
			 * user to provide on. If all goes well I will delete the property
			 * form my db. Less cruft!
			 */
			if(!isset($_SESSION['nexus_nonce'])){
				include_once('nexus-create-nonce.php');
				$_SESSION['nexus_nonce'] = create_nexus_nonce();
			} else {
				/**
				 * Sire he said they've already got one and it look's very very nice.
				 *
				 * So we are going to build a giant rabbit add...
				 *
				 * We are still missing the oauth tokens. 
				 *
				 * Actually we will present a link and possibly a button to the user
				 * The link will open a new page on the bridge site where the user can
				 * authorize the linkedin connection.
				 * 
				 * The button should execute the retrieval of the oauth token pair once
				 * they have completed that operation. Yeah I'm not 100% on this either.
				 */
				include_once('nexus-bridge-connection.php');				 
				nexus_bridge_connection();
				/*
				include_once('nexus-retrieve-oauth-token.php');
				$nexus_retreived_oauth_token = nexus_retrieve_oauth_token();
				print('<br />');
				print($nexus_retreived_oauth_token['oauth_token']);
				print('<br />');
				print($nexus_retreived_oauth_token['oauth_token_secret']);	
				print('<br />');
				*/

			}
			
			/**
			 * If we don't have those keys then we need to get some.
			 * At this point I have several borked ideas
			 * include_once('nexus-retrieve-oauth-token.php');
			 * to get the nexus_retrieve_oauth_token();
			 */

		
		}		
	} 
	
	// probably not necessary since it's global but I would like to phase out the global so...
	//return $nexus;
	
} // end nexus_primary_connector

/**
 * Save Nonce Field & retrieve the oauth tokens
 *
 * You might want to buckle up because this is going to get a little bumby.
 *
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
	include_once('nexus-retrieve-oauth-token.php');
	$nexus_retreived_oauth_token = nexus_retrieve_oauth_token();
	print('<br />');
	print($nexus_retreived_oauth_token['oauth_token']);
	print('<br />');
	print($nexus_retreived_oauth_token['oauth_token_secret']);	
	print('<br />');
	
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		update_usermeta( $user_id, 'nexus_access_key', $nexus_retreived_oauth_token['oauth_token'] );
		update_usermeta( $user_id, 'nexus_access_secret', $nexus_retreived_oauth_token['oauth_token_secret'] );
}
add_action( 'personal_options_update', 'nexus_access_key_pair' );
add_action( 'edit_user_profile_update', 'nexus_access_key_pair' );




?>