<?php





if ((strlen($inputUserName) >= 6) && (strlen($inputUserPass) >= 6))
{
	//***********load user account access****************
	
	
	
	
	$query = sprintf('SELECT * FROM `UserAccounts`'
	.' WHERE `uaName` = CONVERT(_utf8 \'%s\' USING latin1)'
	.' COLLATE latin1_swedish_ci ',mysql_real_escape_string($inputUserName));

	$result = mysql_query($query);
	
	

	
	
	if ($masterDebug == TRUE)
	{
				array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly"><br> Login Query: <span class="important">%s</span></span>',$query),
		'alabel' => 'login'));
		
	}

	
	
	
	if (mysql_num_rows($result) == "1")
	{
		$userAccountData = mysql_fetch_array($result, MYSQL_ASSOC);

		
		$userPassHash = myPassHash($inputUserPass,$userAccountData['uaSalt']); 
		if ($masterDebug == TRUE)
	{
				array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly"><br> Requested account password: <span class="important">%s</span></span>',$userPassHash),
		'alabel' => 'login'));
		
	}
		
		if ($userPassHash == $userAccountData['uaPass'])
			{
			
			$accessType="UserAccount";
			array_push($statusMessages,  array( 'message' => sprintf('<span class="good">User Login Successful, <span class="important">%s</span></span>',$userAccountData['uaName']),
			'alabel' => 'login'));

			}
		else
			{
			
			$userAccountData = NULL;
			$accessType = "none";
			array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">User Login Unsuccessful, <span class="important">%s</span></span>',htmlspecialchars($inputUserName)),
			'alabel' => 'login'));
			$s_state = 'A';
			
			}
		
		
		$s_state = 'I';
	}
	else
	{
		$userAccountData = NULL;
		$accessType = "none";
		array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">User Login Unsuccessful, <span class="important">%s</span></span>',htmlspecialchars($inputUserName)),
		'alabel' => 'login'));
		$s_state = 'A';
	}
	
}



elseif (strlen($inputPassWord)>16)
	{

	//*****************load bank account access************

	$bankPassHash = myPassHash(substr($inputPassWord,0,36),FALSE);
	
	$query = sprintf('SELECT * FROM `BankAccounts`'
	.' WHERE `baPPASS` = CONVERT(_utf8 \'%s\' USING latin1)'
	.' COLLATE latin1_swedish_ci ',
	mysql_real_escape_string($bankPassHash));

	//perform query
	$result = mysql_query($query);

	if (mysql_num_rows($result) == "1")
		{
		$bankAccountData = mysql_fetch_array($result, MYSQL_ASSOC);
		$accessType="BankAccount";
		array_push($statusMessages, array('message' => sprintf('<span class="good">Bank Account Access Granted, <span class="important">%s</span></span>',$bankAccountData['baAccountName']),
		'alabel' => 'login'));
		$s_state = 'I';
		}
	else
		{
		$bankAccountData = NULL;
		$accessType = "none";
		array_push($statusMessages, array( 'message' => sprintf('<span class="bad">Bank Account Access Denied</span>'),
		'alabel' => 'login'));
		$s_state = 'A';
		}
	
	}
elseif ($aPermOverride==TRUE)
{
$query = sprintf('SELECT * FROM `UserAccounts`'
	.' WHERE `uaNumber` = 1');
	
		//perform query
$result = mysql_query($query);
	
$userAccountData = mysql_fetch_array($result, MYSQL_ASSOC);
$accessType="UserAccount";
}
else
	{
		$bankAccountData = NULL;
		$userAccountData = NULL;
		$accessType = "none";
	
	
array_push($statusMessages, array( 'message' => sprintf('<span class="bad">Invalid Login</span>'),
		'alabel' => 'login'));
	
	$s_state = 'A';

	}


	
//html display of access section

$s_id = 'login';
$s_title = 'Account Login';
$s_body = sprintf('<span class="inputsection">Current Bank Account: %s',
	$bankAccountData['baAccountName'])
	.sprintf('<span class="importantinput"><form method="post" action="?view=atp" name="Automatic Teller Panel">Bank Account Name: <input name="account" value="%1$s" type="text"><br />Bank Account Password: <input maxlength="160" size="32" name="password" value="%2$s" type="password"><br><input type="hidden" name="action" value="login"><input name="Submit" value="Bank Account Login" type="submit"></form></span></span>'
	,$bankAccountData['baAccountName']
	,htmlspecialchars($_POST['password'], ENT_QUOTES))
	.sprintf('<span class="inputsection">Current User Account: %s',
	$userAccountData['uaName'])
	.sprintf('<span class="importantinput"><form method="post" action="?view=atp">User Account Name: <input type="text" name="username" value="%s"><br>User Account Password: <input type="password" name="userpass" value="%s"><input type="hidden" name="action" value="login"><br><input type="submit" value="User Account Login" > </form></span></span>'
	,htmlspecialchars($_POST['username'], ENT_QUOTES)
	,htmlspecialchars($_POST['userpass'], ENT_QUOTES));

$contentsection[] = array('id'=> $s_id,
'title'=> $s_title,
'body'=> $s_body,
'state'=> $s_state,
'float'=> 10);







?>