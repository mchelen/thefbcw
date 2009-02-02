<?php


$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die                      ('Error connecting to mysql');



if(!is_resource($conn))
{

	echo "Failed to connect to the server\n";
	// ... log the error properly

}
else
{

	mysql_select_db($dbname,$conn) or die('Cannot select database');

}

?>