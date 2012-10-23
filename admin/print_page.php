<style>
div#main{
    width: 600px;

}

div.card{
	display: block;
	width: 100%;
	height: 100px;
	font-size: 16px;
	padding: 0px;
	margin:0 0px 8px 0px;
	text-align: center;
	font-weight: bold;
}

span.code{
	display: inline-block;
	font-size: 32px;
	font-weight: bold;
	text-align: center;
}
img{
	margin:0;
	padding:0;
	float: left;
}
 .page-break  { display: block; page-break-before: always; }
</style>

<div id="main">
<?php
require_once('../config.php');
if(isset($_GET)){
	$album_id = $mysqli->real_escape_string($_GET['album']);

	if($c = $mysqli->query("select * from config where id = 1")){
		if(!$config = $c->fetch_assoc()){
			printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
		}
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);	
	}

	if($result = $mysqli->query("select a.artist as artist, a.album as album, c.code as code from albums a left outer join codes c on c.album = a.id")){
		$i=0;
		while(null !== ($row = $result->fetch_assoc())){	
			echo '<div class="card">';
			echo '<img src="assets/'.$config['code_logo'].'" height="100" />';			
			echo $row['artist'].' - <em>'.$row['album'].'</em><br />';
			echo '<span class="code">'.$row['code'].'</span><br />';
			echo $config['url'];
			echo '</div>';
			$i++;
			if($i == 8){
				echo '<div class="page-break"></div>';
				$i=0;
			}
		}

	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}
	mysqli_free_result($result);
}
?>
</div>