<?php

/* Just a simple dev test...
*/
$URL='http://www.olivent.com/linkedin-bridge/basket/123456.php';

$file = fopen ($URL, "r");
if (!$file) {
    echo "<p>Unable to open remote file.\n";
    exit;
}

while (!feof ($file)) {
    $line = fgets ($file, 1024);
	//print($line);
	//print("\n");
	print_r(json_decode($line, TRUE));
}
fclose($file);
?>
