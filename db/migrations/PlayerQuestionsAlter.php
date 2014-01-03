<?php

if( ! isset($migrations) )
	die("Migrations Array Not found. This Script must run from migrate.php's scope");

if( ! isset($connection) )
	die("Connection to mysql database must already be established by migrate.php's scope");

/**
* 
*/
class PlayerQuestionsAlter extends Migration {
	function __construct($name, $ran = false, $connection) {
		$this->name = $name;
		$this->ran = $ran;
		$this->connection = $connection;
	}

	protected function up(){
		$res = mysql_query(
			"ALTER TABLE questions 
			ADD COLUMN p1_body VARCHAR(512), -- player one question initial body
			ADD COLUMN p2_body VARCHAR(512), -- player two question initial body
			DROP COLUMN body
			;", $this->connection
		);
		if(false === $res)
			die("Failed to migrate " . $this->name . " " . mysql_error());
		return $res;
	}

	protected function down(){
		$res = mysql_query("ALTER TABLE questions
			DROP COLUMN p1_body,
			DROP COLUMN p2_body,
			ADD COLUMN body VARCHAR(512)
			;");
		if(false === $res)
			die("Failed to revert migration " . $this->name . mysql_error());
		return $res;
	}
}


$mName = "Add p1 & p2 fields to questions";
$m = new PlayerQuestionsAlter($mName, Migration::isRan($mName, $connection), $connection);
$migrations[] = $m;

?>