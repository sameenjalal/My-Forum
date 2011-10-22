<? include_once('helpers.php'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Sameen's Forum: Posts</title>
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
		
		//echo "Session: ";debug_r($_SESSION);
		//echo "Request: "; debug_r($_REQUEST);	
		//echo "SERVER: "; debug_r($_SERVER);	

		if(isset($_SESSION['access']) && getUserAccess($_SESSION['access'])) { ?> 
			<p id="user_logged">Logged in as: <? echo getUserAccess($_SESSION['access']); ?></p>
	
			<form action="" id="logout" method="post">
				<input type="Submit" name="logout" id="logout" value="Logout"/>
				<input type="Submit" name="back" id="back" value="Back"/>
				<input type="Submit" name="new" id="new" value="Enter new post on this topic!"/>
				<input type="hidden" value="<?=htmlspecialchars($_REQUEST['id'])?>" name="id"/>
			</form> 
		<? } else {
			header('Location: http://sameenjalal.com/forum/login.php'); 
		} ?>

		<? if(isset($_POST['logout'])) {
			unset($_SESSION['access']);
			unset($_SESSION['topic']);
			header('Location: http://sameenjalal.com/forum/login.php');
		} ?>

		<? if(isset($_POST['back'])) {
			unset($_SESSION['topic']);
			header('Location: http://sameenjalal.com/forum/topic.php');
		} ?>

		<? if(isset($_POST['register'])) {
			addNewPost($_GET['id'],getUserAccess($_SESSION['access']),$_POST['body']);
		} ?>

		<? if(isset($_GET['del'])) {
			//incNumEdits($_GET['id']);
			deletePost($_GET['del']);
			//unset($_GET['del']);
		} ?>

		<h1 style="word-wrap: break-word; padding-left:50px; padding-right:50px; line-height:100%"><?echo getTopicName($_GET['id']);?></h1>
		<div id="tbl">
			<table id="table" border=0px width=300px>
				<tr id="row" width=50px>
					<td width=10px id="number">
						<b>By</b>
					</td>
					<td width=50px id="title">
						<h2><b>Post</b></h2>
					</td>
					<td width=10px id="del">
						<b></b>
					</td>
				</tr>

			<?
			 foreach (db_fetch_assoc(db_query("SELECT * FROM post WHERE topic_num='%s'",$_REQUEST['id'])) AS $x):?>
				<tr id="row" height="12px" width=50px>
				<?// debug_r($x); ?>
					<td id="number" width="10px">
						<i><? echo $x['username']; ?></i>
					</td>

					<td id="title" width="50px">
						<h4 style="word-wrap: break-word;"><? echo $x['body'] ?></h4><br>
						<span id="timestamp"><i><? echo $x['time'] ?></i></span>
					</td>

					<td id="del" width="10px" style="padding-left:50px;">
						<form id="del" action="" method="get"> <? echo '<a href="posts.php?id=',urlencode($_GET['id']),'&del=', urlencode($x['post_id']), '">' ?> X
						<input type="hidden" value="<?=$_REQUEST['id']?>" name="id"/>
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
				<span id="message">Write a new post about the topic!</span> <br>
				<form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
					<textarea rows=10 cols=40 name="body">Enter your text here... </textarea>
					<input type="hidden" value="<?=$_REQUEST['id']?>" name="id"/>
					<input type="Submit" value="Send" name="register" id="input_button" />
				</form>
			</div> <!-- details ends -->
		</div> <!-- overlay closes -->

		</div> <!-- wrapper ends -->
	</body>
</html>
