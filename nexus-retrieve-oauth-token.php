<?php
/**
 * nexus-retrieve-oauth-token - call a prespecified URL and receive a JSON
 * entity that contains oauth token pair.
 * 
 * I may replace the param with the $SESSION['nexus_nonce']
 *
 * @author Mikel King <mikel.king@olivent.com>
 *
 * @param int $SESSION['nexus_nonce']
 * 
 */

function nexus_retrieve_oauth_token() {

	// assemble the pickup URL
	$url_fmt = '%s%s.php';
	$base_url = 'http://www.olivent.com/linkedin-bridge/basket/';
	$bridge_url = sprintf($url_fmt, $base_url, $_SESSION['nexus_nonce']);

/**
 * I know it's bad to ignore all the HTTP header crap but sometimes
 * working now verse working right is the better choice. In this instance
 * the called URL
 */
$file = fopen ($bridge_url, "r");
if (!$file) {
   echo "<p>Unable to open remote file.\n";
   exit;
}

while (!feof ($file)) {
   $line = fgets ($file, 1024);
//	print($line);
//	print("\n");
	return(json_decode($line, TRUE));
}
fclose($file);
}

?>