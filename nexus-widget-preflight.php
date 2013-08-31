<?php

//$nexus_plugin_url = trailingslashit( WP_PLUGIN_URL.'/'. dirname( plugin_basename(__FILE__) );

function Nexus_Widget(array $args= array()) { 

	global $nexus;
	global $user;


	// sets the widget to the admin id. In the future this may be configurable on the settings page.
	$main_id = 1;
	
	$nexus_access_key = get_the_author_meta('nexus_access_key', $main_id);
	$nexus_access_secret = get_the_author_meta('nexus_access_secret', $main_id);

	
	// extract the parameters 
	extract($args);
	
	// get our options 
	$options=get_option('nexus'); 
	$title=$options['nexus_title'];
	
	// print the theme compatibility code 
	echo $before_widget; 
	echo $before_title . $title . $after_title;
	
	/* These values do not work if out of scope and they need to be called
	 just prior to the OAuth library. Otherwise they get clobbered by Wordpress's
	 name space.
	*/
	include_once('consumer.php');
	
	//$nexus = new LinkedInOAuth($consumer['key'],$consumer['secret'],$access['token'],$access['token_secret']);

	$nexus = new LinkedInOAuth($consumer['key'],$consumer['secret'],get_the_author_meta('nexus_access_key', $main_id),get_the_author_meta('nexus_access_secret',$main_id));

	$profile = "people/~:(picture-url,current-status)";
	$nexus->fetchProfile($profile);

	printf("<img src=\"%s\" align=\"left\" valign=\"top\" > <p>%s</p>\n",$nexus->response['pictureUrl'],$nexus->response['currentStatus']);

	print("</p>\n");

	echo $after_widget;
}


function Nexus_WidgetControl() {
	// get saved options 
	$options = get_option('nexus');
	// handle user input 
	if ( $_POST["nexus_submit"] ) {
		$options['nexus_title'] = strip_tags( stripslashes( $_POST["nexus_title"] ) ); 
		update_option('nexus', $options);
	}
	
	$title = $options['nexus_title'];
	
	// print out the widget control 
	include('nexus-widget-control.php');

}
?>