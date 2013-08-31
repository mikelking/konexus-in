<?php
 
 register_activation_hook( __FILE__, Ônexus_installÕ );
 
	function nexus_install () {
 
 		$nexus_options = array(
 			'api-key' => 'r6lf11jg0hv4',
 			'api-secret' => 'nmtIluRnjW1xpkwY'
 		);
 	
 		update_option( Ônexus_optionsÕ, $nexus_options );
 	}
 	
?>