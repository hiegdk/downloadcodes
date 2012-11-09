<?php
require_once('../config.php');
require_once('simpleimage.php');
require_once('top.php');


$name="";
$main_logo="";
$url="";
$code_logo="";
$logosql = "";
$mainsql = "";


if(isset($_POST) && isset($_POST['name'])){
	$name = $mysqli->real_escape_string($_POST['name']);
	$url = $mysqli->real_escape_string($_POST['url']);

	if(isset($_FILES['main_logo']) && is_uploaded_file($_FILES['main_logo']['tmp_name'])){
		if(move_uploaded_file($_FILES["main_logo"]["tmp_name"], '../img/'.$_FILES["main_logo"]["name"])){
			$mainsql = ", main_logo='".$mysqli->real_escape_string($_FILES["main_logo"]["name"])."' ";
		}else{
			echo '<div class="alert alert-error">Error: Could not upload file ../img/'.$_FILES["main_logo"]["name"].' ('.$_FILES['main_logo']['tmp_name'].')</div>';		
		}		
	}


	if (isset($_FILES['code_logo']) && is_uploaded_file($_FILES['code_logo']['tmp_name'])) {		
		$fn=time().'_'.$_FILES['code_logo']['name'];
		$filename = './assets/'.$fn;
		if(move_uploaded_file($_FILES["code_logo"]["tmp_name"],$filename)){
			
			$image1 = new SimpleImage();
		    $image1->load($filename);
		    $image1->resizeToHeight(100);		
		    $image1->save($filename);


			$logo = $mysqli->real_escape_string($fn);
			$logosql = ", code_logo='$logo' ";
		}else{
			echo '<div class="alert alert-error">Error: Could not upload file '.$filename.' ('.$_FILES['code_logo']['tmp_name'].')</div>';	
		}
		
	}

	if($mysqli->query("update config set name = '$name', url='$url' $logosql $mainsql where id = 1")){
		printf("<div class=\"alert alert-success\">Config Saved!</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}

}

if(isset($_GET) && isset($_GET['del_code_logo'])){

	$result = $mysqli->query("select code_logo from config where id = 1");
	if($row = $result->fetch_assoc()){
		$file = $row['code_logo'];
	}

	//delete te file
	if(!@unlink('../img/'.$file)){
		printf("<div class=\"alert alert-warning\">Warning: Could not delete main logo '%s'</div>", $file);
	}

	$code_logo = '';
	//clear the db
	if($mysqli->query("update config set code_logo = null where id = 1")){
		printf("<div class=\"alert alert-success\">Code Logo removed!</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}
}


if(isset($_GET) && isset($_GET['del_main_logo'])){

	$result = $mysqli->query("select main_logo from config where id = 1");
	if($row = $result->fetch_assoc()){
		$file = $row['main_logo'];
	}

	//delete te file
	if(!@unlink('../img/'.$file)){
		printf("<div class=\"alert alert-warning\">Warning: Could not delete main logo '%s'</div>", $file);
	}

	$code_logo = '';
	//clear the db
	if($mysqli->query("update config set main_logo = null where id = 1")){
		printf("<div class=\"alert alert-success\">Main Logo removed!</div>");
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}
}


$result = $mysqli->query("select * from config where id = 1");
if($row = $result->fetch_assoc()){
	//print_r($row);
	$name = $row['name'];
	$main_logo = $row['main_logo'];
	$code_logo = $row['code_logo'];
	$url = $row['url'];
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
	    <label class="control-label" for="inputURL">URL</label>
	    <div class="controls">
	      <input type="text" id="inputURL" placeholder="URL" name="url" value="<?php echo $url; ?>">
	    </div>
	  </div>
	  <div class="control-group">
	    <label class="control-label" for="inputMainLogo">Main Logo</label>
	    <div class="controls">
	    	<?php
	    	if(!$main_logo){
	    		print '<input type="file" id="inputMainLogo" placeholder="Main Logo" name="main_logo">';
	    	}else{
	    		print '<img src="../img/'.$main_logo.'" class="img-polaroid" /> <a class="btn btn-mini btn-danger" href="config.php?del_main_logo=true"><i class="icon-remove"></i> Remove Main Logo</a>';
	    	}
	    	?>
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
	      <button type="submit" class="btn btn-large btn-success">Save</button>
	    </div>
	  </div>
	</form>
</div>


<?php
require_once('bottom.php');
?>