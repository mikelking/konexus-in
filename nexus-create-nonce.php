<?php

/**
 * A handy random number generator
 */
function create_nexus_nonce() {
 return(base_convert(mt_rand(0x1D39D3E06400000, min(0x41C21CB8E0FFFFFF, mt_getrandmax())), 10, 36));
}
?>