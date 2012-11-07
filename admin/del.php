<?php
require_once('../config.php');
require_once('top.php');

if(isset($_GET)){
	if(isset($_GET['id'])){
		$id = $mysqli->real_escape_string($_GET['id']);

		if($result = $mysqli->query("select * from albums where id = ".$id)){
			if($album = $result->fetch_assoc()){
				if(unlink('../img/'.$album['thumbnail']) && unlink('../img/t_'.$album['thumbnail'])){
					echo '<div class="alert alert-success">Thumbnail Deleted!</div>';
				}else{
					echo '<div class="alert alert-error">Could not delete Thumbnail: '.'../img/'.$album['thumbnail'].'</div>';
				}

				if(unlink($FILE_LOCATION.$album['file'])){
					echo '<div class="alert alert-success">Thumbnail Deleted!</div>';
				}else{
					echo '<div class="alert alert-error">Could not delete Thumbnail: '.$FILE_LOCATION.$album['file'].'</div>';
				}

				if($mysqli->query("delete from albums where id = ".$id)){
					echo '<div class="alert alert-success">Album Deleted!</div>';
					echo 'Redirecting to the main screen...';
					?>
					<script>
					$(function() {
						var delay = 2000;
			    		setTimeout(function(){ window.location = 'index.php';}, delay);  
			    	});
					</script>  
					<?php
				}else{
					printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);			
				}
			}	
		}	
	}else{
		echo '<div class="alert alert-error">No Album ID provided.</div>';	
	}
}else{
	echo '<div class="alert alert-error">No Arguments provided.</div>';	
}
require_once('bottom.php');
?>