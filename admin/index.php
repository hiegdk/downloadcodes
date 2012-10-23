<?php
require_once('../config.php');
require_once('simpleimage.php');
require_once('top.php');



if(isset($_POST)){

	//Create new codes for existing album
	if(isset($_POST['album'])){
		$album = $mysqli->real_escape_string($_POST['album']);
		$num_codes = $mysqli->real_escape_string($_POST['num_codes']);
		$num_dls = $mysqli->real_escape_string($_POST['num_dls']);

		if($num_codes > 0 && $num_dls > 0){
			$result = $mysqli->query("select max(batch) + 1 as batch from codes where album = '".$album."'");
			if($row = $result->fetch_assoc()){
				//print_r($row);
				$batch = $row['batch'];
			}


			$insert_sql = "insert into codes (batch,code,album,num_downloads) values ";
			$string = 'Code,Album,Batch'.PHP_EOL;
			for($i=0;$i<$num_codes;$i++){
				//generate code
				$new_code = create_code();
				$insert_sql .= "('".$batch."','".$new_code."','".$album."','".$num_dls."'),";
				$string .= $new_code.','.$album.','.$batch.PHP_EOL;
			}
			$insert_sql = substr($insert_sql,0,-1);
			//print '<pre>'.$insert_sql.'</pre>';
			if($mysqli->query($insert_sql)){
				//create backup file
				if($result = $mysqli->query("select * from albums where id = ".$album)){
					$row = $result->fetch_assoc();
					$file = './backups/'.$row['artist'].' - '.$row['album'].' - Batch '.$batch.'.txt';
					file_put_contents($file, $string);
				}	


				printf("<div class=\"alert alert-success\">%s Codes Added!</div>",$num_codes);
			}else{
				printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
			}	
		}else{
			printf("<div class=\"alert alert-error\"><strong># Codes</strong> &amp; <strong># Dls per Code</strong> must both be greater than 0.</div>");	
		}

		
	//Create new album
	}elseif(isset($_POST['name'])){
		//check for uploaded files
		if (isset($_FILES['thumb']['tmp_name']) && is_uploaded_file($_FILES['thumb']['tmp_name']) && isset($_FILES['zip']['tmp_name']) && is_uploaded_file($_FILES['zip']['tmp_name']) ) {
			$tfn = time().'_'.$_FILES['thumb']['name'];
			$zfn = time().'_'.$_FILES['zip']['name'];

			if(move_uploaded_file($_FILES["thumb"]["tmp_name"], '../img/'.$tfn)){

				//resize
				$image1 = new SimpleImage();
			    $image1->load('../img/'.$tfn);
			    $image1->resizeToWidth(150);		
			    $image1->save('../img/'.$tfn);

			    $image2 = new SimpleImage();
			    $image2->load('../img/'.$tfn);
			    $image2->resizeToWidth(80);		
			    $image2->save('../img/t_'.$tfn);

				if(move_uploaded_file($_FILES["zip"]["tmp_name"], $FILE_LOCATION.$zfn)){
					
					$artist_name = $mysqli->real_escape_string($_POST['artist']);
					$album_name = $mysqli->real_escape_string($_POST['name']);
					$num_codes = $mysqli->real_escape_string($_POST['num_codes']);
					$num_dls = $mysqli->real_escape_string($_POST['num_dls']);
					$thumb = $mysqli->real_escape_string($tfn);
					$zip = $mysqli->real_escape_string($zfn);

					if($mysqli->query("insert into albums (artist,album,thumbnail,file) values ('$artist_name','$album_name','$thumb','$zip')")){
						printf("<div class=\"alert alert-success\">Album Created!</div>");

						$insert_sql = "insert into codes (code,album,num_downloads) values ";
						for($i=0;$i<$num_codes;$i++){
							//generate code
							$insert_id = $mysqli->insert_id;
							$insert_sql .= "('".create_code()."','".$insert_id."','".$num_dls."'),";
						}
						$insert_sql = substr($insert_sql,0,-1);
						//print '<pre>'.$insert_sql.'</pre>';
						if(!$mysqli->query($insert_sql)){
							printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
						}else{
							//create backup file
							if($result = $mysqli->query("select * from codes where album = ".$insert_id)){
								$string = 'Code,Album,Batch'.PHP_EOL;
								while(null !== ($row = $result->fetch_assoc())){
									$string .= $row['code'].','.$row['album'].','.$row['batch'].PHP_EOL;
								}
								$file = './backups/'.$artist_name.' - '.$album_name.' - Batch 1.txt';
								file_put_contents($file, $string);
							}	
						}
					}else{
						printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
					}

				}else{
					echo '<div class="alert alert-error">ZIP/RAR could not be uploaded to ./img/'.$zfn.'</div>';	
				}
			}else{
				echo '<div class="alert alert-error">Thumbnail could not be uploaded to '.$FILE_LOCATION.$tfn.'</div>';
			}
		}else{
			echo '<div class="alert alert-error">Thumbnail and/or ZIP/RAR File not uploaded.</div>';		
		}
	}
}



