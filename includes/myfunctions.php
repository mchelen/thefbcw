<?php
function dateDiff($dformat, $endDate, $beginDate)
{
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
	$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
	return $end_date - $start_date;
}

function findBalance($accountKey, $intRate, $date, $histResult)
{


	if ($masterDebug == 1)
	{
		echo sprintf('<span class="ugly">Find balance called: findBalance(%d, %f, %s, %s, %s, %s)</span>',$accountKey, $intRate, $date, $print, $sortby, $sortdir);
	}


	while($row = mysql_fetch_array($histResult, MYSQL_ASSOC))
	{
		$itemDate1 = $row['thDate'];
		$itemDate2 = explode(" ",$itemDate1);
		$itemDate3 = $itemDate2[0];
		$dateDiffNum = dateDiff("-",$date,$itemDate3);
		$curSenderKey = $row['thSenderKey'];
		$curRecipientKey = $row['thRecipientKey'];
		$curSenderAccount = $row['thSenderAccount'];
		$curRecipientAccount = $row['thRecipientAccount'];


		//check to see if the transaction lists the current account as sender or recipient
		//then adjusts the $lineBalance accordingly
		if($accountKey == $curSenderKey)
		{
			$lineBalance = -(float)$row['thBalance'];
		}
		elseif ($accountKey == $curRecipientKey)
		{
			$lineBalance = (float)$row['thBalance'];
		}
		else {echo "Transaction tabulation error";}

		//adds on interest rate
		$lineTotal = $lineBalance * pow(1+$intRate,$dateDiffNum);

		//add on the final line total to the cumulative total
		$total = $total + $lineTotal;

	}

	return $total;

}

//function to find the account name given the account key
function findName($accountKey)
{

	echo "account key entered: $accountKey";



	return $theName;

	/*
	 while()
	 {
	 ;
	 $counter +=1;
	 }
	 $theName = $curName;
	 if ($counter<2){}
	 */
}


