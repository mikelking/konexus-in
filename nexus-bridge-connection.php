<?php

	
function nexus_bridge_connection(){

	// assemble the request URL
	$url_fmt = '%s%s';
	$base_url = 'http://www.olivent.com/linkedin-bridge/bridge.php?nonce=';
	$request_url =sprintf($url_fmt, $base_url, $_SESSION['nexus_nonce']);

	$fmt  = '<h3>Authorize LinkedIn Connectivity</h3>Please visit this URL: &nbsp; &nbsp; <a href="';
	$fmt .= $request_url;
	$fmt .= '" target="new_window_baby" > Use bridge site to connect to LinkedIn</a><br /><br />';
	$fmt .='When you have successfully authorized the connection to LinkedIn return here and save your profile.';

	/**
	 * Here we include a hidden element that will get sent upon SUBMIT.
	 * This will trigger the nexus_save_nonce_field() method which will 
	 * in turn trigger the nexus_retrieve_oauth_token() method and hopfully
	 * the force will be with us. If it all works then it will save the 
	 * oauth tokens as properties in the user meta which means we can move on.
	 */
	$fmt .= '<input type="hidden" name="nexus_nonce" id="nexus_nonce" value="';
	$fmt .= $_SESSION['nexus_nonce'];
	$fmt .= '" />';

	// setup the Nonce capture form element
	//$fmt .= '<table><tr><th><label for="nexus_nonce">Verify PIN:</label></th>';
	//$fmt .= '<td><input type="text" name="nexus_nonce" id="nexus_nonce" value="" />';
	//$fmt .= '<br />Please update the profile after entering the PIN.</span></td></tr></table>';

	// we only need to see this once
	if(strcmp($_SESSION['nexus_called_by'], 'nexus_custom_avatar_field') != 0) {
		print($fmt);
	}
}
?>