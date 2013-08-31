<?php

/**
 * Setup the LinkedInOauth object for access token retrieval
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 * 
 * @return object LinkedInOauth
 */
function nexus_auxilary_connector(){

	global $nexus;
	global $user;

	/* These values do not work if out of scope and they need to be called
	 just prior to the OAuth library. Otherwise they get clobbered by Wordpress's
	 name space.
	*/
	include_once('consumer.php');
	

	// load the LinkedIn OAuth class
	include_once('LinkedInOAuth.php');

	/**
	 * If the object is not then we get one. However, we should check for $access first byt examining the User's 
	 * options. Each user should have their own key & secret pair stored for all of this to work.
	 */
	if(!isset($nexus)) {
		$nexus = new LinkedInOAuth($consumer['key'],$consumer['secret']);
	} 
	
	// probably not necessary since it's global but I would like to phase out the global so...
	return $nexus;
}


?>