function makeTransfer($sendLimit)
{

	//get input, WTF FORMAT USERID

	$userid = trim($_POST["inputPass"]);
	$userid = substr($userid,0,96);
	$date1 = date("Y-m-d");
	$total = 0;

	//query BA to find stuff
	/*
	 include("../../../../spec/fbcc/readconfig.php");
	 include("../../../../spec/fbcc/opendb.php");

	 $accQuery = 'Select `BankAccounts`.*'
	 . ' FROM `BankAccounts`'
	 . ' WHERE (`BankAccounts`.`baPPASS` LIKE "%'
	 .$userid
	 .'%")'
	 . ' ORDER BY `BankAccounts`.`baAccountName` ASC';
	 $accResult = mysql_query($accQuery);
	 $accArr = mysql_fetch_array($accResult, MYSQL_ASSOC);
	 $name1 = $accArr['baAccountName'];
	 $intRate = (double)$accArr['baInterestRate'];
	 echo "Your Account Name: ".$name1;
	 echo "<br>Today's Date: ".$date1;
	 */

	//query TH to find sendLimit
	/*
	 $histQuery = 'SELECT `TransactionHistory`.*'
	 . ' FROM TransactionHistory'
	 . ' WHERE (((`TransactionHistory`.`thSenderAccount` ="'
	 .$name1
	 .'") OR (`TransactionHistory`.`thRecipientAccount` ="'
	 .$name1
	 .'")) AND (`TransactionHistory`.`thDate` <="'
	 .$date1
	 .'"))'
	 . ' ORDER BY `TransactionHistory`.`thDate` DESC';
	 $histResult = mysql_query($histQuery);
	 while($row = mysql_fetch_array($histResult, MYSQL_ASSOC))
	 {
	 $itemDate1 = $row['thDate'];
	 $itemDate2 = explode(" ",$itemDate1);
	 $itemDate3 = $itemDate2[0];
	 $dateDiffNum = dateDiff("-",$date1,$itemDate3);
	 $curSenderAccount = $row['thSenderAccount'];
	 $curRecipientAccount = $row['thRecipientAccount'];
	 if(strcmp($name1, $curSenderAccount)==0){
	 $lineBalance = -(float)$row['thBalance'];
	 }
	 else {
	 $lineBalance = (float)$row['thBalance'];
	 }
	 $lineTotal = $lineBalance * pow(1+$intRate,$dateDiffNum);
	 $total = $total + $lineTotal;
	 }
	 $sendLimit = $total;
	 */



	if ($_POST['inputRecipientName'] && $_POST['inputAmount']){
		//perform transfer entry

		$valid = 1;
		$recSearchCount = 0;
		$sendAccName = $name1;
		$transDate = $date1;
		$transLabel = "Internal Bank Transfer";
		$recAccName = addslashes($_POST['inputRecipientName']);
		if ($_POST['inputThreadNum']){$transThreadNum = $_POST['inputThreadNum'];}

		//query DB to get account name and see how many were returned
		$accQuery2 = 'Select `BankAccounts`.*'
		. ' FROM `BankAccounts`'
		. ' WHERE (`BankAccounts`.`baAccountname` LIKE "%'
		.$recAccName
		.'%")'
		. ' ORDER BY `BankAccounts`.`baAccountName` ASC';
		$accResult2 = mysql_query($accQuery2);
		while ($accArr2 = mysql_fetch_array($accResult2, MYSQL_ASSOC)){
			$recSearchCount += 1;
		}
		include($pathincludes."closedb.php");

		//validate entered values

		if ((float)$_POST['inputAmount'] > 0 && (float)$_POST['inputAmount'] < 10000){
			$transAmt = (float)$_POST['inputAmount'];
			echo "<br><font color=green>Transfer amount greater than 0 less than 10,000 OK</font>";

		}
		else {
			$valid = 0;
			echo "<br><font color=red>Error: Transfer amount must exceed 0 and be less than 10,000</font>";
		}

		if ( $transAmt <= $sendLimit){

			echo "<br><font color=green>Transfer amount does not exceed current balance OK</font>";
		}
		else {
			$valid = 0;
			echo "<br><font color=red>Error: Transfer amount of $".$transAmt." cannot exceed current balance of $".$sendLimit."</font>";
		}
		if (!strcasecmp($sendAccName, $recAccName)){
			$valid = 0;
			echo "<br><font color=red>Error: You can't send money to yourself!</font>";
		}

		if ($recSearchCount == 0){
			echo "<br><font color=red>Error: No matching accounts found</font>";
			$valid = 0;
		}
		elseif ($recSearchCount > 1){
			echo "<br><font color=red>Too many matching accounts found</font>";
			$valid = 0;
		}
		else {echo "<br><font color=green>Recipient account found OK</font>";}


		//finished with validation
		//set input variables





		//$transThreadNum = $_POST['inputThreadNum'];
		$transMemo = addslashes($_POST['inputMemo']);
		$transPMemo1 = $_POST['inputPMemo1'];
		$transPMemo2 = $_POST['inputPMemo2'];

		//proceed with insertion (giggity)
		if ($valid){

			echo "<br><font color=blue>Transfer accepted</font><br><br>Processing...    <br>";

			include($pathincludes."opendb.php");



			$sql = 'INSERT INTO `TransactionHistory` (`thRef`, `thDate`, `thSenderAccount`, `thRecipientAccount`, `thBalance`, `thLabel`, `thThreadNumber`, `thMemo`, `thPMemo1`, `thPMemo2`) VALUES (NULL, "'
			.$date1
			.'", "'
			.$name1
			.'", "'
			.$recAccName
			.'", "'
			.$transAmt
			.'", "'
			.$transLabel
			.'", "'
			.$transThreadNum
			.'", "'
			.$transMemo
			.'", "'
			.$transPMemo1
			.'", "'
			.$transPMemo2
			.'")';

			$result = mysql_query($sql);
			//output the query
			//echo $sql;
			//close database
			include($pathincludes."closedb.php");
			echo "<font color=blue>Completed! Transfer entered.</font> <p> Click \"View Account\" to return to your account summary, your transfer should be visible.</font>";

		}
		else {
			echo "<br>Current Balance: $".round($sendLimit,2);

			echo "<br><br>";
		}


	}




}


