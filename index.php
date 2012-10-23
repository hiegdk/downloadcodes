<?php
require_once('config.php');
$result = $mysqli->query("select * from config where id = 1");
$config = $result->fetch_assoc();
require_once('top.php');
?>


<div class="row">
	<div class="span2"><img src="img/<?php echo $config['main_logo'] ?>" /></div>
	<div class="span7">
		<form action="dl.php" method="post">
			<h1>Enter Your Download Code</h1>
			<div class="input-append">
			  <input class="span4" id="appendedInputButton" size="16" type="text" name="code"><button type="submit" class="btn" type="button">Go!</button>
			</div>
			<p class="muted">Your download code is case sensitive.</p>
		</form>
	</div>	
</div>






<?php
require_once('bottom.php');
?>