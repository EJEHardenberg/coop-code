<?php

if( ! isset($migrations) )
	die("Migrations Array Not found. This Script must run from migrate.php's scope");

if( ! isset($connection) )
	die("Connection to mysql database must already be established by migrate.php's scope");

/**
* 
*/
class AnswersTable extends Migration {
	function __construct($name, $ran = false, $connection) {
		$this->name = $name;
		$this->ran = $ran;
		$this->connection = $connection;
	}

	protected function up(){
		$res = mysql_query(
			"CREATE TABLE answers (
        		id INT(20) NOT NULL auto_increment PRIMARY KEY,
        		qid INT(20), 
        		answers VARCHAR(512), -- store php var_export or serialized arrays?
        		INDEX question_fk (qid),
        		FOREIGN KEY (qid) REFERENCES questions(id) ON DELETE CASCADE
    		) ENGINE InnoDB;", $this->connection
		);
		if(false === $res)
			die("Failed to migrate " . $this->name . " " . mysql_error());
		return $res;
	}

	protected function down(){
		$res = mysql_query("DROP TABLE answers;");
		if(false === $res)
			die("Failed to revert migration " . $this->name . mysql_error());
		return $res;
	}
}


$mName = "Create table for answers";
$m = new AnswersTable($mName, Migration::isRan($mName, $connection), $connection);
$migrations[] = $m;

?>