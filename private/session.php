<?php 
class Session {
	const TIMEALIVE = 180; // Session is alive for X seconds	
	public static function check_session_valid() { 				        
			self::session_start_anyway();
					
			if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > self::TIMEALIVE)) {				
				// last request was more than "TIMEALIVE" seconds ago
				session_unset();     // unset $_SESSION variable for the run-time 
				session_destroy();   // destroy session data in storage
			}
			
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
			
			if (!isset($_SESSION['CREATED'])) {				
				$_SESSION['CREATED'] = time();
			} else if (time() - $_SESSION['CREATED'] > self::TIMEALIVE) {
				// session started more than 30 minutes ago
				session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
				$_SESSION['CREATED'] = time();  // update creation time
			}
	}
	
	public static function session_start_anyway()
	{
		if(!isset($_SESSION)) 
			{ 
				session_start(); 
			} 
	}
}
?>