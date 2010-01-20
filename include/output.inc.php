<?php

$username = $path[0];
//echo $username;

$url = "http://twitter.com/users/show/".$username.".json";
// echo "url: $url";

$json = @file_get_contents($url);
if (strpos($http_response_header[0], "200")) { 
$obj = json_decode($json);
$id = $obj->{'id'};
// echo "id: $id";

echo "Username: $username  Account Number: $id <br />";

include('include/opendb.php');

$Query = sprintf('SELECT * FROM `%1$s`.`main` WHERE `fromId` = %2$d OR `toId` = %2$d ORDER BY `timeStamp` DESC',$dbname,$id);

$Result = mysql_query($Query) or die(mysql_error()); 
while ($row = mysql_fetch_array($Result, MYSQL_ASSOC)) {
    echo "From: "
    .$row['fromUser']
    ."  To: "
    .$row['toUser']
    ."  Amount: "
    .$row['amount']
    ."  Currency: "
    .$row['currency']
    .'  Created At: <a href="http://twitter.com/'
    .$row['fromUser']
    .'/status/'   
    .$row['statusId']
    .'">'
    .$row['createdAt']
    ."</a><br />";
};

include('include/closedb.php');


} else { 
echo "twitter account not found";
}

?>
