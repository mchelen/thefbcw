<?php

//create Status section


$s_id = 'status';
$s_title = 'Status';
$s_body ='';

for ($i=0;$i<count($statusMessages);$i++)
	{

		if (($statusMessages[$i]['alabel'] == $inputAction) || ($statusMessages[$i]['alabel']== "broadcast") || ($masterDebug == TRUE))
		{
			$s_body .= sprintf('%s <br />',$statusMessages[$i]['message']);
		}
	
	}

	
$s_state = 'A';
	
$contentsection[] = array('id'=> $s_id,
'title'=> $s_title,
'body'=> $s_body,
'state'=> $s_state,
'float'=> 1);

?>