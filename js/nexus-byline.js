

	function nexus-byline-checkbox() {
		var button = document.getElementById("nexus_custom_byline");
		button.onclick = nexus-byline(headline)
	}

	function nexus-change-byline(byline) { 
		document.getElementById("description")=byline;
	}
	
	
<?php add_action( �init�, �nexus_js_add_script� );
	function nexus_js_add_script() { wp_enqueue_script( $handle, $src, $dependencies, $ver, $in_footer );
} ?>