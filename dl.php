<?php
require_once('config.php');
require_once('top.php');

if(isset($_POST) && isset($_POST['code'])){
	$code = $mysqli->real_escape_string($_POST['code']);

	if($result = $mysqli->query("
		select 
			a.artist as artist, 
			a.album as album, 
			a.thumbnail as thumbnail, 
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

	if($row['downloaded'] < $row['num_downloads']){
		header("Location: file.php?code=".$code);
	}

}
?>

<h1><img class="img-polaroid" src="img/t_<?php echo $row['thumbnail']?>" width="80" alt=""/> <?php echo $row['artist']; ?> - <em><?php echo $row['album']; ?></em></h1>

<?php
if($row['downloaded'] < $row['num_downloads']){
	echo '<p class="muted">Your download will start momentarily. If it does not please <a href="file.php?code='.$code.'">click here to download you file</a></p>';
	$remaining = $row['num_downloads'] - $row['downloaded'];
	if($remaining == 1){
		echo '<p class="muted">You can download this file 1 more time.</p>';
	}else{
		echo '<p class="muted">You can download this file '.$remaining.' more times.</p>';
	}
}else{
	echo '<p class="text-error">We\'re sorry but it looks like you\'ve reached the maximum download limit of '.$row['downloaded'].'.</p>';	
}
?>

<?php
require_once('bottom.php');
?>