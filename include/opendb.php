<?php
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error with mysql_connect'); 
if (!is_resource($conn)) {
    echo '$conn is not a resource';
    }
else {
    mysql_select_db($dbname,$conn) or die('Error selecting database');
    }
?>
