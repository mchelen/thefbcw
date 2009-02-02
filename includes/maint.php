<?php


if (($inputAction == "backup") && ($actionExecute == TRUE))
{

$suffix = date("Y-m-d_H-i-s");

/*
$tempDir = sprintf('mysqltemp');

exec(sprintf('mkdir %s',$tempDir));

exec(sprintf('chmod 700 %s',$tempDir));
*/

$sqlFilename = sprintf('%1$s.%2$s.%3$s.sql',$dbhost
,$dbname
,$suffix);

$bakFilename = sprintf('%1$s.%2$s.%3$s.sql.tar',$dbhost,$dbname,$suffix);


		array_push($statusMessages, array( 'message' => sprintf('Performing backup of %2$s on %1$s as %3$s'
,$dbhost
,$dbname
,$sqlFilename),
		'alabel' => 'backup'));



exec(sprintf('mysqldump --opt -u%1$s -p%2$s -h %3$s %4$s > %6$s/%5$s'
,$dbuser
,$dbpass
,$dbhost
,$dbname
,$sqlFilename
,$arcDir));

//exec(sprintf('tar -zcf %1$s/%2$s -C %1$s %3$s',$arcDir,$bakFilename,$sqlFilename));
//
//exec(sprintf('rm -r %s/',$tempDir));

//exec(sprintf('mutt /%1$s -a %4$s/%2$s -s "MySQL Backup for %3$s"',$generalSettings['emailaddress'],$bakFilename,$dbname,$arcDir));

}


?>