?>

<h1>Generate New Codes</h1>
<div class="row">
	<div class="span4 well">
		<form method="post">
			<legend>For an existing album</legend>
			
	    	<select name="album">
	    		<?php
	    		if($result = $mysqli->query("select id,artist,album from albums")){
					while(null !== ($row = $result->fetch_assoc())){
						echo '<option value="'.$row['id'].'">'.$row['artist'].' - '.$row['album'].'</option>';
					}
				}	
	    		?>
			</select>

			<input type="text" class="input-medium" placeholder="# Codes" name="num_codes">
  			<input type="text" class="input-medium" placeholder="# DLs per Code" name="num_dls">
  			<input type="submit" class="btn btn-large btn-success" name="submit" value="Add Codes!" />
		</form>
	</div>
	<div class="span1"> - OR - </div>
	<div class="span4 well">		
		<form method="post" enctype='multipart/form-data'>
			<legend>Create a new album</legend>
			<input type="text" class="input-medium" placeholder="Artist Name" name="artist">
			<input type="text" class="input-medium" placeholder="Album Name" name="name">
			<input type="text" class="input-medium" placeholder="# Codes" name="num_codes">
			<input type="text" class="input-medium" placeholder="# DLs per Code" name="num_dls">
			<div class="control-group well">
			    <label class="control-label" for="thumb"><i class="icon-picture"></i> Album Cover</label>
				<div class="controls">
					<input type="file" class="input-medium" id="thumb" placeholder="Album Thumb" name="thumb">	
				</div>
		  	</div>
		  	<div class="control-group well">
			    <label class="control-label" for="zip"><i class="icon-music"></i> ZIP/RAR File</label>
				<div class="controls">
					<input type="file" class="input-medium" placeholder="ZIP/RAR File" name="zip">	
				</div>
		  	</div>
		  	<input type="submit" class="btn btn-large btn-success" name="submit" value="Create Album!" />
		</form>
	</div>

</div>




<h1>Existing Codes</h1>
<?php
echo '<table class="table table-hover">';
echo '<tr><th>Artwork</th><th>Artist/Album/Info</th><th>File Name</th><th>Actions</th></td>';
if($result = $mysqli->query("
select 
    a.id as id,
    c.batch as batch, 
    a.artist as artist, 
    a.album as album, 
    a.file,
    a.thumbnail as thumbnail, 
    count(c.id) as count,
    count(l.id) as downloads
from
    albums a left outer join codes c on c.album = a.id 
    left outer join (select * from logs group by code) l on l.code = c.id
group by 
    a.id
order by c.batch desc
")){
	while(null !== ($row = $result->fetch_assoc())){		
		echo '<tr>';
		echo '<td><img class="img-polaroid" src="../img/t_'.$row['thumbnail'].'" width="80" alt=""/></td>';
		echo '<td>';
		echo $row['artist'].' - '.$row['album'].'<br />';
		echo $row['count'].' unique codes';
		echo '</td>';
		echo '<td>';
		echo '<i class="icon-share"></i> '.$row['file'].'<br />';
		echo 'Downloaded '.$row['downloads'].' times';
		echo '</td>';
		echo '<td>';
		echo '<p><a class="btn btn-mini btn-success" href="print.php?album='.$row['id'].'"><i class="icon-print"></i> Print Codes</a></p>';
		echo '<p><a class="btn btn-mini btn-warning" href="index.php?edit='.$row['id'].'"><i class="icon-edit"></i> Modify</a></p>';
		echo '<p><a class="btn btn-mini btn-danger" href="index.php?del='.$row['id'].'"><i class="icon-remove"></i> Remove</a></p>';
		echo '</td>';
		echo '</tr>';
	}

}
mysqli_free_result($result);

echo '</table>';
?>

<?php
require_once('bottom.php');
?>