<?php
header( "Content-Type: application/json" );
require_once dirname(__DIR__) . '/db/config.php';

if( ! isset($_SERVER['REQUEST_METHOD']) )
	die("Must be called from the browser not the cli");

$connection = null;
if($_SERVER['REQUEST_METHOD'] == 'GET'){
/* GET Requests 
 * Returns JSON Array with fields:
 * { messages : [
 * 			{ 'to' : "ALL"|<User Id>, 'msg' : "stringified JSON object"}
 *  	]
 * ]
 * MUST Submit within query parameters to=<to ID String>
*/
	$data = new stdClass();
	if( ! isset($_GET['to']) || empty($_GET['to']) ){
		$data->error = "Must specify to in query string parameters";
		goto echo_json;
	}

	$to_name = $_GET['to'];

	$connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
	if( $connection === false )
		die("Could Not Connect to database! Have you created it?");

	if( false === mysql_select_db(DB_NAME, $connection) ){
		mysql_close($connection);
		die("Database does not exist");
	}

	$res = mysql_query("SELECT sender, msg FROM messages WHERE `to` ='". mysql_real_escape_string($to_name) ."' ");
	if( mysql_num_rows($res) == 0 ){
		$data->messages = array();
	}else{
		$data->messages = mysql_fetch_array($res);
	}
	$data->total = mysql_num_rows($res);

	echo_json:
	echo json_encode($data);
	if( ! is_null($connection) )
		mysql_close($connection);
	exit();

} else if($_SERVER['REQUEST_METHOD'] == 'POST'){
/* POST Requests 
 * Send a message to another User or to Broadcast ALL 
 * Must submit the following fields:
 * to: <String ID of User> | "ALL"
 * from: <String ID of User>
 * msg: JSON.stringify-ed msg to send.
 * Returns a code 200/400 for success or failure
*/
	$data = new stdClass();
	if( ! isset($_POST['to']) || empty($_POST['to']) ){
		$data->error = "Must specify `to` in post data";
		$data->code = 400;
		goto echo_json;
	}

	if( ! isset($_POST['from']) || empty($_POST['from']) ){
		$data->error = "Must specify `from` in post data";
		$data->code = 400;
		goto echo_json;
	}

	if( ! isset($_POST['msg']) || empty($_POST['msg']) ){
		$data->error = "Must specify `msg` in post data";
		$data->code = 400;
		goto echo_json;
	}

	$connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);

	$to = mysql_real_escape_string($_POST['to']);
	$from = mysql_real_escape_string($_POST['from']);
	$msg = mysql_real_escape_string($_POST['msg']);

	if( $connection === false )
		die("Could Not Connect to database! Have you created it?");

	if( false === mysql_select_db(DB_NAME, $connection) ){
		mysql_close($connection);
		die("Database does not exist");
	}

	$res = mysql_query("INSERT INTO messages (`to` , `sender`, `msg` ) VALUES ('$to', '$from', '$msg')");
	
	if($res){
		$data->code = 200;
	}else{
		$data->code = 400;
		$data->error = mysql_error($connection);
	}

	echo json_encode($data);
	mysql_close($connection);
	exit();

}
echo json_encode(new Exception("Could not handle HTTP Method"));
die();

?>