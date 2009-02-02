<?php
if ($mDebug) {echo 'content type is: "'.$content_type.'"';}
if ($content_type=="html")
	{
	
	usort($contentsection,'sortByFloat');
	
	require 'html_template.php';
	
	}
elseif ($content_type =="javascript")
	{
	
	Header("content-type: application/x-javascript");
	
	echo $body_javascript;
	
	}
elseif ($content_type =="xml")
	{
	require 'xml_template.php';
	}
elseif ($content_type =="csv")
	{
	require 'csv_template.php';
	}

?>