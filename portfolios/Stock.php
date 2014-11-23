<?php

/**
* implements a stock holding the stock name and the number of shares owned
* author: John Pigott
*/

class Stock
{
	private $stockSymbol = "";
	private $stockName = "";	
	private $numShares = 0;

	public function __construct($symbol, $name, $num)
	{
		$this->stockName = $name;
		$this->stockSymbol = $symbol;

		if($num < 0)
			throw new InvalidArgumentException("Number of shares must be greater than or equal to 0. Input was: " . $num);
		
		$this->numShares = $num;
	}

	public function getStockName()
	{
		return (String)$this->stockName;
	}

	public function getNumShares()
	{
		return	(int) $this->numShares;
	}

	public function getStockSymbol()
	{
		return (String) $this->stockSymbol();
	}
	
	public function toString()
	{
		$stringForm = "";

		$stringForm = $this->stockSymbol . "," . $this->getStockName() . "," . $this->getNumShares() . ";";

		return $stringForm;
	}
}
