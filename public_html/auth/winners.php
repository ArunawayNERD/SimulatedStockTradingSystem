<h2>Past Competitions</h2>
    <table class="table">";
      <tr>
        <th>Competition</th>
        <th>Creator</th>
        <th>Start</th>
        <th>End</th>
        <th>Buy-in</th>
        <th>Results</th>
      </tr>


<?php
  $pastComps = getPastComps();
  
  foreach($pastComps as $past) {
    echo "<tr>";
    echo "<td>" . $past["name"] . "</td>";
    echo "<td>" . $past["creator"] . "</td>";
    echo "<td>" . $past["start_time"] . "</td>";
    echo "<td>" . $past["end_time"] . "</td>";
    echo "<td>" . sprintf("$%.2f", $past["buyin"]) . "</td>";
    echo "<td><ol>";
    echo "<li>" . $past["top1"] . " ";
    echo sprintf("$%.2f", $past["top1value"]) . "</li>";
    echo "<li>" . $past["top2"] . " ";
    echo sprintf("$%.2f", $past["top2value"]) . "</li>";
    echo "<li>" . $past["top3"] . " ";
    echo sprintf("$%.2f", $past["top3value"]) . "</li>";
    echo "</ol></td>";
    echo "</tr>";
  }
?>
  </table>
