<?php

/**
 * LinkedInOauth - a class that encapsulates the OAuth process specific to LinkedIn
 *
 * @author Mikel King <mikel.king@olivent.com>
 * @copyright 2012 Olivent Technologies, llc
 * @version 0.1
 * @dependency php5.2+(pecl-oauth & libcurl)
 *
 * @license http://opensource.org/licenses/bsd-license.php New/Simplified BSD License
 * @todo add more phpdoc style headers. 
 *
 */

class LinkedInOAuth {
	/* Contains the last HTTP status code returned. */
	public $http_code;
	/* Contains the last API call. */
	public $url;
	/* Set up the API root URL. */
	public $timeout = 30;
	/* Set connect timeout. */
	public $connecttimeout = 30;
	/* Verify SSL Cert. */
	public $ssl_verifypeer = FALSE;
	/* Respons format. */
	private $format = array('format' => 'json');
	/* Decode returned json data. */
	public $decode_json = TRUE;
	/* Contains the last HTTP headers returned. */
	public $http_info;
	/* Set the useragnet. */
	public $useragent = 'NexusOAuth v0.1.0-beta1';
	/* Immediately retry the API call if the response was not successful. */
	//public $retry = TRUE;

	/* number used once */
	public $nonce;
	
	/* The result of an operation */
	public $response;


	/* OAuth Status */
	public $status;

	/* OAuth Connection ~ I treat this sort of liek a db connection. */
	public $oauth;

	/* Setting the appropriate base URLs */
	/* Server API URL */
	public $server_api_url = 'http://api.linkedin.com/v1';
	
	/* OAuth API URL */
	public $oauth_api_url = 'https://api.linkedin.com/uas/oauth';
	
	/**
	* Set API URLS
	*/
	function accessTokenURL()  { return '$this->oauth_api_url/access_token'; }
	
	// https://www.linkedin.com/uas/oauth/authenticate?oauth_token=requestToken
	function authenticateURL()  { return "$this->oauth_api_url/authenticate"; }
	function authorizeURL()  { return "$this->oauth_api_url/authorize"; }
	function requestTokenURL()  { return "$this->oauth_api_url/requestToken"; }
	function invalidateTokenURL()  { return "$this->oauth_api_url/invalidateToken"; }

	/** 
	 * Call Back URL note implemented yet /uas/oauth/requestToken?oauth_callback=
	 * urlencoded data... 
	 */
	function callBackURL()  { return  urlencode('http://wp.olivent.com/nexusCallBack'); }

	
	/**
	 * construct TwitterOAuth object
	 */
	function __construct($consumer_key, $consumer_secret, $access_token = NULL,$access_token_secret = NULL) {
	
   
		if(!extension_loaded('oauth')) {
	 		// the PECL OAuth extension is not present
	 		die('The PECL OAuth extension is not loaded. Please add this to your php installation.');
		}
	
   		if(!extension_loaded('curl')) {
	 		// the curl extension is not present
	 		die('The curl extension is not loaded. Please add this to your php installation.');
		}
		
		if (!empty($access_token) && !empty($access_token_secret)) {
	
		// create a new instance of the OAuth PECL extension class
		// Think Star Trek::Ohora_Open_Channel
			try {
				$this->oauth = new OAuth($consumer_key, $consumer_secret); 
		//		$this->checkObjectivity();
			} catch (OAuthException $e) {
				$buffer = $e;
			}
			$this->oauth->setToken($access_token,$access_token_secret);
			
			// inform the crew that we are ready to beam them up b/c Mr Scott says Aye!
			$this->oauth->status = 'ready';			
			
		} else {
			// we need to request a token
			try{
				$this->oauth = new OAuth($consumer_key, $consumer_secret);
			} catch (OAuthException $e) {
				$buffer = $e;
			}
			$this->oauth->status = 'request_new_token';
		} 
	} // end constructor

	/**
	 * Think Star Trek::Ohora_Open_Channel
	 * @param string $consumer_key
	 * @param string $consumer_secret
	 * @return void
	 */
	function initiate_oauth($consumer_key, $consumer_secret){
		$this->oauth = new OAuth($consumer_key, $consumer_secret); 
	} //end initiate_oauth
	
	
	/**
	 * Crazy big got get'em function
	 * return array
	 */
	function nonce_based_token_request(){
		// https://api.linkedin.com/uas/oauth/requestToken
		// get our request token
		try {
			$rt_info = $this->oauth->getRequestToken($this->requestTokenURL());
		} catch (OAuthException $e) {
				$buffer = $e;
		} 
		// now set the token so we can get our access token
		$this->oauth->setToken($rt_info["oauth_token"], $rt_info["oauth_token_secret"]);
 		
 		return($rt_info);
	} //end nonce_based_token_request

