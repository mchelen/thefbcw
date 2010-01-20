<?php
function makeSuggest() {
return;
}

// source: http://www.lampjunkie.com/2008/04/check-if-linux-process-is-still-alive-from-php/
function pidExists($pid)
{
     // create our system command
     $cmd = "ps $pid";
 
     // run the system command and assign output to a variable ($output)
     exec($cmd, $output, $result);
 
     // check the number of lines that were returned
     if(count($output) >= 2){
 
          // the process is still alive
          return true;
     }
 
     // the process is dead
     return false;
}



function startPid($myCommand) {
    $command = 'nohup '.$myCommand.' > /dev/null 2>&1 & echo $!';
    exec($command ,$op);
    return (int)$op[0];
}


?>
