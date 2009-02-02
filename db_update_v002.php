<?php

$mtdbVersion = 2;

	//database structure upgrade to version 002

require 'includes/startup.php';	

require 'private/dbconfig.php';


	include("includes/opendb.php");

	
	//optional
	include 'content_update_custom.php';
	
	
		//alter BankAccounts table
		$sql = sprintf('ALTER TABLE `%s`.`BankAccounts` CHANGE `baForumForumNumber` `baForumForumNumber` INT(64) NULL DEFAULT NULL, CHANGE `baForumUserNumber` `baForumUserNumber` INT(64) NULL DEFAULT NULL, CHANGE `baAccountManager` `baAccountManager` INT(64) NULL DEFAULT NULL, CHANGE `baBranchManager` `baBranchManager` INT(64) NULL DEFAULT NULL, CHANGE `baGameNumber` `baGameNumber` INT(64) NULL DEFAULT NULL',$dbname);
		//perform query
		$result = mysql_query($sql);	

	

		//add UIN field
		$sql = sprintf('ALTER TABLE `%s`.`BankAccounts` ADD `baUIN` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT \'A unique identifying name (alphanumeric key)\';',$dbname);
		//perform query
		$result = mysql_query($sql);			

		//encrypt BankAccount passwords
		$sql = sprintf('SELECT * FROM `%s`.`BankAccounts` WHERE `baPPASS` IS NOT NULL',$dbname);
		
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
			$row['baPPASS'] = myPassHash(substr($row['baPPASS'],0,36),FALSE);
		
			$sql2 = sprintf('UPDATE `%s`.`BankAccounts` SET `baPPASS` = \'%s\' WHERE `BankAccounts`.`baKey` =%d LIMIT 1 ;'
			,$dbname
			,$row['baPPASS']
			,$row['baKey']);
			echo "<br>about to execute: $sql2 ";
			$aresult = mysql_query($sql2);
			}
		
			
			
		
		//add random UINs
		$sql = sprintf('SELECT * FROM `%s`.`BankAccounts`',$dbname);
		
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
			$seed = rand(-32768,32768);
			$newuin = myPassHash((string)time(),(string)$seed);
		
			$sql2 = sprintf('UPDATE `%s`.`BankAccounts` SET `baUIN` = \'%s\' WHERE `BankAccounts`.`baKey` =%d LIMIT 1 ;'
			,$dbname
			,$newuin
			,$row['baKey']);
			echo "<br>about to execute: $sql2 ";
			$aresult = mysql_query($sql2);
			}		
			
		$sql = sprintf('ALTER TABLE `%s`.`BankAccounts` ADD UNIQUE (`baUIN`);',$dbname);
		//perform query
		$result = mysql_query($sql);		
			
	
		//query to create table: UserAccounts	
		$sql = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`UserAccounts` (`uaNumber` INT(64) NOT NULL AUTO_INCREMENT PRIMARY KEY, `uaUIN` INT(64) NOT NULL, `uaName` VARCHAR(255) NOT NULL UNIQUE, `uaPass` VARCHAR(255) NOT NULL, `uaSalt` VARCHAR(255) NOT NULL, `uaEmail` VARCHAR(255) NOT NULL, `uaIdentName` VARCHAR(255) NULL, `uaIdentNumber` INT(64) NULL, `uaRoles` VARCHAR(255) NULL, INDEX (`uaPass`, `uaSalt`), UNIQUE (`uaName`, `uaEmail`, `uaIdentName`, `uaIdentNumber`)) ENGINE = InnoDB CHARACTER SET latin1 COLLATE latin1_swedish_ci COMMENT = \'User account system\'',$dbname);

		//perform query
		$result = mysql_query($sql);


		//create BankRates table
		$sql = sprintf('CREATE TABLE IF NOT EXISTS `%s`.`BankRates` (`brKey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `brType` VARCHAR(255) NOT NULL, `brTitle` VARCHAR(255) NOT NULL, `brStartDate` DATETIME NOT NULL, `brEndDate` DATETIME NOT NULL, `brAPY` DOUBLE NOT NULL, `brCreate` VARCHAR(255) NULL, UNIQUE (`brType`)) ENGINE = InnoDB COMMENT = \'For storing the available bank account types\'',$dbname);

		 //perform query
		$result = mysql_query($sql);	
		
		
			
		//create the GeneralSettings table
		$sql = sprintf(' CREATE TABLE IF NOT EXISTS `%s`.`GeneralSettings` (`gsNumber` INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT \'Primary key\',`gsName` VARCHAR( 255 ) NOT NULL COMMENT \'Unique alphanumeric setting name\',`gsValue` VARCHAR( 255 ) NULL COMMENT \'Value for setting\',`gsLoadset` VARCHAR( 255 ) NOT NULL DEFAULT \'always\' COMMENT \'When to load this, default is always loaded\',INDEX ( `gsValue` , `gsLoadset` ) ,UNIQUE ( `gsName` )) ENGINE = InnoDB CHARACTER SET latin1 COLLATE latin1_swedish_ci COMMENT = \'For different site settings\' ',$dbname);
	
		 //perform query
		$result = mysql_query($sql);
		
		
		
		//set up blank GeneralSettings defaults
		$sql = sprintf('INSERT INTO `%1$s`.`GeneralSettings` (`gsNumber`, `gsName`, `gsValue`, `gsLoadset`) VALUES (NULL, \'sitename\', \'BLANK\', \'always\'), (NULL, \'sitetitle\', \'Default blank site title\', \'always\'), (NULL, \'paneltitle\', \'Default blank panel title\', \'always\'), (NULL, \'siteurl\', \'http://www.______.com/\', \'always\'), (NULL, \'transferlimit\', \'999999999\', \'always\'), (NULL, \'masterdebug\', \'FALSE\', \'always\'), (NULL, \'uinurlprefix\', \'http://www.______.com/\', \'always\'), (NULL, \'emailaddress\', \'_@_\', \'always\'), (NULL, \'dbversion\', \'%2$s\', \'always\') ;',$dbname,$mtdbVersion);

			//perform query
		$result = mysql_query($sql);	

		
		
		//set up blank BankRates defaults
		$sql = sprintf('INSERT INTO `%1$s`.`BankRates` (`brKey`, `brType`, `brTitle`, `brStartDate`, `brEndDate`, `brAPY`, `brCreate`) VALUES (NULL, \'default\', \'Default\', \'2000-01-01 00:00:01\', \'3000-01-01 00:00:01\', \'0\', \'admin\');',$dbname);
		//perform query
		$result = mysql_query($sql);
		
		
		
		
		//register admin account
		$inputRegister_Name = "administrator";
		$inputRegister_UIN = 1;
		$inputRegister_Pass = createRandomPassword(18);
		echo "<br />Making admin account New user password: $inputRegister_Pass <br />";
		$inputRegister_Roles = 'admin';
		$actionExecute = TRUE;
		$inputAction = "register";
		require 'includes/register.php';

		
		//make archive directory
		$arcDir = sprintf('archive');
exec(sprintf('mkdir %s',$arcDir));
exec(sprintf('chmod 700 %s',$arcDir));
		
		

?>