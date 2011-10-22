<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('DB_USER', 'samjalal');
define('DB_PASS', 'akash123');
define('DB_HOST', 'mysql.sameenjalal.com');
define('DB_NAME', 'sameen_test');

define('DEBUG', 0);
define('SQL_EXPLAIN', 0);

//echo 'REQUEST'; debug_r($_REQUEST);

function debug_r($arr)
{
	if(!DEBUG)
		return;
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

/** This function connects us to the database defined in the macros above. */
function db_connect()
{
	mysql_connect(DB_HOST, DB_USER, DB_PASS);		
	mysql_select_db(DB_NAME);
}

/** This function is equivalent to calling mysql_query(sprintf(query, ...)). It will mysql_real_escape all args after the first arg in sprintf.
 * This is awesome for avoiding SQL injections.
 * You can set sql_explain=1 in the $_REQUEST to debug sql_queries.
 */
function db_query($query)
{
	$num_args = func_num_args();

	$args = array();
	$args[] = $query;

	for($i = 1; $i < $num_args; $i++)
		$args[] = htmlspecialchars(mysql_real_escape_string(func_get_arg($i)));

	$query = call_user_func_array('sprintf', $args);


	if(DEBUG && @$_REQUEST['sql_explain'] == 1 || SQL_EXPLAIN)
		debug_r('Query: '.$query."\n");

	//debug_r(debug_backtrace());

	$res = mysql_query($query) or die('Mysql Error occurred: '.mysql_error(). '	 File: '.__FILE__. ' Line: '.__LINE__. "\n Query: $query\n");
//	echo "$res";
	return $res;
}


/** This function returns back a list of entries in the table. Each entry is an associative array of stuff.
 * DO NOT USE THIS FOR BIG TABLES
 */
function db_fetch_assoc($res)
{
	$rv = array();
	while($row = mysql_fetch_assoc($res))
		$rv[] = $row;
	
	return $rv;
}

db_connect();
$success = session_start();
if(!$success)
{
	die('Session was not successfully started in '.__FILE__.' on line '.__LINE__);
}

function addNewUser($user, $pass, $email)
{
	db_query("INSERT INTO forum_user (username,password,email) VALUES ('%s', '%s', '%s')", $user, $pass, $email);
}

function usrInDB($user, $pass)
{
	$md5pass = md5($pass);
	$arr = db_fetch_assoc(db_query("SELECT * FROM forum_user WHERE username='%s' AND password='%s'", $user, $md5pass));
	//debug_r($arr);

	if (count($arr) == 1) { return true; } else { return false; }
}

function addNewTopic($title)
{
	db_query("INSERT INTO topic (title,num_edits) VALUES ('%s',%d)",$title,1);
}

function addNewPost($topic_num,$username,$body)
{
	incNumEdits($topic_num);
	db_query("INSERT INTO post (topic_num,username,body) VALUES (%d,'%s','%s')",$topic_num,$username,$body);
}

function deleteTopic($num)
{
	db_query("DELETE FROM topic WHERE topic_num=%d",$num);
	db_query("DELETE FROM post WHERE topic_num=%d",$num);
}

function deletePost($num) 
{
	db_query("DELETE FROM post WHERE post_id=%d",$num);
}

function makeAccessToken($user) {
	$length = 12;
	$validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ+-*#&@!?";
	$validCharNumber = strlen($validCharacters);
	$result = "";
	for ($i = 0; $i < $length; $i++) {
		$index = mt_rand(0, $validCharNumber - 1);
		$result .= $validCharacters[$index];
	}
	db_query("UPDATE forum_user SET access='%s' WHERE username='%s'",$result,$user);
	echo "makeAccess called";
	//debug_r($result);
	return $result;
}

function getUserAccess($access)
{
	$query = db_query("SELECT username FROM forum_user WHERE access='%s'",$access);
	$arr = db_fetch_assoc($query);
	//debug_r($arr);
	if($arr) {
		return $arr[0]['username'];
	}
	else {
		return false;
	}
}

function getTopicName($num) {
	$query = db_query("SELECT title FROM topic WHERE topic_num='%s'",$num);
	$nam = db_fetch_assoc($query);
	if($nam)
		return $nam[0]['title'];
	else
		return false;
}

//Set the max time for a topic
function lastEdit($topic_id)
{
	// Outputs the max time for a certain topic ID
	$assoc = db_fetch_assoc(db_query("SELECT MAX(time) AS max_time FROM (SELECT time,topic_num FROM post WHERE topic_num='%s' ORDER BY topic_num) AS A",$topic_id));
	debug_r($assoc);
	if($assoc) {
		db_query("UPDATE topic SET last_edit='%s' WHERE topic_num='%s'",$assoc[0]['max_time'],$topic_id);
		return $assoc['0']['max_time'];
	}
	else return false;
}

function incNumEdits($topic_id)
{
	db_query("UPDATE topic SET num_edits=num_edits+1 WHERE topic_num='%s'",$topic_id);
}
