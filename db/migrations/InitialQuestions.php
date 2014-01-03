<?php

if( ! isset($migrations) )
	die("Migrations Array Not found. This Script must run from migrate.php's scope");

if( ! isset($connection) )
	die("Connection to mysql database must already be established by migrate.php's scope");


class QuestionsTable extends Migration {
	function __construct($name, $ran = false, $connection) {
		$this->name = $name;
		$this->ran = $ran;
		$this->connection = $connection;
	}

	protected function up(){
		$res = mysql_query(
			"CREATE TABLE questions (
        		id INT(20) NOT NULL auto_increment PRIMARY KEY,
        		title VARCHAR(128), 
        		body VARCHAR(512)
    		) ENGINE InnoDB;", $this->connection
		);
		if(false === $res)
			die("Failed to migrate " . $this->name . " " . mysql_error());
		return $res;
	}

	protected function down(){
		$res = mysql_query("DROP TABLE questions;");
		if(false === $res)
			die("Failed to revert migration " . $this->name . mysql_error());
		return $res;
	}
}


$mName = "QuestionsTable";
$m = new QuestionsTable($mName, Migration::isRan($mName, $connection), $connection);
$migrations[] = $m;

?>