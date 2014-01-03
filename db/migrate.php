<?php
/* Read in each migration file and execute them in User Defined Order
 * 
 * Migration files are given an array by this migration scripe here
 * that they add themselves to, then we can check the status of the 
 * database 
*/

/* 
Run the SQL below if neccesary:
create database coop; 
create user 'coop'@'localhost' identified by 'coop'; grant all on coop.* to 'coop'@'localhost' ; flush privileges;
*/
require_once 'config.php';

$migrations = array(); 
$files = array();
/* Setup Migration Table if it doesn't exist */

$connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if($connection === false)
	die("Could Not Connect to database! Have you created it?");

if(false === mysql_select_db(DB_NAME, $connection) ) {
	/* Database has not been created yet */
	if(false === mysql_query("CREATE DATABASE coop;"))
		die("Could not create database, please do so manually");
	
}

$tableCreated = False;
if( mysql_num_rows( mysql_query ( "SHOW TABLES LIKE 'migrations' ") ) != 1 ){
	/* Create the Migrations Table if it doesn't exist */
	if( false === mysql_query(
		" CREATE TABLE migrations (
        	id INT(10) NOT NULL auto_increment PRIMARY KEY, 
        	name VARCHAR(32),
        	ran BOOL
    	) ENGINE InnoDB;") )
		die("Could not create migrations table");
	else
		$tableCreated = true;
}

/* Retrieve each migration */
$migrationsFolder = dirname(__FILE__) . '/migrations';

if($handle = opendir($migrationsFolder)) {
	while( false !== ($file = readdir($handle) ) )
		if($file != "." && $file != "..")
			$files[filemtime($migrationsFolder . "/" . $file)] = $file;
	ksort($files);

	require_once "Migration.php";
	foreach ($files as $file) {
		include $migrationsFolder . "/" . $file;
	}

	/* Perform Migrations if neccesary */
	if(isset($_POST['migrate']) && $_POST['migrate'] == "go"){
		unset($_POST['migrate']);
		if( isset($_POST['migrations']) ){
			$to_migrate = array_values($_POST['migrations']);
			foreach ($migrations as $migration) {
				if( in_array($migration->getName(), $to_migrate)  ){
					try{
						if( ! $migration->migrate() )
							echo "Failed To Migrate: " . $migration->getName();
					}catch(Exception $e){
						echo "Failed To Migrate: " . . $migration->getName() .' ' . $e->getMessage();
					}
				}
				else
					try{
						if( ! $migration->revert() )
							echo "Failed To Revert: " . $migration->getName();
					}catch(Exception $e){
						echo "Failed To Revert: " . . $migration->getName() .' ' . $e->getMessage();
					}
			}
			unset($_POST);
			header('Location: '.$_SERVER['REQUEST_URI']);
		}else{
			/* No migrations at all. Down em all! */
			foreach ($migrations as $migration) {
				if( ! $migration->revert() )
					echo "Failed to Revert: " . $migration->getName();
			}
		}
	}
}


?>
<?php
			
		?>
<html>
	<head>
		<title>Migrations</title>
		<style type="text/css">
		.ran{
			background-color: green;
		}
		.not-ran{
			background-color: red;
		}
		</style>
	</head>
	<body>
		<h1>Migration Management</h1>
		<p>
			Make sure to have setup the Initial Database User and created
			the database itself via the SQL defined at the top of this file
		</p>
		<p>
			Migration Information Pulled from:
			<em><?php echo $migrationsFolder; ?></em>
		</p>
		<form method="POST">
		<table>
			<thead>
				<tr>
					<th>Ran</th>
					<th>Name</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($migrations as $migration) {
					$migration->toTR();
				}
				?>
			</tbody>
		</table>
		<input type="submit" value="Apply Migrations" />
		<input type="hidden" value="go" name="migrate" />
		</form>
		<script type="text/javascript">
			/* Javascript to force incremental changes 
			 * If checkbox 1,2, 5 are checked then all
			 * migrations 1,2,3,4,5 should be applied.
			 * If checkbox 3 is unchecked, all checkboxes
			 * after 3 should be unchecked as well.
			*/
			var inputs = document.getElementsByName("migrations[]");
			for (var i = inputs.length - 1; i >= 0; i--) {
				inputs[i].onclick = function(evt){
					var parentTr = this.parentNode.parentNode.parentNode;
					if( this.checked ){
						/* Find and set the previous children to true*/
						while(parentTr){
							parentTr.firstChild.firstChild.firstChild.checked = true;
							parentTr = parentTr.previousElementSibling;
						}
					}else{
						/* Find and set the future children to false */
						while(parentTr){
							parentTr.firstChild.firstChild.firstChild.checked = false;
							parentTr = parentTr.nextElementSibling;
						}
					}
				}
			};
		</script>
	</body>
</html>