<?php
if (strcmp($accType,"checking")==0){$intRate = 0.0065;}
	elseif (strcmp($accType,"corporate")==0){$intRate = 0.0020;}
	elseif (strcmp($accType,"charity")==0){$intRate = 0.0060;}
	elseif (strcmp($accType,"tfund")==0){$intRate = 0.0065;}
	elseif (strcmp($accType,"cd30")==0){$intRate = 0.0070;}
	elseif (strcmp($accType,"cd90")==0){$intRate = 0.0080;}
	elseif (strcmp($accType,"ploan")==0){$intRate = 0.0085;}
	elseif (strcmp($accType,"cloan")==0){$intRate = 0.0085;}
	elseif (strcmp($accType,"employee")==0){$intRate = 0.0060;}
	elseif (strcmp($accType,"holding")==0){$intRate = 0.0000;}
	elseif (strcmp($accType,"smanaged")==0){$intRate = 0.0060;}

	$accTypeForm = '<option value="checking">Checking</option>
<option value="corporate">Corporate: 0.20%</option>
<option value="charity">Charity: 0.60%</option>
<option value="ploan">Personal Loan: 0.85%</option>
<option value="cloan">Corporate Loan: 0.85%</option>
<option value="tfund">Trust Fund: 0.65%</option>
<option value="cd30">30 Day CD: 0.70%</option>
<option value="cd90">90 Day CD: 0.80%</option>
<option value="employee">Employee: 0.00%</option>
<option value="smanaged">Standard Managed: 0.60%</option>'; ?>