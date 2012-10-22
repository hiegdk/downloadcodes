<?php

//Database Connection Info
$HOSTNAME = "localhost";
$DATABASE = "downloads";
$USERNAME = "downloads";
$PASSWORD = "downloads";

$msqli_error = false;
$mysqli = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);
if (mysqli_connect_errno($mysqli)) {
    $msqli_error = mysqli_connect_error();
}

//location to store files, should not be under the webroot
$FILE_LOCATION = "c:\\wamp\\download_files\\";


?>