<div id="stocks-wrapper">
<div class="stocks-wrapper">
<h1>Current Stock Prices</h1>

<input type="text" id="searchTerm" class="form-control" onkeyup="doSearch()" placeholder="Search"/>
<div class="stocks-nav">
<ul id="alphabet">
   <li><a href="javascript:;">All</a></li>
   <li><a href="javascript:;">A</a></li>
   <li><a href="javascript:;">B</a></li>
   <li><a href="javascript:;">C</a></li>
   <li><a href="javascript:;">D</a></li>
   <li><a href="javascript:;">E</a></li>
   <li><a href="javascript:;">F</a></li>
   <li><a href="javascript:;">G</a></li>
   <li><a href="javascript:;">H</a></li>
   <li><a href="javascript:;">I</a></li>
   <li><a href="javascript:;">J</a></li>
   <li><a href="javascript:;">K</a></li>
   <li><a href="javascript:;">L</a></li>
   <li><a href="javascript:;">M</a></li>
   <li><a href="javascript:;">N</a></li>
   <li><a href="javascript:;">O</a></li>
   <li><a href="javascript:;">P</a></li>
   <li><a href="javascript:;">Q</a></li>
   <li><a href="javascript:;">R</a></li>
   <li><a href="javascript:;">S</a></li>
   <li><a href="javascript:;">T</a></li>
   <li><a href="javascript:;">U</a></li>
   <li><a href="javascript:;">V</a></li>
   <li><a href="javascript:;">W</a></li>
   <li><a href="javascript:;">X</a></li>
   <li><a href="javascript:;">Y</a></li>
   <li><a href="javascript:;">Z</a></li>
</ul>
</div>

<?php

// make connection
include '../creds.php'; 
$conn = new mysqli($host, $user, $pass, $db);

// verify connection
if ($conn->connect_error) {
  die("Connection Failure: " . $conn->connect_error );
}

$result = $conn->query("Select * from stocks;");

  if ($result->num_rows > 0) {
    echo "<table class='table table-striped table-hover tablesorter' id='stocks'><thead><tr>
            <th>Symbol</th>
            <th>Company Name</th>
            <th>Trade Price</th>
            <th>Opening Price</th>
            <th>Change</th>
            <th>Last Trade Date</th>
            <th>Last Trade Time</th></tr></thead><tbody>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo "<tr id='".$row["symbol"]."' 
        onclick='buildGraph(\"" . $row["symbol"] . "\")' >
        <td>".$row["symbol"]."</td>
	<td>".$row["name"]."</td>
        <td>".$row["last_trade_price"]."</td>
	<td>".$row["open_price"]."</td>
        <td>".$row["price_change"]."</td>
	<td>".$row["last_trade_date"]."</td>
        <td>".$row["last_trade_time"]."</td>
      </tr>";
    }
    echo "</tbody></table>";
  } else {
    echo "0 results";
}

$conn->close();
?>
<div id="dialog" >
  <img id="graph" src="" alt=""/>
</div>
  <script type="text/javascript">
    function buildGraph(stock) {
      document.getElementById("graph").src="CurrentGraph.php?ticker="+stock;
      $("#dialog").dialog({width: 550});
    } 
  </script>

</div>
</div>
<script>
function doSearch() {
   var searchText = document.getElementById('searchTerm').value;
   var targetTable = document.getElementById('stocks');
   var targetTableColCount;
            
   //Loop through table rows
   for (var rowIndex = 0; rowIndex < targetTable.rows.length; rowIndex++) {
      var rowData = '';

      //Get column count from header row
      if (rowIndex == 0) {
         targetTableColCount = targetTable.rows.item(rowIndex).cells.length;
         continue; //do not execute further code for header row.
      }
                
      //Process data rows. (rowIndex >= 1)
      for (var colIndex = 0; colIndex < targetTableColCount; colIndex++) {
         rowData += targetTable.rows.item(rowIndex).cells.item(colIndex).textContent;
      }

      // Make search case insensitive.
      rowData = rowData.toLowerCase();
      searchText = searchText.toLowerCase();


      //If search term is not found in row data
      //then hide the row, else show
      if (rowData.indexOf(searchText) == -1)
         targetTable.rows.item(rowIndex).style.display = 'none';
      else
         targetTable.rows.item(rowIndex).style.display = 'table-row';
   }
}
</script>
