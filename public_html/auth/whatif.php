<h1>What-If Scenario Generator</h1>
<p>Please Enter What If Scenario Information</p>
<p class="text-muted">*Note: Start and End Dates must be days that the Stock Market is open</p>
<div id="whatif-form">
<form action="index.php?whatif" method="post">
   <div class="form-group">
	<label for="histDate">Start Date</label>
	<input type="text" name="histDate" id="histDate" class="datepicker2 form-control" /></br>
   </div>
   <div class="form-group">
	<label for="endDate">End Date</label>
	<input type="text" name="endDate" id="endDate" class="datepicker2 form-control" /></br>
   </div>
   <div class="form-group">
	<label for="ticker">Stock Ticker</label>
	<input type="text" name="ticker" id="ticker" class="form-control" /></br>
   </div>
   <div class="form-group">
	<label for="numShares">Share Count</label>
	<input type="number" name="numShares" id="numShares" class="form-control" step="1" min="0"/></br>
   </div>
	<input type="submit" value="Create Scenario" />
</form>
</div> <!-- End of whatif-form div -->
<script type="text/javascript">
	$(function() {
		$( ".datepicker2" ).datepicker({
		  beforeShowDay: $.datepicker.noWeekends,
		  maxDate: -1, minDate: "-9Y"
		});
	});
</script>

<?php
	require_once "/home/ssts/simulatedstocktradingsystem/whatIf/WhatIfEngine.php";
	$histDate = 0;
	$endDate = 0;
	$ticker = 0;
	$numShares = 0;

	
	if(trim($_POST["histDate"]) != "")
	 	$histDate = trim($_POST["histDate"]);

	if(trim($_POST["endDate"]) != "")
		$endDate = trim($_POST["endDate"]);
	
	if(trim($_POST["ticker"])!= "")
		$ticker = trim($_POST["ticker"]);
	
	if(trim($_POST["numShares"]) != "")
		$numShares = trim($_POST["numShares"]);
	
	echo('<div id="whatIfOutput">');
	if(!($histDate == 0 && $endDate == 0 && $ticker == 0 && $numShares == 0))
	{
		$date = explode('/', $histDate);
		$eDate = explode('/', $endDate);

		
		$result =  getWhatIf($ticker, $date[2], $date[0], $date[1], $eDate[2], $eDate[0], $eDate[1], $numShares);
		if(is_array($result))	
		{
			echo('<table class="table table-bordered">');
			echo('<tr>');

			//draw all the table headings
			echo('<th>Stock</th><th>Stock Name</th>'.
			     '<th>Share Count</th><th> Start Date</th>'.
			     '<th>Start Value</th><th>Start Total</th>'.
			     '<th>End Date</th><th>End Value</th>'.
			     '<th>End Total</th><th>Profit</th></tr>');

			//draw the results
			echo('<tr>');
			/*foreach($result as $item)
			{
				if(is_int($item) || is_double($item) || is_float($item))
					echo('<td> 
					echo('<td>'.$item.'</td>');
		
			}*/
			echo('<td>' . $result['ticker'] .'</td>');
			echo('<td>' . $result['name'] . '</td>');
			echo('<td>' . $result['numShares'] . '</td>');
			echo('<td>' . $result['histDate']. '</td>');
			echo('<td>' . sprintf('$%.2f', $result['histPrice']) . '</td>');
			echo('<td>' . sprintf('$%.2f', $result['histTotal']) . '</td>');
			echo('<td>' . $result['endDate'] . '</td>');
			echo('<td>' . sprintf('$%.2f', $result['endPrice']) . '</td>');
			echo('<td>' . sprintf('$%.2f', $result['endTotal']) . '</td>');
			echo('<td>' . sprintf('$%.2f', $result['profit']) . '</td>');

			echo('</tr>');

			echo('</table>');

			echo("</br></br>");

			echo("<img src=\"Graph.php?ticker=" 
			  . $result["ticker"] 
			  . "&start=" . $result["histDate"] 
			  . "&end=" . $result["endDate"] . "\""
			  . " class=\"whatif-graph\"/>");
		}
		else
		{
			switch($result)
			{
			case -1:
				echo('<span class="text-danger">The entered date is not valid</span>');
				break;

			case -2:
				echo('<span class="text-danger">The entered Stock Ticker does not exist in our database</span>');
				break;
			case -3:
				echo('<span class="text-danger">We do not have historical data for the entered start date</span>');
				break;

			case -4: 
				echo('<span class="text-danger">We do not have historical data for the entered end date</span>');
				break;

			}
		}
	}
	echo('</div>');
	
?>



