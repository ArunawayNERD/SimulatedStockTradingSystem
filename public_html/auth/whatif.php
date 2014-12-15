<h1>What-If Scenario Generator</h1>
<p>Please Enter What If Scenario Information</p>
<div id="whatif-form">
<form action="index.php?whatif" method="post">
   <div class="form-group">
	<label for="histDate">Start Date</label>
	<input type="text" name="histDate" id="histDate" class="datepicker form-control"></br>
   </div>
   <div class="form-group">
	<label for="endDate">End Date</label>
	<input type="text" name="endDate" id="endDate" class="datepicker form-control"></br>
   </div>
   <div class="form-group">
	<label for="ticker">Stock Ticker</label>
	<input type="text" name="ticker" id="ticker" class="form-control"></br>
   </div>
   <div class="form-group">
	<label for="numShares">Share Count</label>
	<input type="number" name="numShares" id="numShares" class="form-control"></br>
   </div>
	<input type="submit" value="Create Scenario">
</form>
</div> <!-- End of whatif-form div -->

<?php
	require "/home/ssts/simulatedstocktradingsystem/whatIf/WhatIfEngine.php";

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
	if(!($histDate == 0 && $endDate == 0 && $ticker == 0 && $numShare == 0))
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
			     '<th>Start Price</th><th>Start Total</th>'.
			     '<th>End Date</th><th>End Price</th>'.
			     '<th>End Total</th><th>Profit</th></tr>');

			//draw the results
			echo('<tr>');
			foreach($result as $item)
			{
				echo('<td>'.$item.'</td>');
			}
			echo('</tr>');

			echo('</table>');
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
				echo('<span class="text-danger">We do not have historical data for the entered start date (Make sure start date isn\'t a weekend)</span>');
				break;

			case -4: 
				echo('<span class="text-danger">We do not have historical data for the entered end date (Make sure end date isn\'t a weekend)</span>');
				break;

			case -5:
				echo('<span class="text-danger"> Number of shares must be positive</span>');
				break;
			}
		}
	}
	echo('</div>');
?>

<script type="text/javascript">
	$(function() {
		$( ".datepicker" ).datepicker({dateFortmat: "yy-mm-dd"});
	});
</script>


