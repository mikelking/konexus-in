<?php
/* 

*/

$nexus = new LinkedInOAuth();
//$profile = "people/~:(id,first-name,last-name,industry,picture-url,current-share)";
//$profile = "people/~/network/updates?scope=self&count=1:(picture-url,current-share)";
$profile = "people/~:(picture-url,current-status)";
$nexus->fetchProfile($profile);

print("<h3>Nexus Master!</h3><p>");

printf("<imgsrc=\"%s\" align=\"left\" > ~ <p>%s</p>\n",$nexus->response['pictureUrl'],$nexus->response['currentStatus']);

print("</p>\n\n\n");

?>
