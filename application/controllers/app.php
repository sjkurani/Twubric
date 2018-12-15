<?php defined('BASEPATH') OR exit('No direct script access allowed');

//Twitter Rest API.
include_once APPPATH."libraries/twitter-oauth-php-codexworld/twitteroauth.php";

class App extends CI_Controller {

	private $consumerKey = '';
	private $consumerSecret = '';
	public $sessUserData;

	public function index() {
		//Authenticate and Login User.
		$userData = array();    	
		$oauthCallback = base_url().'app/';

		//Get existing token and token secret from session
		$sessToken = $this->session->userdata('token');
		$sessTokenSecret = $this->session->userdata('token_secret');

		//Get status and user info from session
		$sessStatus = $this->session->userdata('status');
		$sessUserData = $this->session->userdata('userData');

		if(isset($sessStatus) && $sessStatus == 'verified') {
			//Connect and get latest tweets
			$connection = new TwitterOAuth($this->consumerKey, $this->consumerSecret, $sessUserData['accessToken']['oauth_token'], $sessUserData['accessToken']['oauth_token_secret']); 
			
			//User info from session
			$userData = $sessUserData;
		}
		else if(isset($_REQUEST['oauth_token']) && $sessToken == $_REQUEST['oauth_token']) {
			//Successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
			$connection = new TwitterOAuth($this->consumerKey, $this->consumerSecret, $sessToken, $sessTokenSecret); //print_r($connection);die;
			$accessToken = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			if($connection->http_code == '200'){
				//Get user profile info
				$userInfo = $connection->get('account/verify_credentials');

				//Preparing data for database insertion
				$name = explode(" ",$userInfo->name);
				$first_name = isset($name[0])?$name[0]:'';
				$last_name = isset($name[1])?$name[1]:'';
				$userData = array(
								'oauth_provider' => 'twitter',
								'oauth_uid' => $userInfo->id,
								'username' => $userInfo->screen_name,
								'first_name' => $first_name,
								'last_name' => $last_name,
								'locale' => $userInfo->lang,
								'profile_url' => 'https://twitter.com/'.$userInfo->screen_name,
								'picture_url' => $userInfo->profile_image_url
							);

				//Store status and user profile info into session
				$userData['accessToken'] = $accessToken;
				$this->session->set_userdata('status','verified');
				$this->session->set_userdata('userData',$userData);

				//Get latest tweets
				$data['tweets'] = $connection->get('statuses/user_timeline', array('screen_name' => $userInfo->screen_name, 'count' => 5));
			}
			else{
				$data['error_msg'] = 'Some problem occurred, please try again later!';
			}
		}
		else {
			//unset token and token secret from session
			$this->session->unset_userdata('token');
			$this->session->unset_userdata('token_secret');

			//Fresh authentication
			$connection = new TwitterOAuth($this->consumerKey, $this->consumerSecret);
			//print_r($connection);
			$requestToken = $connection->getRequestToken($oauthCallback);
			//print_r($requestToken);


			//Received token info from twitter
			$this->session->set_userdata('token',$requestToken['oauth_token']);
			$this->session->set_userdata('token_secret',$requestToken['oauth_token_secret']);

			//Any value other than 200 is failure, so continue only if http code is 200
			if($connection->http_code == '200'){
				//redirect user to twitter
				$twitterUrl = $connection->getAuthorizeURL($requestToken['oauth_token']);
				$data['oauthURL'] = $twitterUrl;
			}
			else {
				$data['oauthURL'] = base_url().'user_authentication';
				$data['error_msg'] = 'Error connecting to twitter! try again later!';
			}
		}
		$data['userData'] = $userData;
		$this->load->view('app_view',$data);
	}

	public function followers() {
		//Get status and user info from session
		$sessStatus = $this->session->userdata('status');
		$sessUserData = $this->session->userdata('userData');
		if(isset($sessStatus) && $sessStatus == 'verified') {
			//Connect and get followers list.
			$connection = new TwitterOAuth($this->consumerKey, $this->consumerSecret, $sessUserData['accessToken']['oauth_token'], $sessUserData['accessToken']['oauth_token_secret']); 
			//$data['tweets'] = $connection->get('statuses/user_timeline', array('screen_name' => $sessUserData['username'], 'count' => 5));
			$data['followers'] = $connection->get('followers/list', array('screen_name' => $sessUserData['username']));
			$data['is_valid'] = 1;
			$this->load->view('followers_view',$data);
		}
		else {
			redirect(base_url());
		}
	}


	public function follower($screen_name) {
		//Get status and user info from session
		$sessStatus = $this->session->userdata('status');
		$sessUserData = $this->session->userdata('userData');
		if(isset($sessStatus) && $sessStatus == 'verified') {
			//Connect and get follower details.
			$connection = new TwitterOAuth($this->consumerKey, $this->consumerSecret, $sessUserData['accessToken']['oauth_token'], $sessUserData['accessToken']['oauth_token_secret']);
			$data['follower_details'] = $connection->get('users/show', array('screen_name' => $screen_name));
			/*$data['final_data'] = $data['follower_details'];
			$data['final_data']->followers_count = $data['follower_details']->followers_count ;
			$data['final_data']->friends_count = $data['follower_details']->friends_count ;
			$data['final_data']->listed_count = $data['follower_details']->listed_count ;
			$data['final_data']->favourites_count = $data['follower_details']->favourites_count ;
			$data['final_data']->statuses_count = $data['follower_details']->statuses_count ;

			$data['final_data']->twubric = array(
				"total" => 6,		// user’s twubric score out of 10
				"friends" => 1.5,		// user’s friend score out of 2
				"influence" => 3,		// user’s influence score out of 4
				"chirpy" => 2
			);*/
			header('Content-type: text/javascript');

			$json =  json_encode($data['follower_details'],JSON_PRETTY_PRINT);
			echo $json;
		}
		else {
			redirect(base_url());
		}
	}

	public function logout() {
	$this->session->unset_userdata('token');
	$this->session->unset_userdata('token_secret');
	$this->session->unset_userdata('status');
	$this->session->unset_userdata('userData');
	$this->session->sess_destroy();
	redirect(base_url().'app');
	}
}

