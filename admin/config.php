<?php
require_once('../config.php');
require_once('top.php');


$name="";
$code_logo="";
$logosql = "";


if(isset($_POST)){
	$name = $mysqli->real_escape_string($_POST['name']);

	if (isset($_FILES['code_logo']['tmp_name']) && is_uploaded_file($_FILES['code_logo']['tmp_name'])) {
		$fn=time().'_'.$_FILES['code_logo']['name'];
		$filename = './assets/'.$fn;
		if(move_uploaded_file($_FILES["code_logo"]["tmp_name"], $filename)){
			$logo = $mysqli->real_escape_string($fn);
			$logosql = ", code_logo='$logo' ";
		}else{
			echo '<div class="alert alert-error">Error: Could not upload file '.$filename.' ('.$_FILES['code_logo']['tmp_name'].')</div>';	
		}
		
	}

	if($mysqli->query("update config set name = '$name' $logosql where id = 1")){
		printf("<div class=\"alert alert-success\">Config Saved!</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}

}

$result = $mysqli->query("select * from config where id = 1");
if($row = $result->fetch_assoc()){
	//print_r($row);
	$name = $row['name'];
	$code_logo = $row['code_logo'];
}



if($_GET && $_GET['del_code_logo']){
	//delete te file
	unlink('assets/'.$code_logo);
	$code_logo = '';
	//clear the db
	if($mysqli->query("update config set code_logo = '' where id = 1")){
		printf("<div class=\"alert alert-success\">Code Logo removed!</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}
}


?>
<h1>Config</h1>

<div class="well">
	<form class="form-horizontal" method="post" enctype='multipart/form-data'>
	  <div class="control-group">
	    <label class="control-label" for="inputName">Name</label>
	    <div class="controls">
	      <input type="text" id="inputName" placeholder="Name" name="name" value="<?php echo $name; ?>">
	    </div>
	  </div>
	  <div class="control-group">
	    <label class="control-label" for="inputLogo">Code Logo</label>
	    <div class="controls">
	    	<?php
	    	if(!$code_logo){
	    		print '<input type="file" id="inputLogo" placeholder="Logo" name="code_logo">';
	    	}else{
	    		print '<img src="assets/'.$code_logo.'" class="img-polaroid" /> <a class="btn btn-mini btn-danger" href="config.php?del_code_logo=true"><i class="icon-remove"></i> Remove Code Logo</a>';
	    	}
	    	?>
	    </div>
	  </div>
	  <div class="control-group">
	    <div class="controls">
	      <button type="submit" class="btn">Save</button>
	    </div>
	  </div>
	</form>
</div>


<?php
require_once('bottom.php');
?>