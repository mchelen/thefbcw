<?php


/**
 * Based on filter-track example http://code.google.com/p/phirehose/source/browse/trunk/example/filter-track.php
 * License: GNU GPLv2
 */
include('lib/phirehose/lib/Phirehose.php');

 
class FilterTrackConsumer extends Phirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name']) && isset($data['in_reply_to_user_id'])) {
    
    print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
    
    
preg_match('/[\s\$](\d+(\.\d+)?)/',urldecode($data['text']),$matches);

$amount = $matches[1];

print "amount: $amount \n";

preg_match('/\$([A-z]+)/',urldecode($data['text']),$matches);

$currency = $matches[1];

print "currency: $currency \n";   

    include('config.php');   
    include('include/opendb.php');
    
$Query = sprintf('INSERT INTO `%1$s`.`main`(
    `localId`,
    `timeStamp`,
    `createdAt`,
    `statusId`,
    `statusText`,
    `fromId`,
    `toId`,
    `fromUser`,
    `toUser`,
    `amount`,
    `currency`
    )
VALUES (
    NULL,
    NULL,
    \'%2$s\',    
    \'%3$d\',
    \'%4$s\',
    \'%5$d\',
    \'%6$d\',
    \'%7$s\',
    \'%8$s\',
    \'%9$f\',
    \'%10$s\'
) ;',
    $dbname,
    $data['created_at'],
    $data['id'],
    urldecode($data['text']),
    $data['user']['id'],
    $data['in_reply_to_user_id'],
    $data['user']['screen_name'],
    $data['in_reply_to_screen_name'],
    $amount,
    $currency
);


//perform query
$Result = mysql_query($Query) or die(mysql_error()); 

include('include/closedb.php');
    
      
    }
  }
}



?>
