<?php
require_once "/private/db.php";
require_once "/private/session.php";

if(isset($_POST['submit']) && $_POST['submit'] == 'Login')
{	
   Login::loginUser();
}

if(isset($_POST['submit']) && $_POST['submit'] == 'Signup')
{
   Login::signupUser();
}

class Login {
	
	const SECRET_KEY = '8679f50b-823d-44d8-b212-5a3e94c37110';
	
	public static function loginUser() {
		// Our database object
		$db = new Db();
		
		// username and password sent from form 
		$username = $_POST['username'];
		$password = $_POST['password'];		
		$usernameVarDb = $db -> setPostVarDb('username'); 		

		$sql="CALL select_user(" . $usernameVarDb . ")";
		$result = $db -> select($sql);		
				
		// Mysql_num_row is counting table row
		$count = count($result);
		
		// If result matched username and password, table row must be 1 row
		if($count == 1){
			$loginTokenFromDB = $result[0]['LoginToken'];
			$loginHashFromDB = $result[0]['LoginHash'];	
			// Calculate hash with password sent from the user
			$loginhash = hash_hmac('sha256', $password , $loginTokenFromDB . self::SECRET_KEY);
			// If login hash stored in db equals to the hash calc from now => login is successful
			if(hash_equals($loginHashFromDB ,$loginhash))
			{
				if(isset($_POST['rememberme']))
				{					
					self::setUserCookie($username);
				}
								
				self::setLoggedInSession($username,$result[0]['UserID']);
			}
		}

		// Redirect to home
		header("Location: home.php");
	    die();
	}
	
	public static function signupUser() {
		// Database connection object
		$db = new Db();

		// Prepear $_POST['filename'] to sent to db (this function stripslashes and add qoutes)
		$firstname = $db -> setPostVarDb('firstname');
		$lastname = $db -> setPostVarDb('lastname');
		$address = $db -> setPostVarDb('address');
		$number = stripslashes($_POST['phone']);
		$cityname = $db -> setPostVarDb('cityname');
		$phone = $db -> setPostVarDb('phone');		
				
		// Generate login information
		$username = $db -> setPostVarDb('username');
		$password = $_POST['password'];
		$logintoken = self::generateRandomToken(); // generate a token
		$loginhash = hash_hmac('sha256', $password , $logintoken . self::SECRET_KEY);
		$logintoken = $db -> setVarDb($logintoken);
		$loginhash =  $db -> setVarDb($loginhash);	
				
		// Prepear sql string command to send to Db
		$sql="CALL 	insert_user(" . $firstname . "," . $lastname . "," . $address . "," . $number . "," . $cityname . "," . $phone . "," . $username . "," . $logintoken . "," . $loginhash . ")";
	
		// Execute command in sql
		$result = $db -> select($sql);		
		$userID = $result[0]['UserID'];
		
		// If userID is not -1 - then the user was successfully generated
		if($userID != -1){						
			self::setLoggedInSession($_POST['username'],$userID);
		}		
		
		// Redirect to home
		header("Location: home.php");
		die();
	}
	
	public static function generateRandomToken()
	{
		$token = bin2hex(mcrypt_create_iv(128, MCRYPT_DEV_URANDOM));
		return $token;
	}
	
	public static function setUserCookie($username) {	
		// Database connection object
		$db = new Db();
		$cookieToken = self::generateRandomToken(); // generate a token, should be 128 - 256 bit
		$usernameVarDb = $db -> setPostVarDb('username');
		$cookieTokenVarDb = $db -> setVarDb($cookieToken);		
		$sql="CALL update_user_cookietoken(" . $usernameVarDb . "," . $cookieTokenVarDb . ")";				
		$result = $db -> select($sql);
		$cookie = $username . ':' . $cookieToken;
		$mac = hash_hmac('sha256', $cookie, self::SECRET_KEY);
		$cookie .= ':' . $mac;
		setcookie('rememberme', $cookie); // PHP function to set cookie
	}
	
	public static function tryToFetchCookie() {
		// Database connection object
		$db = new Db();
		
		$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
		if ($cookie) {			
			list ($username, $cookieToken, $mac) = explode(':', $cookie);
			if (!hash_equals(hash_hmac('sha256', $username . ':' . $cookieToken, self::SECRET_KEY), $mac)) {
				return false;
			}
			
			$usernameVarDb = $db -> setVarDb($username); 		
            $sql="CALL select_user(" . $usernameVarDb . ")";
		    $result = $db -> select($sql);							
			$cookietokenFromDb = $result[0]['CookieToken'];
						
			if ($cookietokenFromDb != null && hash_equals($cookietokenFromDb, $cookieToken)) {
				self::setLoggedInSession($username,$result[0]['UserID']);
			}
		}		
	}
	
	// Start session and store user info in $_SESSION var
	public static function setLoggedInSession($username,$userID)
	{
		Session::session_start_anyway();
		$_SESSION['loggedin'] = true;		
		$_SESSION['username'] = $username;
		$_SESSION['userid'] = $userID;		
	}
}
?>