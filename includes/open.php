<?php

//get input info

$accName = addslashes($_POST['inputName']);
$accMan = addslashes($_POST['inputAMan']);
$branMan = addslashes($_POST['inputBMan']);
$forforNum = addslashes($_POST['inputFFN']);
$foruseNum = addslashes($_POST['inputFUN']);
$ccUni = addslashes($_POST['inputCUni']);
$ccNum = addslashes($_POST['inputCNum']);
$accType = addslashes($_POST['inputType']);

if($_POST['inputLimit']){$limit = addslashes($_POST['inputLimit']);}
else {$limit = 0;}
$pass1 = createRandomPassword(16);
$pass3 = createRandomPassword(128);

$action = addslashes($_POST['inputAction']);


if ($bankname == "FBCC")
{
	//get $accType and $accTypeForm
	require 'includes/fbcc.php';
}
elseif ($bankname == "FBCN")
{
	//get $accType and $accTypeForm
	require 'includes/fbcn.php';

}

if ($action=="newaccount")
{
	if ($accName&&$accMan)
	{
			
		if ($_POST['inputRate']){$intRate = addslashes((double)$_POST['inputRate']);}

		include($path."opendb.php");

		echo "Processing<br>";

		$sql = 'INSERT INTO `BankAccounts` (`baKey`, `baAccountName`, `baInterestRate`, `baType`, `baLimit`, `baAccountManager`, `baBranchManager`, `baForumForumNumber`, `baForumUserNumber`, `baHPASS1`, `baPPASS`, `baGameUnique`, `baGameNumber` ) VALUES (NULL, "'
		.$accName
		.'", "'
		.$intRate
		.'", "'
		.$accType
		.'", "'
		.$limit
		.'", "'
		.$accMan
		.'", "'
		.$branMan
		.'", "'
		.$forforNum
		.'", "'
		.$foruseNum
		.'", "'
		.$pass1
		.'", "'
		.$pass3
		.'", "'
		.$ccUni
		.'", "'
		.$ccNum
		.'")';

		$result = mysql_query($sql);

		echo "Made new $accType account $accName with $intRate <br> Accman: $accMan <br> Branman: $branMan <br>Hpasses:<br>\"$pass1\" $pass2";

		
		$query = 'Select `BankAccounts`.*'
. ' FROM BankAccounts'
. ' WHERE (`BankAccounts`.`baPPASS` = "'
.$pass3
. '")'
. ' ORDER BY `BankAccounts`.`baAccountName` ASC';

$result = mysql_query($query);

while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{


	echo "<p>Please keep a copy of this information!<p>Bank Name:"
	.$bankname
	."<br>Account Name: "
	.$row['baAccountName']
	."<br>Password: "
	.$row['baPPASS']
	."<br>PIN: "
	.$row['baHPASS1'];

}

		include($path."closedb.php");

	}
	else
	{
	
	echo '<html>

<form method="post">
<br>required<br>
<br>Account Name: <input type ="text" name="inputName"><br>
Account Type: <select name="inputType">'
	.$accTypeForm
	.'</select>
<br>
Account Manager: <input type ="text" name="inputAMan"><br>
Branch Manager: <input type ="text" name="inputBMan"><br>
<br>suggested<br>
Account FBCC forum forum number:<input type ="text" name="inputFFN"><br>
Account FBCC forum user number:<input type ="text" name="inputFUN"><br>
Account CC Unique:<input type ="text" name="inputCUni"><br>
Account CC ID Number:<input type ="text" name="inputCNum"><br>
<br>optional<br>
Account Limit: <input type ="text" name="inputLimit"><br>
Interest Rate Override (decimal):<input type ="text" name="inputRate"><br>
<input type="hidden" name="inputAction" value="newaccount">
<input type="submit" >
</form>';
	}

}



else

{

	


}




echo '</html>';



?>