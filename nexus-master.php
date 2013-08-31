<?php
/*
Plugin Name: Nexus
Version: 1
Description: Connects to LinkedIn and performs some arbitrary unspecified function.
Author: Mikel King
Author URI: http://mikelking.com
Plugin URI: http://olivent.com/wordpress-plugins/nexus-master
*/

// I would like to add session checking but am moving in the direction of functionality first.

/* Version check */
global $wp_version;
$exit_msg='Nexus Master This requires WordPress 3.3 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';

session_start();

// connection to nexus class
global $nexus;
global $linkedin_status;

// load the LinkedIn OAuth class
include_once('LinkedInOAuth.php');

/**
 * @todo Must add an if $nexus object exists conditional so that we can setup the 
 * appropriate connectivity for each user. This sort of solution will likely munge
 * the widget so I will try to code that to reference the admin user's key pair.
 *
 * Basically we must check for the nexus_access_key and nexus_access_secret properties
 * under the active user. Obviously this will get tricky as only the user logged in 
 * will be accessible?!? Not sure but we're going to try creating the properties now.
 *
 * get_the_author_meta('nexus_access_key') && get_the_author_meta('nexus_access_secret')
 */

/**
 * I totally borked the system attempting to add the whole linkedin authorization step.
 * This seems to be the ticket to fixing my error. I honestly don't know how I broke it but 
 * this is allowing me to fix it or at least return to the functionality I had. 
 *
 * Unfortunately this has resulted in some fallout where admin will see this info on every user
 * profile page. I have not tested this with a basic user yet. I need to sort out the authorization
 * method and then see if it worls with others.
 */


global $user;
 
add_action( 'admin_init', 'nexus_admin_init' );
function nexus_admin_init(){
 
	global $user;
	 
	// Get the current user object.
	$user = wp_get_current_user();
}

 
	// initiate a linkedin connection for a new user
	//include_once('nexus-set-access-properties.php');


 	// add the linkedin avatar option
	include_once('nexus-init.php');
	
	// setup the linkedin connection
	include_once('nexus-primary-connector.php');

	// sets up the widget
	include_once('nexus-widget-preflight.php');

	if(strcmp($linkedin_status,'ready')){
	// adds the byline from the linkedin headline option
	include_once('nexus-custom-byline.php');

	// add the linkedin avatar option
	include_once('nexus-custom-avatar.php');
	}
?>

