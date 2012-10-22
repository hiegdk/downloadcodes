<?php
require_once('../config.php');
require_once('top.php');

echo '<h1>Install Download Codes</h1>';

//sql to create the db tables
$sql_drop_albums = "DROP TABLE albums;";
$sql_drop_codes = "DROP TABLE codes;";
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
	"code varchar(64),".
	"album int,".
	"primary key(id));";

//create the DB tables
if($_POST && $_POST['step'] == "createdb"){
	
	$mysqli->query($sql_drop_albums);
	$mysqli->query($sql_drop_codes);	
	
	if ($mysqli->query($sql_create_albums) === TRUE)	{
    	printf("<div class=\"alert alert-success\">Table albums successfully created.</div>");
	}else{
		printf("<div class=\"alert alert-important\">Error: %s</div>", $mysqli->error);
	}

	if ($mysqli->query($sql_create_codes) === TRUE)	{
    	printf("<div class=\"alert alert-success\">Table codes successfully created.</div>");
	}else{
		printf("<div class=\"alert alert-important\">Error: %s</div>", $mysqli->error);
	}

	echo '<a href="index.php" class="btn btn-large btn-success">Get Started...</a>';

//check to see if we have good creds and the DB doesn't already exist
}else{

	$continue = false;
	//can we connect using these creds?
	if($msqli_error)	{
		echo '<div class="alert alert-important">Could not connect to database. '.$msqli_error.'</div>';
	}else{	
		echo '<div class="alert alert-success">Database connection good.</div>';
		//do the tables already exist?
		if ($result = $mysqli->query("show tables")){
			$continue = true;
			$row = $result->fetch_all(MYSQLI_NUM);

			if(!in_array("codes", $row[1])){
				$continue = true;
			}else{
				echo '<div class="alert alert-warning">The table "codes" already exists int the DB. Continuing will DROP this table and recreat it.</div>';
			}

			if(!in_array("albums", $row[0])){
				$continue = true;	
			}else{
				echo '<div class="alert alert-warning">The table "albums" already exists int the DB. Continuing will DROP this table and recreat it.</div>';		
			}
		}else{
			echo '<div class="alert alert-important">Could not get table list from '.$DATABASE.'.</div>';	
		}
	}


	if($continue){
		echo '<form method="post"><input type="hidden" name="step" value="createdb" /><input class="btn btn-large btn-success" type="submit" value="Continue..." name="submit" id="continue" /></form>';
	}


}
require_once('bottom.php');
?>
