<?php

if ($_GET['test']=="hash"){
$algos = hash_algos();
$word="aslijfhsalidfh9pan8iouhn87ti8mitm7y98l8y39pu8ra9p3u8xaslijfhsalidfh9pan8y39pu8ra9p3u8xm9p8ux9p8au3zp9u8awr9p438ysn9w8yc";

foreach($algos as $algo)
{
    echo $algo.": ";
    $time=microtime(1);
    echo hash($algo, $word);
    echo "<br>".(microtime(1)-$time)."<br><hr>";
}

}

if ($_GET['test']=="qpc"){

echo "<br>GPC(): ".get_magic_quotes_gpc();

echo "<br>POST input: ".$_POST['input'];

echo '<br><form method="post">
	<input type="text" name="input" value="default">
	<input type="submit">
	</form> ';	

}

//if ($_GET['test']=="sinfo"){

echo sprintf('The include path: %s The document root: %s The script url: %s',get_include_path()
,$_SERVER['DOCUMENT_ROOT']
,$_ENV["SCRIPT_URL"]);


//}


?>