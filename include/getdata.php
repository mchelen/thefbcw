<?php
/**
 * Based on filter-track example http://code.google.com/p/phirehose/source/browse/trunk/example/filter-track.php
 * License: GNU GPLv2
 */

// Start streaming
$sc = new FilterTrackConsumer($twitterUsername, $twitterPassword, Phirehose::METHOD_FILTER);
$sc->setTrack($search);
$sc->consume();

?>
