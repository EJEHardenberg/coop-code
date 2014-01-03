<?php

if( ! isset($migrations) )
	die("Migrations Array Not found. This Script must run from migrate.php's scope");

if( ! isset($connection) )
	die("Connection to mysql database must already be established by migrate.php's scope");

class MessagesTable extends Migration {
	protected function up(){
		$res = mysql_query(
			"CREATE TABLE messages (
        		`id` INT(20) NOT NULL auto_increment PRIMARY KEY,
        		`to` VARCHAR(128),
        		`sender` VARCHAR(128),
        		`msg` varchar(1024),
        		INDEX (`to`) USING HASH
    		) ENGINE Memory;", $this->connection
		);
		if(false === $res)
			die("Failed to migrate " . $this->name . " " . mysql_error());
		return $res;
	}

	protected function down(){
		$res = mysql_query("DROP TABLE messages;");
		if(false === $res)
			die("Failed to revert migration " . $this->name . mysql_error());
		return $res;
	}
}


$mName = "Messages Table";
$m = new MessagesTable($mName, Migration::isRan($mName, $connection), $connection);
$migrations[] = $m;

?>