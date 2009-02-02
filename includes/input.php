<?php

//get form data

if ($_POST['action'])
	{
	$inputAction = $_POST['action'];
	}
elseif ($_GET['action'])
	{
	$inputAction = $_GET['action'];
	}
$inputInstitute = $_POST['institute'];

$inputView = $_GET['view'];
$inputDisplay = $_GET['disp'];

$inputUserName = $_POST['username'];
$inputUserPass = $_POST['userpass'];

$inputAccountName = $_POST['account'];
if (strlen($_GET['lo'])>=6)
	{
	$inputPassWord = $_GET['lo'];
	}
else
	{
	$inputPassWord = $_POST['password'];
	}




$inputPanelWidth = $_GET['pwidth'];
$inputPanelHeight = $_GET['pheight'];

$inputPreview = $_POST['preview'];
$inputSubmit = $_POST['submit'];

$inputBank_UIN = $_POST['b_uin'];

$inputRegister_UIN = $_POST['r_uin']; 
$inputRegister_Name = $_POST['r_username'];
$inputRegister_Pass = $_POST['r_userpass']; 

$inputRegister_Email= $_POST['r_email'];
$inputRegister_IdentName= $_POST['r_identname'];
$inputRegister_IdentNumber= $_POST['r_identnumber'];
$inputRegister_Roles= $_POST['r_roles'];

//these are old and not used currently
$transPMemo1 = $_POST['inputPMemo1'];
$transPMemo2 = $_POST['inputPMemo2'];
$transThreadNum = $_POST['inputThreadNum'];

$inputTransfer_RecipientName = $_POST['inputRecipientName0'];
$inputTransfer_Amount = $_POST['inputAmount0'];
$inputMemo = $_POST['inputMemo0'];

$i = 0;
while ($_POST['inputRecipientName'.$i]!="")
	{
	$inputTransfers[$i]['inputRecipientName'] = $_POST['inputRecipientName'.$i];
	$inputTransfers[$i]['inputAmount'] = $_POST['inputAmount'.$i];
	$inputTransfers[$i]['inputMemo'] = $_POST['inputMemo'.$i];
	$i++;
	
	}


array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly" >Action: %s</span>',htmlspecialchars($_POST['action'])),
		'alabel' => 'broadcast'));


if ($inputPreview!=NULL)
{
$actionExecute = FALSE;
array_push($statusMessages,  array( 'message' => sprintf('<span class="ugly">Preview Mode</span>'),
		'alabel' => 'broadcast'));

}
else
{
$actionExecute = TRUE;
}



/*
 if($inputAction!=""){$html_debug .="inputAction| $inputAction<br>";}
 if($inputInstitute!=""){$html_debug .="inputInstitute| $inputInstitute<br>";}
 if($inputView!=""){$html_debug .="inputView| $inputView<br>";}
 if($inputUserName!=""){$html_debug .="inputUserName| $inputUserName<br>";}
 if($inputUserPass!=""){$html_debug .="inputUserPass| $inputUserPass<br>";}
 if($inputPassWord!=""){$html_debug .="inputPassWord| $inputPassWord<br>";}
 if($forumuser!=""){$html_debug .="forumuser| $forumuser<br>";}

*/
?>