<?php
/* This is a cli criver to test the LinkedInOAuth class

*/

require('consumer.php');
require('service.php');


include_once('LinkedInOAuth.php');

$nexus = new LinkedInOAuth(API_CONSUMER_KEY,API_CONSUMER_SECRET,$access_token,$access_token_secret);
//$profile = "people/~:(id,first-name,last-name,industry,picture-url,current-share)";
//$profile = "people/~/network/updates?scope=self&count=1:(picture-url,current-share)";
$profile = "people/~:(picture-url,current-status)";


$nexus->fetchProfile($profile);

printf("<src img=\"%s\" align=\"left\" > ~ <p>%s</p>\n",$nexus->response['pictureUrl'],$nexus->response['currentStatus']);

print("\n\n\n");


$nexus->printProfile();

print("\n\n\n");

//$nexus->testShit();


?>