//returns javascript suggestion thing for account names
function makeSuggest()
{

	$sql = 'Select `BankAccounts`.`baKey` , `BankAccounts`.`baAccountName`'
	. ' FROM `BankAccounts`'
	. ' ORDER BY `BankAccounts`.`baAccountName` ASC';


	$result = mysql_query($sql);

	$start = "
/**
 * Provides suggestions for account names.
 * @class
 * @scope public
 */
function StateSuggestions() {
    this.states = [";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		$mid =  $mid."\""
		.$row['baAccountName']
		."\", ";
	}
	$end = "];
}

/**
 * Request suggestions for the given autosuggest control. 
 * @scope protected
 * @param oAutoSuggestControl The autosuggest control to provide suggestions for.
 */
StateSuggestions.prototype.requestSuggestions = function (oAutoSuggestControl /*:AutoSuggestControl*/,
                                                          bTypeAhead /*:boolean*/) {
    var aSuggestions = [];
    var sTextboxValue = oAutoSuggestControl.textbox.value;
    
    if (sTextboxValue.length > 0){
    
        //search for matching states
        for (var i=0; i < this.states.length; i++) { 
            if (this.states[i].indexOf(sTextboxValue) == 0) {
                aSuggestions.push(this.states[i]);
            } 
        }
    }

    //provide suggestions to the control
    oAutoSuggestControl.autosuggest(aSuggestions, bTypeAhead);
};";
	$final = $start.$mid.$end;
	return $final;
}





