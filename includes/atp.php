<?php
//setup variables
$total = 0;
if ($inputDisplay=="xml")
	{
		$content_type="xml";
	}
elseif ($inputDisplay=="csv")
	{
		$content_type="csv";
	}
else
	{
	$content_type="html";
	}

if ($accessType=="UserAccount" || $accessType=="BankAccount")
	{
	if ($accessType=="UserAccount")
		{
		$userAccountNumber = $userAccountData['uaNumber'];
		$userAccountName = $userAccountData['uaName'];
		$userAccountEmail = $userAccountData['uaEmail'];
		$userAccountIdentNumber = $userAccountData['uaIdentNumber'];
		$userAccountIdentName = $userAccountData['uaIdentName'];
		$userAccountRoles = explode(",",$userAccountData['uaRoles']);
		$userAccountUIN = $userAccountData['uaUIN'];

		//output info on roles
		if ($mDebug)
			{
			array_push($statusMessages,  array( 'message' => sprintf('Debug <span class="ugly">You have %d roles</span>',count($userAccountRoles)),
			'alabel' => 'broadcast'));			
			}
		
		
		
		//load owned bank account data
		$sql = sprintf('SELECT * FROM `BankAccounts` WHERE `baForumUserNumber` = \'%d\'',
		mysql_real_escape_string($userAccountUIN));
		$result = mysql_query($sql);
		while ($row=mysql_fetch_array($result, MYSQL_ASSOC))
			{
			$bankAccounts[$row['baUIN']] = array(
			'bankAccountName' => $row['baAccountName'],
			'bankAccountKey' => $row['baKey'],
			'bankAccountType' => $row['baType'],
			'bankAccountMan' => $row['baAccountManager'],
			'bankAccountLimit' => $row['baLimit']
			);
			if (substr($row['baUIN'],0,12)==$inputBank_UIN)
				{
				$curBankAccountUIN = $bankAccountData['baUIN'];
				}
			}
	
		//allow logged in user to manage bank accounts
		require 'manageaccounts.php';
		}
	elseif ($accessType=="BankAccount")
		{

		$bankAccounts[$bankAccountData['baUIN']]= array(
			'bankAccountName'=>$bankAccountData['baAccountName'],
			'bankAccountKey'=>$bankAccountData['baKey'],
			'bankAccountType'=>$bankAccountData['baType'],
			'bankAccountMan'=>$bankAccountData['baAccountManager'],
			'bankAccountLimit'=>$bankAccountData['baLimit'],
			'bankAccountOwnerNumber'=>$bankAccountData['baForumUserNumber']
		);
			
		$curBankAccountUIN = $bankAccountData['baUIN'];
		if ($mDebug)
			{
			echo sprintf('Current Bank Account UIN: %s ',$curBankAccountUIN);
			}
		}



	if (count($bankAccounts)>0)
		{
		foreach ($bankAccounts as $a => $b)
			{
	
			$accountName = $b['bankAccountName'];
			$accountKey = $b['bankAccountKey'];
			$accountType = $b['bankAccountType'];
			$accMan = $b['bankAccountMan'];
			$accountOwnerUserNumber = $b['bankAccountOwnerNumber'];
			$bankAccountLimit = $b['bankAccountLimit'];
	
			if ($mDebug)
				{
				echo sprintf('Account Loaded: %s ',$accountName);
				}
	
			//load account type data
			$sql = sprintf('SELECT * FROM `BankRates` WHERE (`brType` = \'%s\')',
			mysql_real_escape_string($accountType));
			$result = mysql_query($sql);
			$BankRatesData = mysql_fetch_array($result, MYSQL_ASSOC);
			$accountTypeTitle = $BankRatesData['brTitle'];
			if ($bankAccountData['baInterestRate']==NULL)
				{
				$intRate = (double)$BankRatesData['brAPY'];
				}
			else
				{
				$intRate = (double)$bankAccountData['baInterestRate'];
				}
	
	
			$myBankAccount = LoadBankAccount($accountKey, $intRate, $nowTimestamp, $compoundPeriodSeconds, $mDebug);
				
			//get current balance
			$balance = $myBankAccount['balance'];
				$sendLimit = $balance - $bankAccountLimit;	
				
				
			//make javascript stuff for the account history
			$jsacchist = $myBankAccount['jsacchist'];
	
			
			
			//make optional XML output
			$xmlacchist = $myBankAccount['xmlacchist'];
			
			$xmloutput .= $xmlacchist;
			
			
			//make optional CSV output
			$csvacchist = $myBankAccount['csvacchist'];
			
			$csvoutput .= $csvacchist;
					
			
			
			//execute internal transfer
			if ($inputAction == "internaltransfer")
			{
				$sendAccKey = $accountKey;
				$sendAccName = $accountName;
				$transDate = $nowTimestamp;
				
					
					
				//if there is a recipient and an amount, perform transfer
				for ($i=0;$i<count($inputTransfers);$i++)
					{
					//perform transfer entry
					$valid = 1;
					$recSearchCount = 0;
					$transLabel = "Internal Transfer";
					
					
	
					//set input variables
					//$transThreadNum = $_POST['inputThreadNum'];
					//$enteredByID = $_POST['forumuser'];
					$enteredByID = 'NULL';
					$transPMemo1 = 'NULL'; 
					$transPMemo2 = 'NULL';
					$transThreadNum = 0;
					
					
					$recAccName = $inputTransfers[$i]['inputRecipientName'];
					$transAmt = (double)$inputTransfers[$i]['inputAmount'];
					$transMemo = $inputTransfers[$i]['inputMemo'];
					
				
					
					$transferrequestdisplay .="<br>Thank you for requesting an Internal Transfer<br>";
	
	
						
					//query DB to get account name and see how many were returned
					$accQuery2 = sprintf('SELECT `BankAccounts`.`baKey`, `BankAccounts`.`baAccountName`'
					. ' FROM `BankAccounts`'
					. ' WHERE `BankAccounts`.`baAccountName` = CONVERT(_utf8 \'%s\' USING latin1)'
					. ' COLLATE latin1_swedish_ci'
					. ' ORDER BY `BankAccounts`.`baKey` ASC'
					,mysql_real_escape_string($recAccName));
					$accResult2 = mysql_query($accQuery2);
					if($accArr2 = mysql_fetch_array($accResult2, MYSQL_ASSOC))
						{
						$recAccKey = $accArr2['baKey'];
						$recSearchCount = 1;
						while ($accArr2 = mysql_fetch_array($accResult2, MYSQL_ASSOC))
							{
							$recSearchCount += 1;
							}
						}
					else
						{
						// no accounts found
						}
	
						//validate entered values
						//check recipient account name
						if ($recSearchCount == 1)
							{
							//exactly one account found
							array_push($statusMessages,  array( 'message' => sprintf('<span class="good"><b><i>Ok</b></i> Recipient account <span class="important">%s</span> found</span>',$recAccName),
							'alabel' => 'internaltransfer')); 
							}
						elseif ($recSearchCount > 1)
							{
							//more than one account
							array_push($statusMessages,  array( 'message' => sprintf('<span class="bad"><b><i>Error</b></i> Too many matching accounts found for recipient <span class="important>%s</span></span>',$recAccName),
							'alabel' => 'internaltransfer'));
							$valid = 0;
							}
						else
							{
							//no accounts found
							array_push($statusMessages,  array( 'message' => sprintf('<span class="bad"><b><i>Error</b></i> No matching accounts found for <span class="important">%s</span></span>',$recAccName),
							'alabel' => 'internaltransfer'));
							$valid = 0;
							}
						if ( $transAmt <= $sendLimit)
							{
							//transfer amount does not exceed limit
							array_push($statusMessages,  array( 'message' => sprintf('<span class="good"><b><i>Ok</b></i> Transfer amount does not exceed current balance</span>'),
							'alabel' => 'internaltransfer'));
							}
						else
							{
							//transfer amount exceeds limit
							$valid = 0;
							array_push($statusMessages,  array( 'message' => sprintf('<span class="bad"><b><i>Error</b></i> Transfer amount of $<span class="important">%f</span> cannot exceed sending limit of $<span class="important">%f</span></span>',$transAmt,$sendLimit),
							'alabel' => 'internaltransfer'));
							}
								
						if (((float)$transAmt > 0 )&& ((float)$transAmt <= $hardlimit))
							{
							//within general single-transfer limit
							array_push($statusMessages,  array( 'message' => sprintf('<span class="good"><b><i>Ok</b></i> Transfer amount of <span class="important">%s</span> within accepted range</font></span>',number_format($transAmt, 2, '.', ',')),
							'alabel' => 'internaltransfer'));
							}
						else
							{
							//not within general single-transfer limit
							$valid = 0;
							array_push($statusMessages,  array( 'message' => sprintf('<span class="bad"><b><i>Error</b></i>: Transfer amount must exceed $<span class="important">0</span> and be less than $<span class="important">%f</span></span>',$hardlimit),
							'alabel' => 'internaltransfer'));
							}
								
						if (!strcasecmp($sendAccName, $recAccName))
							{
							//can't send a transfer to yourself!
							$valid = 0;
							array_push($statusMessages,  array( 'message' => sprintf('<span class="bad"><b><i>Error</b></i> You cannot send money to yourself!</span>'),
							'alabel' => 'internaltransfer'));
							}
							//finished with validation
						if ($valid)
							{
							//proceed with insertion (giggity)
							if ($actionExecute == FALSE)
								{
								//we are in preview mode
								//notify user that the transfer is acceptable
								array_push($statusMessages,  array( 'message' => sprintf('<span class="good">Preview: Transfer Will Be Accepted</span>'),
								'alabel' => 'internaltransfer'));
								}
							elseif ($actionExecute == TRUE)
								{
								//live mode
								//notify user transfer accepted
								$transferrequestdisplay .= "<br><font color=blue>Transfer accepted</font><br><br>Processing...    <br>";
								//create insert query
								$sql = 'INSERT INTO `TransactionHistory` (`thRef`, `thDate`, `thSenderAccount`, `thRecipientAccount`, `thSenderKey`, `thRecipientKey`, `thBalance`, `thLabel`, `thThreadNumber`, `thEnteredByForumID`, `thMemo`, `thPMemo1`, `thPMemo2`) VALUES (NULL, "'
								.mysql_real_escape_string($transDate).'", "'
								.mysql_real_escape_string($sendAccName).'", "'
								.mysql_real_escape_string($recAccName).'", "'
								.mysql_real_escape_string($sendAccKey).'", "'
								.mysql_real_escape_string($recAccKey).'", "'
								.mysql_real_escape_string($transAmt).'", "'
								.mysql_real_escape_string($transLabel).'", "'
								.mysql_real_escape_string($transThreadNum).'", "'
								.mysql_real_escape_string($enteredByID).'", "'
								.mysql_real_escape_string($transMemo).'", "'
								.mysql_real_escape_string($transPMemo1).'", "'
								.mysql_real_escape_string($transPMemo2).'")';
								//perform query
								$result = mysql_query($sql);
								$lastid = mysql_insert_id();
								
								$transfer_success = TRUE;
								
								//make receipt
								$receiptData['id'] = $lastid;
								$receiptData['date'] = date("Y-m-d H:i:s",$transDate);
								$receiptData['amount'] = $transAmt;
								$receiptData['sendname'] = $sendAccName;
								$receiptData['recname'] = $recAccName;
								$receiptData['label'] = $transLabel;
								$receiptData['memo'] = $transMemo;
								//notify user that the transfer was accepted
								array_push($statusMessages,  array( 'message' => sprintf('<span class="good">Completed! Transfer entered. Printing receipt...</span>'),
								'alabel' => 'internaltransfer'));
	
								//print receipt
								array_push($statusMessages,  array( 'message' => sprintf('<span class="notice">Receipt<br />Transfer ID Number: <span class="important">%1$d</span><br />Date: <span class="important">%2$s</span><br />Amount: $<span class="important">%3$s</span><br />Sender: <span class="important">%4$s</span><br />Recipient: <span class="important">%5$s</span><br />Label: <span class="important">%6$s</span><br />Memo: <span class="important">%7$s</span><br /></span>'
								,htmlspecialchars($receiptData['id'])
								,htmlspecialchars($receiptData['date'])
								,number_format($receiptData['amount'], 2, '.', '')
								,htmlspecialchars($receiptData['sendname'])
								,htmlspecialchars($receiptData['recname'])
								,htmlspecialchars($receiptData['label'])
								,htmlspecialchars($receiptData['memo'])),
								'alabel' => 'internaltransfer'));
								}
							}
						else
							{
							//transfer has been denied
							array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">Sorry, your transfer cannot be processed. Please check for errors.</span>'),
							'alabel' => 'internaltransfer'));
							}
						}
				/*
				 *show error if no transfers are valid 
				   else
					{
					//recipient and amount are not be specified
					array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">Please include both a recipient and transfer amount.</span>'),
					'alabel' => 'internaltransfer'));
					}*/
	
				if ($actionExecute == FALSE)
					{
					//preview mode
					array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">Preview Mode</span>'),
					'alabel' => 'internaltransfer'));
					}
				}
	//end internal transfer processing
			
	
			$myBankAccount = LoadBankAccount($accountKey, $intRate, $nowTimestamp, $compoundPeriodSeconds, $mDebug);
				
			//get current balance
			$bankAccounts[$bankAccountUIN]['balance'] = $myBankAccount['balance'];
			$balance = $myBankAccount['balance'];
	
			$sendLimit = $balance - $bankAccountLimit;
			
			//make javascript stuff for the account history
			$jsacchist = $myBankAccount['jsacchist'];
	
	
			$html_head .= '<script type="text/javascript"> ';
			$html_head .= 'var contentholder = new Object(); ';
			$html_head .= 'var historyData = new Array(); ';
			$html_head .= $jsacchist;
			$html_head .= '
	function displaycontent(idname,contentvar)
		{
		if (document.getElementById(idname).innerHTML == "")
			{
			document.getElementById(idname).innerHTML = contentholder[contentvar];
			}
		else
			{
			document.getElementById(idname).innerHTML = "";
			}
		}
	
	function goHist(sortcolumn, sortorder)
		{
		var tempvar2 = "";
	
		if (sortcolumn == "sendername")
			{
			if (sortorder == "ASC"){historyData.sort(bySendernameASC);}
			if (sortorder == "DESC"){historyData.sort(bySendernameDESC);}
			}
	
		if (sortcolumn == "recipientname")
			{
			if (sortorder == "ASC"){historyData.sort(byRecipientnameASC);}
			if (sortorder == "DESC"){historyData.sort(byRecipientnameDESC);}
			}
	
		if (sortcolumn == "date")
			{
			if (sortorder == "ASC"){historyData.sort(byDateASC);}
			if (sortorder == "DESC"){historyData.sort(byDateDESC);}
			}
	
		if (sortcolumn == "ref")
			{
			if (sortorder == "ASC"){historyData.sort(byRefASC);}
			if (sortorder == "DESC"){historyData.sort(byRefDESC);}
			}
	
		if (sortcolumn == "amount")
			{
			if (sortorder == "ASC"){historyData.sort(byAmountASC);}
			if (sortorder == "DESC"){historyData.sort(byAmountDESC);}
			}
		
		tempvar2 ='
		.' \'<table border="0" width="100%" cellspacing="2"><tr bgcolor="#66CCFF">'
		.'<td align="center"><table width="100%" border = "1"><tr><td align="center" rowspan="2">'
		.'Ref#'
		.'</td><td width="10">'
		.'<a href="javascript:goHist(\\\'ref\\\',\\\'ASC\\\')">'
		.'<img src="files/buttonup.gif" border="0" height="25" width="24"></a></td></tr><tr><td>'
		.'<a href="javascript:goHist(\\\'ref\\\',\\\'DESC\\\')">'
		.'<img src="files/buttondn.gif" border="0" height="25" width="24"></a></td></tr></table></td>'
		.'<td align="center"><table width="100%" border = "1"><tr><td align="center" rowspan="2">'
		.'Date'
		.'</td><td width="10">'
		.'<a href="javascript:goHist(\\\'date\\\',\\\'ASC\\\')">'
		.'<img src="files/buttonup.gif" border="0" height="25" width="24"></a></td></tr><tr><td>'
		.'<a href="javascript:goHist(\\\'date\\\',\\\'DESC\\\')">'
		.'<img src="files/buttondn.gif" border="0" height="25" width="24"></a></td></tr></table></td>'
		.'<td align="center"><table width="100%" border = "1"><tr><td align="center" rowspan="2">'
		.'Amount'
		.'</td><td width="10">'
		.'<a href="javascript:goHist(\\\'amount\\\',\\\'ASC\\\')">'
		.'<img src="files/buttonup.gif" border="0" height="25" width="24"></a></td></tr><tr><td>'
		.'<a href="javascript:goHist(\\\'amount\\\',\\\'DESC\\\')">'
		.'<img src="files/buttondn.gif" border="0" height="25" width="24"></a></td></tr></table></td>'
		.'<td align="center"><table width="100%" border = "1"><tr><td align="center" rowspan="2">'
		.'From'
		.'</td><td width="10">'
		.'<a href="javascript:goHist(\\\'sendername\\\',\\\'ASC\\\')">'
		.'<img src="files/buttonup.gif" border="0" height="25" width="24"></a></td></tr><tr><td>'
		.'<a href="javascript:goHist(\\\'sendername\\\',\\\'DESC\\\')">'
		.'<img src="files/buttondn.gif" border="0" height="25" width="24"></a></td></tr></table></td>'
		.'<td align="center"><table width="100%" border = "1"><tr><td align="center" rowspan="2">'
		.'To'
		.'</td><td width="10">'
		.'<a href="javascript:goHist(\\\'recipientname\\\',\\\'ASC\\\')">'
		.'<img src="files/buttonup.gif" border="0" height="25" width="24"></a></td></tr><tr><td>'
		.'<a href="javascript:goHist(\\\'recipientname\\\',\\\'DESC\\\')">'
		.'<img src="files/buttondn.gif" border="0" height="25" width="24"></a></td></tr></table></td>'
		.'</tr>'
		.''
		.'\';
	
	
		for (i = 0; i < historyData.length; i++)
			{
			tempvar = historyData[i];
			if (tempvar["Type"]>0){tempvar2 = tempvar2 + \'<tr class="tHistory" name="credit">\';}
			else if (tempvar["Type"]<0){tempvar2 = tempvar2 + \'<tr class="tHistory" name="debit">\';}
			else if (tempvar["Type"]==0){tempvar2 = tempvar2 + \'<tr class="tHistory" name="none">\';}
			tempvar2 += \'<td name="reference">\' + tempvar["Reference"];
			tempvar2 += \'</td><td name="date" >\' + tempvar["DateText"];
			tempvar2 += \'</td><td name="amount" > $  \' + tempvar["Amount"];
			tempvar2 += \'</td><td name="sendername" >\' + tempvar["Sendername"];
			tempvar2 += \'</td><td name="recipientname" >\' + tempvar["Recipientname"];
			tempvar2 += \'</td><td name="details"><div>Details for # \'+tempvar["Reference"]+\'<br />Memo: \' + tempvar["Memo"] + \'</div></td></tr>\'
			}
		tempvar2 += \'</table>\';
	
		contentholder["accounthistorycontent"]=tempvar2;
	
		document.getElementById("accounthistory").innerHTML = contentholder["accounthistorycontent"];
		}
	
	
	function displayNewWin()
		{
		DispWin = window.open(\'\',\'NewWin\', \'toolbar=no,status=no,width=300,height=200\')
		message = document.refform.fullcont.value;
		DispWin.document.write(message);
		}
		
	function goRefDetail(number)
		{
		var avar44 = historyData[number];
		var blah55;
		blah55 = \'Type: \';
		if (avar44["Type"]>0){blah55 = blah55 + \'Credit\'}
		else if (avar44["Type"]<0){blah55 = blah55 + \'Credit\'}
		else {blah55 = blah55 + \'Other\'} 
		blah55 = blah55 + \'<br> Reference: \' + avar44["Reference"] + \'<br> Sender: \' + avar44["Sendername"] + \'<br> Recipient: \' + avar44["Recipientname"] + \'<br> Date: \' + avar44["DateText"] + \'<br> Label: \' + avar44["Label"] + \'<br> Memo: \' + avar44["Memo"];
		if (avar44["Topic"]){blah55 = blah55 + \'<br> Topic: <a href="http://z10.invisionfree.com/First_Bank_of_CC/index.php?showtopic=\' + avar44["Topic"] + \'">Click</a>\';}
		refform.fullcont.value = blah55;
		displayNewWin();
		//document.refform.submit();
		}
		
	function bySendernameASC(a, b) 
		{
	    var x = a.Sendername.toLowerCase();
	    var y = b.Sendername.toLowerCase();
	    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		}
		
	function bySendernameDESC(a, b) 
		{
	    var x = a.Sendername.toLowerCase();
	    var y = b.Sendername.toLowerCase();
	    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
		}
		
	function byRecipientnameASC(a, b) 
		{
	    var x = a.Recipientname.toLowerCase();
	    var y = b.Recipientname.toLowerCase();
	    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		}
		
	function byRecipientnameDESC(a, b) 
		{
	    var x = a.Recipientname.toLowerCase();
	    var y = b.Recipientname.toLowerCase();
	    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
		}
		
	function byDateASC(a, b) 
		{
	    var x = a.Date;
	    var y = b.Date;
	    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		}
		
	function byDateDESC(a, b) 
		{
	    var x = a.Date;
	    var y = b.Date;
	    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
		}
		
	function byRefASC(a, b) 
		{
	    var x = a.Reference;
	    var y = b.Reference;
	    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		}
		
	function byRefDESC(a, b) 
		{
	    var x = a.Reference;
	    var y = b.Reference;
	    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
		}
		
	function byAmountASC(a, b) 
		{
	    var x = a.Amount;
	    var y = b.Amount;
	    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		}
		
	function byAmountDESC(a, b) 
		{
	    var x = a.Amount;
	    var y = b.Amount;
	    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
		}
		
	</script>';
		
		
			$html_footer .= '<script type="text/javascript">
		goHist(\'ref\',\'DESC\');
		
		</script>';
				
			//output from an internal transfer
			$html_status .= $transferrequestdisplay;
	
			//set up html display of account summary section
			$s_id = 'summary';
			$s_title = 'Account Summary';
			$s_state = 'A';
			$s_body = sprintf('<br><b>Account Name: </b><span class="important">'
				.'%1$s</span><br /><b>Current Date: </b><span class="important">'
				.'%2$s</span><br /><b>Current Balance: </b><span class="important">$'
				.'%3$s</span>'
				.'<br /><b>Daily Periodic Rate: </b><span class="important">%8$s%%</span>'
				.'<br /><b>Annual Percentage Yield: </b><span class="important">%4$s%%</span>'
				.'<br /><b>Account Owner: </b><span class="important"><a href="%5$s%9$d">Profile</a></span>'
				.'<br /><b>Account Manager: </b><span class="important"><a href="%5$s%6$d">Profile</a></span>'
				.'<br /><b>Account Type: </b><span class="important">%7$s</span>'
				,$accountName
				,date("Y-m-d H:i:s",$nowTimestamp)
				,number_format($balance, 2, '.', ',')
				,round_significant($intRate*100,6)
				,$uinURL
				,$accMan
				,$accountTypeTitle
				,round_significant((exp((86400/$compoundPeriodSeconds)*log($intRate+1))-1)*100,6)
				,$accountOwnerUserNumber
				);
			
			if (($bankAccountData['baLimit']>0)||($bankAccountData['baLimit']<0))
				{
				$s_body .='<br /><b>Credit Limit:</b> $<span class="important">'
				.number_format($bankAccountData['baLimit'], 2, '.', ',')
				.'</span>'
				.'<br /><b>Transfer Limit:</b> $<span class="important">'
				.number_format($sendLimit, 2, '.', ',')
				.'</span>';
				}
	
			$contentsection[] = array('id'=> $s_id,
			'title'=> $s_title,
			'body'=> $s_body,
			'state'=> $s_state,
			'float'=> 3);
	
			//set up html display of transfer history section
			
			if($inputAction == "internaltransfer"){$s_state='A';}
			else {$s_state='I';}
			
			$s_id = 'history';
			$s_title = 'Transaction History';
			$s_body = '<span id="accounthistory">&nbsp;</span>';
			
			$contentsection[] = array('id'=> $s_id,
			'title'=> $s_title,
			'body'=> $s_body,
			'state'=> $s_state,
			'float'=> 5);
	
	
			//set up transfer html section
		
			$transferHtml=  'Your Transfer Limit: $<b>%7$s</b>
			<span class="inputsection">Requested transfer information
				<form name="internalTransferForm" method="post" id="ITform"><br />
					<div id="transferForm">
						<span class="importantinput" name="Receiving Account:"> 
							<input type="text" id="recname" name="inputRecipientName0" value="%4$s"  >
						</span><br />
						
						<span class="importantinput" name="Transfer Amount:"> 
							<input  type="text" id="transamt" name="inputAmount0" value="%5$s" >
						</span><br />
						
						<span class="importantinput" name="Memo:"> 
							<textarea  name="inputMemo0" id="transmemo" maxlength=255 cols="40" rows="6" wrap="virtual">%6$s</textarea>
						</span>
					</div>
					
					<input type="hidden" name="action" value="internaltransfer">
					<input type="hidden" name="inputPMemo2" value="Yup it really was.">
					<input type="hidden" name="password" value="%1$s">
					<input type="hidden" name="forumuser" value="%2$s">
					<input type="hidden" name="institute" value="%3$s">
					
					<br />
					<span class="importantinput" style="height:2em;" >
						<input style="width:5em;float:left;height:1.6em;" type="submit" name="preview" value="Preview" >
						<input style="width:5em;float:right;height:1.6em;" type="submit" name="submit" value="Submit">
					</span>
				</form>
			</span>
			
			<br />
			<span class="inputsection"><span class="importantinput" name="Batch Entry">
				<textarea id="batchEntry" maxlength=2048 cols="64" rows="6" wrap="off" ></textarea>
				<br />
				<button name="loadbutton" onClick="loadBatch(\'batchEntry\',\'transferForm\')">
					Click to Load Batch Entry
				</button>
				</span>
				<br />Warning: this will clear any currently entered transfer information.
			</span>
			
			';
			
			
			
			
			$s_id = 'transfer';
			$s_title = 'Internal Transfer';
			
			if(($inputAction == "internaltransfer")&&($transfer_success == FALSE))
				{
			
				$s_body = sprintf($transferHtml,
				$_POST["password"],
				$_POST["forumuser"],
				htmlspecialchars($inputInstitute),
				htmlspecialchars($recAccName),
				number_format($transAmt, 2, '.', ''),
				$inputMemo,
				number_format($sendLimit, 2, '.', ','));
			
			
				$s_state = "A";
				
				}
			
			else
				{
			
				$s_body = sprintf($transferHtml,
				$_POST["password"],
				$_POST["forumuser"],
				htmlspecialchars($inputInstitute),
				"",
				number_format(0, 2, '.', ''),
				"",
				number_format($sendLimit, 2, '.', ','));
				
				$s_state = "I";
				}
				
			$contentsection[] = array('id'=> $s_id,
			'title'=> $s_title,
			'body'=> $s_body,
			'state'=> $s_state,
			'float'=> 4);
			}
		}
	else
		{
		//user does not have any bank accounts
		array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">No bank accounts found for user: <span class="important">%s</span></span>',$userAccountName),
		'alabel' => 'broadcast')); 
		}
	}
else
	{
	//user is not logged in to user account or bank account	
	array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">You are not logged in.</span>'),
	'alabel' => 'broadcast')); 
	}

?>

