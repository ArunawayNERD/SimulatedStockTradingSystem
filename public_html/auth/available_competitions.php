
<button class="btn btn-primary btn-small" data-toggle="modal" 
  data-target="#createCompetitionsModal"> 
  Create Competitions 
</button>

<h2> Available Competitions </h2>
  <table class="table">  
    <tr>
      <th>Competition</th>
      <th>Creator</th>
      <th>Start</th>
      <th>End</th>
      <th>Buy-in</th>
    </tr>
    <?php
      $comps = listAvailableComps($uid, $portfolio); 
      foreach($comps as $comp) {
        echo "<tr>";
	echo "<td>" . $comp['name'] . "</td>";
	echo "<td>" . $comp['creator'] . "</td>";
	echo "<td>" . $comp['start_time'] . "</td>";
	echo "<td>" . $comp['end_time'] . "</td>";
	echo "<td>" . sprintf("$%.2f",$comp['buyin']) . "</td>";
        echo "<td><form method=\"POST\" action=\"index.php?competitions\">";
	echo "<input type=\"submit\" value=\"Join\" />";
	echo "<input type=\"hidden\" name=\"joinComp\" ";
	echo "value=\"" . $comp["cid"]  . "\" />";
	echo "</form></td>";
	echo "</tr>\n";
      }
    ?>
  </table>
<?php include 'winners.php'; ?>  
