<html>
<body>
	<?php

	include_once "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";
	include_once '/home/ssts/simulatedstocktradingsystem/portfolios/Stock.php';

	echo(getSingleStock(7, "test", "IBM"). "\n");
	
	getAllStocks(7, "test");


	$names =getUserPortfolios(7);
	foreach($names as $name)
	{
		echo($name. " ");
		echo(getCash(7, $name). " ");
		echo(getCompetitionState(7, $name). "\n");
	}

	if(makeNewPortfolio(11, "aBouncingBadyPortfolio"))
		echo("created new portfolio\n");
	else
		echo("no new protfolio created\n");

	switch(changePortfolioName(11, "JohnsTest1", "JohnsTest1")) 
	{
		case 1: 
			echo("Named changed\n");
			break;
		case 0:
			echo("Name already in use\n");
			break;
		case -1:
			echo("No portfolio with old name to change\n");
			break;
	}		
	?>
</body>
</html>
