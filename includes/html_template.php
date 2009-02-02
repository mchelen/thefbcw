<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>

<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">


<link title="Page stylesheet" rel="stylesheet" href="default.css"
	type="text/css">



<SCRIPT type="text/javascript" SRC="includes/default.js"></SCRIPT>




<?php echo  $html_head; ?>

<title><?php echo  $banktitle; ?></title>

</head>

<body>

<span class="logo">&nbsp;</span>

<span class="welcome">Welcome to the <?php echo $banktitle." ".$paneltitle; ?></span>


<span class="atpcontent">
	<span class="atpmenu" style="height: 30px;"><?php

	for ($i=0; $i < count($contentsection); $i++){

	echo sprintf('<a  class="button%2$s" onclick="switchclass(\'%1$s\',\'sectionA\',\'sectionI\'); switchclass(\'%1$sb\',\'buttonA\',\'buttonI\')" id="%1$sb">%1$s</a>',$contentsection[$i]['id'],$contentsection[$i]['state']);

	} ?>
	</span>
<?php

for ($i=0;$i<count($contentsection);$i++){

	echo sprintf('<span class="section%4$s" id="%1$s">
<span class="sectiontitle">%2$s</span>
<span class="sectionbody">%3$s</span></span>',
$contentsection[$i]['id'],
$contentsection[$i]['title'],
$contentsection[$i]['body'],
$contentsection[$i]['state']);


}

?> 
</span>
<br>
<br>










<br>
<br>
<span class="bottom"><?php
echo sprintf('Thank you for using %s automatic teller v%f-%s <!--%f-->',
$bankname,$mVersion,$mBuild,pi()); ?>

</span>
<?php echo $html_footer; ?>

</body>
</html>
