<?php
// http://sourceforge.net/projects/meritomony

//codebase version number
$mVersion = 0.9751;

//codebase loose version
$mBuild = "a";

//old date format - DO NOT USE 
$today = date("Y-m-d H:i:s");

//new date format
$nowTimestamp = time();

if($debug){echo "<br />nowTimestamp: $nowTimestamp time: ".time();}
 

//what this file is saved as - usually index.php
$filename = "index.php";

//debug mode
$masterDebug=0;
$mDebug = FALSE;

//for users IP adress
$userIP=$_SERVER['REMOTE_ADDR'];


$statusMessages[0]=array('alabel' => 'standard',
'message' => 'Welcome');


//load functions
require 'myfunctions.php';

//file to store archive files
$arcDir = sprintf('archive');

$compoundPeriodSeconds = 31556926;
//compounding period in seconds 
// 1 year
// = 31 556 926 seconds
// = 365 days, 5 hours, 48 minutes, and 46 seconds
// = 365 days and 20926/86400 seconds
// = approximately 365.2422 days



?>