<?php 
require_once "/private/db.php"; 
require_once "/private/session.php"; 
require_once "/login.php"; 
?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Login Form</title>
</head>
<body>
   <?php 		
		// Session::session_start_anyway(); OPTION TO SET SESSION TIMEOUT
		 Session::check_session_valid();	
         Login::tryToFetchCookie();		 
         if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) { ?>
		<div> Hello <?php echo $_SESSION['username'] ?></div>
		<a href="logout.php">Logout</a>
   <?php } else { ?>   
	<div class="login">
		  <h1>Login</h1>
		  <form method="post" action="login.php">
			<p><input type="text" name="username" value="" placeholder="Username"></p>
			<p><input type="password" name="password" value="" placeholder="Password"></p>

			<p class="submit">
			  <input id="loginSubmit" type="submit" name="submit" value="Login">
			  <input type="checkbox" name="rememberme" value="rememberme">Remember me<br>
			</p>
		  </form>
	</div>
	<div class="signup">
		  <h1>Signup</h1>
		  <form method="post" action="login.php">
				<p><input type="text" name="username" value="" placeholder="Username"></p>
				<p><input type="text" name="firstname" value="" placeholder="Firstname"></p>
				<p><input type="text" name="lastname" value="" placeholder="Lastname"></p>		
				<p><input type="text" name="address" value="" placeholder="address"></p>				
				<p><input type="text" name="number" value="" placeholder="number"></p>	
				<p>						   
				  <select name="cityname">				  
					<?php 
					   // Our database object						   
					   $db = new Db();					   
					   $sql="SELECT Name FROM city";
					   $result = $db -> select($sql);
					   
					   foreach ($result as $value) {					   
					?>						
						   <option value="<?php echo $value['Name']?>"><?php echo $value['Name'] ?></option>
					  <?php } ?>
				  </select>
				</p>
				<p><input type="text" name="phone" value="" placeholder="phone"></p>	
				<p><input type="password" name="password" value="" placeholder="Password"></p>

				<p class="submit"><input id="signupSubmit" type="submit" name="submit" value="Signup"></p>
		  </form>	      
	</div>
	<?php } ?>
</body>
</html>