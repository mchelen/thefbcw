<?php

include('include/opendb.php');
$Query = sprintf('SELECT * FROM `%1$s`.`config` WHERE `name` = CONVERT(_utf8 \'pid\' USING latin1) COLLATE latin1_swedish_ci',$dbname);
$Result = mysql_query($Query) or die(mysql_error()); 
$row = mysql_fetch_array($Result, MYSQL_ASSOC);
include('include/closedb.php');

$pid = $row['value']*1;
// echo $pid;
if (pidExists($pid)) {
//    echo "true";
    if ($pid == getmypid()) {
        include('include/getdata.php');
    }
    else {
//        echo "hooray";
        include('include/input.inc.php');
        include('include/output.inc.php');
    }
}
else {
    echo "false";
    $newPid = startPid("/usr/local/php5/bin/php index.php");
    echo $newPid;
    include('include/opendb.php');
    $Query = sprintf('UPDATE `%1$s`.`config` SET `value` = \'%2$d\' WHERE `name` = CONVERT(_utf8 \'pid\' USING latin1) COLLATE latin1_swedish_ci LIMIT 1;',$dbname,$newPid);
    $Result = mysql_query($Query) or die(mysql_error()); 
    include('include/closedb.php');   
};
// $cmd = "ps -ef | grep processname";
// 
/*
// or if you got the pid, however here only the status() metod will work.
$process = new Process();

$process.setPid($pid);

if ($process.status()) {
    echo "The process is currently running";
    }
    else {
        echo "The process is not running.";
    }

*/

/*
if ($pid == null) {

    }
*/

?>
