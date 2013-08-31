<?php

/**
 * Use Custom avatar if entered
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 * 
 * @param object $user
 */
function nexus_custom_avatar_field( $user ) { 

	global $nexus;
	
	// check for linkedin connection if there isn't one make one.
	if (!isset($nexus)) {
			$_SESSION['nexus_called_by'] = 'nexus_custom_avatar_field';
		nexus_primary_connector( $user );
		$profile = "people/~:(pictureUrl)";
		if(strcmp($_SESSION['nexus_status'],'ready') == 0) {
			$nexus->fetchProfile($profile);
		}
	} else {
		$profile = "people/~:(pictureUrl)";
		if(strcmp($_SESSION['nexus_status'],'ready') == 0) {
			$nexus->fetchProfile($profile);
		}

	}
		if(strcmp($_SESSION['nexus_status'],'ready') == 0) {

?>
	<h3>Custom Avatar</h3>
	 
	<table>
	<tr>
	<th><label for="nexus_custom_avatar">Custom Avatar URL:</label></th>
	<td>
	<input type="text" name="nexus_custom_avatar" id="nexus_custom_avatar" value="<?php echo esc_attr( get_the_author_meta( 'nexus_custom_avatar', $user->ID ) ); ?>" /><br />
	<span>Type in the URL of the image you'd like to use as your avatar. This will override your default Gravatar, or show up if you don't have a Gravatar. <br /><strong>Image should be 70x70 pixels.</strong><br />
	Recommend using your LinkedIn profile image: <strong>
	<?php print($nexus->response['pictureUrl']);?>
	</strong><br /></span>
	</td>
	</tr>
	</table>
	<?php 
	}

}
add_action( 'show_user_profile', 'nexus_custom_avatar_field' );
add_action( 'edit_user_profile', 'nexus_custom_avatar_field' );

/**
 * Save Custom Avatar Field
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 *
 * @param int $user_id
 */
function nexus_save_custom_avatar_field( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		update_usermeta( $user_id, 'nexus_custom_avatar', $_POST['nexus_custom_avatar'] );
}
add_action( 'personal_options_update', 'nexus_save_custom_avatar_field' );
add_action( 'edit_user_profile_update', 'nexus_save_custom_avatar_field' );


/**
 * Use Custom Avatar if Provided
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 * 
 */
function nexus_gravatar_filter($avatar, $id_or_email, $size, $default, $alt) {
	$custom_avatar = get_the_author_meta('nexus_custom_avatar');
	if ($custom_avatar) 
		$return = '<img src="'.$custom_avatar.'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';
	elseif ($avatar) 
		$return = $avatar;
	else 
		$return = '<img src="'.$default.'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';

	return $return;
}
add_filter('get_avatar', 'nexus_gravatar_filter', 10, 5);


?>