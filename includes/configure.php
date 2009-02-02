<?php


$sql = 'SELECT * FROM `GeneralSettings` WHERE `gsLoadset` = CONVERT(_utf8 \'always\' USING latin1) COLLATE latin1_swedish_ci';


$result = mysql_query($sql);

while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
	if ($row['gsLoadset']=='always')
		{
		$generalSettings[$row['gsName']]=$row['gsValue'];
		}
	}


$bankname = $generalSettings['sitename'];

$banktitle = $generalSettings['sitetitle'];

$paneltitle = $generalSettings['paneltitle'];

$siteURL = $generalSettings['siteurl'];

$uinURL = $generalSettings['uinurlprefix'];

$hardlimit = (float)$generalSettings['transferlimit'];

if ($generalSettings['masterdebug']=="TRUE")
	{
	$masterDebug = TRUE;
	}
elseif ($generalSettings['masterdebug']=="FALSE")
	{
	$masterDebug = FALSE;
	}


?>