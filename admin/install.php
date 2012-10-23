<?php
require_once('../config.php');
require_once('top.php');

echo '<h1>Install Download Codes</h1>';

//sql to create the db tables
$sql_drop_albums = "DROP TABLE albums;";
$sql_drop_codes = "DROP TABLE codes;";
$sql_drop_config = "DROP TABLE config;";
$sql_drop_logs = "DROP TABLE logs;";
$sql_create_albums = 
	"CREATE TABlE albums(".
	"id int unsigned not null auto_increment,".
	"artist varchar(64),".
	"album varchar (64),".
	"thumbnail varchar(64),".
	"file varchar(128),".
	"primary key(id));";

$sql_create_codes = 
	"CREATE TABlE codes(".
	"id int unsigned not null auto_increment,".
	"batch int default 1,".
	"code varchar(64),".
	"album int,".
	"num_downloads int,".
	"primary key(id));";

$sql_create_config = 
	"CREATE TABlE config(".
	"id int unsigned not null auto_increment,".
	"main_logo varchar(256),".
	"code_logo varchar(256),".
	"name varchar(64),".
	"url varchar(64),".
	"primary key(id));";

$sql_create_logs = 
	"CREATE TABlE logs(".
	"id int unsigned not null auto_increment,".
	"album int,".
	"code int,".
	"date timestamp default CURRENT_TIMESTAMP,".
	"user_agent varchar(1024),".
	"primary key(id));";

$sql_insert_config = "insert into config (id,name) values (1,'Download')";

//create the DB tables
if($_POST && $_POST['step'] == "createdb"){
	
	$mysqli->query($sql_drop_albums);
	$mysqli->query($sql_drop_codes);
	$mysqli->query($sql_drop_config);	
	$mysqli->query($sql_drop_logs);
	
	if ($mysqli->query($sql_create_albums) === TRUE)	{
    	printf("<div class=\"alert alert-success\">Table 'albums' successfully created.</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}

	if ($mysqli->query($sql_create_codes) === TRUE)	{
    	printf("<div class=\"alert alert-success\">Table 'codes' successfully created.</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}

	if ($mysqli->query($sql_create_config) === TRUE)	{
    	printf("<div class=\"alert alert-success\">Table 'config' successfully created.</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}

	if ($mysqli->query($sql_insert_config) === TRUE)	{
    	printf("<div class=\"alert alert-success\">Initial Config settings inserted.</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}

	if ($mysqli->query($sql_create_logs) === TRUE)	{
    	printf("<div class=\"alert alert-success\">Table 'logs' created.</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}


	echo '<a href="index.php" class="btn btn-large btn-success">Get Started...</a>';

//check to see if we have good creds and the DB doesn't already exist
}else{

	$continue = false;
	//can we connect using these creds?
	if($msqli_error)	{
		echo '<div class="alert alert-error">Could not connect to database. '.$msqli_error.'</div>';
	}else{	
		echo '<div class="alert alert-success">Database connection good.</div>';
		//do the tables already exist?
		if ($result = $mysqli->query("show tables")){
			$continue = true;
			$row = $result->fetch_all(MYSQLI_NUM);

			if(isset($row[0]) && !in_array("codes", $row[1])){
				$continue = true;
			}else{
				echo '<div class="alert alert-warning">The table "codes" already exists int the DB. Continuing will DROP this table and recreat it.</div>';
			}

			if(isset($row[1]) && !in_array("albums", $row[0])){
				$continue = true;	
			}else{
				echo '<div class="alert alert-warning">The table "albums" already exists int the DB. Continuing will DROP this table and recreat it.</div>';		
			}

			if(isset($row[2]) && !in_array("config", $row[2])){
				$continue = true;
			}else{
				echo '<div class="alert alert-warning">The table "config" already exists int the DB. Continuing will DROP this table and recreat it.</div>';
			}

			if(isset($row[3]) && !in_array("logs", $row[3])){
				$continue = true;
			}else{
				echo '<div class="alert alert-warning">The table "logs" already exists int the DB. Continuing will DROP this table and recreat it.</div>';
			}

		}else{
			echo '<div class="alert alert-error">Could not get table list from '.$DATABASE.'.</div>';	
		}
	}


	if($continue){
		echo '<form method="post"><input type="hidden" name="step" value="createdb" /><input class="btn btn-large btn-success" type="submit" value="Continue..." name="submit" id="continue" /></form>';
	}


}
require_once('bottom.php');
?>
