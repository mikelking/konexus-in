<?php

/**
 * This section adds the option of using the headlin from your LinkedIn profile page as your byline. 
 * Originally I attempted this with jq but the wp_enqueue_script() requires a src url and given my dynamic
 * IP address compbined with flaky internet access I opted for the more solid solution. 
 *
 */

/**
 * Use Custom byline if checkeded
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 * 
 * @param object $user
 */
function nexus_custom_byline_field( $user ) { 

	global $nexus;
	// check for linkedin connection if there isn't one try to make one.
	if (!isset($nexus)) {
		try {
			$_SESSION['nexus_called_by'] = 'nexus_custom_byline_field';
			nexus_primary_connector( $user );
		} catch (OAuthException $e) {
			$buffer = $e;
		}
		$profile = "people/~:(headline)";
		if(strcmp($_SESSION['nexus_status'],'ready') == 0) {
			$nexus->fetchProfile($profile);
		}

	} else {
		$profile = "people/~:(headline)";
		if(strcmp($_SESSION['nexus_status'],'ready') == 0) {
			$nexus->fetchProfile($profile);
		}

	}

	/**
	 * The byline insertion was designed with this work flow because I wanted to leave the possibility
	 * of the user changing their mind before saving the new bio. 
	 */
		if(strcmp($_SESSION['nexus_status'],'ready') == 0) {
?>
	<h3>Custom Byline</h3>
	
	<table>
	<tr>
	<th><label for="nexus_custom_byline">Set Custom Byline:</label></th>
	<td>
	<input type="checkbox" name="description" id="nexus_custom_byline" value="<?php print($nexus->response['headline']);?>" />
	<span>Replace Bio with: <?php print($nexus->response['headline']);?><br />
	<strong>NOTE:</strong>Once you save the changes it is not undoable.<br /></span>
	</td>
	</tr>
	</table>
	<?php 
	}
	
} // end 

add_action( 'show_user_profile', 'nexus_custom_byline_field' );
add_action( 'edit_user_profile', 'nexus_custom_byline_field' );


/**
 * Save Custom Byline Field
 * @author Mikel King <mikel.king@olivent.com>
 *
 * @param int $user_id
 */
function nexus_save_custom_byline_field( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		update_usermeta( $user_id, 'nexus_custom_byline', $_POST['nexus_custom_byline'] );
}
add_action( 'personal_options_update', 'nexus_save_custom_byline_field' );
add_action( 'edit_user_profile_update', 'nexus_save_custom_byline_field' );


?>