function LoadBankAccount($accountKey, $APY, $date, $cPeriodSeconds, $debug)
{
//accountKey is the account id number
//APY is annual percentage rate
//date is the current UNIX TIMESTAMP
//cPeriodSeconds is the compounding period in seconds
//debug is a boolean for debugging

	$i=0;
	$final = "";

	if ($debug)
	{
		echo sprintf('Load Bank Account: %d %f %s ',$accountKey, $intRate, $date);
	}

	
	//transaction history for this bank account
	$sql = 'SELECT `TransactionHistory`.* FROM TransactionHistory WHERE (((`TransactionHistory`.`thSenderKey` ="%1$d") OR (`TransactionHistory`.`thRecipientKey` ="%1$d")) AND (`TransactionHistory`.`thDate` <="%2$s")) ORDER BY `TransactionHistory`.`thRef` DESC';
	$result = mysql_query(sprintf($sql,$accountKey,$date));
	

	$finalFormatJS ='historyData[%1$d] = '
	.'{Sendername:"%2$s", '
	.'Recipientname:"%3$s", '
	.'Reference:%4$d, '
	.'Type:%5$d, '
	.'Label:"%6$s", '
	.'Topic:"%7$d", '
	.'Memo:"%8$s", '
	.'DateText:"%9$s", '
	.'Date:%10$f, '
	.'Amount:%11$f}; ';
	
	
	$finalFormatXML ='<transfer label="%6$s" type="%5$d"><index>%1$d</index>
	<sender>%2$s</sender>
	<recipient>%3$s</recipient>
	<reference>%4$d</reference>
	<memo>%8$s</memo>
	<date tick="%10$f">%9$s</date>
	<amount>%11$f</amount></transfer>';
		
	$finalXML .= '<history>';

	
	$finalFormatCSV =PHP_EOL.'%1$d,%4$d,%11$f,%10$f,"%9$s",%5$d,"%6$s","%2$s","%3$s","%8$s"';

	$finalCSV .= '"Index","Reference","Amount","Date","DateText","Type","Label","Sender","Recipient","Memo"';
		
		
	
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if($debug)
			{
			echo sprintf('Transfer found: %d %s ',$row['thRef'],$row['thDate']);
			}
	
			
		/*	
		$itemDate1 = $row['thDate'];
		$itemDate2 = explode(" ",$itemDate1);
		$itemDate3 = $itemDate2[0];
		$dateDiffNum = dateDiff("-",$date,$itemDate3);
		*/
		
		$thTimestamp = 	$row['thDate'];
			
		$curSenderKey = $row['thSenderKey'];
		$curRecipientKey = $row['thRecipientKey'];
		$curSenderAccount = $row['thSenderAccount'];
		$curRecipientAccount = $row['thRecipientAccount'];


		//check to see if the transaction lists the current account as sender or recipient
		//then adjusts the $lineBalance accordingly
		if($accountKey == $curSenderKey)
		{
			$lineBalance = -(float)$row['thBalance'];
		}
		elseif ($accountKey == $curRecipientKey)
		{
			$lineBalance = (float)$row['thBalance'];
		}
		else {echo "Transaction tabulation error";}

		//adds on interest rate

		//$lineTotal = $lineBalance * pow(1+$intRate,$dateDiffNum);

		if($debug)
			{
			echo "<br />date: $date thTimestamp: $thTimestamp cPeriodSeconds: $cPeriodSeconds
			<br />P = $lineBalance
			<br />APY = $APY
			<br />T = ".($date-$thTimestamp)/$cPeriodSeconds."
			<br />R = ".log(1+$APY)." 
			<br />";
			}
		$lineTotal = $lineBalance * exp(
			log(1+$APY) * (
				(
					$date-$thTimestamp
				)
				/
				$cPeriodSeconds
			)
		);
		
		
		
		
		//add on the final line total to the cumulative total
		$total = $total + $lineTotal;
		
		if($debug)
			{
			echo sprintf("Transfer line total and balance: %f %f",$lineTotal,$total);
			}


		//set up Javascript account history array
		if ($row['thRecipientKey']==$accountKey){$type = 1;}
		elseif ($row['thSenderKey']==$accountKey){$type = -1;}
		else {$type = 0;}

				
		
		$finalJS .= sprintf($finalFormatJS,
		$i,
		addslashes(htmlspecialchars($row['thSenderAccount'])),
		addslashes(htmlspecialchars($row['thRecipientAccount'])),
		$row['thRef'],
		$type,
		addslashes($row['thLabel']),
		$row['thThreadNumber'],
		addslashes(nl2br2(htmlspecialchars($row['thMemo']))),
		strftime("%b %e, %Y",$row['thDate']),
		$row['thDate'],
		$row['thBalance']);
		
		
		$finalXML .= sprintf($finalFormatXML,
		$i,
		htmlspecialchars($row['thSenderAccount']),
		htmlspecialchars($row['thRecipientAccount']),
		$row['thRef'],
		$type,
		htmlspecialchars($row['thLabel']),
		$row['thThreadNumber'],
		nl2br2(htmlspecialchars($row['thMemo'])),
		strftime("%b %e, %Y",$row['thDate']),
		$row['thDate'],
		$row['thBalance']);

		
		
		$finalCSV .= sprintf($finalFormatCSV,
		$i,
		htmlspecialchars($row['thSenderAccount']),
		htmlspecialchars($row['thRecipientAccount']),
		$row['thRef'],
		$type,
		htmlspecialchars($row['thLabel']),
		$row['thThreadNumber'],
		nl2br2(htmlspecialchars($row['thMemo'])),
		strftime("%b %e, %Y",$row['thDate']),
		$row['thDate'],
		$row['thBalance']);


		
		
		$i++;
		

	}

	$finalXML .= '</history>';
	
	
	
	$summary['balance'] = $total;
	if ($debug){echo sprintf("Balance: %f",$total);}
	$summary['jsacchist'] = $finalJS;
	$summary['xmlacchist'] = $finalXML;
	$summary['csvacchist'] = $finalCSV;

	return $summary;

}


