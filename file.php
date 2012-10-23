<?php
require_once('config.php');

if(isset($_GET) && isset($_GET['code'])){
	$code = $mysqli->real_escape_string($_GET['code']);

	if($result = $mysqli->query("
		select 
			a.artist as artist,
			a.id as aid, 
			a.album as album, 
			a.thumbnail as thumbnail, 
			a.file as file,
			c.id as cid,
			c.num_downloads as num_downloads, 
			count(l.id) as downloaded
		from 
			albums a left outer join codes c on a.id = c.album 
			left outer join logs l on c.id = l.code
		where
			c.code = '".$code ."'
		")){
		$row = $result->fetch_assoc();	
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}
}

if($row['downloaded'] < $row['num_downloads']){
	$file_url = $FILE_LOCATION .$row['file'];
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"".$row['file']."\""); 
	readfile($file_url);

	//make log entry
	$mysqli->query("insert into logs (album, code, user_agent) values ('".$row['aid']."','".$row['cid']."','".substr($_SERVER['HTTP_USER_AGENT'],0,1024)."')");

}else{
	echo 'Access Denied.';	
}
?>