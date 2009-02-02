<?php

$mtdbVersion = 2;

//the path to the php includes
$pathincludes = 'includes/'; 

//config file
require 'private/dbconfig.php';

include("includes/opendb.php");



		//query to create table: BankAccounts
		$Query = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`BankAccounts` (
  `baKey` int(10) NOT NULL auto_increment,
  `baAccountName` varchar(255) NOT NULL,
  `baInterestRate` float(15,5) default NULL,
  `baType` varchar(255) default NULL,
  `baLimit` float default NULL,
  `baForumForumNumber` int(10) default NULL,
  `baForumUserNumber` int(10) default NULL,
  `baAccountManager` int(10) default NULL,
  `baBranchManager` int(10) default NULL,
  `baHPASS1` varchar(36) default NULL,
  `baHPASS2` varchar(16) default NULL,
  `baPPASS` varchar(128) default NULL,
  `baSalt` varchar(128) default NULL,
  `baGameUnique` varchar(255) default NULL,
  `baGameNumber` varchar(255) default NULL,
  PRIMARY KEY  (`baKey`),
  UNIQUE KEY `baAccountName` (`baAccountName`),
  UNIQUE KEY `baPPASS` (`baPPASS`),
  KEY `baType` (`baType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;',$dbname;


		//perform query
		$Result = mysql_query($Query);

		
		
		
//query to create table: TransactionHistory
		$Query = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`TransactionHistory` (
  `thRef` int(10) NOT NULL auto_increment,
  `thDate` datetime default NULL,
  `thSenderAccount` varchar(255) default NULL,
  `thRecipientAccount` varchar(255) default NULL,
  `thSenderKey` int(11) NOT NULL,
  `thRecipientKey` int(11) NOT NULL,
  `thBalance` float default NULL,
  `thLabel` varchar(255) default NULL,
  `thThreadNumber` varchar(255) default NULL,
  `thEnteredByForumID` int(11) default NULL,
  `thMemo` varchar(255) default NULL,
  `thPMemo1` text,
  `thPMemo2` text,
  PRIMARY KEY  (`thRef`),
  KEY `thAccount` (`thRecipientAccount`),
  KEY `thDate` (`thDate`),
  KEY `thSenderKey` (`thSenderKey`,`thRecipientKey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;',$dbname);


		//perform query
		$Result = mysql_query($Query);
		
		
		
		
		
		//query to create table: UserAccounts	
		$sql = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`UserAccounts` (`uaNumber` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `uaUIN` INT NOT NULL UNIQUE, `uaName` VARCHAR(255) NOT NULL UNIQUE, `uaPass` VARCHAR(255) NOT NULL, `uaSalt` VARCHAR(255) NOT NULL, `uaEmail` VARCHAR(255) NOT NULL, `uaIdentName` VARCHAR(255) NOT NULL, `uaIdentNumber` INT NOT NULL, `uaRoles` VARCHAR(255) NULL, INDEX (`uaPass`, `uaSalt`), UNIQUE (`uaName`, `uaEmail`, `uaIdentName`, `uaIdentNumber`)) ENGINE = InnoDB CHARACTER SET latin1 COLLATE latin1_swedish_ci COMMENT = \'User account system\'',$dbname);
		
		//perform query
		$Result = mysql_query($Query);
		
		
		
		
		
		//create BankRates table
		$sql = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`BankRates` (`brKey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `brType` VARCHAR(255) NOT NULL, `brTitle` VARCHAR(255) NOT NULL, `brStartDate` DATETIME NOT NULL, `brEndDate` DATETIME NOT NULL, `brAPY` DOUBLE NOT NULL, `brCreate` VARCHAR(255) NULL, UNIQUE (`brType`)) ENGINE = InnoDB COMMENT = \'For storing the available bank account types\'',$dbname);

		 //perform query
		$result = mysql_query($sql);
		
		
		
		
		
		//create the GeneralSettings table
		$sql = sprintf(' CREATE TABLE IF NOT EXISTS `%s`.`GeneralSettings` (`gsNumber` INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT \'Primary key\',`gsName` VARCHAR( 255 ) NOT NULL COMMENT \'Unique alphanumeric setting name\',`gsValue` VARCHAR( 255 ) NULL COMMENT \'Value for setting\',`gsLoadset` VARCHAR( 255 ) NOT NULL DEFAULT \'always\' COMMENT \'When to load this, default is always loaded\',INDEX ( `gsValue` , `gsLoadset` ) ,UNIQUE ( `gsName` )) ENGINE = InnoDB CHARACTER SET latin1 COLLATE latin1_swedish_ci COMMENT = \'For different site settings\' ',$dbname);

		//perform query
		$result = mysql_query($sql);
		
		
		
		
		//set up blank defaults		
		$sql = sprintf('INSERT INTO `%1$s`.`GeneralSettings` (`gsNumber`, `gsName`, `gsValue`, `gsLoadset`) VALUES (NULL, \'sitename\', \'BLANK\', \'always\'), (NULL, \'sitetitle\', \'Default blank site title\', \'always\'), (NULL, \'paneltitle\', \'Default blank panel title\', \'always\'), (NULL, \'siteurl\', \'http://www.______.com/\', \'always\'), (NULL, \'transferlimit\', \'999999999\', \'always\'), (NULL, \'masterdebug\', \'FALSE\', \'always\'), (NULL, \'uinurlprefix\', \'http://www.______.com/\', \'always\'), (NULL, \'emailaddress\', \'_@_\', \'always\'), (NULL, \'dbversion\', \'%2$s\', \'always\') ;',$dbname,$mtdbVersion);
		
		//perform query
		$result = mysql_query($sql);	
		
		
		//archive directory
		$arcDir = sprintf('archive');
exec(sprintf('mkdir %s',$arcDir));
exec(sprintf('chmod 700 %s',$arcDir));

//private directory
		$privDir = sprintf('private');
exec(sprintf('mkdir %s',$privDir));
exec(sprintf('chmod 700 %s',$privDir));
		

include("includes/closedb.php");
		
		
		
?>