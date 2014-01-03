<?php

if( ! isset($migrations) )
	die("Migrations Array Not found. This Script must run from migrate.php's scope");

if( ! isset($connection) )
	die("Connection to mysql database must already be established by migrate.php's scope");

class AnswersTableAlter extends Migration {
	function __construct($name, $ran = false, $connection) {
		$this->name = $name;
		$this->ran = $ran;
		$this->connection = $connection;
	}

	protected function up(){
		$res = mysql_query(
			"ALTER TABLE answers 
			ADD COLUMN inputs VARCHAR(512) -- serialized arrays go here
			;", $this->connection
		);
		if(false === $res)
			die("Failed to migrate " . $this->name . " " . mysql_error());
		return $res;
	}

	protected function down(){
		$res = mysql_query("ALTER TABLE answers DROP COLUMN inputs;");
		if(false === $res)
			die("Failed to revert migration " . $this->name . mysql_error());
		return $res;
	}
}


$mName = "Add inputs field to answers";
$m = new AnswersTableAlter($mName, Migration::isRan($mName, $connection), $connection);
$migrations[] = $m;

?>