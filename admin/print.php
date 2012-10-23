<?php
require_once('../config.php');
require_once('top.php');



if(isset($_GET)){
	$album_id = $mysqli->real_escape_string($_GET['album']);

	if($result = $mysqli->query("select * from albums where id = '$album_id'")){
		$album = $result->fetch_assoc();	
	}else{
		printf("<div class=\"alert alert-error\">Error: %s</div>", $mysqli->error);
	}
}

?>

<h1><img class="img-polaroid" src="../img/t_<?php echo $album['thumbnail']?>" width="80" alt=""/> Print Codes for "<?php echo $album['artist']; ?> - <?php echo $album['album']; ?>" </h1>

<?php
echo '<table class="table table-hover">';
echo '<tr><th>Batch</th><th>Stats</th><th>Actions</th></td>';
if($result = $mysqli->query("
select 
    a.id as id,
    c.batch as batch, 
    a.artist as artist, 
    a.album as album, 
    a.thumbnail as thumbnail, 
    count(c.id) as count,
    count(l.id) as downloads
from
    albums a left outer join codes c on c.album = a.id 
    left outer join (select * from logs group by code) l on l.code = c.id
where 
    c.album = ".$album_id."
group by 
    a.id,
    c.batch
order by c.batch desc
")){
	while(null !== ($row = $result->fetch_assoc())){		
		echo '<tr>';
		echo '<td>';
		echo '<h1 style="display:inline;">#'.$row['batch'].'</h1>';
		echo '</td>';
		echo '<td>';
		echo '<p class="muted" style="display: inline;">'.$row['count'].' unique codes</p><br />';
		echo '<p class="muted" style="display: inline;">'.$row['downloads'].' downloads</p>';
		echo '</td>';
		echo '<td>';
		echo '<p><a class="btn btn-large btn-success" href="print_page.php?album='.$row['id'].'" target="_blank"><i class="icon-print"></i> Print this Batch</a></p>';
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