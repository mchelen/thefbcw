<?php

    
    //get input account info
	$inputAccName = $_POST['inputName'];
    $inputAccUIN = $_POST['inputUIN'];
    
    
    //user info
    $accMan = $userAccountData['uaUIN'];
    $branMan = $userAccountData['uaUIN'];
    
    $forforNum = $_POST['inputFFN'];
    $foruseNum = $_POST['inputFUN'];
    $accCUni = $_POST['inputCUni'];
    $accCNum = $_POST['inputCNum'];
    $accType = $_POST['inputType'];
    
    

    
    if ($inputAction=="newaccount")
    	{
    	//autoexpand section
    	$s_state = "A";
    	
        $valid=TRUE;
//check account name length
        if (strlen($inputAccName)<5)
        	{
            $valid=FALSE;
            array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">Bank account name must be at least 5 characters.</span>'
			,htmlspecialchars($inputAccName))
			,'alabel' => 'newaccount'));
        	}
//allow admin to set arbitrary rate and limit  	
       	if ($admin_override)
        	{
        	if ($_POST['inputAMan'])
	        	{
	        	$accMan = (double)$_POST['inputAMan'];
	        	}
        	if ($_POST['inputBMan'])
	        	{
	        	$branMan = (double)$_POST['inputBMan'];
	        	}
        	
            if ($_POST['inputRate'])
            	{
                $intRate = (double)$_POST['inputRate'];
            	}
            if ($_POST['inputLimit'])
            	{
                $limit = (double)$_POST['inputLimit'];
            	}
        	}
        	
        	$sql = sprintf('SELECT `baAccountName` FROM `BankAccounts` WHERE `baAccountName` = CONVERT(_utf8 \'%s\' USING latin1) COLLATE latin1_swedish_ci',$inputAccName);
        	$result = mysql_query($sql);
        	if (($row = mysql_fetch_array($result, MYSQL_ASSOC)) == NULL)
	        	{
	        	
	        	}
        	else
	        	{
	        	$valid = FALSE;
	        	array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">Bank account name already exists: <span class="important">%s</span></span>'
				,htmlspecialchars($inputAccName))
				,'alabel' => 'newaccount'));
	        	}
	        	
