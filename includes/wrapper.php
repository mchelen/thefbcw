<?php


//load automatic teller panel javascript

$content_type ="javascript";

$wrapper=$_GET['board'];

$section=$_GET['part'];



if ($wrapper=="IPBv1.3"){include 'includes/IPBv1.3.php';}


if ($section=="footer"){$body_javascript = $wrapfooter;}

?>