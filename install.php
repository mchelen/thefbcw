<?php



require_once('config.php');

require_once('include/opendb.php');

// set up main table


 $Query = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`main` (
`localId` bigint NOT NULL auto_increment,
`timeStamp` timestamp DEFAULT CURRENT_TIMESTAMP,
`createdAt` varchar(255) NOT NULL,
`statusId` bigint NOT NULL,
`statusText` varchar(255) NOT NULL,
`fromId` bigint NOT NULL,
`toId` bigint NOT NULL,
`fromUser` varchar(255) NOT NULL,
`toUser` varchar(255) NOT NULL,
`amount` float NOT NULL,
`currency` varchar(255) NOT NULL,
PRIMARY KEY (`localId`),
UNIQUE KEY `statusId` (`statusId`),
KEY `fromId` (`fromId`),
KEY `toId` (`toId`),
KEY `currency` (`toId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;',$dbname);


echo "adding main table <br /><br />";
// perform query
$Result = mysql_query($Query) or die(mysql_error());


//query to create config table
$Query = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`config` (
`id` int NOT NULL auto_increment,
`name` varchar(255) NOT NULL,
`value` varchar(255),
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;',$dbname);


echo "adding config table <br /><br />"; 
// perform query
$Result = mysql_query($Query) or die(mysql_error());


 //set up config defaults
$Query = sprintf('INSERT INTO `%1$s`.`config` (`id`, `name`, `value`) VALUES (NULL, \'siteName\', \'%2$s\'), (NULL, \'dbVersion\', \'%3$s\') ;',
    $dbname,
    $siteName,
    $dbVersion);

echo "setting up config defaults<br /><br />"; 
//perform query
$Result = mysql_query($Query) or die(mysql_error()); 
 
 
 $Query = sprintf('INSERT INTO `%1$s`.`config` (`id`, `name`, `value`) VALUES (NULL, \'pid\', NULL);',
    $dbname);
    
 $Result = mysql_query($Query) or die(mysql_error()); 
 
 
require_once('include/closedb.php');
?>
