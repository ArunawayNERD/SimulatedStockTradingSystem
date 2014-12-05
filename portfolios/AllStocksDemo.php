<?php

	require_once "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";

	//returns an array of stock objects
	$stocks = getAllStocks(22, "John");

	//you can get the data in two ways.
	//way 1 with getter methods if you only need some of the data
	foreach($stocks as $stock)
	{
		echo($stock->getStockSymbol() . " ");
		echo($stock->getStockName() . " ");
		echo($stock->getNumShares() ." ");

		echo("\n");
	}

	echo("\n\n\n");

	//the second way is to use the toString method to get all the data at once. 
	foreach($stocks as $stock)
	{
		$parts = explode(",", $stock->toString());

		foreach($parts as $part)
			echo($part . " ");
		
		echo("\n");
	}
