<?php
 
 register_activation_hook( __FILE__, �nexus_install� );
 
	function nexus_install () {
 
 		$nexus_options = array(
 			'api-key' => 'r6lf11jg0hv4',
 			'api-secret' => 'nmtIluRnjW1xpkwY'
 		);
 	
 		update_option( �nexus_options�, $nexus_options );
 	}
 	
?>