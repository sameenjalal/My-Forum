<? include_once('helpers.php'); ?>

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

				$('#new').click(function(event){
					$('#overlay').show();

					event.preventDefault();
					return false;
				});
			});
		</script>
	</head>

	<body>
		<img src="img/forum_banner.png" />
		
		<div id="wrapper">
		<?	
		
		debug_r($_SESSION);
	
		if(isset($_SESSION['access']) && getUserAccess($_SESSION['access'])) { ?> 
			<p id="user_logged">Logged in as: <? echo getUserAccess($_SESSION['access']); ?></p>
			<form action="" id="logout" method="post">
				<input type="Submit" name="logout" id="logout" value="Logout"/>
				<input type="Submit" name="new" id="new" value="Create New Topic!"/>
			</form> 
		<? } else {
			echo $_SESSION['access'];
			header('Location: http://sameenjalal.com/forum/login.php'); 
		} ?>

		<? if(isset($_POST['logout'])) {
			unset($_SESSION['access']);
			header('Location: http://sameenjalal.com/forum/login.php');
		} ?>

		<? if(isset($_POST['register'])) {
			addNewTopic($_POST['title']);
			header('Location: http://sameenjalal.com/forum/topic.php');
		} ?>

		<? if(isset($_GET['del'])) {
			deleteTopic($_GET['del']);
			header('Location: http://sameenjalal.com/forum/topic.php');
		} ?>

		<h1> List of all Topics </h1>
		<!-- debug_r($_POST);
		debug_r($_SESSION); 	-->
		<div id="tbl">
			<table id="table" border=0px width=300px>
				<tr id="row" width=50px>
					<td width=50px id="title">
						<h2><b>Title</b></h2>
					</td>
					<td width=10px id="del">
						<b></b>
					</td>
				</tr>

			<? foreach (db_fetch_assoc(db_query("SELECT * FROM topic")) AS $x) : ?>
				<tr id="row" height="12px" width=50px>
					<td id="title" width="50px">
								<h4 style="word-wrap: break-word;">
									<i>
		<? echo '<a href="posts.php?id=', urlencode($x['topic_num']), '">' ?> 
						<? echo $x['title'] ?>
					</a>
									</i>
								</h4>
							&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
							&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 

							<?=$x['num_edits']?> :edits
							&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
							<?=lastEdit($x['topic_num']);?> :last edit
					</td>

					<td id="del" width="10px" style="padding-left:50px;">
						<form id="del" action="" method="get">
					<? echo '<a href="topic.php?del=', urlencode($x['topic_num']), '">' ?> 
						X
					</a>
						</form>
					</td>
				</tr>
		<? endforeach; ?>
			</table>
			
		</div> <!-- tbl ends -->

		<div id="overlay" class="overlay"> 
			<div id="details"> 
				<a id="overlay-close" class="close" title="Close">Close </a>
				<br>
				<span id="message">Post a new topic!</span> <br>
				<form action="" method="post">
					<p>Title: <input name="title" type="text" id="title"/></p>
					<input type="Submit" value="Send" name="register" id="input_button" />
				</form>
			</div> <!-- details ends -->
		</div> <!-- overlay closes -->

		</div> <!-- wrapper ends -->
	</body>
</html>