function makeJSAccHist2($accKey,$date)
{


	$sql = 'SELECT `TransactionHistory`.*'
	. ' FROM TransactionHistory'
	. ' WHERE (((`TransactionHistory`.`thSenderKey` ="'
	.$accKey
	.'") OR (`TransactionHistory`.`thRecipientKey` ="'
	.$accKey
	.'")) AND (`TransactionHistory`.`thDate` <="'
	.$date
	.'"))'
	. ' ORDER BY '
	.'`TransactionHistory`.`thRef` DESC';

	$result = mysql_query($sql);

	$i=0;
	$final = "";
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{

		if ($row['thRecipientKey']==$accKey){$type = 1;}
		elseif ($row['thSenderKey']==$accKey){$type = -1;}
		else {$type = 0;}

		$final .='historyData['
		.$i
		.'] = {Sendername:"'
		.addslashes(htmlspecialchars($row['thSenderAccount']))
		.'", Recipientname:"'
		.addslashes(htmlspecialchars($row['thRecipientAccount']))
		.'", Reference:'
		.$row['thRef']
		.', Type:'
		.$type
		.', Label:"'
		.addslashes($row['thLabel'])
		.'", Topic:"'
		.$row['thThreadNumber']
		.'", Memo:"'
		.addslashes(nl2br2(htmlspecialchars($row['thMemo'])))
		.'", DateText:"'
		.strftime("%b %e, %Y",strtotime($row['thDate']))
		.'", Date:'
		.strtotime($row['thDate'])
		.', Amount:'
		.$row['thBalance']
		.'}; ';
		$i++;
	}

	return $final;
}

function nl2br2($string) {
	$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
	return $string;
}

function employeeInfo($name,$date)
{

	//$myAccountKey,$today,"sum"


	$sql = 'Select `BankAccounts`.*'
	. ' FROM `BankAccounts`'
	. ' WHERE (`BankAccounts`.`baAccountManager` LIKE "%'
	.$name
	.'%")'
	. ' ORDER BY `BankAccounts`.`baAccountName` ASC';

	$result = mysql_query($sql);

	WHILE ($accArr = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$total = 0;
		$numAccounts++;
		$name1 = $accArr['baAccountName'];
		$key1 = $accArr['baKey'];
		$intRate = (double)$accArr['baInterestRate'];
		$accMan = $accArr['baAccountManager'];


		$balance = findBalance($key1, $intRate, $date1, FALSE, "thRef", "ASC");
		$accManTotal = $accManTotal + $balance;

	}

	$sum = $accManTotal;

	$salary = round((($accManTotal*.007)+10*$numAccounts),2);

	$date1 = $date;



	$finalreturn['sum'] = $sum;
	$finalreturn['date'] = $date1;
	$finalreturn['salary'] = $salary;

	return $result;
}


function createRandomPassword($pLength)
{
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
	srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;
	while ($i <= ($pLength-1)) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}

function getPostInput($postID)
{
	// Reverse magic_quotes_gpc/magic_quotes_sybase effects on those vars if ON.
	if (isset($_POST[$postID]))
	{
		if(get_magic_quotes_gpc())
		{
			$inputValue       = stripslashes($_POST[$postID]);
		}
		else
		{
			$inputValue        = $_POST[$postID];
			return $inputValue;
		}
	}
	else
	{
		return FALSE;
	}
}



function sortByFloat($a, $b)
{
	if ($a['float'] == $b['float']) return 0;
	return ($a['float'] < $b['float']) ? -1 : 1;
}

function myPassHash($password,$salt)
{
	$passHash = hash('sha256',$password);
	if ($salt == FALSE)
	{
		return $passHash;
	}
	elseif (strlen($salt)>1)
	{
		return hash('sha256', $passHash.$salt);
	}
}


function my_isin($array1,$array2)
	{
	foreach ($array1 as $element1)
		{
		foreach ($array2 as $element2)
			{
			if ($element1 == $element2)
				{
				return TRUE;
				}
			}
		}


	return FALSE;

	}

/**
 * Round to significant digits
 *
 * @param float   $f The number to be rounded
 * @param integer $n Number of significant digits
 */
function round_significant($f, $n)
{
    if ($f==0) return $f;
    return round($f, $n-floor(log10(abs($f)))-1);
}	
	
	

?>