	/**
	 * If all goes as planned the nonce will be accepted and LinkedIn will pass us the access key pair,
	 * which we will have to return to the calling entity so that it can be stored for future reference.
	 * @param string $nonce
	 * @return array $at_info
	 */
	function verify_nonce($nonce){
	     
		// get the access token now that we have the verifier pin
		$at_info = $this->oauth->getAccessToken("https://api.linkedin.com/uas/oauth/accessToken", "", $nonce);
 
		return($at_info); //
	}
	/**
	 * Assuming set the access token so that we can make queries
	 * against the LinkedIn API
	 *
	 * @param string $access_token
	 * @param string $access_token_secret
	 * @return array ?
	 */
	function set_access_token($access_token,$access_token_secret){
	     
		// 
		$this->oauth->setToken($access_token,$access_token_secret);
	}
	/**
	 * I honestly don't know if this will work but it's worth a go...
	 * Session existance check.
	 * 
	 * Helper function that checks to see that we have a 'set' $_SESSION that we can
	 * use for the demo.   
	 */ 
	function oauth_session_exists() {
		if((is_array($_SESSION)) && (array_key_exists('oauth', $_SESSION))) {
			return TRUE;
		} else {
			// start the session
			if(!session_start()) {
			//throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
				return FALSE;
			}
		}
	} //end oauth_session_exists
	
	function checkObjectivity() {
			if(is_object($this->oauth)) {
			print("\nNexus Master rules!<br>\n");
			print("CLI works wonderful.<br>\n");
		} else {
			print("\nUh Houston we have a problem...;-S<br>\n");
			print("Wordpress has sucked the life out of OAuth...<br>\n");
		}
	} // end checkObjectiviety
	
	/* fetchProfile returns something like this
	
	<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
	<person>
		<first-name>Mikel</first-name>
		<last-name>King</last-name>
		<headline>Master Jedi of Stuff</headline>
		<site-standard-profile-request>
			<url>http://www.linkedin.com/profile?viewProfile=&amp;key=11976986&amp;authToken=8tZv&amp;authType=name&amp;trk=api*a182184*s190345*</url>
		</site-standard-profile-request>
	</person>
	*/
	
	/**
	 * It is important to suggest that additional logic be added to extract the elements
	 * or massage the output in the desiered manner. The default output seems to be a XML 
	 * page that is less than optimale for some applications. this is definitely somethign worht investigating.
	 */ 
	 //&& strcmp($this->status,'ready')
	function fetchProfile($profile){		
		if ( isset($profile)  ) {
		try {

			$this->oauth->fetch("$this->server_api_url/$profile",$this->format);
			//or die(print_r($this->oauth->debugInfo, true));
			
//			$response_info = $this->oauth->getLastResponseInfo();

			/**
			 * Probably should turn on output buffering if you intend to use the header line.
			 */
//			header("Content-Type: {$response_info["content_type"]}");

			$this->response=json_decode($this->oauth->getLastResponse(),true);
			
		} catch(OAuthException $E) {
			$error_msg = "Exception caught!\n";
			$error_msg .= "Response: ". $E->lastResponse . "\n";
			//print($error_msg);
		}
		}
	} // end fetchProfile
	
	/**
	 * This is a troubleshooting aid used this to force output.
	 */
	function testStuff() {
	
		$stuff = array (
  "currentStatus"=> "How to setup rsyncd on Mac OS X http://t.co/0w6cAOEH #JAFDIP #in",
  "pictureUrl"=>"http://media.linkedin.com/mpr/mprx/0__by8vx0jcL6NB_O13570vp21v5tBz_a1Tie0vjpGEiQboFJPfTuuRge2Ue-enkf0CkYYssarpEXS"
	);
	
		printf("<img src=\"%s\" align=\"left\" > <p>%s</p>\n",$stuff['pictureUrl'],$stuff['currentStatus']);

	} // end testStuff
	
	/**
	 * Yet another troubleshooting aid. It dumps what you recieved in your call 
	 * to the current output device.
	 */
	function printProfile() {
	
		var_dump($this->response);
	
	} // end printProfile


	/**
	 * Since I am currently on a crapy internet connection I can not perform a callback url based
	 * authorization. It's one of those things I should have picked up on more quickly but I 
	 * thought it was my lack of experience with the API that was biting me. Turns out I overlooked
	 * internet 101 LinkedIn can not call me back if I am not publically accessible ;-S Doh!
	 *
	 * So I am left with using the nonce based redirection smoke and mirrors kind of solution.
	 * No I am not happy about this but I want the plugin to be definable on a user by user basis
	 * so I must do something.
	 */
	function requestNewToken() {
		// since the callback functionality is not available here >;-|
		//$request_token_response = $oauth->getRequestToken(requestTokenURL(),callBackURL());
		$request_token_response = $oauth->getRequestToken(requestTokenURL());


		/**
		 * Because of the whole chicken/egg controversary 
		 * this has moved to nexus_auxilary_connector.php
		 * $this->oauth = new OAuth($consumer_key, $consumer_secret); 
		 */
 
		if($request_token_response === FALSE) {
        	throw new Exception("Failed fetching request token, response was: " . $oauth->getLastResponse());
		} else {
			// now set the token so we can get our access token
			$this->oauth->setToken($request_token_response["oauth_token"], $request_token_response["oauth_token_secret"]);
    	    $request_token = $request_token_response;
		}
		/*
		print "Request Token:\n";
		printf("    - oauth_token        = %s\n", $request_token['oauth_token']);
		printf("    - oauth_token_secret = %s\n", $request_token['oauth_token_secret']);
		print "\n";
		*/
	} // end requestNewToken

} //end LinkedInOAuth class




?>