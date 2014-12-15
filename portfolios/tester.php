<html>
<body>
	<?php

	include_once "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";
	include_once '/home/ssts/simulatedstocktradingsystem/portfolios/Stock.php';

/*	echo(getSingleStock(7, "test", "IBM"). "\n");
	
	getAllStocks(7, "test");


	$names =getUserPortfolios(7);
	foreach($names as $name)
	{
		echo($name. " ");
		echo(getPortfolioCash(7, $name). " ");
		echo(getCompetitionState(7, $name). "\n");
	}
*/
/*	if(makeNewPortfolio(11, "aBouncingBadyPortfolio"))
		echo("created new portfolio\n");
	else
		echo("no new protfolio created\n");
*/
/*	switch(changePortfolioName(11, "JohnsTest1", "JohnsTest1")) 
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
*/	
	echo(setPortfolioCash(22, "John", 0 ,100000). "\n");

//	echo(adjustPortfolioCash(11, "JohnsTest1", -1). "\n");
	
//	echo(deletePortfolio(11, "aBouncingBadyPortfolio")."\n");

//	echo(buyStockAmmount(16, "John", "IBM", 0) . "\n");

//	echo(sellStockAmmount(16, "John", "IBM", 0). "\n");
/*	echo(makeNewPortfolio(22, "JohnsTest2")."\n");

	echo((string)getActivePortfolio(22)."\n");
	echo(setActivePortfolio(22, "John"). "\n");
	echo(getActivePortfolio(22). "\n");

	echo(adjustPortfolioCash(22, "JohnsTest2", 1)."\n");
	
	echo(addStockAmount(22, "JohnsTest2", "IBM", 1)."\n");
	echo(removeStockAmount(22, "JohnsTest2", "IBM", 1)."\n");

	echo(changePortfolioName(22, "JohnsTest2", "JohnsTest")."\n"); 

	echo(deletePortfolio(22, "JohnsTest")."\n");
*/

//	echo(addStockAmount(22, "JohnTester2", "IBM", 1)."\n");
//	echo(removeStockAmount(22, "JohnTester2", "IBM", 1)."\n");

	echo(addStockAmount(22, "John", "AAIT", 1)."\n");
	?>
</body>
</html>
