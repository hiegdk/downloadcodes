<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Bootstrap, from Twitter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Download Codes</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="#">Home</a></li>
              <li class="active"><a href="#">Admin</a></li>
              <li><a href="#about">About</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <h1>Install Download Codes</h1>

<?php
require_once('../config.php');

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

	echo '<a href="../index.php" class="btn btn-large btn-success">Get Started...</a>';

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
?>


    </div> <!-- /container -->

  </body>
</html>