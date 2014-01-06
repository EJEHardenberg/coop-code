<?php
header( "Content-Type: application/json" );
require_once dirname(__DIR__) . '/db/config.php';

if( ! isset($_SERVER['REQUEST_METHOD']) )
	die("Must be called from the browser not the cli");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
/* GET Requests 
 * Returns JSON with fields:
 * {total : int, questions array}
 * Each question has it's database fields exposed. So refer to current
 * schema to find out what to expect.
*/
	$connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
	if( $connection === false )
		die("Could Not Connect to database! Have you created it?");

	if( false === mysql_select_db(DB_NAME, $connection) ){
		mysql_close($connection);
		die("Database does not exist");
	}

	$data = new stdClass();
	$res = mysql_query("SELECT * FROM questions ");
	if( mysql_num_rows($res) == 0 ){
		$data->questions = array();
	}else{
		$data->questions = mysql_fetch_array($res);
	}
	$data->total = mysql_num_rows($res);
	echo json_encode($data);
	mysql_close($connection);
	exit();

}
echo json_encode(new Exception("Could not handle HTTP Method"));
die();

?>