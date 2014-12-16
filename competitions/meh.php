<?php
  include 'CompetitionEngine.php';

  $pastComps = getPastComps();

  foreach ($pastComps as $past) {
    echo $past["name"];
  }
?>
