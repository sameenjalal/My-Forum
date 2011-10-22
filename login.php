<? include('helpers.php'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Sameen's Forum</title>
		<meta name="author" content="Sameen Jalal" />
		<link rel="stylesheet" type="text/css" href="main.css" media="screen" />
		<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="js/core.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#overlay-close').click(function(){
					$('#overlay').hide();
				});

				$('#register').click(function(event){
					$('#overlay').show();

					event.preventDefault();
					return false;
				});
			});
		</script>
	</head>

	<body id="forum">

		<img src="img/forum_banner.png" />
		<div id="wrapper">
		<? 
			debug_r($_POST);
			debug_r($_SESSION);
			if(isset($_POST['user']) && isset($_POST['pass']) && usrInDB($_POST['user'],$_POST['pass'])){
				$_SESSION['access'] = makeAccessToken($_POST['user']); 
				header('Location: http://sameenjalal.com/forum/topic.php');
		} ?>
			<div id="buttons">
				<br> <br> 
				<p>Welcome to my forum! After reviewing some of my work, I want to see some of yours.</p>
				<p>If you have any questions, suggestions, or comments feel free to post. If I don't get back to you, someone else will.</p>
				<p>Have fun!</p>
				<br>

				<form action="" method="post">
					<p>Username: <input type="text" name="user" id="user"/></p>
					<p>Password: <input type="password" name="pass" id="pass"/></p>
					<input type="Submit" name="go" value="Login" id="go"/>
				</form>

				<br> <br>
				<input type="Submit" value="Register?" id="register" />
			</div> <!--buttons ends -->

			<div id="overlay" class="overlay"> 
				<div id="details"> 
					<a id="overlay-close" class="close" title="Close">Close </a>
					<?
					if(isset($_POST['register']))
					{
					    /* Make sure all fields were entered */
						if(!$_POST['user'] || !$_POST['pass']) {
							//die('You didn\'t fill in a required field.');
						   ?> <script>alert("You didn\'t fill in a required field.");</script><?
							//header('Location: http://sameenjalal.com/forum/login.php');
							return;
						}

					    /* Spruce up username, check length */
					    $_POST['user'] = trim($_POST['user']);
						if(strlen($_POST['user']) > 20) {
							//die("Sorry, the username is longer than 20 characters, please shorten it.");
						   ?> <script>alert("Sorry, the username is longer than 20 characters, please shorten it.");</script><?
							//header('Location: http://sameenjalal.com/forum/login.php');
							return;
						}

					   /* Add the new account to the database */
					   $md5pass = md5($_POST['pass']);
						if(usrInDB($_POST['user'],$_POST['pass'])) {
							//echo "Retry with a unique user and pass, please";
							//header('Location: http://sameenjalal.com/forum/login.php');
						   ?> <script>alert("Username exists, please use a different one.");</script><?
							//header('Location: http://sameenjalal.com/forum/login.php');
							return;
						}

					   $_SESSION['regresult'] = addNewUser($_POST['user'], $md5pass, $_POST['email']);
					   $_SESSION['registered'] = true;
					   ?> <script>alert("You have successfully registered!");</script><?
					   echo "You have successfully registered!";
					   return;
					} 
					else 
					{ ?>
						<br>
						<span id="message">Haven't registered yet?! Join now!</span> <br>
						<form action="" method="post">
							<p>Username: <input name="user" type="text" id="user"/></p>
							<p>Password: <input name="pass" type="password" id="pass"/></p>
							<br/>
							<p>Email: <input type="email" name="email" id="email"/></p>
							<input type="Submit" value="Send" name="register" id="input_button" />
						</form>
					<? } ?>
				</div> <!-- details ends -->
			</div> <!-- overlay closes -->

		</div> <!-- wrapper ends -->

		<div id="footer">
			<br> Copyright &copy; 2011 <a href="http://sameenjalal.com">Sameen Jalal</a>
		</div> <!-- footer ends -->
	</body>
</html>
