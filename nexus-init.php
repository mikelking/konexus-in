<?php
function Nexus_Init() {
	// register widget 
	wp_register_sidebar_widget('nexus_1','Nexus', 'Nexus_Widget',array('description' => 'Connects to LinkedIn.'));
	
	// register widget control 
	wp_register_widget_control('nexus_1','Nexus', 'Nexus_WidgetControl');
	
	
}

add_action('init', 'Nexus_Init');
?>