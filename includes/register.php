<?php
if ($inputAction == "register")
	{
	$s_state = "A";
	$newUserAccountData['uaName']=$inputRegister_Name;
	$newUserAccountData['uaUIN']=(int)$inputRegister_UIN;
	$newUserAccountData['uaSalt']=hash('sha256',uniqid(rand(), true));
	$newUserAccountData['uaPass']=myPassHash($inputRegister_Pass,$newUserAccountData['uaSalt']);
	$newUserAccountData['uaEmail']=$inputRegister_Email;
	$newUserAccountData['uaIdentName']=$inputRegister_IdentName;
	$newUserAccountData['uaIdentNumber']=(int)$inputRegister_IdentNumber;
	$newUserAccountData['uaRoles']=$inputRegister_Roles;
		
	array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">Account Requested: <span class="important"><br />%1$s<br />%2$s<br />%3$s<br />%4$s<br />%5$s<br />%6$s<br />%7$s</span></span>'
		,$newUserAccountData['uaName']
		,$newUserAccountData['uaUIN']
		,$newUserAccountData['uaSalt']
		,$newUserAccountData['uaPass']
		,$newUserAccountData['uaEmail']
		,$newUserAccountData['uaIdentName']
		,$newUserAccountData['uaIdentNumber'])
		,'alabel' => 'register'));
	

	if ($mDebug)
		{
		echo " New user account data: ";
		var_dump($newUserAccountData);
		}
		
	if ($actionExecute == TRUE)
		{
		$sql = sprintf('INSERT INTO `%1$s`.`UserAccounts` '
		.'(`uaNumber`, `uaUIN`, `uaName`, `uaPass`, `uaSalt`, `uaEmail`, `uaIdentName`, `uaIdentNumber`, `uaRoles` ) '
		.'VALUES '
		.'(NULL, %2$d, \'%3$s\', \'%4$s\', \'%5$s\', \'%6$s\', \'%7$s\', \'%8$d\', \'%9$s\'); '
		,$dbname
		,mysql_real_escape_string($newUserAccountData['uaUIN'])
		,mysql_real_escape_string($newUserAccountData['uaName'])
		,mysql_real_escape_string($newUserAccountData['uaPass'])
		,mysql_real_escape_string($newUserAccountData['uaSalt'])
		,mysql_real_escape_string($newUserAccountData['uaEmail'])
		,mysql_real_escape_string($newUserAccountData['uaIdentName'])
		,mysql_real_escape_string($newUserAccountData['uaIdentNumber'])
		,mysql_real_escape_string($newUserAccountData['uaRoles'])
		);

//perform query
		$result = mysql_query($sql);
			
		if ($mDebug)
			{
			echo "<br> query:".$sql;
			$result = mysql_query($sql);	
			}
			/*
			array_push($statusMessages,  array( 'message' => sprintf('<span class="good">Account created!<span class="important">%s</span></span>'
			,$newUserAccountData['uaName'])
			,'alabel' => 'register'));
			*/
		}
	}	
			
//html display of register section
$s_id = 'register';
$s_title = 'User Registration';
$s_body = sprintf('<form method="post" action="?view=newuseraccount">'
.'<input type="hidden" name="username" value="%1$s">'
.'<input type="hidden" name="userpass" value="%2$s">'
.'<input type="hidden" name="action" value="register">'
.'Preview? <input type="checkbox" name="preview" checked><br>'
.'Username: <input type="text" name="r_username" value="%3$s"><br>'
.'Userpass: <input type="text" name="r_userpass" value="%4$s"><br>'
.'Email: <input type="text" name="r_email" value="%5$s"><br>'
.'Identity Name: <input type="text" name="r_identname" value="%6$s"><br>'
.'Identity Number: <input type="text" name="r_identnumber" value="%7$d"><br>'
.'Roles: <input type="text" name="r_roles" value="%8$s"><br>'
.'UIN: <input type="text" name="r_uin" value="%9$d"><br>'
.'<input type="submit"></form></span>'
,htmlspecialchars($_POST['username'], ENT_QUOTES)
,htmlspecialchars($_POST['userpass'], ENT_QUOTES)
,htmlspecialchars($inputRegister_Name)
,htmlspecialchars($inputRegister_Pass)
,htmlspecialchars($inputRegister_Email)
,htmlspecialchars($inputRegister_IdentName)
,htmlspecialchars($inputRegister_IdentNumber)
,htmlspecialchars($inputRegister_Roles)
,htmlspecialchars($inputRegister_UIN));

$contentsection[] = array('id'=> $s_id,
'title'=> $s_title,
'body'=> $s_body,
'state'=> $s_state,
'float'=> 10);


?>