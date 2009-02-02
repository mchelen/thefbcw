<?php
//backup interval in minutes
$backup_interval = 180;

$now_time = (float)time();
if ($mDebug){echo sprintf('now_time: %f something: %f else: %f',$now_time,floor($now_time / 60),(floor($now_time / 60)%$backup_interval));}

//rum backup if we are within the correct time interval
if ((floor($now_time / 60)%$backup_interval)<20)
		{
		$inputAction = "backup";
		$actionExecute = true;
		require 'maint.php';
		}
?>