//load rates that are currently available for new accounts      
		$right_now = date('Y-m-d H:i:s');	
        $sql = 'SELECT * FROM `BankRates` WHERE `brType` = CONVERT( _utf8 \'%1$s\' USING latin1) COLLATE latin1_swedish_ci AND `brStartDate` <= \'%2$s\' AND `brEndDate` >= \'%2$s\' ';
        $query=sprintf($sql,mysql_real_escape_string($accType),mysql_real_escape_string($right_now));
        $result = mysql_query($query);
        
        if ($mDebug){echo " New Account rates query: $query ";}
        
        
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
        	{
        	//load the named roles that are allowed to create this account type 

        	$accountCreateRoles = explode(",",$row['brCreate']);
			

            if($mDebug)
            	{
            	echo "New Account section, ";
            	echo " userAccountRoles: ".var_dump($userAccountRoles);
            	echo " accountCreateRoles: ".var_dump($accountCreateRoles);
            	}
            	
            //checks if one of the elements is shared in the two arrays 
            if (count(array_intersect($userAccountRoles,$accountCreateRoles))>0)
            	{
            	$intRate = $row['brAPY'];
            	}
            else
            	{
            	$valid = FALSE;
            	
            	//show error
	        	array_push($statusMessages,  array( 'message' => sprintf('<span class="bad">You do not have permission to create this type of account.</span>'
	            ,htmlspecialchars($accType))
				,'alabel' => 'newaccount'));
	            }
        	}

        //the account is accepted for creation
        if ($valid)
        	{
        		//provide account information
	        	array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">Account request is valid:</span><br />Type: <span class="important">%1$s</span><br />Account Name: <span class="important">%2$s</span><br />Interest Rate: <span class="important">%3$f</span><br />Owner User Number: <span class="important">%4$s</span><br />Account Manager User Number: <span class="important">%5$s</span><br />Branch Manager User Number:<span class="important">%6$s</span>'
	            ,htmlspecialchars($accType)
	            ,htmlspecialchars($inputAccName)
	            ,htmlspecialchars($intRate)
	            ,htmlspecialchars($foruseNum)
	            ,htmlspecialchars($accMan)
	            ,htmlspecialchars($branMan))
				,'alabel' => 'newaccount'));
        	
        	if ($actionExecute)
        		{
	        	//generate a UIN and password (and password hash)
	            $seed = rand(-32768,32768);
	            $newuin = myPassHash((string)time(),(string)$seed);
	            $pass1=createRandomPassword(36);
	            $passhash = myPassHash($pass1,FALSE);
	            
	            array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">Processing account: <span class="important">%s</span></span>'
				,htmlspecialchars($inputAccName))
				,'alabel' => 'newaccount'));
	
				//create the account
	            $sql = 'INSERT INTO `BankAccounts` (`baKey`, `baAccountName`, `baInterestRate`, `baType`, `baLimit`, `baAccountManager`, `baBranchManager`, `baForumForumNumber`, `baForumUserNumber`, `baPPASS`, `baGameUnique`, `baGameNumber`, `baUIN` ) VALUES (NULL, "%1$s", "%2$f", "%3$s", "%4$f", "%5$s", "%6$s", "%7$d", "%8$d", "%9$s", "%10$s", "%11$d", "%12$s")';
	
	            $query = sprintf($sql,mysql_real_escape_string($inputAccName),mysql_real_escape_string($intRate),mysql_real_escape_string($accType),mysql_real_escape_string($limit),mysql_real_escape_string($accMan),mysql_real_escape_string($branMan),mysql_real_escape_string($forforNum),mysql_real_escape_string($foruseNum),mysql_real_escape_string($passhash),mysql_real_escape_string($accCUni),mysql_real_escape_string($accCNum),mysql_real_escape_string($newuin));
	            
	            $result = mysql_query($query);
	
				array_push($statusMessages,  ba_confirm($pass1));	            

        		}
        	else
        		{
        		

        		}
        	}
        else
        	{
        	//account creation denied
           	array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">Error Making Account</span><br>')
                ,'alabel' => 'newaccount'));
		    }
		}
       


		if ($inputAction=="bapasswd")
			{
			//	autoexpand section
			$s_state = "A";
			


			 	//generate a password (and password hash)
	            $pass1=createRandomPassword(36);
						
	            $passhash = myPassHash($pass1,FALSE);
         
	            array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">Processing account ID: <span class="important">%s</span></span>'
				,htmlspecialchars($inputAccUIN))
				,'alabel' => 'bapasswd'));
	
				//update the account
	            $sql = 'UPDATE `fbcctest`.`BankAccounts` SET `baPPASS` = \'%1$s\' WHERE CONVERT(`BankAccounts`.`baUIN` USING utf8) = \'%2$s\' LIMIT 1;';
	
	            $query = sprintf($sql, mysql_real_escape_string($passhash), mysql_real_escape_string($inputAccUIN));
	            
	            $result = mysql_query($query);
	
	          //confirm that account login works  
	            $query = 'Select `BankAccounts`.*'
	            . ' FROM BankAccounts'
	            . ' WHERE (`BankAccounts`.`baPPASS` = "'
	            .myPassHash($pass1,FALSE)
	            . '")'
	            . ' ORDER BY `BankAccounts`.`baAccountName` ASC';
	            
	            $result = mysql_query($query);
	            
	            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	            	{
	            	//confirm bank account credentials
	                array_push($statusMessages,  array( 'message' => sprintf('<span class="good">Account Updated</span><br /><span class="ugly">Account Name: <span class="important">%1$s</span></span><br /><span class="ugly">Account ID: <span class="important">%3$s</span></span><br /><span class="ugly">Password: <span class="important">%2$s</span></span>'
		                	,htmlspecialchars($row['baAccountName'])
		                	,htmlspecialchars($pass1)
		                	,$row['baUIN'])
		                ,'alabel' => 'bapasswd'));
	            	}
	            
			}



		//load account types that are available to this user       	
        $right_now = date('Y-m-d H:i:s');
		$sql = 'SELECT * FROM `BankRates` WHERE ((`brStartDate` <= \'%1$s\') AND (`brEndDate` >= \'%1$s\'))';
        $query=sprintf($sql,mysql_real_escape_string($right_now));
        if($mDebug){echo "Bank Rates query: $query ";}	
        $result = mysql_query($query);
        
        if ($result != NULL)
	        {
	        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	        	{

		        $curAccountCreateRoles = explode(",",$row['brCreate']);	

	        	if($mDebug)
	            	{
	            	echo "New account menu section: ";
	            	echo " userAccountRoles: ";
	            	var_dump($userAccountRoles);
	            	echo " accountCreateRoles: ";
	            	var_dump($curAccountCreateRoles);
	            	}
			        	
	        	$checkoverlap = array_intersect($userAccountRoles,$curAccountCreateRoles);
	        	
	        	if($mDebug)
	            	{
	            	echo "New account menu role overlap: ";
	            	echo " checkoverlap: ";
	            	var_dump($checkoverlap);
	            	}
	        	
	        	
				if (count($checkoverlap)>0)
					{
					//check to see if it should be selected
					if($row['brType']==$accType)
						{
						$baType_selected="selected";
						}
					else
						{
						$baType_selected="";
						}
					//allowed account type
					$accTypeForm .= sprintf('<option value="%1$s" %4$s >%2$s %3$f%%</option>',$row['brType'],$row['brTitle'],$row['brAPY'],$baType_selected);
					if($mDebug)
		            	{
		            	echo sprintf(" Allowed account type: %s ",$row['brType']);
		            	}
					}
	        	}
	        }
	        
	        
	        
	        
	        
		//load managed accounts that are available to this user       	
        $sql = 'SELECT * FROM `BankAccounts` WHERE ((`baAccountManager` = \'%1$s\'))';
        $query=sprintf($sql,mysql_real_escape_string($userAccountData['uaUIN']));
        if($mDebug){echo "Managed bank accounts query: $query ";}	
        $result = mysql_query($query);
        
        if ($result != NULL)
	     	{
	        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	        	{
				$accManForm .= sprintf('<option value="%2$s">%1$s (ID: %2$s)</option>',$row['baAccountName'],$row['baUIN']);
				if($mDebug)
		      	{
		         	echo sprintf(" Managed account name: %s (ID: %s)",$row['baAccountName'],$row['baUIN']);
					}
				}
	        }	        
	                
	        
	        
	        
    //set up section
    $s_id = "manage";
    $s_title = "Manage Bank Accounts";
    
    //add form for adding or changing account
    $s_body = sprintf('<form action="?view=atp" method="post">' .
    		'<br /><span class="inputsection"><center><b><font size="5">Create New Bank Account</font></b></center><br /><b><font size="2">Required</font></b>' .
    		'<br /><span class="importantinput">Account Name: <input type ="text" name="inputName" value="%6$s"></span>' .
    		'<br /><span class="importantinput">Account Type: <select name="inputType">%1$s</select></span>' .
    		'<br /><span class="importantinput">Owner Forum User Number:<input type ="text" name="inputFUN" value="%7$d"></span>' .
    		'<br /><span class="importantinput">Account Manager User Number: <input type ="text" name="inputAMan" value="%2$d" ></span>' .
    		'<br /><span class="importantinput">Branch Manager User Number: <input type ="text" name="inputBMan" value="%3$d" ></span>' .
    		'<br /><b><font size="2">Optional</font></b>' .
    		'<br /><span class="importantinput">Identity Name:<input type ="text" name="inputCUni" value="%9$s"></span>' .
    		'<br /><span class="importantinput">Identity Number:<input type ="text" name="inputCNum" value="%10$d"></span>' .
    		'<br /><span class="importantinput">Account Link Suffix:<input type ="text" name="inputFFN" value="%8$d"></span>' .
    		'<br /><span class="importantinput">Account Limit: <input type ="text" name="inputLimit" value="%11$f"></span>' .
    		'<br /><span class="importantinput">Interest Rate Override (decimal):<input type ="text" name="inputRate" value="%12$f"></span>' .
    		'<input type="hidden" name="action" value="newaccount">' .
    		'<input type="hidden" name="username" value="%4$s">' .
    		'<input type="hidden" name="userpass" value="%5$s">' .
    		'<br /><span class="importantinput">Preview: <input type="checkbox" id="previewcheck" name="preview" value="preview" checked ></span>' .
			'<span class="importantinput"><input type="submit" value="Create Account" ></span></span>' .
			'</form><br />' .
    		'<form action="?view=atp" method="post">' .
    		'<br /><span class="inputsection"><center><b><font size="5">Reset Password</font></b></center><br />' .
    		'<br /><span class="importantinput">Account Name: <select name="inputUIN">%13$s</select></span>' .
    		'<input type="hidden" name="action" value="bapasswd">' .
    		'<input type="hidden" name="username" value="%4$s">' .
    		'<input type="hidden" name="userpass" value="%5$s">' .
			'<br /><span class="importantinput">Preview: <input type="checkbox" id="previewcheck" name="preview" value="preview" checked ></span>' .
    		'<span class="importantinput"><input type="submit" value="Reset Password" ></span></span>' .
			'</form><br />'
    ,$accTypeForm
    ,$accMan
    ,$branMan
    ,htmlspecialchars($_POST['username'], ENT_QUOTES)
    ,htmlspecialchars($_POST['userpass'], ENT_QUOTES)
    ,$inputAccName
    ,$foruseNum
    ,$forforNum
    ,$accCUni
    ,$accCNum
    ,$limit
    ,$intRate
    ,$accManForm);
    
    
    
    
    //add html content section
    $contentsection[] = array('id'=> $s_id,
		'title'=> $s_title,
		'body'=> $s_body,
		'state'=> $s_state,
		'float'=> 4);
    

?>

