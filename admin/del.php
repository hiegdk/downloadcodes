<?php
require_once('../config.php');
require_once('top.php');

if(isset($_GET)){
	if(isset($_GET['id'])){
		$id = $mysqli->real_escape_string($_GET['id']);
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
	}else{
		echo '<div class="alert alert-error">No Album ID provided.</div>';	
	}
}else{
	echo '<div class="alert alert-error">No Arguments provided.</div>';	
}


?>







<?php
require_once('bottom.php');
?>