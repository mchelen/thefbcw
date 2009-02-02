<?php



if (($accessType=="UserAccount")&&($userAccountData['uaNumber']==1))
	{

	$admin_override = TRUE;
	$mDebug = TRUE;
	
	if ($inputAction == "backup")
		{
		require 'maint.php';	
		}
	
		$admin_panel_html = '		<form method="post">
		<input type="hidden" name="username" value="%1$s">
		<input type="hidden" name="userpass" value="%2$s">
		<input type="hidden" name="action" value="backup">
		<input type="submit" value="Make Backup">
		</form>
		<form method="post" action="?view=cron">
		<input type="hidden" name="username" value="%1$s">
		<input type="hidden" name="userpass" value="%2$s">
		<input type="submit" value="Run Cron">
		</form>';
		$s_body=sprintf($admin_panel_html,
		htmlspecialchars($_POST['username'], ENT_QUOTES),
		htmlspecialchars($_POST['userpass'], ENT_QUOTES));
	
		
	$s_body.="Site Settings:<br>";
	$s_id = 'admin';
	$s_title = 'Administer';


	foreach ($generalSettings as $settingName => $settingValue)
		{
		$s_body .= sprintf('<span class="important">%s</span> has been set to <span class="important">%s</span><br>',$settingName,$settingValue);
		}

	$contentsection[] = array('id'=> $s_id,
	'title'=> $s_title,
	'body'=> $s_body,
	'state'=> $s_state,
	'float'=> 0);

	
require 'register.php';
	

		
		
		
	}
	else
	{
	
	$adminoverride = FALSE;
	}
	
?>